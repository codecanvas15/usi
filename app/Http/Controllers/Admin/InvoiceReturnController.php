<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderGeneral;
use App\Models\DeliveryOrderGeneralDetail;
use App\Models\InvoiceReturn as model;
use App\Models\InvoiceReturnDetail as model_detail;
use App\Models\InvoiceReturnDetail;
use App\Models\InvoiceReturnTax;
use App\Models\SaleOrderGeneralDetailTax;
use App\Models\SaleOrderTax;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class InvoiceReturnController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'invoice-return';

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
            $data = model::leftJoin('customers', 'customers.id', 'invoice_returns.customer_id')
                ->select('invoice_returns.*', 'customers.nama as customer_nama')
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('invoice_returns.branch_id', get_current_branch_id()));

            if ($request->from_date) {
                $data = $data->whereDate('invoice_returns.date', '>=', Carbon::parse($request->from_date))
                    ->whereDate('invoice_returns.date', '<=', Carbon::parse($request->to_date));
            }

            if ($request->customer_id) {
                $data->where('invoice_returns.customer_id', $request->customer_id);
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
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'invoice-return',
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => $row->check_available_date ? $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? $row->status != "approve" &&  $row->status != "reject" &&  $row->status != "void" : false,
                            ],
                        ],
                    ]);
                })
                ->addColumn('customer_nama', function ($row) {
                    return $row->customer_nama ?? '';
                })
                ->editColumn('export', function ($row) {
                    $link = route('invoice-return.export', ['id' => encryptId($row->id)]);
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
        $reference_model = DeliveryOrderGeneral::class;
        if ($request->type == "trading") {
            $reference_model = DeliveryOrder::class;
        }

        $delivery_order = $reference_model::find($request->reference_parent_id);

        DB::beginTransaction();
        try {
            $delivery = $reference_model::find($request->reference_parent_id);

            $model = new model();
            $model->loadModel([
                'code' => generate_code(model::class, 'code', 'date', "RTJ", date: Carbon::parse($request->date)->format('Y-m-d'), branch_sort: $delivery_order->branch->sort),
                'status' => 'pending',
                'created_by' => auth()->user()->id,
                'branch_id' => $delivery_order->branch_id,
                'currency_id' => $request->currency_id,
                'customer_id' => $request->customer_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate),
                'date' => Carbon::parse($request->date),
                'ware_house_id' => $request->ware_house_id,
                'reference_model' => $reference_model,
                'reference_id' => $request->reference_parent_id,
                'project_id' => $request->project_id,
                'reference' => $request->referensi,
                'type' => $request->type,
                'tax_number' => $request->tax_number,
            ]);

            $model->save();

            foreach ($request->reference_model as $key => $detail_reference) {
                $reference_id = $request->reference_id[$key];
                $qty = thousand_to_float($request->qty[$key]);
                $do_qty = thousand_to_float($request->do_qty[$key]);
                $return_qty = thousand_to_float($request->return_qty[$key]);
                $price = thousand_to_float($request->price[$key]);
                $hpp = thousand_to_float($request->hpp[$key]);

                $subtotal = $qty * $price;
                $total_tax_amount = 0;
                $total = 0;

                $model_detail = new model_detail();
                $model_detail->invoice_return_id = $model->id;
                $model_detail->reference_model = $detail_reference;
                $model_detail->reference_id = $request->reference_id[$key];
                $model_detail->item_id = $request->item_id[$key];
                $model_detail->unit_id = $request->unit_id[$key];
                $model_detail->do_qty = $do_qty;
                $model_detail->qty = $qty;
                $model_detail->return_qty = $return_qty;
                $model_detail->hpp = $hpp;
                $model_detail->hpp_total = $hpp * $qty;
                $model_detail->price = $price;
                $model_detail->subtotal = $subtotal;
                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $total;
                $model_detail->save();

                foreach ($request->tax_id[$reference_id] ?? [] as $key => $tax_id) {
                    $tax_value = $request->tax_value[$reference_id][$key];
                    $tax_amount = $subtotal * $tax_value;

                    $invoice_return_tax = new InvoiceReturnTax();
                    $invoice_return_tax->invoice_return_detail_id = $model_detail->id;
                    $invoice_return_tax->tax_id = $tax_id;
                    $invoice_return_tax->value = $tax_value;
                    $invoice_return_tax->amount = $tax_amount;
                    $invoice_return_tax->save();

                    $total_tax_amount += $tax_amount;
                }

                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $subtotal + $total_tax_amount;
                $model_detail->save();
            }

            $model->hpp_total = $model->invoice_return_details->sum('hpp_total');
            $model->subtotal = $model->invoice_return_details->sum('subtotal');
            $model->tax_total = $model->invoice_return_details->sum('tax_amount');
            $model->total = $model->invoice_return_details->sum('total');
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Retur Penjualan",
                subtitle: Auth::user()->name . " mengajukan Retur Penjualan " . $model->code,
                link: route('admin.invoice-return.show', $model),
                update_status_link: route('admin.invoice-return.update-status', ['id' => $model->id]),
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
        $reference_model = DeliveryOrderGeneral::class;
        if ($request->type == "trading") {
            $reference_model = DeliveryOrder::class;
        }

        DB::beginTransaction();
        try {
            $model = model::find($id);

            // Check Available Date Closing
            if (!$model->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
            }

            $model->loadModel([
                'branch_id' => $request->branch_id,
                'customer_id' => $request->customer_id,
                'project_id' => $request->project_id,
                'type' => $request->type,
                'ware_house_id' => $request->ware_house_id,
                'currency_id' => $request->currency_id,
                'exchange_rate' => thousand_to_float($request->exchange_rate),
                'reference_model' => $reference_model,
                'reference_id' => $request->reference_parent_id,
                'reference' => $request->referensi,
                'type' => $request->type,
                'date' => Carbon::parse($request->date),
                'tax_number' => $request->tax_number,
            ]);

            $model->save();

            $invoice_return_detail_id = [];
            foreach ($request->invoice_return_detail_id as $key => $value) {
                if ($value) {
                    array_push($invoice_return_detail_id, $value);
                }
            }

            $delete_return_detail = InvoiceReturnDetail::where('invoice_return_id', $model->id)
                ->whereNotIn('id', $invoice_return_detail_id)
                ->get();

            foreach ($delete_return_detail as $key => $data) {
                InvoiceReturnTax::where('invoice_return_detail_id', $data->id)
                    ->delete();
                $data->delete();
            }

            foreach ($request->reference_model as $key => $detail_reference) {
                $reference_id = $request->reference_id[$key];
                $qty = thousand_to_float($request->qty[$key]);
                $do_qty = thousand_to_float($request->do_qty[$key]);
                $return_qty = thousand_to_float($request->return_qty[$key]);
                $price = thousand_to_float($request->price[$key]);
                $hpp = thousand_to_float($request->hpp[$key]);
                $subtotal = $qty * $price;
                $total_tax_amount = 0;
                $total = 0;

                $model_detail = model_detail::where('reference_model', $detail_reference)
                    ->where('reference_id', $reference_id)
                    ->where('invoice_return_id', $model->id)
                    ->first();

                if (!$model_detail) {
                    $model_detail = new model_detail();
                }
                $model_detail->invoice_return_id = $model->id;
                $model_detail->item_id = $request->item_id[$key];
                $model_detail->unit_id = $request->unit_id[$key];
                $model_detail->reference_model = $detail_reference;
                $model_detail->reference_id = $request->reference_id[$key];
                $model_detail->do_qty = $do_qty;
                $model_detail->qty = $qty;
                $model_detail->return_qty = $return_qty;
                $model_detail->hpp = $hpp;
                $model_detail->hpp_total = $hpp * $qty;
                $model_detail->price = $price;
                $model_detail->subtotal = $subtotal;
                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $total;
                $model_detail->save();

                foreach ($request->tax_id[$reference_id] ?? [] as $key => $tax_id) {
                    $tax_value = $request->tax_value[$reference_id][$key];
                    $tax_amount = $subtotal * $tax_value;

                    $invoice_return_tax = InvoiceReturnTax::where('invoice_return_detail_id', $model_detail->id)
                        ->where('tax_id', $tax_id)
                        ->first();

                    if (!$invoice_return_tax) {
                        $invoice_return_tax = new InvoiceReturnTax();
                    }

                    $invoice_return_tax->invoice_return_detail_id = $model_detail->id;
                    $invoice_return_tax->tax_id = $tax_id;
                    $invoice_return_tax->value = $tax_value;
                    $invoice_return_tax->amount = $tax_amount;
                    $invoice_return_tax->save();

                    $total_tax_amount += $tax_amount;
                }

                $model_detail->tax_amount = $total_tax_amount;
                $model_detail->total = $subtotal + $total_tax_amount;
                $model_detail->save();
            }

            $model->hpp_total = $model->invoice_return_details->sum('hpp_total');
            $model->subtotal = $model->invoice_return_details->sum('subtotal');
            $model->tax_total = $model->invoice_return_details->sum('tax_amount');
            $model->total = $model->invoice_return_details->sum('total');
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "Retur Penjualan",
                subtitle: Auth::user()->name . " mengajukan Retur Penjualan " . $model->code,
                link: route('admin.invoice-return.show', $model),
                update_status_link: route('admin.invoice-return.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
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

        // Check Available Date Closing
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang dipilih sudah closing'));
        }

        DB::beginTransaction();
        try {
            $delete_return_detail = InvoiceReturnDetail::where('invoice_return_id', $model->id)
                ->get();

            foreach ($delete_return_detail as $key => $data) {
                InvoiceReturnTax::where('invoice_return_detail_id', $data->id)
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
    public function do_select(Request $request)
    {
        $result = [];
        // $do_trading = DeliveryOrder::where('status', 'done');
        // if ($request->search) {
        //     $do_trading->where(function ($query) use ($request) {
        //         $query->orWhere('code', 'like', "%$request->search%");
        //     });
        // }

        // if ($request->customer_id) {
        //     $do_trading = $do_trading->whereHas('so_trading', function ($s) use ($request) {
        //         $s->where('customer_id', $request->customer_id);
        //     });
        // }

        // if ($request->branch_id) {
        //     $do_trading = $do_trading->where('branch_id', $request->branch_id);
        // }

        // $do_trading = $do_trading->orderByDesc('created_at')
        //     ->limit(10)
        //     ->get();

        // foreach ($do_trading as $key => $trading) {
        // array_push($result, [
        //     'type' => 'trading',
        //     'id' => $trading->id,
        //     'code' => $trading->code . " # trading",
        // ]);
        // }

        $do_general = DeliveryOrderGeneral::whereIn('status', ['approve', 'done']);

        if ($request->search) {
            $do_general->where(function ($query) use ($request) {
                $query->orWhere('code', 'like', "%$request->search%");
            });
        }

        if ($request->customer_id) {
            $do_general = $do_general->where('customer_id', $request->customer_id);
        }

        if ($request->branch_id) {
            $do_general = $do_general->where('branch_id', $request->branch_id);
        }

        $do_general = $do_general->orderByDesc('created_at')
            ->limit(10)
            ->get();

        foreach ($do_general as $key => $general) {
            array_push($result, [
                'type' => 'general',
                'id' => $general->id,
                'code' => $general->code . " # general",
            ]);
        }

        return $this->ResponseJsonData($result);
    }


    public function get_do_detail(Request $request, $id)
    {
        if ($request->ajax()) {
            if ($request->type == "trading") {
                $delivery_order = DeliveryOrder::find($id);
                $delivery_order_details = [$delivery_order];
                $ware_house = $delivery_order->item_receiving_report->ware_house ?? null;
                $delivery_date = $delivery_order->load_date;
            } else {
                $delivery_order = DeliveryOrderGeneral::find($id);
                $delivery_order_details = DeliveryOrderGeneralDetail::where('delivery_order_general_id', $id)->get();
                $ware_house = $delivery_order->ware_house;

                $currency = $delivery_order->sale_order_general->currency;
                $exchange_rate = $delivery_order->sale_order_general->exchange_rate;
                $delivery_date = $delivery_order->date_send;
            }

            $return_data = [];
            foreach ($delivery_order_details as $key => $delivery_order_detail) {
                $detail_model = get_class($delivery_order_detail);
                $return_qty = InvoiceReturnDetail::whereHas('invoice_return', function ($p) {
                    $p->where('status', '!=', 'reject');
                    $p->where('status', '!=', 'void');
                })
                    ->where('reference_model', $detail_model)
                    ->where('reference_id', $delivery_order_detail->id)
                    ->sum('qty');

                $push_data['reference_model'] = $detail_model;
                $push_data['reference_id'] = $delivery_order_detail->id;
                if ($request->type == "trading") {
                    $so_trading = $delivery_order_detail->so_trading;
                    $so_trading_detail = $so_trading->so_trading_detail;
                    $currency = $so_trading->currency;
                    $exchange_rate = $so_trading->exchange_rate;

                    $push_data['item'] = $so_trading_detail->item;
                    $push_data['qty'] = $delivery_order_detail->unload_quantity_realization;
                    $push_data['unit'] = $so_trading_detail->item->unit;
                    $push_data['price'] = $so_trading_detail->harga;
                    $push_data['hpp'] = $delivery_order_detail->hpp ?? 0;
                    $push_data['taxes'] = SaleOrderTax::where('so_trading_id', $so_trading->id)
                        ->with('tax')
                        ->get();
                } else {
                    $push_data['item'] = $delivery_order_detail->item;
                    $push_data['qty'] = $delivery_order_detail->quantity_received;
                    $push_data['unit'] = $delivery_order_detail->item->unit;
                    $push_data['price'] = $delivery_order_detail->sale_order_general_detail->price;
                    $push_data['hpp'] = $delivery_order_detail->hpp ?? 0;
                    $push_data['taxes'] = SaleOrderGeneralDetailTax::where('so_general_detail_id', $delivery_order_detail->sale_order_general_detail_id)
                        ->with('tax')
                        ->get();
                }
                $push_data['return_qty'] = $return_qty;

                if ($delivery_order_detail->item->item_category->item_type->nama == "purchase item") {
                    array_push($return_data, $push_data);
                }
            }

            $data['ware_house'] = $ware_house;
            $data['currency'] = $currency;
            $data['exchange_rate'] = $exchange_rate;
            $data['details'] = $return_data;
            $data['delivery_date'] = $delivery_date ?  Carbon::parse($delivery_date)->format('d-m-Y') : null;

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

    public function export_pdf($id, Request $request)
    {
        $model = model::with([
            'branch',
            'currency',
            'ware_house',
            'project',
            'customer',
            'invoice_return_details.item',
            'invoice_return_details.unit',
            'invoice_return_details.invoice_return_taxes',
        ])->findOrfail(decryptId($id));
        $fileName = 'Return Penjualan ' . strtoupper($model->item) . '.pdf';

        $qr_url = route('invoice-return.export', ['id' => $id]);
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
}
