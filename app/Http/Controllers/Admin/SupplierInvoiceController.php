<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\AccountPayable;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\CashAdvancePayment;
use App\Models\Currency;
use App\Models\ItemReceivingReport;
use App\Models\LpbTaxSummary;
use App\Models\Purchase;
use App\Models\SupplierInvoice as model;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceDetail as detail;
use App\Models\SupplierInvoiceDetail;
use App\Models\SupplierInvoiceDownPayment;
use App\Models\SupplierInvoiceParent;
use App\Models\SupplierInvoicePayment;
use App\Models\SupplierInvoiceTaxSummary;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SupplierInvoiceController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'supplier-invoice';

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
            $branch = Branch::find(get_current_branch_id());

            $data = model::with(['vendor'])
                ->join('vendors', 'vendors.id', 'supplier_invoices.vendor_id')
                ->select('supplier_invoices.*')
                ->when($request->from_date, function ($row) use ($request) {
                    $row->whereDate('accepted_doc_date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($row) use ($request) {
                    $row->whereDate('accepted_doc_date', '<=', Carbon::parse($request->to_date));
                });

            if (!get_current_branch()->is_primary) {
                $data->where('supplier_invoices.branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $data->where('supplier_invoices.branch_id', $request->branch_id);
            }

            if ($request->vendor_id) {
                $data->where('supplier_invoices.vendor_id', $request->vendor_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('created_at', function (model $invoice) {
                    return localDate($invoice->created_at);
                })
                ->editColumn('date', function (model $invoice) {
                    return localDate($invoice->date);
                })
                ->editColumn('accepted_doc_date', function (model $invoice) {
                    return localDate($invoice->accepted_doc_date);
                })
                ->addColumn('vendor', function (model $invoice) {
                    return $invoice->vendor->nama;
                })
                ->addColumn('top', function (model $invoice) {
                    return localDate($invoice->top_due_date);
                })
                ->addColumn('grand_total', function (model $invoice) {
                    return floatDotFormat($invoice->grand_total);
                })
                ->addColumn('status', function (model $invoice) {
                    $badge = '';
                    if ($invoice->status == 'approve') {
                        $badge =  '<span class="badge badge-info">Approved</span>';
                    } elseif ($invoice->status == 'pending') {
                        $badge =  '<span class="badge badge-warning">Pending - waiting approval</span>';
                    } elseif ($invoice->status == 'reject') {
                        $badge =  '<span class="badge badge-dark">Reject - Purchase Invoice Rejected</span>';
                    } elseif ($invoice->status == 'void') {
                        $badge =  '<span class="badge badge-dark">Void - Purchase Invoice Void</span>';
                    } else {
                        $badge =  '<span class="badge badge-dark">Revert - Purchase Invoice Reverted</span>';
                    }

                    if ($invoice->receipt_status) {
                        $badge .= '<br><span class="badge badge-sm badge-success">Kwitansi Tercetak</span>';
                    }

                    return  $badge;
                })
                ->addColumn('payment_status', function (model $invoice) {
                    if ($invoice->payment_status == 'unpaid') {
                        return '<span class="badge badge-danger">Unpaid</span>';
                    } elseif ($invoice->payment_status == 'paid') {
                        return '<span class="badge badge-success">Paid</span>';
                    } else {
                        return '<span class="badge badge-warning">Partial</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $checkAuthorizePrint = authorizePrint('supplier-invoice');
                    $model = model::class;
                    $url = route('admin.supplier-invoice.print-receipt') . "?vendor_id=$row->vendor_id&selected_id=$row->id";
                    $export = "<a href='$url' class='btn btn-sm btn-flat btn-info' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrint ? "data-model='$model' data-id='$row->id' data-print-type='invoice_general' data-link='$url' data-code='$row->code'" : "") . ">Tanda Terima</a>";

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' =>
                        [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => in_array($row->status, ['pending', 'revert']) ? true : false && ($row->model_permission['edit'] ?? true),
                            ],
                            'delete' => [
                                'display' => (in_array($row->status, ['pending', 'revert']) ? true : false) && ($row->model_permission['revert'] ?? true),
                            ],
                        ],
                    ]) . $export;
                })
                ->addColumn('checkbox', function ($row) use ($request) {
                    if ($request->vendor_id) {
                        return '
                            <input type="checkbox"
                                name="select_supplier_invoice_id[]"
                                value="' . $row->id . '"
                                id="select_supplier_invoice_id' . $row->id . '" class="" onchange="checkSelectedSupplierInvoice()">';
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['status', 'payment_status', 'action', 'checkbox'])
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
        $request->validate([
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
            'tax_file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        DB::beginTransaction();

        $branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $branch = Branch::find($branch_id);

        // * create data
        $model = new model();
        $model->code = generate_code(model::class, 'code', 'accepted_doc_date', 'PI', branch_sort: $branch->sort ?? null, date: $request->accepted_doc_date);

        $model->loadModel([
            'vendor_id' => $request->vendor_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'branch_id' => $branch_id,
            'reference' => $request->reference,
            'tax_reference' => $request->tax_reference,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'accepted_doc_date' => Carbon::parse($request->accepted_doc_date)->format('Y-m-d'),
            'term_of_payment' => $request->term_of_payment,
            'top_days' => $request->top_days ?? 0,
            'top_due_date' => Carbon::parse($request->date)->addDays($request->top_days ?? 0)->format('Y-m-d'),
            'po_reference_id' => $request->po_reference_id,
            'po_reference_model' => $request->po_reference_model,
            'po_reference_kode' => $request->po_reference_kode,
            'sub_total' => $request->sub_total,
            'tax_total' => $request->tax_total,
            'grand_total' => $request->grand_total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        if (count($request->item_receiving_report_id ?? []) == 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tidak ada LPB yang dipilih !'));
        }

        // * saving
        try {
            if ($request->hasFile('file')) {
                $model->file = $this->upload_file($request->file('file'), 'supplier-invoice');
            }

            if ($request->hasFile('tax_file')) {
                $model->tax_file = $this->upload_file($request->file('tax_file'), 'supplier-invoice');
            }

            $model->save();

            foreach ($request->item_receiving_report_id as $key => $lpb) {
                if ($request->check_lpb[$key]  ?? null) {
                    $lpbs = ItemReceivingReport::find($lpb);

                    $detail = new detail();
                    $detail->supplier_invoice_id = $model->id;
                    $detail->item_receiving_report_id = $lpb;
                    $detail->reference_id = $lpbs->reference_id;
                    $detail->reference_model = $lpbs->reference_model;
                    $detail->sub_total = $request->item_sub_total[$key];
                    $detail->tax = $request->item_tax[$key];
                    $detail->total = $request->item_total[$key];
                    $detail->notes = $request->notes[$key] ?? '';
                    $detail->save();
                }
            }

            if ($request->cash_advance_payment_id) {
                foreach ($request->cash_advance_payment_id as $key => $cash_advance_payment_id) {
                    SupplierInvoiceDownPayment::create([
                        'supplier_invoice_id' => $model->id,
                        'cash_advance_payment_id' => $cash_advance_payment_id
                    ]);
                }
            }

            // tax summary
            $item_receiving_report_id = $model->detail()->pluck('item_receiving_report_id')->toArray();
            $lpb_tax_summaries = LpbTaxSummary::whereIn('item_receiving_report_id', $item_receiving_report_id)
                ->get();

            $tax_summaries = $lpb_tax_summaries
                ->groupBy('tax_id')
                ->map(function ($item) {
                    return $item->groupBy('value');
                });

            foreach ($tax_summaries as $key => $tax_summary) {
                foreach ($tax_summary as $key2 => $tax_summary2) {
                    SupplierInvoiceTaxSummary::create([
                        'supplier_invoice_id' => $model->id,
                        'tax_id' => $key,
                        'tax_value' => $tax_summary2->first()->tax_value,
                        'sub_total' => $tax_summary2->sum('sub_total'),
                        'tax_amount' => $tax_summary2->sum('tax_amount'),
                    ]);
                }
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->grand_total ?? 0,
                title: "Purchase Invoice",
                subtitle: Auth::user()->name . " mengajukan Purchase Invoice " . $model->code,
                link: route('admin.supplier-invoice.show', $model),
                update_status_link: route('admin.supplier-invoice.update-status', ['id' => $model->id]),
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

        if ($model->detail()->count() == 0) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tidak ada LPB yang dipilih !'));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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
        $model = model::with(['branch', 'vendor', 'detail.item_receiving_report'])->find($id);
        $supplier_invoice_payments = SupplierInvoicePayment::where('supplier_invoice_model', model::class)
            ->where('pay_amount', '>', 0)
            ->where('supplier_invoice_id', $id)
            ->get();

        $is_only_down_payment = $supplier_invoice_payments->where('model', SupplierInvoiceDownPayment::class)->count() == $supplier_invoice_payments->count();

        if ($request->ajax()) {
            return $this->ResponseJson($model);
        }
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = $model->status == "approve" && $model->check_available_date;
        $authorization_logs['can_void'] = false;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->status == "approve" && ($model->payment_status == "unpaid" || $is_only_down_payment) && $model->check_available_date && $model->model_permission['revert'];
        $authorization_logs['can_void_request'] = $model->status == "approve" && ($model->payment_status == "unpaid" || $is_only_down_payment) && $model->check_available_date && $model->model_permission['void'];
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view('admin.' . $this->view_folder . '.show', compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::with(['branch', 'vendor', 'detail.item_receiving_report'])->find($id);
        $model->po_reference_model = str_replace("\\", "\\\\", $model->po_reference_model);

        if (!$model->check_available_date) {
            return abort(403);
        }

        $supplier_invoice_payments = SupplierInvoicePayment::where('supplier_invoice_model', model::class)
            ->where('pay_amount', '>', 0)
            ->where('supplier_invoice_id', $id)
            ->get();

        $is_only_down_payment = $supplier_invoice_payments->where('model', SupplierInvoiceDownPayment::class)->count() == $supplier_invoice_payments->count();


        if (!in_array($model->status, ['pending', 'revert']) || ($model->payment_status != "unpaid" && !$is_only_down_payment) || !$model->model_permission['edit']) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diubah karena sudah dibayar'));
        }

        return view('admin.' . $this->view_folder . '.edit', compact('model'));
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
        $request->validate([
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
            'tax_file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000'
        ]);

        DB::beginTransaction();
        $model = model::findOrfail($id);

        $supplier_invoice_payments = SupplierInvoicePayment::where('supplier_invoice_model', model::class)
            ->where('pay_amount', '>', 0)
            ->where('supplier_invoice_id', $id)
            ->get();

        $is_only_down_payment = $supplier_invoice_payments->where('model', SupplierInvoiceDownPayment::class)->count() == $supplier_invoice_payments->count();


        if (!in_array($model->status, ['pending', 'revert']) || ($model->payment_status != "unpaid" && !$is_only_down_payment) || !$model->model_permission['edit']) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Data tidak dapat diubah karena sudah dibayar'));
        }

        // * updating parent
        $model->fill([
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'accepted_doc_date' => Carbon::parse($request->accepted_doc_date)->format('Y-m-d'),
            'top_due_date' => Carbon::parse($request->date)->addDays($request->top_days ?? 0)->format('Y-m-d'),
            'reference' => $request->reference,
            'tax_reference' => $request->tax_reference,
            'sub_total' => $request->sub_total,
            'tax_total' => $request->tax_total,
            'grand_total' => $request->grand_total,
            'po_reference_id' => $request->po_reference_id,
            'po_reference_model' => str_replace("\\\\", "\\", $request->po_reference_model),
            'po_reference_kode' => $request->po_reference_kode,

        ]);

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        if (count($request->item_receiving_report_id ?? []) == 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tidak ada LPB yang dipilih !'));
        }

        // * saving parent data
        try {
            if ($request->hasFile('file')) {
                Storage::delete($model->file);
                $model->file = $this->upload_file($request->file('file'), 'supplier-invoice');
            }

            if ($request->hasFile('tax_file')) {
                Storage::delete($model->tax_file);
                $model->tax_file = $this->upload_file($request->file('tax_file'), 'supplier-invoice');
            }

            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        // find childs
        $detail = detail::where('supplier_invoice_id', $id);

        try {
            $detail->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        // find all item receiving report
        $lpbs = ItemReceivingReport::wherein('id', $request->item_receiving_report_id ?? [])->get();

        if ($lpbs->count() == 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tidak ada LPB yang dipilih !'));
        }

        // find not approved item receiving report
        $unapproved_lpbs = $lpbs->filter(function ($item) {
            return !in_array($item->status, ['approve', 'return-all']);
        });

        if ($unapproved_lpbs->count() > 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Terdapat LPB yang tidak approve !'));
        }

        SupplierInvoiceDetail::where('supplier_invoice_id', $model->id)->delete();

        // create new details data
        foreach ($request->item_receiving_report_id as $key => $lpb) {
            // find item receiving report
            $data_lpb = $lpbs->where('id', $lpb)->first();

            // return error if item receiving report not found
            if (!$data_lpb) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, "data lpb tidak ditemukan"));
            }

            // check if checked
            if (isset($request->check_lpb[$key])) {
                // * create new details data
                $detail = new detail();
                $detail->fill([
                    'supplier_invoice_id' => $model->id,
                    'item_receiving_report_id' => $data_lpb->id,
                    'reference_id' => $data_lpb->reference_id,
                    'reference_model' => $data_lpb->reference_model,
                    'sub_total' => $request->item_sub_total[$key],
                    'tax' => $request->item_tax[$key],
                    'total' => $request->item_total[$key],
                    'notes' => $request->notes[$key] ?? null,
                ]);

                try {
                    $detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
                }
            }
        }

        if ($request->cash_advance_payment_id) {
            SupplierInvoiceDownPayment::where('supplier_invoice_id', $model->id)
                ->whereNotIn('cash_advance_payment_id', $request->cash_advance_payment_id)
                ->delete();

            foreach ($request->cash_advance_payment_id as $key => $cash_advance_payment_id) {
                SupplierInvoiceDownPayment::updateOrCreate([
                    'supplier_invoice_id' => $model->id,
                    'cash_advance_payment_id' => $cash_advance_payment_id
                ], [
                    'supplier_invoice_id' => $model->id,
                    'cash_advance_payment_id' => $cash_advance_payment_id
                ]);
            }
        } else {
            SupplierInvoiceDownPayment::where('supplier_invoice_id', $model->id)->delete();
        }

        // tax summary
        $item_receiving_report_id = $model->detail()->pluck('item_receiving_report_id')->toArray();
        $lpb_tax_summaries = LpbTaxSummary::whereIn('item_receiving_report_id', $item_receiving_report_id)
            ->get();

        $tax_summaries = $lpb_tax_summaries
            ->groupBy('tax_id')
            ->map(function ($item) {
                return $item->groupBy('value');
            });

        SupplierInvoiceTaxSummary::where('supplier_invoice_id', $model->id)->delete();
        foreach ($tax_summaries as $key => $tax_summary) {
            foreach ($tax_summary as $key2 => $tax_summary2) {
                SupplierInvoiceTaxSummary::create([
                    'supplier_invoice_id' => $model->id,
                    'tax_id' => $key,
                    'tax_value' => $tax_summary2->first()->tax_value,
                    'sub_total' => $tax_summary2->sum('sub_total'),
                    'tax_amount' => $tax_summary2->sum('tax_amount'),
                ]);
            }
        }

        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        $authorization->init(
            branch_id: $model->branch_id,
            user_id: auth()->user()->id,
            model: model::class,
            model_id: $model->id,
            amount: $model->grand_total ?? 0,
            title: "Purchase Invoice",
            subtitle: Auth::user()->name . " mengajukan Purchase Invoice " . $model->code,
            link: route('admin.supplier-invoice.show', $model),
            update_status_link: route('admin.supplier-invoice.update-status', ['id' => $model->id]),
            division_id: auth()->user()->division_id ?? null
        );

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD());
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
        DB::beginTransaction();

        $model = model::findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert']) || $model->payment_status != 'unpaid' || !$model->model_permission['delete']) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data tidak dapat dihapus karena sudah dibayar'));
        }

        $detail = detail::where('supplier_invoice_id', $id);

        // * saving and make reponse
        try {
            $detail->delete();
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, $th->getMessage(), 422);
            }

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'delete');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}

    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);

        $supplier_invoice_payments = SupplierInvoicePayment::where('supplier_invoice_model', model::class)
            ->where('pay_amount', '>', 0)
            ->where('supplier_invoice_id', $id)
            ->get();

        $is_only_down_payment = $supplier_invoice_payments->where('model', SupplierInvoiceDownPayment::class)->count() == $supplier_invoice_payments->count();

        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update status', null, 'Tanggal sudah closing'));
        }

        if ($model->payment_status != "unpaid" && !$is_only_down_payment) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data telah memiliki pembayaran'));
        }

        if ($request->status == 'revert' && !$model->model_permission['revert']) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data tidak dapat diubah'));
        }

        if ($request->status == 'void' && !$model->model_permission['void']) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data tidak dapat diubah'));
        }

        // * saving and make reponse
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
                $model->approved_by = auth()->user()->id;
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

    public function getCurrency()
    {
        $currencies = Currency::all();
        return $this->ResponseJsonData($currencies);
    }

    public function getVendor()
    {
        $vendors = Vendor::all();
        return $this->ResponseJsonData($vendors);
    }

    public function getPo(string $vendor_id, Request $request)
    {
        $data = Purchase::whereHas('item_receiving_reports', function ($q) {
            $q->whereNotIn('status', ['pending', 'revert', 'reject', 'void'])
                ->whereDoesntHave('supplier_invoice_detail', function ($q) {
                    $q->whereHas('supplier_invoice', function ($q) {
                        $q->whereIn('status', ['pending', 'approve', 'revert']);
                    });
                });
        })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->where('vendor_id', $vendor_id)
            ->whereIn('status', ['approve', 'done'])
            ->get();

        foreach ($data as $d) {
            $d->is_has_lpb = $d->poHasLpb();
            if ($d->tipe === 'trading') {
                $d->reference->trading;
            } else if ($d->tipe === 'general') {
                $d->reference->general;
            } else if ($d->tipe === 'jasa') {
                $d->reference->service;
            } else {
                $d->reference->transport;
            }
        }

        return $data;
    }

    public function isReferenceExists(Request $request)
    {
        $model = model::where('reference', $request->reference)->first();
        if (null) {
            return response()->json([
                'message' => 'exists',
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => 'not exists',
                'status' => true,
            ], 200);
        }
    }

    public function isTaxReferenceExists(Request $request)
    {
        $model = model::where('tax_reference', $request->tax_reference)->first();
        if (null) {
            return response()->json([
                'message' => 'exists',
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => 'not exists',
                'status' => true,
            ], 200);
        }
    }

    public function vendor_with_top(Request $request)
    {
        $vendor = Vendor::find($request->id);

        $data['top'] = $vendor->term_of_payment;

        if ($vendor->term_of_payment == 'by days') {
            $data['top_days'] = $vendor->top_days;
        }

        return $this->ResponseJsonData($data);
    }

    public function getLpb(Request $request)
    {
        $supplier_invoice_details = $request->supplier_invoice_id ? SupplierInvoiceDetail::where('supplier_invoice_id', $request->supplier_invoice_id)->pluck('item_receiving_report_id')->toArray() : [];
        $lpbs = ItemReceivingReport::when($request->po_model, function ($query, $po_model) {
            $po_model = str_replace("\\\\", "\\", $po_model);
            $query->where('reference_model', $po_model);
        })
            ->when($request->po_id, function ($query, $po_id) {
                $query->where('reference_id', $po_id);
            })
            ->where('vendor_id', $request->vendor_id)
            ->whereIn('status', ['approve', 'return-all'])
            ->when($request->branch_id, function ($query, $branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->where(function ($q) use ($request) {
                $q->whereDoesntHave('supplier_invoice_detail', function ($supplier_invoice_detail) use ($request) {
                    $supplier_invoice_detail->when($request->supplier_invoice_id, function ($supplier_invoice_detail, $supplier_invoice_id) {
                        $supplier_invoice_detail->where('supplier_invoice_id', '!=',  $supplier_invoice_id);
                    })->whereHas('supplier_invoice', function ($supplier_invoice) {
                        $supplier_invoice->whereIn('status', ['pending', 'revert', 'approve']);
                    });
                })
                    ->when($request->supplier_invoice_id, function ($q) use ($request) {
                        $q->orWhere(function ($q) use ($request) {
                            $q->whereHas('supplier_invoice_detail', function ($q) use ($request) {
                                $q->where('supplier_invoice_id', $request->supplier_invoice_id);
                            });
                        });
                    });
            })
            ->get();

        foreach ($lpbs as $lpb) {
            $lpb->reference;
            if (in_array($lpb->id, $supplier_invoice_details)) {
                $lpb->checked = 'checked';
            } else {
                $lpb->checked = '';
            }

            $cash_advance = $lpb->reference->purchase->cash_advance_payments ?? [];
            if (count($cash_advance) > 0) {
                $lpb->is_has_cash_advance = true;
            } else {
                $lpb->is_has_cash_advance = false;
            }

            foreach ($lpb->item_receiving_report_details as $lpb_detail) {
                $lpb_detail->price;
                $lpb_type = $lpb_detail->item_receiving_report->tipe;

                if ($lpb_type == 'general') {
                    foreach ($lpb->reference->purchaseOrderGeneralDetails as $po_general_detail) {
                        $po_general_detail->purchase_order_general_detail_items;
                    }

                    foreach ($lpb_detail->reference->purchase_order_general_detail_item_taxes as $pog_detail_item_tax) {
                        $pog_detail_item_tax->tax;
                    }
                }

                if ($lpb_type == 'jasa') {
                    foreach ($lpb->reference->purchaseOrderServiceDetails as $po_service_detail) {
                        $po_service_detail->purchase_order_service_detail_items;
                    }

                    foreach ($lpb_detail->reference->purchase_order_service_detail_item_taxes as $pos_detail_item_tax) {
                        $pos_detail_item_tax->tax;
                    }
                }
            }

            if ($lpb->tipe == 'trading') {
                $lpb->reference->po_trading_detail;
                $lpb->item_receiving_report_po_trading->price;

                foreach ($lpb->reference->purchase_order_taxes as $item_tax) {
                    $item_tax->tax;
                }
            }

            if ($lpb->tipe == 'transport') {
                $lpb->reference;
                $lpb->reference->purchase_transport_taxes;
                $lpb->item_receiving_report_purchase_transport;
            }
        }

        return $this->ResponseJsonData($lpbs);
    }

    /**
     * print receipt
     *
     * @param int|string $id
     * @return \Illuminate\Http\Response
     */
    public function print_receipt(Request $request)
    {
        $vendor = Vendor::withTrashed()->find($request->vendor_id);
        $data['vendor'] = $vendor;
        $data['supplier_invoices'] = SupplierInvoice::whereIn('id', explode(',', $request->selected_id))
            ->get();

        SupplierInvoice::whereIn('id', explode(',', $request->selected_id))
            ->update([
                'receipt_status' => 'printed',
            ]);

        $pdf = Pdf::loadView("admin.$this->view_folder.pdf.receipt", $data);
        $pdf->setPaper($request->paper ?? 'a4', $request->orientation ?? 'landscape');

        return $pdf->stream("TANDA TERIMA PURCHASE INVOICE - $vendor->nama.pdf");
    }

    public function history($id, Request $request)
    {
        try {
            $supplier_invoices = DB::table('supplier_invoice_details')
                ->join('supplier_invoices', 'supplier_invoices.id', '=', 'supplier_invoice_details.supplier_invoice_id')
                ->join('supplier_invoice_parents', function ($j) {
                    $j->on('supplier_invoice_parents.reference_id', '=', 'supplier_invoices.id')
                        ->where('supplier_invoice_parents.model_reference', 'App\Models\SupplierInvoice');
                })
                ->whereNull('supplier_invoices.deleted_at')
                ->whereNull('supplier_invoice_details.deleted_at')
                ->where('supplier_invoices.id', $id)
                ->select(
                    'supplier_invoices.id',
                    'supplier_invoices.code',
                    'supplier_invoices.accepted_doc_date as date',
                    'supplier_invoice_details.item_receiving_report_id',
                    'supplier_invoice_parents.id as supplier_invoice_parent_id'
                )
                ->get();

            $item_receiving_reports = DB::table('item_receiving_reports')
                ->whereIn('id', $supplier_invoices->pluck('item_receiving_report_id')->toArray())
                ->whereNull('item_receiving_reports.deleted_at')
                ->select(
                    'item_receiving_reports.id',
                    'item_receiving_reports.kode as code',
                    'item_receiving_reports.date_receive as date',
                    'item_receiving_reports.reference_id',
                    'item_receiving_reports.reference_model',
                    'item_receiving_reports.tipe'
                )
                ->get();

            $purchase_order_generals = DB::table('purchase_order_general_details')
                ->join('purchase_order_generals', 'purchase_order_generals.id', '=', 'purchase_order_general_details.purchase_order_general_id')
                ->whereNull('purchase_order_generals.deleted_at')
                ->whereIn('purchase_order_general_id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PurchaseOrderGeneral')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_order_generals.id',
                    'purchase_order_generals.code',
                    'purchase_order_generals.date',
                    'purchase_order_generals.status',
                    'purchase_order_general_details.purchase_request_id',
                )
                ->get();

            $purchase_order_services = DB::table('purchase_order_service_details')
                ->join('purchase_order_services', 'purchase_order_services.id', '=', 'purchase_order_service_details.purchase_order_service_id')
                ->whereNull('purchase_order_services.deleted_at')
                ->whereIn('purchase_order_services.id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PurchaseOrderService')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_order_services.id',
                    'purchase_order_services.code',
                    'purchase_order_services.date',
                    'purchase_order_services.status',
                    'purchase_order_service_details.purchase_request_id',
                )
                ->get();

            $purchase_orders = DB::table('purchase_orders')
                ->whereIn('id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PoTrading')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_orders.id',
                    'purchase_orders.nomor_po as code',
                    'purchase_orders.tanggal as date',
                    'purchase_orders.status',
                )
                ->get();

            $purchase_transports = DB::table('purchase_transports')
                ->whereIn('id', $item_receiving_reports
                    ->where('reference_model', 'App\Models\PurchaseTransport')
                    ->pluck('reference_id')->toArray())
                ->select(
                    'purchase_transports.id',
                    'purchase_transports.kode as code',
                    'purchase_transports.target_delivery as date',
                    'purchase_transports.status',
                )
                ->get();

            $purchase_request_id = $purchase_order_generals->pluck('purchase_request_id')->toArray();
            $purchase_request_id = array_merge($purchase_request_id, $purchase_order_services->pluck('purchase_request_id')->toArray());

            $purhase_requests = DB::table('purchase_requests')
                ->whereIn('id', $purchase_request_id)
                ->whereNull('deleted_at')
                ->whereIn('status', ['approve', 'done', 'partial'])
                ->select(
                    'id',
                    'kode as code',
                    'tanggal as date'
                )
                ->get();

            $purchase_returns = DB::table('purchase_returns')
                ->whereIn('item_receiving_report_id', $item_receiving_reports->pluck('id')->toArray())
                ->whereIn('status', ['approve', 'done'])
                ->whereNull('purchase_returns.deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'item_receiving_report_id'
                )
                ->get();

            $fund_submissions = DB::table('fund_submission_supplier_details')
                ->join('fund_submissions', 'fund_submissions.id', '=', 'fund_submission_supplier_details.fund_submission_id')
                ->whereNull('fund_submission_supplier_details.deleted_at')
                ->whereNull('fund_submissions.deleted_at')
                ->whereIn('fund_submissions.status', ['pending', 'revert', 'approve'])
                ->whereIn('fund_submission_supplier_details.supplier_invoice_parent_id', $supplier_invoices->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'fund_submission_supplier_details.fund_submission_id as id',
                    'fund_submissions.code',
                    'fund_submissions.date',
                    'fund_submission_supplier_details.supplier_invoice_parent_id'
                )
                ->get();

            $account_payables = DB::table('account_payable_details')
                ->join('account_payables', 'account_payables.id', '=', 'account_payable_details.account_payable_id')
                ->leftJoin('bank_code_mutations', function ($j) {
                    $j->on('account_payables.id', '=', 'bank_code_mutations.ref_id')
                        ->where('bank_code_mutations.ref_model', AccountPayable::class);
                })
                ->whereNull('account_payable_details.deleted_at')
                ->whereNull('account_payables.deleted_at')
                ->whereIn('account_payables.status', ['approve'])
                ->whereIn('account_payable_details.supplier_invoice_parent_id', $supplier_invoices->pluck('supplier_invoice_parent_id')->toArray())
                ->select(
                    'account_payables.id',
                    'account_payables.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'account_payables.date',
                    'account_payable_details.supplier_invoice_parent_id'
                )
                ->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $supplier_invoices = $supplier_invoices->map(function ($item) {
                $item->link = route('admin.supplier-invoice.show', $item->id);
                $item->menu = 'purchase invoice';
                return $item;
            });

            $purchase_order_generals = $purchase_order_generals->map(function ($item) {
                $item->link = route('admin.purchase-order-general.show', $item->id);
                $item->menu = 'purchase order general';
                return $item;
            });

            $purchase_order_services = $purchase_order_services->map(function ($item) {
                $item->link = route('admin.purchase-order-service.show', $item->id);
                $item->menu = 'purchase order service';
                return $item;
            });

            $purchase_orders = $purchase_orders->map(function ($item) {
                $item->link = route('admin.purchase-order.show', $item->id);
                $item->menu = 'purchase order trading';
                return $item;
            });

            $purchase_transports = $purchase_transports->map(function ($item) {
                $item->link = route('admin.purchase-order-transport.show', $item->id);
                $item->menu = 'purchase order transport';
                return $item;
            });

            $item_receiving_reports = $item_receiving_reports->map(function ($item) {
                if ($item->tipe == 'jasa') {
                    $item_type = 'item-receiving-report-service';
                } elseif ($item->tipe == 'general') {
                    $item_type = 'item-receiving-report-general';
                } elseif ($item->tipe == 'trading') {
                    $item_type = 'item-receiving-report-trading';
                } elseif ($item->tipe == 'transport') {
                    $item_type = 'item-receiving-report-transport';
                }


                $item->link = route('admin.' . $item_type . '.show', $item->id);
                $item->menu = 'penerimaan barang ' . $item->tipe;
                return $item;
            });

            $purhase_requests = $purhase_requests->map(function ($item) {
                $item->link = route('admin.purchase-request.show', $item->id);
                $item->menu = 'purchase request';
                return $item;
            });

            $purchase_returns = $purchase_returns->map(function ($item) {
                $item->link = route('admin.purchase-return.show', $item->id);
                $item->menu = 'retur pembelian';
                return $item;
            });
            $fund_submissions = $fund_submissions->map(function ($item) {
                $item->link = route('admin.fund-submission.show', $item->id);
                $item->menu = 'pengajuan dana';
                return $item;
            });

            $account_payables = $account_payables->map(function ($item) {
                $item->link = route('admin.account-payable.show', $item->id);
                $item->menu = 'pelunasan hutang';
                return $item;
            });

            $histories = $purhase_requests->unique('id')
                ->merge($purchase_order_generals->unique('id'))
                ->merge($purchase_order_services->unique('id'))
                ->merge($purchase_orders->unique('id'))
                ->merge($purchase_transports->unique('id'))
                ->merge($item_receiving_reports->unique('id'))
                ->merge($supplier_invoices->unique('id'))
                ->merge($fund_submissions->unique('id'))
                ->merge($account_payables->unique('id'))
                ->merge($purchase_returns->unique('id'))
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

    public function payment_information(Request $request)
    {
        $invoices = SupplierInvoiceParent::whereIn('id', $request->supplier_invoice_ids)->get();
        $invoice_payments = SupplierInvoicePayment::whereIn('supplier_invoice_id', $invoices->pluck('reference_id'))
            ->when($request->date, function ($query) use ($request) {
                return $query->whereDate('date', '<', Carbon::parse($request->date));
            })
            ->with('currency')
            ->get();

        $invoices = $invoices->map(function ($item) use ($invoice_payments) {
            $item->payment_informations = $invoice_payments->where('supplier_invoice_id', $item->reference_id)
                ->where('supplier_invoice_model', $item->model_reference)
                ->sortBy('date')
                ->values()
                ->all();

            return $item;
        });

        return response()->json($invoices);
    }

    public function select_cash_advance(Request $request)
    {
        if (!$request->has('accepted_doc_date')) {
            return response()->json(['error' => 'accepted_doc_date is required'], 400);
        }

        $acceptedDocDate = Carbon::parse($request->accepted_doc_date);

        $model = CashAdvancePayment::where('to_model', Vendor::class)
            ->where('to_id', $request->vendor_id)
            ->where('returned_amount', 0)
            ->where('status', 'approve')
            ->where('currency_id', $request->currency_id)
            ->whereDate('date', '<=', $acceptedDocDate)
            ->where(function ($q) use ($request, $acceptedDocDate) {
                $q->whereDoesntHave('supplier_invoice_down_payments', function ($q) use ($request) {
                    $q->whereHas('supplier_invoice', function ($q) use ($request) {
                        $q->whereIn('status', ['pending', 'approve', 'revert']);
                    });
                })
                    ->when($request->selected_id, function ($q) use ($request) {
                        if (is_array($request->selected_id)) {
                            $q->orWhereIn('id', $request->selected_id);
                        } else {
                            $q->orWhere('id', $request->selected_id);
                        }
                    });
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('reference', 'like', "%$request->search%")
                        ->orWhere('code', 'like', "%$request->search%");
                });
            })
            ->paginate(10);

        // Transform the collection
        $model->getCollection()->transform(function ($item) {
            $item->code = $item->bank_code_mutation;
            return $item;
        });

        // Optionally check if selected CashAdvancePayment(s) meet the accepted_doc_date rule
        if ($request->has('selected_id')) {
            $selectedIds = is_array($request->selected_id) ? $request->selected_id : [$request->selected_id];
            $invalidSelections = CashAdvancePayment::whereIn('id', $selectedIds)
                ->whereDate('date', '>', $acceptedDocDate)
                ->get();

            if ($invalidSelections->isNotEmpty()) {
                $message = 'Warning: Some selected CashAdvancePayment entries do not meet the accepted_doc_date rule.';
                return $this->ResponseJson($model, $message);
            }
        }

        return $this->ResponseJson($model);
    }

    public function update_tax($id, Request $request)
    {
        $request->validate([
            'tax_reference' => 'required',
            'tax_file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5000',
        ]);

        DB::beginTransaction();
        try {
            $model = SupplierInvoice::findOrFail($id);

            $tax_reference = $request->tax_reference;
            $file_path = '';
            if ($request->file('tax_file')) {
                $file_path =  $this->upload_file($request->file('tax_file'), 'supplier-invoice');
            } else {
                $file_path = $model->tax_file ?? '';
            }

            $model->tax_reference = $tax_reference;
            $model->tax_file = $file_path;
            $model->save();

            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit', null));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }

    public function lock($id)
    {
        $model = model::findOrFail($id);
        $invoice_parent = SupplierInvoiceParent::where('model_reference', model::class)->where('reference_id', $model->id)->first();
        DB::beginTransaction();
        try {
            $invoice_parent->lock_status = $invoice_parent->lock_status == 1 ? 0 : 1;
            $invoice_parent->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' =>  $invoice_parent->lock_status == 1 ? 'berhasil mengunci invoice' : 'berhasil membuka kunci invoice',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $invoice_parent->lock_status == 1 ? 'gagal membuka kunci invoice' : 'berhasil mengunci invoice',
            ], 500);
        }
    }

    public function generate_supplier_invoice_down_payment()
    {
        DB::beginTransaction();
        try {
            $supplier_invoice_payments = SupplierInvoicePayment::where('model', SupplierInvoiceDownPayment::class)
                ->get();

            foreach ($supplier_invoice_payments as $key => $supplier_invoice_payment) {
                $supplier_invoice = $supplier_invoice_payment->supplier_invoice_model_ref;
                $item_receiving_report = $supplier_invoice->detail->filter(function ($item) use ($supplier_invoice_payment) {
                    return $item->item_receiving_report->reference->purchase_id == $supplier_invoice_payment->reference_model_ref->cash_advance_payment->purchase_id;
                })->first();

                if ($item_receiving_report) {
                    $supplier_invoice_payment->item_receiving_report_id = $item_receiving_report->item_receiving_report_id;
                    $supplier_invoice_payment->save();
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'berhasil generate purchase invoice down payment'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
