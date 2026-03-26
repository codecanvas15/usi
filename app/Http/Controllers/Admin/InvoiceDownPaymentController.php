<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PrintHelper;
use App\Models\Employee;
use App\Models\InvoiceDownPayment as model;
use App\Models\InvoiceDownPayment;
use App\Models\InvoiceDownPaymentTax;
use App\Models\SaleOrderGeneral;
use App\Models\SoTrading;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class InvoiceDownPaymentController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'invoice-down-payment';

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
            $data = InvoiceDownPayment::query()
                ->join('customers', 'customers.id', '=', 'invoice_down_payments.customer_id')

                ->leftJoin((new SaleOrderGeneral)->getTable() . ' as sog', function ($join) {
                    $join->on('sog.id', '=', 'invoice_down_payments.sale_order_model_id')
                        ->where('invoice_down_payments.sale_order_model', SaleOrderGeneral::class);
                })

                ->leftJoin((new SoTrading)->getTable() . ' as sot', function ($join) {
                    $join->on('sot.id', '=', 'invoice_down_payments.sale_order_model_id')
                        ->where('invoice_down_payments.sale_order_model', SoTrading::class);
                })


                ->when($request->customer_id, function ($query) use ($request) {
                    $query->where('invoice_down_payments.customer_id', $request->customer_id);
                })

                ->when($request->from_date, function ($query) use ($request) {
                    $query->whereDate('invoice_down_payments.date', '>=', Carbon::parse($request->from_date));
                })

                ->when($request->to_date, function ($query) use ($request) {
                    $query->whereDate('invoice_down_payments.date', '<=', Carbon::parse($request->to_date));
                })

                ->groupBy('invoice_down_payments.id')
                ->selectRaw('
                    invoice_down_payments.*,
                    customers.nama,
                    COALESCE(sog.kode, sot.nomor_so) as so_code
                ');


            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('down_payment', fn($row) => formatNumber($row->down_payment))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . get_invoice_status()[$row->status]['color'] . '">
                                    ' . get_invoice_status()[$row->status]['label'] . ' - ' . get_invoice_status()[$row->status]['text'] . '
                                </div>';

                    return $badge;
                })
                ->editColumn('payment_status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . payment_status()[$row->payment_status]['color'] . '">
                                    ' . payment_status()[$row->payment_status]['label'] . ' - ' . payment_status()[$row->payment_status]['text'] . '
                                </div>';

                    return $badge;
                })
                ->editColumn('so_code', function ($row) {
                    return view('components.datatable.detail-link', [
                        'field' => $row->so_code,
                        'row' => $row->sale_order_model_id,
                        'main' => $row->sale_order_model === SaleOrderGeneral::class
                            ? 'sales-order-general'
                            : 'sales-order',
                    ]);
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => in_array($row->status, ['pending', 'revert']),
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending']),
                            ],
                        ],
                    ]);
                })
                ->filterColumn('so_code', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('sog.kode', 'like', "%{$keyword}%")
                            ->orWhere('sot.nomor_so', 'like', "%{$keyword}%");
                    });
                })

                ->rawColumns(['nama', 'so_code', 'action', 'status', 'payment_status'])
                ->make(true);
        }
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

        return view("admin.$this->view_folder.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'bank_internal_id' => 'required|exists:bank_internals,id',
            'date' => 'required',
            'due_date' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',
            'sale_order_model' => 'required',
            'sale_order_model_id' => 'required',
            'note' => 'required',
            'total_amount' => 'required',
            'down_payment' => 'required',
            'tax_id' => 'nullable',
        ]);

        DB::beginTransaction();

        // * create invoice
        $model = new model();
        $last_code = model::orderByDesc('id')
            ->withTrashed()
            ->whereMonth('date', Carbon::parse($request->date))
            ->whereYear('date', Carbon::parse($request->date))
            ->first();

        $model->loadModel([
            'code' => generate_code_transaction("INVDP", $last_code->code ?? null, date: $request->date),
            'customer_id' => $request->customer_id,
            'bank_internal_id' => $request->bank_internal_id,
            'date' => Carbon::parse($request->date),
            'due_date' => Carbon::parse($request->due_date),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'sale_order_model' => $request->sale_order_model,
            'sale_order_model_id' => $request->sale_order_model_id,
            'note' => $request->note,
            'tax_number' => $request->tax_number,
            'total_amount' => thousand_to_float($request->total_amount),
            'down_payment' => thousand_to_float($request->down_payment),
        ]);

        try {
            $tax_total = 0;
            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                $tax_total += $model->down_payment * $tax->value;
            }

            $model->tax_total = $tax_total;
            $model->grand_total = $model->down_payment + $tax_total;
            if ($request->hasFile('tax_attachment')) {
                $model->tax_attachment = $this->upload_file($request->file('tax_attachment'), 'invoice-down-payment');
            }
            $model->save();

            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                InvoiceDownPaymentTax::create([
                    'invoice_down_payment_id' => $model->id,
                    'tax_id' => $tax_id,
                    'value' => $tax->value ?? 0,
                    'amount' => $model->down_payment * $tax->value,
                ]);
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: InvoiceDownPayment::class,
                model_id: $model->id,
                amount: $model->grand_total ?? 0,
                title: "Invoice Down Payment $model->item",
                subtitle: Auth::user()->name . " mengajukan Invoice Down Payment " . $model->code,
                link: route('admin.' . $this->view_folder . '.show', $model),
                update_status_link: route('admin.' . $this->view_folder . '.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating invoice down payment", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.invoice.index")->with($this->ResponseMessageCRUD(true, 'create', "creating invoice down payment"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] =  $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve' && $model->payment_status == 'unpaid';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $model = model::findOrFail($id);

        return view("admin.$this->view_folder.edit", compact('model'));
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
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id',
            'bank_internal_id' => 'required|exists:bank_internals,id',
            'date' => 'required',
            'due_date' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',
            'sale_order_model' => 'required',
            'sale_order_model_id' => 'required',
            'note' => 'required',
            'total_amount' => 'required',
            'down_payment' => 'required',
            'tax_id' => 'nullable',
        ]);

        DB::beginTransaction();

        // * create invoice
        $model = model::find($id);

        $model->loadModel([
            'customer_id' => $request->customer_id,
            'bank_internal_id' => $request->bank_internal_id,
            'date' => Carbon::parse($request->date),
            'due_date' => Carbon::parse($request->due_date),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'sale_order_model' => $request->sale_order_model,
            'sale_order_model_id' => $request->sale_order_model_id,
            'note' => $request->note,
            'tax_number' => $request->tax_number,
            'total_amount' => thousand_to_float($request->total_amount),
            'down_payment' => thousand_to_float($request->down_payment),
        ]);

        try {
            $tax_total = 0;
            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                $tax_total += $model->down_payment * $tax->value;
            }

            $model->tax_total = $tax_total;
            $model->grand_total = $model->down_payment + $tax_total;
            if ($request->hasFile('tax_attachment')) {
                Storage::delete($model->tax_attachment);
                $model->tax_attachment = $this->upload_file($request->file('tax_attachment'), 'invoice-down-payment');
            }
            $model->save();

            InvoiceDownPaymentTax::where('invoice_down_payment_id', $model->id)->delete();
            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                InvoiceDownPaymentTax::create([
                    'invoice_down_payment_id' => $model->id,
                    'tax_id' => $tax_id,
                    'value' => $tax->value ?? 0,
                    'amount' => $model->down_payment * $tax->value,
                ]);
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: InvoiceDownPayment::class,
                model_id: $model->id,
                amount: $model->grand_total ?? 0,
                title: "Invoice Down Payment $model->item",
                subtitle: Auth::user()->name . " mengajukan Invoice Down Payment " . $model->code,
                link: route('admin.' . $this->view_folder . '.show', $model),
                update_status_link: route('admin.' . $this->view_folder . '.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating invoice down payment", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.invoice.index")->with($this->ResponseMessageCRUD(true, 'create', "updating invoice down payment"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = model::findOrFail($id);

        if (!checkAvailableDate($model->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'destroy', 'periode sudah tutup'));
        }

        DB::beginTransaction();
        try {
            $model->delete();
            DB::commit();

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'destroy'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'destroy', $th->getMessage()));
        }
    }

    /**
     * update_status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrFail($id);

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                // * create status log
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                $model->loadModel([
                    'status' => $request->status,
                ]);

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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "updating invoice status", $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', "updating invoice status"));
    }

    public function export($id,  Request $request)
    {
        set_time_limit(300);
        $model = model::with('customer', 'bank_internal', 'approved_by_user')->findOrFail(decryptId($id));
        $invoice_down_payment_details = $model->invoice_down_payment_details;
        // return $model;
        $invoice_down_payment_additionals = $model->invoice_down_payment_additionals;
        $so_references = SaleOrderGeneral::whereIn('id', $model->so_references ?? [])->get()
            ->map(function ($item) {
                return $item->no_po_external ?? $item->kode;
            })
            ->toArray();

        if (!$request->preview && authorizePrint('invoice_down_payment')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'invoice_down_payment',
            );

            if (!$result) {
                return abort(403);
            }
        }



        $file = public_path('/pdf_reports/Report-Invoice-down-payment-' . microtime(true) . '.pdf');
        $fileName = 'Report-Invoice-down-payment-' . microtime(true) . '.pdf';

        $qr_url = route('admin.invoice-down-payment.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = \App\Models\Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve');
            }])
            ->where('model_id', $model->id)
            ->first();

        $direktur = Employee::whereHas('position', function ($q) {
            $q->where('nama', 'like', '%direktur%');
        })->first();
        // return $model;

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact(
            'model',
            'qr',
            // 'unique_taxes',
            // 'unique_additional_taxes',
            // 'unique_all_taxes',
            // 'approval',
            // 'direktur',
            'so_references'
        ))->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_invoice_down_payment');
            $tmp_file_name = 'invoice_down_payment_' . time() . '.pdf';
            $path = 'tmp_invoice_down_payment/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    public function select(Request $request)
    {
        $model = model::where('customer_id', $request->customer_id)
            ->where('payment_status', 'paid')
            ->where('currency_id', $request->currency_id)
            ->where(function ($q) use ($request) {
                $q->whereDoesntHave('down_payment_invoices', function ($q) use ($request) {
                    $q->whereHas('invoice_parent', function ($q) {
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
                    $q->where('note', 'like', "%$request->search%")
                        ->orWhere('code', 'like', "%$request->search%");
                });
            })
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    public function select_sales_order(Request $request)
    {
        $model = $request->sale_order_model::where('customer_id', $request->customer_id)
            ->where('currency_id', $request->currency_id)
            ->where('payment_status', 'unpaid')
            ->orderBy('created_at', 'desc')
            ->when($request->search, function ($q) use ($request) {
                if ($request->sale_order_model == SaleOrderGeneral::class) {
                    $q->where(function ($q) use ($request) {
                        $q->where('kode', 'like', "%$request->search%");
                    });
                } else {
                    $q->where(function ($q) use ($request) {
                        $q->where('nomor_so', 'like', "%$request->search%");
                    });
                }
            })
            ->paginate(10);

        $model->getCollection()->transform(function ($item) {
            $item->code = $item->kode ?? $item->nomor_so;
            return $item;
        });

        return $this->ResponseJsonData($model);
    }

    public function update_tax($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $model = InvoiceDownPayment::findOrFail($id);

            $tax_number = $request->tax_number;
            $file_path = '';
            if ($request->file('tax_attachment')) {
                $file_path =  $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            } else {
                $file_path = $model->tax_attachment ?? '';
            }

            $model->tax_number = $tax_number;
            $model->tax_attachment = $file_path;
            $model->save();
            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit', null));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }
}
