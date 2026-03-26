<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PrintHelper;
use App\Models\Authorization;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\PurchaseDownPayment as model;
use App\Models\PurchaseDownPayment;
use App\Models\PurchaseDownPaymentTax;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class PurchaseDownPaymentController extends Controller
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
    protected string $view_folder = 'purchase-down-payment';

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
            $data = PurchaseDownPayment::with(['vendor', 'purchase'])
                ->whereNull('purchase_down_payments.deleted_at')
                ->when($request->vendor_id, function ($query) use ($request) {
                    return $query->where('purchase_down_payments.vendor_id', $request->vendor_id);
                })
                ->when($request->from_date, function ($query) use ($request) {
                    return $query->whereDate('purchase_down_payments.date', '>=', Carbon::parse($request->from_date));
                })
                ->when($request->to_date, function ($query) use ($request) {
                    return $query->whereDate('purchase_down_payments.date', '<=', Carbon::parse($request->to_date));
                })
                ->select('purchase_down_payments.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('code', function ($row) {
                    $link = route('purchase-down-payment.export.id', ['id' => encryptId($row->id)]);
                    $linkDetail = route('admin.purchase-down-payment.show', $row->id);

                    $button = view('components.button-auth-print', [
                        'type' => 'purchase_order_general',
                        'href' => $link,
                        'model' => PurchaseDownPayment::class,
                        'did' => $row->id,
                        'link' => $linkDetail,
                        'code' => $row->code,
                        'condition' => authorizePrint('purchase_down_payment'),
                        'size' => 'xs',
                    ]);

                    return view('components.datatable.detail-link', [
                        'field' => $row->code,
                        'row' => $row,
                        'main' => $this->view_folder,
                    ]) . '<br>' . $button;
                })
                ->addColumn('po_code', function ($row) {
                    $type = $row->purchase->tipe;
                    switch ($type) {
                        case 'general':
                            return view('components.datatable.detail-link', [
                                'field' => $row->purchase->reference->code,
                                'row' => $row->purchase->reference,
                                'main' => 'purchase-order-general',
                                'permission_name' => 'purchase-general',
                            ]);
                        case 'jasa':
                            return view('components.datatable.detail-link', [
                                'field' => $row->purchase->reference->code,
                                'row' => $row->purchase->reference,
                                'main' => 'purchase-order-service',
                                'permission_name' => 'purchase-service',
                            ]);
                        case 'trading':
                            return view('components.datatable.detail-link', [
                                'field' => $row->purchase->reference->nomor_po,
                                'row' => $row->purchase->reference,
                                'main' => 'purchase-order',
                                'permission_name' => 'purchase-order',
                            ]);
                        default:
                            return view('components.datatable.detail-link', [
                                'field' => $row->purchase->reference->kode,
                                'row' => $row->purchase->reference,
                                'main' => 'purchase-order-transport',
                                'permission_name' => 'purchase-transport',
                            ]);
                    }
                })
                ->addColumn('vendor_name', fn($row) => $row->vendor->nama)
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
                ->rawColumns(['code', 'po_code', 'nama', 'action', 'status', 'payment_status'])
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
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required',
            'due_date' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',
            'purchase_id' => 'required',
            'note' => 'required',
            'total_amount' => 'required',
            'down_payment' => 'required',
            'tax_id' => 'nullable',
        ]);

        DB::beginTransaction();

        // * create purchase
        $model = new model();
        $last_code = model::orderByDesc('id')
            ->withTrashed()
            ->whereMonth('date', Carbon::parse($request->date))
            ->whereYear('date', Carbon::parse($request->date))
            ->first();

        $model->loadModel([
            'code' => generate_code_transaction("PODP", $last_code->code ?? null, date: $request->date),
            'vendor_id' => $request->vendor_id,
            'date' => Carbon::parse($request->date),
            'due_date' => Carbon::parse($request->due_date),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'purchase_id' => $request->purchase_id,
            'note' => $request->note,
            'tax_number' => $request->tax_number,
            'is_include_tax' => $request->is_include_tax ?? 0,
            'total_amount' => thousand_to_float($request->total_amount),
            'subtotal' => thousand_to_float($request->subtotal),
            'down_payment' => thousand_to_float($request->down_payment),
        ]);

        try {
            $tax_total = 0;
            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                $tax_total += $model->subtotal * $tax->value;
            }

            $model->tax_total = $tax_total;
            $model->grand_total = $model->subtotal + $tax_total;
            if ($request->hasFile('tax_attachment')) {
                $model->tax_attachment = $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            }
            $model->save();

            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                PurchaseDownPaymentTax::create([
                    'purchase_down_payment_id' => $model->id,
                    'tax_id' => $tax_id,
                    'value' => $tax->value ?? 0,
                    'amount' => $model->subtotal * $tax->value,
                ]);
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: PurchaseDownPayment::class,
                model_id: $model->id,
                amount: $model->grand_total ?? 0,
                title: "Purchase Down Payment $model->item",
                subtitle: Auth::user()->name . " mengajukan Purchase Down Payment " . $model->code,
                link: route('admin.' . $this->view_folder . '.show', $model),
                update_status_link: route('admin.' . $this->view_folder . '.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "creating purchase down payment", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, 'create', "creating purchase down payment"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = model::with([
            'purchase.general',
            'purchase.trading',
            'purchase.service',
            'purchase.transport',
        ])
            ->findOrFail($id);

        $related_down_payments = $model->purchase->purchase_down_payments
            ->filter(function ($item) use ($model) {
                return in_array($item->status, ['approve', 'pending', 'revert']);
            })
            ->filter(function ($item) use ($model) {
                return Carbon::parse($item->date)->lte(Carbon::parse($model->date));
            })
            ->sortBy('date')
            ->values();

        $model_key = $related_down_payments->search(function ($item) use ($model) {
            return $item->id == $model->id;
        });

        $related_down_payments = $related_down_payments->slice(0, $model_key)->values();

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

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'related_down_payments'));
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
        $purchase_data = $model->purchase->reference;
        $down_payment =  $model->purchase->purchase_down_payments->filter(function ($item) use ($model) {
            return in_array($item->status, ['pending', 'approve']) && $item->id != $model->id;
        })->sum('grand_total');

        $model->po_outstanding =  $purchase_data->total - $down_payment;

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
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required',
            'due_date' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'required',
            'purchase_id' => 'required',
            'note' => 'required',
            'total_amount' => 'required',
            'down_payment' => 'required',
            'tax_id' => 'nullable',
        ]);

        DB::beginTransaction();

        // * create purchase
        $model = model::find($id);

        $model->loadModel([
            'vendor_id' => $request->vendor_id,
            'date' => Carbon::parse($request->date),
            'due_date' => Carbon::parse($request->due_date),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'exchange_rate' => thousand_to_float($request->exchange_rate),
            'purchase_id' => $request->purchase_id,
            'note' => $request->note,
            'tax_number' => $request->tax_number,
            'is_include_tax' => $request->is_include_tax ?? 0,
            'total_amount' => thousand_to_float($request->total_amount),
            'subtotal' => thousand_to_float($request->subtotal),
            'down_payment' => thousand_to_float($request->down_payment),
        ]);

        try {
            $tax_total = 0;
            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                $tax_total += $model->subtotal * $tax->value;
            }

            $model->tax_total = $tax_total;
            $model->grand_total = $model->subtotal + $tax_total;
            if ($request->hasFile('tax_attachment')) {
                Storage::delete($model->tax_attachment);
                $model->tax_attachment = $this->upload_file($request->file('tax_attachment'), 'purchase-down-payment');
            }
            $model->save();

            PurchaseDownPaymentTax::where('purchase_down_payment_id', $model->id)->delete();
            foreach ($request->tax_id ?? [] as $key => $tax_id) {
                $tax = Tax::find($tax_id);
                PurchaseDownPaymentTax::create([
                    'purchase_down_payment_id' => $model->id,
                    'tax_id' => $tax_id,
                    'value' => $tax->value ?? 0,
                    'amount' => $model->subtotal * $tax->value,
                ]);
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: PurchaseDownPayment::class,
                model_id: $model->id,
                amount: $model->grand_total ?? 0,
                title: "Purchase Down Payment $model->item",
                subtitle: Auth::user()->name . " mengajukan Purchase Down Payment " . $model->code,
                link: route('admin.' . $this->view_folder . '.show', $model),
                update_status_link: route('admin.' . $this->view_folder . '.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "updating purchase down payment", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.purchase.index")->with($this->ResponseMessageCRUD(true, 'create', "updating purchase down payment"));
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

            Authorization::where('model', PurchaseDownPayment::class)
                ->where('model_id', $model->id)
                ->delete();

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
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "updating purchase status", $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', "updating purchase status"));
    }

    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('purchase_down_payment')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                \App\Models\PurchaseDownPayment::class,
                decryptId($id),
                'purchase_down_payment',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = \App\Models\PurchaseDownPayment::with([
            'purchase_down_payment_taxes',
            'purchase.general',
            'purchase.trading',
            'purchase.service',
            'purchase.transport'
        ])
            ->findOrFail(decryptId($id));

        $related_down_payments = $model->purchase->purchase_down_payments
            ->filter(function ($item) use ($model) {
                return in_array($item->status, ['approve', 'pending', 'revert']);
            })
            ->filter(function ($item) use ($model) {
                return Carbon::parse($item->date)->lte(Carbon::parse($model->date));
            })
            ->sortBy('date')
            ->values();

        $model_key = $related_down_payments->search(function ($item) use ($model) {
            return $item->id == $model->id;
        });

        $related_down_payments = $related_down_payments->slice(0, $model_key)->values();


        $approval = Authorization::where('model', \App\Models\PurchaseDownPayment::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $qr_url = route('purchase-down-payment.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = PDF::loadView("admin.$this->view_folder.export", compact('model', 'related_down_payments', 'qr', 'approval'));
        $pdf->setPaper($request->paper ?? 'a4', $request->orientation ?? 'potrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        if ($request->preview) {
            Storage::disk('public')->deleteDirectory('tmp_purchase_down_payment');
            $tmp_file_name = 'purchase_down_payment_' . time() . '.pdf';
            $path = 'tmp_purchase_down_payment/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream("PURCHASE DOWN PAYMENT $model->date - $model->code.pdf");
    }

    public function select(Request $request)
    {
        $model = model::where('vendor_id', $request->vendor_id)
            ->where('payment_status', 'paid')
            ->where('currency_id', $request->currency_id)
            ->where(function ($q) use ($request) {
                $q->whereDoesntHave('fund_submissions', function ($q) use ($request) {
                    $q->whereIn('status', ['pending', 'approve', 'revert']);
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

    public function select_purchase(Request $request)
    {
        $model = Purchase::join('vendors', 'vendors.id', 'purchases.vendor_id')
            ->whereNotNull('purchases.model_id')
            ->whereNotIn('status', ['pending', 'void', 'reject', 'cancel'])
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($query) use ($request) {
                    $query->whereHas('general', function ($query) use ($request) {
                        $query->where('code', 'like', "%$request->search%");
                    })->orWhereHas('trading', function ($query) use ($request) {
                        $query->where('nomor_po', 'like', "%$request->search%");
                    })->orWhereHas('service', function ($query) use ($request) {
                        $query->where('code', 'like', "%$request->search%");
                    })->orWhereHas('transport', function ($query) use ($request) {
                        $query->where('kode', 'like', "%$request->search%");
                    });
                });
            })
            ->where(function ($q) use ($request) {
                $q->when($request->selected_id, function ($q) use ($request) {
                    if (is_array($request->selected_id)) {
                        $q->orWhereIn('id', $request->selected_id);
                    } else {
                        $q->orWhere('id', $request->selected_id);
                    }
                });
            })
            ->when($request->vendor_id, function ($query, $vendor_id) {
                $query->where('vendor_id', $vendor_id);
            })
            ->when($request->currency_id, function ($query, $currency_id) {
                $query->where('currency_id', $currency_id);
            })
            ->selectRaw('
                purchases.*,
                vendors.nama as vendor_name,
                (CASE WHEN purchases.model_reference = "App\\\Models\\\PurchaseOrderGeneral" THEN 
                    (SELECT code FROM purchase_order_generals where purchase_order_generals.id = purchases.model_id)
                WHEN purchases.model_reference = "App\\\Models\\\PurchaseOrderService" THEN 
                    (SELECT code FROM purchase_order_services where purchase_order_services.id = purchases.model_id)
                WHEN purchases.model_reference = "App\\\Models\\\PurchaseTransport" THEN 
                    (SELECT kode FROM purchase_transports where purchase_transports.id = purchases.model_id)
                ELSE (SELECT nomor_po FROM purchase_orders where purchase_orders.id = purchases.model_id)
                END) as kode
            ')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    public function purchase_detail($id)
    {
        $model = Purchase::findOrFail($id);
        $purchase_data = $model->model_reference::find($model->model_id);
        $purchase_data->outstanding_amount = $purchase_data->total - $model->purchase_down_payments->filter(function ($item) {
            return in_array($item->status, ['pending', 'approve']);
        })->sum('grand_total');

        return $this->ResponseJson($purchase_data);
    }

    public function history($id, Request $request)
    {
        try {
            $purchase_down_payments = DB::table('purchase_down_payments')
                ->whereNull('purchase_down_payments.deleted_at')
                ->where('purchase_down_payments.id', $id)
                ->select(
                    'purchase_down_payments.id',
                    'purchase_down_payments.code',
                    'purchase_down_payments.date',
                    'purchase_down_payments.status',
                )
                ->get();

            $purchase_down_payments = $purchase_down_payments->map(function ($item) {
                $item->link = route('admin.purchase-down-payment.show', $item->id);
                $item->menu = 'purchase down payment';
                return $item;
            });

            $fund_submissions = DB::table('fund_submissions')
                ->whereNull('fund_submissions.deleted_at')
                ->whereIn('fund_submissions.status', ['approve'])
                ->whereIn('purchase_down_payment_id', $purchase_down_payments->pluck('id')->toArray())
                ->select(
                    'fund_submissions.id',
                    'fund_submissions.code',
                    'fund_submissions.date',
                )
                ->get();

            $fund_submissions = $fund_submissions->map(function ($item) {
                $item->link = route('admin.fund-submission.show', $item->id);
                $item->menu = 'pengajuan dana';
                return $item;
            });

            $cash_advance_payments = DB::table('cash_advance_payments')
                ->whereIn('cash_advance_payments.status', ['approve'])
                ->whereIn('cash_advance_payments.fund_submission_id', $fund_submissions->pluck('id')->toArray())
                ->select(
                    'cash_advance_payments.id',
                    'cash_advance_payments.code',
                    'cash_advance_payments.date',
                )
                ->get();

            $cash_advance_payments = $cash_advance_payments->map(function ($item) {
                $item->link = route('admin.cash-advance-payment.show', $item->id);
                $item->menu = 'pembayaran uang muka';
                return $item;
            });

            $histories = $purchase_down_payments->unique('id')
                ->merge($fund_submissions->unique('id'))
                ->merge($cash_advance_payments->unique('id'));

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
