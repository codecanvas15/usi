<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\InvoiceParent;
use App\Models\InvoicePayment;
use App\Models\InvoiceReturn;
use Illuminate\Http\Request;
use App\Models\ReceivablesPayment as model;
use App\Models\ReceivablesPayment;
use App\Models\ReceivablesPaymentDetail as model_detail;
use App\Models\ReceivablesPaymentDetail;
use App\Models\ReceivablesPaymentInvoiceReturn;
use App\Models\ReceivablesPaymentOther;
use App\Models\ReceivablesPaymentVendor;
use App\Models\ReceivablesPaymentVendorLpb;
use App\Models\ReceivePayment;
use App\Models\SupplierInvoiceParent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReceivablesPaymentController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'receivables-payment';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::join('customers', 'customers.id', 'receivables_payments.customer_id')
                ->join('currencies', 'currencies.id', 'receivables_payments.currency_id')
                ->leftJoin('bank_code_mutations', function ($q) {
                    $q->on('receivables_payments.id', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', model::class);
                })
                ->groupBy('receivables_payments.id')
                ->select('receivables_payments.*', 'customers.nama as customer_nama', 'currencies.nama as currency_nama');

            if ($request->customer_id) {
                $data->where('receivables_payments.customer_id', $request->customer_id);
            }

            if (!get_current_branch()->is_primary) {
                $data->where('receivables_payments.branch_id', get_current_branch_id());
            }
            if ($request->branch_id) {
                $data->where('receivables_payments.branch_id', $request->branch_id);
            }

            $data = $data->whereDate('receivables_payments.date', '>=', Carbon::parse($request->from_date))
                ->whereDate('receivables_payments.date', '<=', Carbon::parse($request->to_date));

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('Y-m-d');
                })
                ->editColumn(
                    'code',
                    fn($row) => view('components.datatable.detail-link', [
                        'field' => $row->bank_code_mutation ?? $row->code,
                        'row' => $row,
                        'main' => $this->view_folder,
                    ]) . '<br>' .
                        view("components.datatable.export-button", [
                            'route' => route("receivables-payment.export.id", ['id' => encryptId($row->id)]),
                            'onclick' => "",
                        ])

                )
                ->editColumn('total', function ($row) {
                    return floatDotFormat($row->total);
                })
                ->editColumn('status', function ($row) {
                    $status = incoming_payment_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    $btn = $row->check_available_date;

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn,
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending', 'revert']) && $btn,
                            ],
                        ],
                    ]);
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y');
                })
                ->rawColumns(['action', 'status', 'export', 'code'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view('admin.' . $this->view_folder . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        // * create data
        $model = new model();
        $model->loadModel([
            'branch_id' => $request->branch_id ?? get_current_branch_id(),
            'coa_id' => $request->coa_id,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'customer_id' => $request->customer_id,
            'project_id' => $request->project_id,
            'currency_id' => $request->currency_id,
            'invoice_currency_id' => $request->invoice_currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'reference' => $request->reference,
            'invoice_currency_id' => $request->invoice_currency_id,
            'receive_payment_id' => $request->receive_payment_id,
            'vendor_id' => $request->vendor_id,
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        // * saving
        try {
            // CHECK CURRENCY AND INVOICE CURRENCY
            $is_same_currency = $model->currency_id == $model->invoice_currency_id;
            $is_pay_with_local_currency = $model->currency->is_local;
            $invoice_currency_is_local = $model->invoice_currency->is_local;
            $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
            $local_operation = $is_pay_with_local_currency ? '*' : '/';
            $rate = $is_same_currency ? 1 : $model->exchange_rate;

            $model->save();

            // !! SAVE INVOICE
            foreach ($request->invoice_id ?? [] as $key => $invoice_id) {
                $invoice = InvoiceParent::find($invoice_id);
                $outstanding_amount = (float) $request->outstanding_amount[$key];
                $receive_amount = (float) $request->receive_amount[$key];
                $receive_amount_foreign = (float) $request->receive_amount_foreign[$key];
                $exchange_rate = $invoice->exchange_rate;

                // $gap = ($model->exchange_rate - $exchange_rate) * $receive_amount_foreign;
                $gap = $receive_amount_foreign * $invoice->exchange_rate -  $receive_amount_foreign * $model->exchange_rate;
                if ($invoice_currency_is_local) {
                    $gap = 0;
                }

                $receive_amount_gap_foreign = $outstanding_amount - $receive_amount_foreign;
                $total_foreign = $receive_amount_foreign;
                $is_clearing = $request->is_clearing[$key] ?? 0;
                if ($is_clearing == 1) {
                    $total_foreign = $receive_amount_foreign + $receive_amount_gap_foreign;
                }

                $detail = new model_detail();
                $detail->coa_id = $request->clearing_coa_id[$key] ?? null;
                $detail->receivables_payment_id = $model->id;
                $detail->invoice_parent_id = $invoice_id;
                $detail->exchange_rate =  $exchange_rate;
                $detail->outstanding_amount = $outstanding_amount;

                $detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON AR CURRENCY
                $detail->receive_amount = $receive_amount;
                $detail->receive_amount_gap = eval("return $receive_amount_gap_foreign $local_operation $rate;");
                $detail->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $receive_amount;
                $detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON INVOICE CURRENCY
                $detail->receive_amount_gap_foreign = $receive_amount_gap_foreign;
                $detail->receive_amount_foreign = $receive_amount_foreign;
                $detail->total_foreign = $total_foreign;
                $detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $detail->exchange_rate_gap_note = $request->exchange_rate_gap_note[$key] ?? '-';
                $detail->note = $request->note[$key] ?? '-';
                $detail->is_clearing = $request->is_clearing[$key] ?? '0';
                $detail->clearing_note = $request->clearing_note[$key] ?? '-';
                $detail->save();
            }

            // !! SAVE ADJUSTMENT
            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $credit = thousand_to_float($request->credit[$key] ?? 0);
                $receivables_payment_other = new ReceivablesPaymentOther();
                $receivables_payment_other->receivables_payment_id = $model->id;
                $receivables_payment_other->coa_id = $coa_detail_id;
                $receivables_payment_other->note = $request->note_other[$key];
                $receivables_payment_other->credit = $credit;
                $receivables_payment_other->credit_foreign = eval("return $credit $foreign_operation $model->exchange_rate;");
                $receivables_payment_other->save();
            }

            // !! SAVE SUPPLIER INVOICE
            if ($request->supplier_invoice_parent_id) {
                foreach ($request->supplier_invoice_parent_id ?? [] as $key => $supplier_invoice_parent_id) {
                    $supplier_invoice = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                    $amount = (float) $request->amount_vendor[$key];
                    $outstanding_amount = (float) $request->outstanding_amount_vendor[$key];
                    $amount_foreign = (float) $request->amount_foreign_vendor[$key];
                    $exchange_rate = $supplier_invoice->exchange_rate;

                    // $gap = ($model->exchange_rate - $exchange_rate) * $amount_foreign;
                    $gap = $amount_foreign * $supplier_invoice->exchange_rate - $amount_foreign * $model->exchange_rate;
                    if ($invoice_currency_is_local) {
                        $gap = 0;
                    }

                    $amount_gap_foreign = $outstanding_amount - $amount_foreign;
                    $total_foreign = $amount_foreign;
                    $is_clearing = $request->is_clearing_vendor[$key] ?? 0;
                    if ($is_clearing == 1) {
                        $total_foreign = $amount_foreign + $amount_gap_foreign;
                    }

                    $supplier_invoice_parent = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                    $accounts_receivable_vendor = new ReceivablesPaymentVendor();
                    $accounts_receivable_vendor->coa_id = $request->clearing_coa_id_vendor[$key] ?? null;
                    $accounts_receivable_vendor->receivables_payment_id = $model->id;
                    $accounts_receivable_vendor->supplier_invoice_parent_id = $supplier_invoice_parent_id;
                    $accounts_receivable_vendor->exchange_rate = $supplier_invoice_parent->exchange_rate;
                    $accounts_receivable_vendor->outstanding_amount = (float) $request->outstanding_amount_vendor[$key];

                    $accounts_receivable_vendor->exchange_rate_gap_idr = $gap;
                    // AMOUNT BASE ON FUND SUBMISSION CURRENCY
                    $accounts_receivable_vendor->amount = $amount;
                    $accounts_receivable_vendor->amount_gap = eval("return $amount_gap_foreign $local_operation $rate;");
                    $accounts_receivable_vendor->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $amount;
                    $accounts_receivable_vendor->exchange_rate_gap = $gap / $exchange_rate;

                    // AMOUNT BASE ON SI CURRENCY
                    $accounts_receivable_vendor->amount_gap_foreign = $amount_gap_foreign;
                    $accounts_receivable_vendor->amount_foreign = $amount_foreign;
                    $accounts_receivable_vendor->total_foreign = $total_foreign;
                    $accounts_receivable_vendor->exchange_rate_gap_foreign = $gap / $exchange_rate;

                    $accounts_receivable_vendor->exchange_rate_gap_note = $request->exchange_rate_gap_note_vendor[$key] ?? '-';
                    $accounts_receivable_vendor->note = $request->note_vendor[$key] ?? '-';
                    $accounts_receivable_vendor->is_clearing = $request->is_clearing_vendor[$key];
                    $accounts_receivable_vendor->clearing_note = $request->clearing_note_vendor[$key] ?? '-';
                    $accounts_receivable_vendor->save();

                    $item_receiving_reports = json_decode($request->item_receiving_reports[$key]);
                    foreach ($item_receiving_reports as $key => $item_receiving_report) {
                        $accounts_receivable_vendor_lpb = new ReceivablesPaymentVendorLpb();
                        $accounts_receivable_vendor_lpb->receivables_payment_vendor_id = $accounts_receivable_vendor->id;
                        $accounts_receivable_vendor_lpb->item_receiving_report_id = $item_receiving_report->id;
                        $accounts_receivable_vendor_lpb->outstanding = $item_receiving_report->outstanding;
                        $accounts_receivable_vendor_lpb->amount = $item_receiving_report->amount;
                        $accounts_receivable_vendor_lpb->amount_foreign = $item_receiving_report->amount_foreign;
                        $accounts_receivable_vendor_lpb->save();
                    }
                }
            }

            // !! SAVE RETURN
            foreach ($request->invoice_return_id ?? [] as $key => $invoice_return_id) {
                $invoice_return = InvoiceReturn::find($invoice_return_id);
                $outstanding_amount = thousand_to_float($request->return_outstanding_amount[$key]);
                $return_amount = thousand_to_float($request->return_amount[$key]);
                $return_amount_foreign = thousand_to_float($request->return_amount_foreign[$key]);
                $exchange_rate = $invoice_return->exchange_rate;
                // $gap = ($model->exchange_rate - $exchange_rate) * $return_amount_foreign;
                $gap = $return_amount_foreign * $invoice_return->exchange_rate - $return_amount_foreign * $model->exchange_rate;
                if ($invoice_currency_is_local) {
                    $gap = 0;
                }
                $exchange_rate_gap_idr = $gap;
                $exchange_rate_gap = $gap / $exchange_rate;
                $exchange_rate_gap_foreign = $gap / $exchange_rate;

                $receivebles_payment_invoice_return = new ReceivablesPaymentInvoiceReturn();
                $receivebles_payment_invoice_return->receivables_payment_id = $model->id;
                $receivebles_payment_invoice_return->invoice_return_id = $invoice_return_id;
                $receivebles_payment_invoice_return->exchange_rate = $exchange_rate;
                $receivebles_payment_invoice_return->outstanding_amount = $outstanding_amount;
                $receivebles_payment_invoice_return->amount = $return_amount;
                $receivebles_payment_invoice_return->amount_foreign = $return_amount_foreign;
                $receivebles_payment_invoice_return->exchange_rate_gap_idr = $exchange_rate_gap_idr;
                $receivebles_payment_invoice_return->exchange_rate_gap = $exchange_rate_gap;
                $receivebles_payment_invoice_return->exchange_rate_gap_foreign = $exchange_rate_gap_foreign;
                $receivebles_payment_invoice_return->save();
            }

            $model->total = $model->receivables_payment_details->sum('receive_amount') - $model->receivables_payment_vendors()->sum('amount') + $model->receivables_payment_others->sum('credit') - $model->receivables_payment_invoice_returns->sum('amount');
            $model->exchange_rate_gap_total = $model->receivables_payment_details->sum('exchange_rate_gap_idr') - $model->receivables_payment_vendors()->sum('exchange_rate_gap_idr');
            $model->save();

            try {
                $code = generate_bank_code(
                    ref_model: ReceivablesPayment::class,
                    ref_id: $model->id,
                    coa_id: $model->coa_id,
                    type: 'in',
                    date: $request->date,
                    is_save: true,
                    code: $request->sequence_code,
                );

                if (!$code) {
                    DB::rollBack();
                    $model->delete();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'generate kode error, periksa bank internal coa kas/bank yang dipilih'));
                }
            } catch (\Throwable $th) {
                DB::rollBack();

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Pelunasan Piutang",
                subtitle: Auth::user()->name . " mengajukan Pelunasan Piutang " . $code,
                link: route('admin.receivables-payment.show', $model),
                update_status_link: route('admin.receivables-payment.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.incoming-payment.index", ['tab' => 'receivable-payment-tab'])->with($this->ResponseMessageCRUD());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $int
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = model::findOrFail($id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];
        $receivables_payment_details = $model->receivables_payment_details;
        $model->receivables_payment_details = $model->receivables_payment_details->map(function ($data) use ($model) {
            $data->invoice_parent = $data->invoice_parent;
            $data->payment_informations = InvoicePayment::where('invoice_model', $data->invoice_parent->model_reference)
                ->where('invoice_id', $data->invoice_parent->reference_id)
                ->whereDate('date', '<=', Carbon::parse($model->date))
                ->where(function ($query) use ($model) {
                    $query->where('model', '!=', ReceivablesPaymentDetail::class)
                        ->orWhere('reference_id', '!=', $model->id);
                })
                ->get();

            $data->is_payment_history = $data->payment_informations->where('receive_amount', '!=', 0)->count() > 0;

            return $data;
        });

        $is_payment_history = $model->receivables_payment_details->filter(function ($data) {
            return $data->payment_informations->where('receive_amount', '!=', 0)->count() > 0;
        })->count() > 0;

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.{$this->view_folder}.show", compact('model', 'status_logs', 'activity_logs', 'receivables_payment_details', 'authorization_log_view', 'auth_revert_void_button', 'is_payment_history'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::with(['receivables_payment_details', 'receivables_payment_others', 'receivables_payment_vendors.receivables_payment_vendor_lpbs.item_receiving_report'])->findOrFail($id);

        $receivables_payment_vendors = $model->receivables_payment_vendors;
        foreach ($receivables_payment_vendors as $key => $detail) {
            foreach ($detail->receivables_payment_vendor_lpbs as $key => $lpb) {
                $lpb->code = $lpb->item_receiving_report->kode;
            }
        }

        if (!$model->check_available_date) {
            return abort(403);
        }

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->route("admin.incoming-payment.index", ['tab' => 'receivable-payment-tab'])->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diedit karena sudah melewati tanggal transaksi / status invalid'));
        }

        return view("admin.{$this->view_folder}.edit", compact('model', 'receivables_payment_vendors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        // * create data
        $model = model::find($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->route("admin.incoming-payment.index", ['tab' => 'receivable-payment-tab'])->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diedit karena sudah melewati tanggal transaksi / status invalid'));
        }

        $model->loadModel([
            'branch_id' => $request->branch_id,
            'coa_id' => $request->coa_id,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'customer_id' => $request->customer_id,
            'project_id' => $request->project_id,
            'currency_id' => $request->currency_id,
            'invoice_currency_id' => $request->invoice_currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'reference' => $request->reference,
            'receive_payment_id' => $request->receive_payment_id,
            'vendor_id' => $request->vendor_id ?? null,
        ]);

        // CHECK CURRENCY AND INVOICE CURRENCY
        $is_same_currency = $model->currency_id == $model->invoice_currency_id;
        $is_pay_with_local_currency = $model->currency->is_local;
        $invoice_currency_is_local = $model->invoice_currency->is_local;
        $foreign_operation = $is_pay_with_local_currency ? '/' : '*';
        $local_operation = $is_pay_with_local_currency ? '*' : '/';
        $rate = $is_same_currency ? 1 : $model->exchange_rate;

        // * saving
        try {
            $model->save();

            $detail = model_detail::where('receivables_payment_id', $model->id);
            $receivables_payment_detail_ids = $request->receivable_payment_detail_id ?? [];
            if (($key = array_search("", $receivables_payment_detail_ids)) !== false) {
                unset($receivables_payment_detail_ids[$key]);
            }
            if (count($receivables_payment_detail_ids) > 0) {
                $detail->whereNotIn('id', $receivables_payment_detail_ids);
            }
            $detail = $detail->delete();

            // !! SAVE INVOICE
            foreach ($request->invoice_id ?? [] as $key => $invoice_id) {
                $invoice = InvoiceParent::find($invoice_id);
                $outstanding_amount = (float) $request->outstanding_amount[$key];
                $receive_amount = (float) $request->receive_amount[$key];
                $receive_amount_foreign = (float) $request->receive_amount_foreign[$key];
                $exchange_rate = $invoice->exchange_rate;

                // $gap = ($model->exchange_rate - $exchange_rate) * $receive_amount_foreign;
                $gap = $receive_amount_foreign * $invoice->exchange_rate - $receive_amount_foreign * $model->exchange_rate;
                if ($invoice_currency_is_local) {
                    $gap = 0;
                }

                $receive_amount_gap_foreign = $outstanding_amount - $receive_amount_foreign;
                $total_foreign = $receive_amount_foreign;
                $is_clearing = $request->is_clearing[$key] ?? 0;
                if ($is_clearing == 1) {
                    $total_foreign = $receive_amount_foreign + $receive_amount_gap_foreign;
                }

                $detail = model_detail::where('invoice_parent_id', $invoice_id)
                    ->where('receivables_payment_id', $model->id)
                    ->first();

                if (!$detail) {
                    $detail = new model_detail();
                }
                $detail->coa_id = $request->clearing_coa_id[$key] ?? null;
                $detail->receivables_payment_id = $model->id;
                $detail->invoice_parent_id = $invoice_id;
                $detail->exchange_rate =  $exchange_rate;
                $detail->outstanding_amount = $outstanding_amount;

                $detail->exchange_rate_gap_idr = $gap;
                // AMOUNT BASE ON AR CURRENCY
                $detail->receive_amount = $receive_amount;
                $detail->receive_amount_gap = eval("return $receive_amount_gap_foreign $local_operation $rate;");
                $detail->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $receive_amount;
                $detail->exchange_rate_gap = $gap / $exchange_rate;

                // AMOUNT BASE ON INVOICE CURRENCY
                $detail->receive_amount_gap_foreign = $receive_amount_gap_foreign;
                $detail->receive_amount_foreign = $receive_amount_foreign;
                $detail->total_foreign = $total_foreign;
                $detail->exchange_rate_gap_foreign = $gap / $exchange_rate;

                $detail->exchange_rate_gap_note = $request->exchange_rate_gap_note[$key] ?? '-';
                $detail->note = $request->note[$key] ?? '-';
                $detail->is_clearing = $request->is_clearing[$key] ?? '0';
                $detail->clearing_note = $request->clearing_note[$key] ?? '-';
                $detail->save();
            }

            // !! SAVE ADJUSTMENT
            ReceivablesPaymentOther::whereNotIn('id', $request->receivables_payment_other_id ?? [])
                ->where('receivables_payment_id', $model->id)
                ->delete();

            foreach ($request->coa_detail_id ?? [] as $key => $coa_detail_id) {
                $credit = thousand_to_float($request->credit[$key] ?? 0);
                $receivables_payment_other = ReceivablesPaymentOther::find($request->receivables_payment_other_id[$key]);
                if (!$receivables_payment_other) {
                    $receivables_payment_other = new ReceivablesPaymentOther();
                }
                $receivables_payment_other->receivables_payment_id = $model->id;
                $receivables_payment_other->coa_id = $coa_detail_id;
                $receivables_payment_other->note = $request->note_other[$key];
                $receivables_payment_other->credit = $credit;
                $receivables_payment_other->credit_foreign = eval("return $credit $foreign_operation $model->exchange_rate;");
                $receivables_payment_other->save();
            }

            $vendor_payments = ReceivablesPaymentVendor::where('receivables_payment_id', $model->id);
            $receivables_payment_vendor_ids = $request->receivable_payment_detail_id ?? [];
            if (($key = array_search("", $receivables_payment_vendor_ids)) !== false) {
                unset($receivables_payment_vendor_ids[$key]);
            }
            if (count($receivables_payment_vendor_ids) > 0) {
                $vendor_payments->whereNotIn('id', $receivables_payment_vendor_ids);
            }
            $vendor_payments = $vendor_payments->delete();

            // !! SAVE SUPPLIER INVOICE
            if ($request->supplier_invoice_parent_id) {
                foreach ($request->supplier_invoice_parent_id ?? [] as $key => $supplier_invoice_parent_id) {
                    $supplier_invoice = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                    $amount = (float) $request->amount_vendor[$key];
                    $outstanding_amount = (float) $request->outstanding_amount_vendor[$key];
                    $amount_foreign = (float) $request->amount_foreign_vendor[$key];
                    $exchange_rate = $supplier_invoice->exchange_rate;

                    // $gap = ($model->exchange_rate - $exchange_rate) * $amount_foreign;
                    $gap = $amount_foreign * $supplier_invoice->exchange_rate - $amount_foreign * $model->exchange_rate;
                    if ($invoice_currency_is_local) {
                        $gap = 0;
                    }

                    $amount_gap_foreign = $outstanding_amount - $amount_foreign;
                    $total_foreign = $amount_foreign;
                    $is_clearing = $request->is_clearing_vendor[$key] ?? 0;
                    if ($is_clearing == 1) {
                        $total_foreign = $amount_foreign + $amount_gap_foreign;
                    }

                    $supplier_invoice_parent = SupplierInvoiceParent::find($supplier_invoice_parent_id);
                    $accounts_receivable_vendor = ReceivablesPaymentVendor::where('supplier_invoice_parent_id', $supplier_invoice_parent_id)
                        ->where('receivables_payment_id', $model->id)
                        ->first();

                    if (!$accounts_receivable_vendor) {
                        $accounts_receivable_vendor = new ReceivablesPaymentVendor();
                    }
                    $accounts_receivable_vendor = new ReceivablesPaymentVendor();
                    $accounts_receivable_vendor->coa_id = $request->clearing_coa_id_vendor[$key] ?? null;
                    $accounts_receivable_vendor->receivables_payment_id = $model->id;
                    $accounts_receivable_vendor->supplier_invoice_parent_id = $supplier_invoice_parent_id;
                    $accounts_receivable_vendor->exchange_rate = $supplier_invoice_parent->exchange_rate;
                    $accounts_receivable_vendor->outstanding_amount = (float) $request->outstanding_amount_vendor[$key];

                    $accounts_receivable_vendor->exchange_rate_gap_idr = $gap;
                    // AMOUNT BASE ON FUND SUBMISSION CURRENCY
                    $accounts_receivable_vendor->amount = $amount;
                    $accounts_receivable_vendor->amount_gap = eval("return $amount_gap_foreign $local_operation $rate;");
                    $accounts_receivable_vendor->total = $is_clearing ? eval("return $total_foreign $local_operation $rate;") : $amount;
                    $accounts_receivable_vendor->exchange_rate_gap = $gap / $exchange_rate;

                    // AMOUNT BASE ON SI CURRENCY
                    $accounts_receivable_vendor->amount_gap_foreign = $amount_gap_foreign;
                    $accounts_receivable_vendor->amount_foreign = $amount_foreign;
                    $accounts_receivable_vendor->total_foreign = $total_foreign;
                    $accounts_receivable_vendor->exchange_rate_gap_foreign = $gap / $exchange_rate;

                    $accounts_receivable_vendor->exchange_rate_gap_note = $request->exchange_rate_gap_note_vendor[$key] ?? '-';
                    $accounts_receivable_vendor->note = $request->note_vendor[$key] ?? '-';
                    $accounts_receivable_vendor->is_clearing = $request->is_clearing_vendor[$key];
                    $accounts_receivable_vendor->clearing_note = $request->clearing_note_vendor[$key] ?? '-';
                    $accounts_receivable_vendor->save();

                    $item_receiving_reports = json_decode($request->item_receiving_reports[$key]);
                    foreach ($item_receiving_reports as $key => $item_receiving_report) {
                        $item_receiving_report_id = $item_receiving_report->item_receiving_report_id ?? $item_receiving_report->id;
                        $accounts_receivable_vendor_lpb = ReceivablesPaymentVendorLpb::where('receivables_payment_vendor_id', $accounts_receivable_vendor->id)
                            ->where('item_receiving_report_id', $item_receiving_report_id)
                            ->first();

                        if (!$accounts_receivable_vendor_lpb) {
                            $accounts_receivable_vendor_lpb = new ReceivablesPaymentVendorLpb();
                        }
                        $accounts_receivable_vendor_lpb->receivables_payment_vendor_id = $accounts_receivable_vendor->id;
                        $accounts_receivable_vendor_lpb->item_receiving_report_id = $item_receiving_report_id;
                        $accounts_receivable_vendor_lpb->outstanding = (float) $item_receiving_report->outstanding;
                        $accounts_receivable_vendor_lpb->amount = (float) $item_receiving_report->amount;
                        $accounts_receivable_vendor_lpb->amount_foreign = (float) $item_receiving_report->amount_foreign;
                        $accounts_receivable_vendor_lpb->save();
                    }
                }
            }

            if ($request->invoice_return_id) {
                ReceivablesPaymentInvoiceReturn::where('receivables_payment_id', $model->id)
                    ->whereNotIn('invoice_return_id', $request->invoice_return_id)
                    ->delete();
            } else {
                ReceivablesPaymentInvoiceReturn::where('receivables_payment_id', $model->id)
                    ->delete();
            }

            // !! SAVE RETURN
            foreach ($request->invoice_return_id ?? [] as $key => $invoice_return_id) {
                $invoice_return = InvoiceReturn::find($invoice_return_id);
                $outstanding_amount = thousand_to_float($request->return_outstanding_amount[$key]);
                $return_amount = thousand_to_float($request->return_amount[$key]);
                $return_amount_foreign = thousand_to_float($request->return_amount_foreign[$key]);
                $exchange_rate = $invoice_return->exchange_rate;
                // $gap = ($model->exchange_rate - $exchange_rate) * $return_amount_foreign;
                $gap = $return_amount_foreign * $invoice_return->exchange_rate - $return_amount_foreign * $model->exchange_rate;
                if ($invoice_currency_is_local) {
                    $gap = 0;
                }
                $exchange_rate_gap_idr = $gap;
                $exchange_rate_gap = $gap / $exchange_rate;
                $exchange_rate_gap_foreign = $gap / $exchange_rate;

                $receivebles_payment_invoice_return = ReceivablesPaymentInvoiceReturn::where('receivables_payment_id', $model->id)
                    ->where('invoice_return_id', $invoice_return_id)
                    ->first();

                if (!$receivebles_payment_invoice_return) {
                    $receivebles_payment_invoice_return = new ReceivablesPaymentInvoiceReturn();
                }
                $receivebles_payment_invoice_return->receivables_payment_id = $model->id;
                $receivebles_payment_invoice_return->invoice_return_id = $invoice_return_id;
                $receivebles_payment_invoice_return->exchange_rate = $exchange_rate;
                $receivebles_payment_invoice_return->outstanding_amount = $outstanding_amount;
                $receivebles_payment_invoice_return->amount = $return_amount;
                $receivebles_payment_invoice_return->amount_foreign = $return_amount_foreign;
                $receivebles_payment_invoice_return->exchange_rate_gap_idr = $exchange_rate_gap_idr;
                $receivebles_payment_invoice_return->exchange_rate_gap = $exchange_rate_gap;
                $receivebles_payment_invoice_return->exchange_rate_gap_foreign = $exchange_rate_gap_foreign;
                $receivebles_payment_invoice_return->save();
            }

            $model->total = $model->receivables_payment_details->sum('receive_amount') - $model->receivables_payment_vendors()->sum('amount') + $model->receivables_payment_others->sum('credit') - $model->receivables_payment_invoice_returns->sum('amount');
            $model->exchange_rate_gap_total = $model->receivables_payment_details->sum('exchange_rate_gap_idr') - $model->receivables_payment_vendors()->sum('exchange_rate_gap_idr');
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Pelunasan Piutang",
                subtitle: Auth::user()->name . " mengajukan Pelunasan Piutang " . ($model->bank_code_mutation ?? $model->code),
                link: route('admin.receivables-payment.show', $model),
                update_status_link: route('admin.receivables-payment.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }


        DB::commit();
        return redirect()->route("admin.incoming-payment.index", ['tab' => 'receivable-payment-tab'])->with($this->ResponseMessageCRUD());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            model_detail::where('receivables_payment_id', $id)
                ->delete();
            ReceivablesPaymentOther::where('receivables_payment_id', $id)
                ->delete();
            $vendor_payments = ReceivablesPaymentVendor::where('receivables_payment_id', $id)
                ->get();
            ReceivablesPaymentVendorLpb::whereIn('receivables_payment_vendor_id', $vendor_payments->pluck('id')->toArray())
                ->delete();
            ReceivablesPaymentVendor::where('receivables_payment_id', $id)
                ->delete();
            ReceivablesPaymentInvoiceReturn::where('receivables_payment_id', $id);

            $model->delete();

            Authorization::where('model', model::class)
                ->where('model_id', $id)
                ->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(false, 'delete');
        }

        return redirect()->route("admin.incoming-payment.index", ['tab' => 'receivable-payment-tab'])->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function invoice_select(Request $request)
    {
        $invoices = InvoiceParent::where('customer_id', $request->customer_id)
            ->where('lock_status', 0)
            ->where('status', 'approve')
            ->when($request->curreny_id, function ($q) use ($request) {
                $q->where('currency_id', $request->curreny_id);
            })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->when($request->selected_id, function ($q) use ($request) {
                $q->whereIn('id', $request->selected_id);
            })
            ->when($request->except_id, function ($q) use ($request) {
                $q->whereNotIn('id', $request->except_id);
            })
            ->when($request->date, function ($q) use ($request) {
                $q->whereDate('date', '<=', Carbon::parse($request->date));
            })
            ->with('currency')
            ->with('customer.customer_coas.coa')
            ->orderBy('date')
            ->where('payment_status', '!=', 'paid');

        $un_approved_ar = ReceivablesPaymentDetail::whereIn('invoice_parent_id', $invoices->pluck('id')->toArray())
            ->whereHas('receivables_payment', function ($q) use ($request) {
                $q->whereIn('status', ['pending', 'revert'])
                    ->when($request->receivable_payment_id, function ($q) use ($request) {
                        $q->where('id', '!=', $request->receivable_payment_id);
                    });
            })->get();

        if ($request->invoice_id) {
            $invoices = $invoices->where('id', $request->invoice_id)->first();
            $invoices->outstanding_amount_temp = $invoices->outstanding_amount -  $un_approved_ar
                ->where('invoice_parent_id', $request->invoice_id)
                ->sum('total_foreign');
        } else {
            $invoices = $invoices->get();

            $invoices = $invoices->each(function ($data) use ($un_approved_ar) {
                $data->outstanding_amount_temp = $data->outstanding_amount -  $un_approved_ar->where('invoice_parent_id', $data->id)->sum('total_foreign');

                return $data;
            });

            // filter invoice where outstanding amount != 0
            $invoices = $invoices->filter(function ($data) {
                return $data->outstanding_amount_temp != 0;
            });
        }


        return response()->json($invoices);
    }

    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        }

        validate_branch($model->branch_id);

        // * saving and make response
        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->status = $request->status;
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
    }
    public function export($id, Request $request)
    {
        $model = Model::with(['customer'])->findOrFail(decryptId($id));

        $model->receivables_payment_details = $model->receivables_payment_details->map(function ($data) use ($model, &$is_payment_history) {
            $data->invoice_parent = $data->invoice_parent;
            $data->payment_informations = InvoicePayment::where('invoice_model', $data->invoice_parent->model_reference)
                ->where('invoice_id', $data->invoice_parent->reference_id)
                ->whereDate('date', '<=', Carbon::parse($model->date))
                ->where(function ($query) use ($model) {
                    $query->where('model', '!=', ReceivablesPaymentDetail::class)
                        ->orWhere('reference_id', '!=', $model->id);
                })
                ->get();

            $data->is_payment_history = $data->payment_informations->where('receive_amount', '!=', 0)->count() > 0;

            return $data;
        });

        $is_payment_history = $model->receivables_payment_details->filter(function ($data) {
            return $data->payment_informations->where('receive_amount', '!=', 0)->count() > 0;
        })->count() > 0;

        $file = public_path('/pdf_reports/Report-Receivables-Payment-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf');
        $fileName = 'PENERIMAAN-CUSTOMER-' . ucfirst($model->item) . '-' . microtime(true) . '.pdf';

        $qr_url = route('receivables-payment.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = PDF::loadView("admin.$this->view_folder.export", compact('model', 'qr', 'is_payment_history'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');

        return $pdf->stream($fileName);
    }


    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function invoice_return_select(Request $request)
    {
        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $invoice_returns = InvoiceReturn::where('customer_id', $request->customer_id)
            ->leftJoin('invoice_return_histories', function ($invoice_return_histories) {
                $invoice_return_histories->on('invoice_return_histories.invoice_return_id', 'invoice_returns.id')
                    ->whereIn('invoice_return_histories.status', ['pending', 'revert', 'approve'])
                    ->whereNull('invoice_return_histories.deleted_at');
            })
            ->selectRaw('invoice_returns.*, COALESCE(sum(invoice_return_histories.amount), 0) as total_used')
            ->havingRaw('invoice_returns.total > total_used')
            ->where('invoice_returns.status', 'approve')
            ->where('invoice_returns.branch_id', $branch_id)
            ->whereHas('currency', function ($currency) use ($request) {
                $currency->where('currency_id', $request->currency_id);
            })
            ->when($request->selected_id, function ($invoice_returns) use ($request) {
                $invoice_returns->whereIn('invoice_returns.id', $request->selected_id);
            })
            ->when($request->except_id, function ($invoice_returns) use ($request) {
                $invoice_returns->whereNotIn('invoice_returns.id', $request->except_id);
            })
            ->when($request->date, function ($invoice_returns) use ($request) {
                $invoice_returns->whereDate('invoice_returns.date', '<=', Carbon::parse($request->date));
            })
            ->with('currency')
            ->with('customer.customer_coas.coa')
            ->orderBy('invoice_returns.date');

        if ($request->invoice_return_id) {
            $invoice_returns = $invoice_returns->where('id', $request->invoice_return_id)->first();
            $invoice_returns->outstanding = $invoice_returns->total - $invoice_returns->total_used;
        } else {
            $invoice_returns = $invoice_returns
                ->groupBy('invoice_returns.id')
                ->get();
            $invoice_returns->each(function ($invoice_return) {
                $invoice_return->outstanding = $invoice_return->total - $invoice_return->total_used;
            });
        }

        return response()->json($invoice_returns);
    }

    public function history($id, Request $request)
    {
        try {
            $receivables_payments = DB::table('receivables_payment_details')
                ->join('receivables_payments', 'receivables_payments.id', '=', 'receivables_payment_details.receivables_payment_id')
                ->leftJoin('bank_code_mutations', function ($query) {
                    $query->on('bank_code_mutations.ref_id', '=', 'receivables_payments.id')
                        ->where('bank_code_mutations.ref_model', '=', 'App\Models\ReceivablesPayment');
                })
                ->join('invoice_parents', 'invoice_parents.id', '=', 'receivables_payment_details.invoice_parent_id')
                ->where('receivables_payments.id', $id)
                ->whereNull('receivables_payments.deleted_at')
                ->select(
                    'receivables_payments.id',
                    'receivables_payments.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'receivables_payments.date',
                    'receivables_payments.status',
                    'receivables_payment_details.invoice_parent_id',
                    'invoice_parents.type as invoice_type',
                )->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $invoice_tradings = DB::table('invoice_tradings')
                ->join('invoice_parents', function ($query) use ($receivables_payments) {
                    $query->on('invoice_parents.reference_id', '=', 'invoice_tradings.id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceTrading')
                        ->whereIn('invoice_parents.id', $receivables_payments
                            ->where('invoice_type', 'trading')
                            ->pluck('invoice_parent_id')->toArray());
                })
                ->select(
                    'invoice_tradings.id',
                    'invoice_tradings.kode as code',
                    'invoice_tradings.date',
                    'invoice_tradings.status',
                    'invoice_parents.id as invoice_parent_id',
                    'invoice_tradings.so_trading_id'
                )->get();

            $sale_orders = DB::table('sale_orders')
                ->whereNull('deleted_at')
                ->whereIn('id', $invoice_tradings->pluck('so_trading_id')->toArray())
                ->select(
                    'id',
                    'sale_orders.nomor_so as code',
                    'sale_orders.tanggal as date',
                    'status',
                )->get();

            $delivery_orders = DB::table('delivery_orders')
                ->where('so_trading_id', $sale_orders->pluck('id')->toArray())
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'delivery_orders.id',
                    'delivery_orders.code',
                    'delivery_orders.target_delivery as date',
                    'delivery_orders.status',
                )->get();

            $invoice_generals = DB::table('invoice_generals')
                ->join('invoice_parents', function ($query) use ($receivables_payments) {
                    $query->on('invoice_generals.id', '=', 'invoice_parents.reference_id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceGeneral')
                        ->whereIn('invoice_parents.id', $receivables_payments
                            ->where('invoice_type', 'general')
                            ->pluck('invoice_parent_id')->toArray());
                })
                ->where('invoice_generals.id', $id)
                ->whereNull('invoice_generals.deleted_at')
                ->select(
                    'invoice_generals.id',
                    'invoice_generals.code',
                    'invoice_generals.date',
                    'invoice_generals.status',
                    'invoice_parents.id as invoice_parent_id',
                    'invoice_generals.sale_order_general_id'
                )->get();

            $sale_order_generals = DB::table('sale_order_general_details')
                ->join('sale_order_generals', 'sale_order_generals.id', '=', 'sale_order_general_details.sale_order_general_id')
                ->whereNull('sale_order_generals.deleted_at')
                ->whereIn('sale_order_general_id', $invoice_generals->pluck('sale_order_general_id')->toArray())
                ->select(
                    'sale_order_generals.id',
                    'sale_order_generals.kode as code',
                    'sale_order_generals.tanggal as date',
                    'sale_order_generals.status',
                )
                ->get();

            $delivery_order_generals = DB::table('delivery_order_generals')
                ->where('sale_order_general_id', $sale_order_generals->pluck('id')->toArray())
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'status',
                )->get();

            $invoice_returns = DB::table('invoice_returns')
                ->whereIn('reference_id', $delivery_order_generals->pluck('id')->toArray())
                ->where('reference_model', 'App\Models\DeliveryOrderGeneral')
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'status',
                )->get();


            $sale_orders = $sale_orders->map(function ($item) {
                $item->link = route('admin.sales-order.show', $item->id);
                $item->menu = 'sales order trading';
                return $item;
            });

            $delivery_orders = $delivery_orders->map(function ($item) {
                $item->link = route('admin.delivery-order.show', $item->id);
                $item->menu = 'delivery order trading';
                return $item;
            });

            $invoice_tradings = $invoice_tradings->map(function ($item) {
                $item->link = route('admin.invoice-trading.show', $item->id);
                $item->menu = 'invoice trading';
                return $item;
            });

            $receivables_payments = $receivables_payments->map(function ($item) {
                $item->link = route('admin.receivables-payment.show', $item->id);
                $item->menu = 'receivables payment';
                return $item;
            });

            $histories = $sale_orders->unique('id')
                ->merge($sale_order_generals->unique('id'))
                ->merge($delivery_orders->unique('id'))
                ->merge($delivery_order_generals->unique('id'))
                ->merge($invoice_tradings->unique('id'))
                ->merge($invoice_generals->unique('id'))
                ->merge($invoice_returns->unique('id'))
                ->merge($receivables_payments->unique('id'))
                ->sortBy('date')
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
