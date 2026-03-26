<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Asset;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\ItemReceivingPoTrading;
use App\Models\ItemReceivingReport;
use App\Models\PurchaseReturn as model;
use App\Models\PurchaseReturnDetail as model_detail;
use App\Models\ItemReceivingReportDetail;
use App\Models\ItemReceivingReportTax;
use App\Models\Lease;
use App\Models\PurchaseOrderGeneralDetailItemTax;
use App\Models\PurchaseOrderTax;
use App\Models\PurchaseReturnDetail;
use App\Models\PurchaseReturnTax;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'purchase-return';

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
            $data = model::leftJoin('vendors', 'vendors.id', 'purchase_returns.vendor_id')
                ->select('purchase_returns.*', 'vendors.nama as vendor_nama');

            if (!get_current_branch()->is_primary) {
                $data->where('purchase_returns.branch_id', get_current_branch_id());
            }

            if ($request->from_date) {
                $data->whereDate('purchase_returns.date', '>=', Carbon::parse($request->from_date));
            }

            if ($request->to_date) {
                $data->whereDate('purchase_returns.date', '<=', Carbon::parse($request->to_date));
            }

            if ($request->vendor_id) {
                $data->where('purchase_returns.vendor_id', $request->vendor_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', fn($row) => Carbon::parse($row->date)->format('d-m-Y'))
                ->editColumn('total', fn($row) => formatNumber($row->total))
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . fund_submission_status()[$row->status]['color'] . '">
                    ' . fund_submission_status()[$row->status]['text'] . '
                                    </div>';

                    return $badge;
                })
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->addColumn('action', function ($row) {
                    $btn = $row->check_available_date;

                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) && $btn : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) && $btn : false,
                            ],
                        ],
                    ]);
                })
                ->addColumn('vendor_nama', function ($row) {
                    return $row->vendor_nama ?? '';
                })
                ->editColumn('export', function ($row) {
                    $link = route('purchase-return.export', ['id' => encryptId($row->id)]);
                    $export = '<a target="_blank" href="' . $link . '" class="btn btn-sm btn-info-light" onclick="show_print_out_modal(event)"><i class="fa fa-file-pdf"></i></a>';

                    return $export;
                })
                ->rawColumns(['status', 'action', 'export'])
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
        $branch = Branch::find($request->branch_id);

        try {
            $item_receiving_report = ItemReceivingReport::find($request->item_receiving_report_id);
            $model = new model();
            $item_receiving_report = ItemReceivingReport::findOrFail($request->item_receiving_report_id);

            $model->loadModel([
                'code' => generate_code(model::class, 'code', 'date', "RTB", branch_sort: $branch->sort ?? null, date: Carbon::parse($request->date)->format('Y-m-d')),
                'status' => 'pending',
                'created_by' => auth()->user()->id,
                'branch_id' => $item_receiving_report->branch_id,
                'currency_id' => $request->currency_id,
                'vendor_id' => $request->vendor_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate),
                'date' => Carbon::parse($request->date),
                'ware_house_id' => $request->ware_house_id,
                'item_receiving_report_id' => $request->item_receiving_report_id,
                'project_id' => $request->project_id,
                'reference' => $request->reference,
                'tax_number' => $request->tax_number ?? null,
            ]);

            $model->save();

            foreach ($request->reference_model as $key => $reference_model) {
                $reference_id = $request->reference_id[$key];
                $qty = thousand_to_float($request->qty[$key]);
                $lpb_qty = thousand_to_float($request->lpb_qty[$key]);
                $return_qty = thousand_to_float($request->return_qty[$key]);
                $price = thousand_to_float($request->price[$key]);
                $subtotal = $qty * $price;
                $total_tax_amount = 0;
                $total = 0;

                $model_detail = new model_detail();
                $model_detail->purchase_return_id = $model->id;
                $model_detail->reference_model = $reference_model;
                $model_detail->reference_id = $request->reference_id[$key];
                $model_detail->item_id = $request->item_id[$key];
                $model_detail->unit_id = $request->unit_id[$key];
                $model_detail->lpb_qty = $lpb_qty;
                $model_detail->qty = $qty;
                $model_detail->return_qty = $return_qty;
                $model_detail->price = $price;
                $model_detail->subtotal = $subtotal;
                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $total;
                $model_detail->save();

                foreach ($request->tax_id[$reference_id] ?? [] as $key => $tax_id) {
                    $tax_value = $request->tax_value[$reference_id][$key];
                    $tax_trading_id = $request->tax_trading_id[$reference_id][$key] ?? null;
                    $tax_amount = $subtotal * $tax_value;

                    $purchase_return_tax = new PurchaseReturnTax();
                    $purchase_return_tax->purchase_return_detail_id = $model_detail->id;
                    $purchase_return_tax->tax_id = $tax_id;
                    $purchase_return_tax->tax_trading_id = $tax_trading_id;
                    $purchase_return_tax->value = $tax_value;
                    $purchase_return_tax->amount = $tax_amount;
                    $purchase_return_tax->save();

                    $total_tax_amount += $tax_amount;
                }

                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $subtotal + $total_tax_amount;
                $model_detail->save();
            }

            $model->subtotal = $model->purchase_return_details->sum('subtotal');
            $model->tax_total = $model->purchase_return_details->sum('tax_amount');
            $model->total = $model->purchase_return_details->sum('total');
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Retur Pembelian",
                subtitle: Auth::user()->name . " mengajukan Retur Pembelian " . $model->code,
                link: route('admin.purchase-return.show', $model),
                update_status_link: route('admin.purchase-return.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
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
        $model = model::findOrFail($id);
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

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
        $model = model::findOrFail($id);

        if (!$model->check_available_date) {
            abort(403);
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
        DB::beginTransaction();
        try {
            $model = model::find($id);
            $model->loadModel([
                'branch_id' => $request->branch_id,
                'currency_id' => $request->currency_id,
                'vendor_id' => $request->vendor_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate),
                'date' => Carbon::parse($request->date),
                'ware_house_id' => $request->ware_house_id,
                'item_receiving_report_id' => $request->item_receiving_report_id,
                'project_id' => $request->project_id,
                'reference' => $request->reference,
                'tax_number' => $request->tax_number ?? null,
            ]);

            $model->save();

            $purchase_return_detail_id = [];
            foreach ($request->purchase_return_detail_id as $key => $value) {
                if ($value) {
                    array_push($purchase_return_detail_id, $value);
                }
            }

            $delete_return_detail = PurchaseReturnDetail::where('purchase_return_id', $model->id)
                ->whereNotIn('id', $purchase_return_detail_id)
                ->get();

            foreach ($delete_return_detail as $key => $data) {
                PurchaseReturnTax::where('purchase_return_detail_id', $data->id)
                    ->delete();
                $data->delete();
            }

            foreach ($request->reference_model as $key => $reference_model) {
                $reference_id = $request->reference_id[$key];
                $qty = thousand_to_float($request->qty[$key]);
                $lpb_qty = thousand_to_float($request->lpb_qty[$key]);
                $return_qty = thousand_to_float($request->return_qty[$key]);
                $price = thousand_to_float($request->price[$key]);
                $subtotal = $qty * $price;
                $total_tax_amount = 0;
                $total = 0;

                $model_detail = model_detail::where('reference_model', $reference_model)
                    ->where('reference_id', $reference_id)
                    ->where('purchase_return_id', $model->id)
                    ->first();

                if (!$model_detail) {
                    $model_detail = new model_detail();
                }
                $model_detail->purchase_return_id = $model->id;
                $model_detail->reference_model = $reference_model;
                $model_detail->reference_id = $request->reference_id[$key];
                $model_detail->item_id = $request->item_id[$key];
                $model_detail->unit_id = $request->unit_id[$key];
                $model_detail->lpb_qty = $lpb_qty;
                $model_detail->qty = $qty;
                $model_detail->return_qty = $return_qty;
                $model_detail->price = $price;
                $model_detail->subtotal = $subtotal;
                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $total;
                $model_detail->save();

                foreach ($request->tax_id[$reference_id] ?? [] as $key => $tax_id) {
                    $tax_value = $request->tax_value[$reference_id][$key];
                    $tax_trading_id = $request->tax_trading_id[$reference_id][$key];
                    $tax_amount = $subtotal * $tax_value;

                    $purchase_return_tax = PurchaseReturnTax::where('purchase_return_detail_id', $model_detail->id)
                        ->where('tax_id', $tax_id)
                        ->first();

                    if (!$purchase_return_tax) {
                        $purchase_return_tax = new PurchaseReturnTax();
                    }

                    $purchase_return_tax->purchase_return_detail_id = $model_detail->id;
                    $purchase_return_tax->tax_id = $tax_id;
                    $purchase_return_tax->tax_trading_id = $tax_trading_id;
                    $purchase_return_tax->value = $tax_value;
                    $purchase_return_tax->amount = $tax_amount;
                    $purchase_return_tax->save();

                    $total_tax_amount += $tax_amount;
                }

                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $subtotal + $total_tax_amount;
                $model_detail->save();
            }

            $model->subtotal = $model->purchase_return_details->sum('subtotal');
            $model->tax_total = $model->purchase_return_details->sum('tax_amount');
            $model->total = $model->purchase_return_details->sum('total');
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Retur Pembelian",
                subtitle: Auth::user()->name . " mengajukan Retur Pembelian " . $model->code,
                link: route('admin.purchase-return.show', $model),
                update_status_link: route('admin.purchase-return.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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
            $delete_return_detail = PurchaseReturnDetail::where('purchase_return_id', $model->id)
                ->get();

            foreach ($delete_return_detail as $key => $data) {
                PurchaseReturnTax::where('purchase_return_detail_id', $data->id)
                    ->delete();
                $data->delete();
            }
            $model->delete();

            Authorization::where('model', model::class)
                ->where('model_id', $model->id)
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request) {}

    public function get_lpb_detail(Request $request, $id)
    {
        if ($request->ajax()) {
            $model = ItemReceivingReport::find($id);

            $return_data = [];
            if ($model->tipe == "trading") {
                $purchase_order = $model->reference_model::find($model->reference_id);
                $purchase_order_details = $model->reference_model::find($model->reference_id)->po_trading_detail;
                $currency = $purchase_order->currency;
                $exchange_rate = $purchase_order->exchange_rate;

                $item_receiving_detail = $model->item_receiving_report_po_trading;

                $return_qty = PurchaseReturnDetail::whereHas('purchase_return', function ($p) {
                    $p->where('status', '!=', 'reject');
                    $p->where('status', '!=', 'void');
                })
                    ->where('reference_model', ItemReceivingPoTrading::class)
                    ->where('reference_id', $item_receiving_detail->id)
                    ->sum('qty');

                $push_data['reference_model'] = ItemReceivingPoTrading::class;
                $push_data['reference_id'] = $item_receiving_detail->id;
                $push_data['item'] = $purchase_order_details->item;
                $push_data['unit'] = $purchase_order_details->item->unit;
                $push_data['qty'] = $item_receiving_detail->liter_15;
                $push_data['return_qty'] = $return_qty;
                $push_data['price'] = $purchase_order_details->harga;
                $push_data['taxes'] = PurchaseOrderTax::with('tax')
                    ->with('tax_trading')
                    ->where('po_trading_id', $purchase_order->id)
                    ->get();

                array_push($return_data, $push_data);
            } else {
                $purchase_order = $model->reference_model::find($model->reference_id);
                $currency = $purchase_order->currency;
                $exchange_rate = $purchase_order->exchange_rate;

                // only item where item type is purchase item or inactive asset item can be returned
                $item_receiving_details = ItemReceivingReportDetail::where('item_receiving_report_id', $model->id)
                    ->whereHas('item', function ($i) {
                        $i->whereHas('item_category', function ($ic) {
                            $ic->whereHas('item_type', function ($it) {
                                $it->whereIn('nama', ['purchase item', 'asset', 'biaya dibayar dimuka']);
                            });
                        });
                    })
                    ->get();

                foreach ($item_receiving_details as $key => $item_receiving_detail) {
                    $receiving_qty = $item_receiving_detail->jumlah_diterima;
                    $purchase_order_detail = $item_receiving_detail->reference_model::find($item_receiving_detail->reference_id);

                    $return_qty = PurchaseReturnDetail::whereHas('purchase_return', function ($p) {
                        $p->where('status', '!=', 'reject');
                        $p->where('status', '!=', 'void');
                    })
                        ->where('reference_model', ItemReceivingReportDetail::class)
                        ->where('reference_id', $item_receiving_detail->id)
                        ->sum('qty');

                    if ($item_receiving_detail->item->item_category->item_type->nama == 'asset') {
                        $active_asset = Asset::where('item_receiving_report_detail_id', $item_receiving_detail->id)
                            ->where('status', '!=', 'cancel')
                            ->count();

                        $return_qty += $active_asset;
                    }
                    if ($item_receiving_detail->item->item_category->item_type->nama == 'biaya dibayar dimuka') {
                        $active_lease = Lease::where('item_receiving_report_detail_id', $item_receiving_detail->id)
                            ->where('status', '!=', 'cancel')
                            ->count();

                        $return_qty += $active_lease;
                    }

                    $push_data['reference_model'] = ItemReceivingReportDetail::class;
                    $push_data['reference_id'] = $item_receiving_detail->id;
                    $push_data['item'] = $purchase_order_detail->item;
                    $push_data['unit'] = $purchase_order_detail->item->unit;
                    $push_data['qty'] = $receiving_qty;
                    $push_data['return_qty'] = $return_qty;
                    $push_data['price'] = $purchase_order_detail->price;
                    $push_data['taxes'] = PurchaseOrderGeneralDetailItemTax::with('tax')
                        ->where('purchase_order_general_detail_item_id', $purchase_order_detail->id)
                        ->get();

                    array_push($return_data, $push_data);
                }
            }

            $data['ware_house'] = $model->ware_house;
            $data['currency'] = $currency;
            $data['exchange_rate'] = $exchange_rate;
            $data['details'] = $return_data;
            $data['date_receive'] = localDate($model->date_receive);

            return $this->ResponseJsonData($data);
        }
    }

    /**
     * update status item receiving report
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        DB::beginTransaction();
        $model = model::findOrfail($id);
        validate_branch($model->branch_id);

        if (!checkAvailableDate($model->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Periode Sudah Tutup'));
        }
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

    /**
     * export pdf
     */
    public function export_pdf($id, Request $request)
    {
        $model = model::with([
            'item_receiving_report',
            'branch',
            'currency',
            'ware_house',
            'project',
            'vendor',
            'purchase_return_details.item',
            'purchase_return_details.unit',
            'purchase_return_details.purchase_return_taxes',
        ])->findOrfail(decryptId($id));
        $fileName = 'Return Pembelian ' . strtoupper($model->item) . '.pdf';

        $qr_url = route('purchase-return.export', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = Pdf::loadview("admin.$this->view_folder.export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM} / {PAGE_COUNT}", '', 8);

        return $pdf->stream($fileName);
    }

    /**
     * check unique tax number
     */
    public function check_unique_tax_number(Request $request)
    {
        $model = model::where('tax_number', $request->tax_number)
            ->where('id', '!=', $request->except_id)
            ->first();

        if ($model) {
            return response()->json(
                [
                    'message' => 'Nomor Faktur Pajak sudah digunakan',
                    'status' => false,
                ],
            );
        }

        return response()->json(
            [
                'message' => 'Nomor Faktur Pajak tersedia',
                'status' => true,
            ],
        );
    }

    public function generate_tax()
    {
        DB::beginTransaction();
        try {
            $purchase_returns = model::where('status', 'approve')
                ->get();

            foreach ($purchase_returns as $key => $model) {
                $group_tax = new Collection();
                $model->purchase_return_details->each(function ($purchase_return_detail) use ($group_tax) {
                    $purchase_return_detail->purchase_return_taxes->each(function ($purchase_return_tax) use ($group_tax) {
                        $group_tax->push($purchase_return_tax->tax);
                    });
                });

                $group_tax = $group_tax->unique('id')
                    ->map(function ($item) use ($model) {
                        $item->total_tax = $model->purchase_return_details->sum(function ($detail) use ($item) {
                            return $detail->purchase_return_taxes->where('tax_id', $item->id)->sum('amount');
                        });

                        return $item;
                    });

                foreach ($group_tax as $key => $tax) {
                    if ($tax->type == "ppn") {
                        $item_receiving_report_tax = ItemReceivingReportTax::where('reference_parent_model', get_class($model))
                            ->where('reference_parent_id', $model->id)
                            ->where('reference_model', get_class($model))
                            ->where('reference_id', $model->id)
                            ->first();

                        if (!$item_receiving_report_tax) {
                            $item_receiving_report_tax = new ItemReceivingReportTax();
                        }
                        $item_receiving_report_tax->reference_parent_model = get_class($model);
                        $item_receiving_report_tax->reference_parent_id = $model->id;
                        $item_receiving_report_tax->reference_model = get_class($model);
                        $item_receiving_report_tax->reference_id = $model->id;
                        $item_receiving_report_tax->date = $model->date;
                        $item_receiving_report_tax->vendor_id = $model->vendor_id;
                        $item_receiving_report_tax->dpp = $model->subtotal;
                        $item_receiving_report_tax->value = $tax->value;
                        $item_receiving_report_tax->amount = $tax->total_tax;
                        $item_receiving_report_tax->tax_id = $tax->id;
                        $item_receiving_report_tax->save();

                        $all_item_receiving_report_tax = ItemReceivingReportTax::where('reference_parent_model', get_class($model))
                            ->where('reference_parent_id', $model->id)
                            ->where('reference_model', get_class($model))
                            ->where('reference_id', $model->id)
                            ->get();

                        if ($all_item_receiving_report_tax->count() > 1) {
                            $all_item_receiving_report_tax->skip(1)->each(function ($item) {
                                $item->delete();
                            });
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(
                [
                    'message' => 'Berhasil generate pajak',
                    'status' => true,
                ],
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Gagal generate pajak',
                    'status' => false,
                    'error' => $th->getMessage(),
                ],
            );
        }
    }
}
