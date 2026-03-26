<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PrintHelper;
use App\Models\Authorization;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderGeneral;
use App\Models\InvoiceGeneral;
use App\Models\SaleOrderGeneral as model;
use App\Models\SaleOrderGeneralDetail;
use App\Models\SaleOrderGeneralDetailTax;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\DataTables;

class SaleOrderGeneralController extends Controller
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
    protected string $view_folder = 'sales-order-general';

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
        return view("admin.$this->view_folder.index");
    }

    /**
     * data
     *
     * @param Request $request
     * @return mixed
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $checkAuthorizePrint = authorizePrint('sale_order_general');

            $model = \App\Models\SaleOrderGeneral::with(['customer'])
                ->join('customers', 'customers.id', '=', 'sale_order_generals.customer_id')
                ->when($request->branch_id, function ($q, $branch_id) {
                    $q->where('sale_order_generals.branch_id', $branch_id);
                })
                ->when($request->customer_id, function ($q, $customer_id) {
                    $q->where('sale_order_generals.customer_id', $customer_id);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('sale_order_generals.status', $status);
                })
                ->when($request->from_date, function ($q, $from_date) {
                    $q->whereDate('sale_order_generals.tanggal', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                })
                ->when($request->to_date, function ($q, $to_date) {
                    $q->whereDate('sale_order_generals.tanggal', '<=', Carbon::parse($to_date)->format('Y-m-d'));
                })
                ->when($request->input('search.value'), function ($q, $search) {
                    $q->where('sale_order_generals.kode', 'like', "%{$search}%")
                        ->orWhere('sale_order_generals.status', 'like', "%{$search}%")
                        ->orWhere('sale_order_generals.no_po_external', 'like', "%{$search}%")
                        ->orWhere('customers.nama', 'like', "%{$search}%");
                })
                ->select('sale_order_generals.*');

            return DataTables::of($model)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return localDate($row->tanggal);
                })
                ->editColumn('kode', function ($row) {
                    return '<a href="' . route("admin.$this->view_folder.index") . '/' . $row->id . '" class="text-primary">' . $row->kode . '</a>';
                })
                ->editColumn('no_po_external', function ($row) {
                    return $row->no_po_external;
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer->nama;
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . sale_order_general_status()[$row->status]['color'] . '">
                                ' . sale_order_general_status()[$row->status]['label'] . ' - ' . sale_order_general_status()[$row->status]['text'] . '
                            </div>';

                    return $badge;
                })
                ->addColumn('export', function ($row) use ($checkAuthorizePrint) {
                    $link = route("sales-order-general.export.id", ['id' => encryptId($row->id)]);
                    $linkDetail = route('admin.sales-order-general.show', ['sales_order_general' => $row->id]);
                    $model_class = get_class($row);
                    return "<a href='$link' class='btn btn-sm btn-flat btn-info' target='_blank' onclick='show_print_out_modal(event)' " . ($checkAuthorizePrint ? "data-model='$model_class' data-id='$row->id' data-print-type='sale_order_general' data-link='$linkDetail' data-code='$row->kode'" : "") . " >Export</a>";
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['kode', 'status', 'export', 'action'])
                ->make(true);
        }

        abort(404);
    }

    public function getSaleOrderInvoice(Request $request)
    {
        if ($request->ajax()) {
            $model = \App\Models\SaleOrderGeneral::with(['customer'])
                ->whereHas('sale_order_general_details', function ($query) {
                    $query->where(function ($query) {
                        $query->whereHas('delivery_order_general_details', function ($query) {
                            $query->whereHas('delivery_order_general', function ($query) {
                                $query->whereIn('status', ['approve', 'done']);
                            })
                                ->whereDoesntHave('invoice_general_details', function ($query) {
                                    $query->whereHas('invoice_general', function ($query) {
                                        $query->whereIn('status', ['approve', 'pending', 'revert', 'done']);
                                    });
                                });
                        })
                            ->orWhereDoesntHave('delivery_order_general_details');
                    });
                })
                ->join('customers', 'customers.id', '=', 'sale_order_generals.customer_id')
                ->when($request->branch_id, function ($q, $branch_id) {
                    $q->where('sale_order_generals.branch_id', $branch_id);
                })
                ->when($request->customer_id, function ($q, $customer_id) {
                    $q->where('sale_order_generals.customer_id', $customer_id);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('sale_order_generals.status', $status);
                })
                ->when($request->from_date, function ($q, $from_date) {
                    $q->whereDate('sale_order_generals.tanggal', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                })
                ->when($request->to_date, function ($q, $to_date) {
                    $q->whereDate('sale_order_generals.tanggal', '<=', Carbon::parse($to_date)->format('Y-m-d'));
                })
                ->when($request->input('search.value'), function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('sale_order_generals.kode', 'like', "%{$search}%")
                            ->orWhere('sale_order_generals.status', 'like', "%{$search}%")
                            ->orWhere('sale_order_generals.no_po_external', 'like', "%{$search}%")
                            ->orWhere('customers.nama', 'like', "%{$search}%");
                    });
                })
                ->select('sale_order_generals.*');

            return DataTables::of($model)
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return localDate($row->tanggal);
                })
                ->editColumn('kode', function ($row) {
                    return '<a href="' . route("admin.$this->view_folder.index") . '/' . $row->id . '" class="text-primary">' . $row->kode . '</a>';
                })
                ->editColumn('no_po_external', function ($row) {
                    return $row->no_po_external;
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer->nama;
                })
                ->editColumn('created_at', function ($row) {
                    return toDayDateTimeString($row->created_at);
                })
                ->addColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . sale_order_general_status()[$row->status]['color'] . '">
                                ' . sale_order_general_status()[$row->status]['label'] . ' - ' . sale_order_general_status()[$row->status]['text'] . '
                            </div>';

                    return $badge;
                })
                ->rawColumns(['kode', 'status'])
                ->make(true);
        }

        abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        return view("admin.$this->view_folder.create", compact('model'));
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

        // * validate
        $this->validate($request, model::rules());

        // Check available date closeing
        if (!checkAvailableDate($request->tanggal)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
        }

        // * create data
        $model = new model();
        $model->kode = generate_code(model::class, 'kode', 'tanggal', 'SOG', branch_sort: get_current_branch()->sort ?? null, date: $request->tanggal);
        $model->loadModel([
            'customer_id' => $request->customer_id,
            'currency_id' => $request->currency_id,
            'tanggal' => Carbon::parse($request->tanggal),
            'exchange_rate' => thousand_to_float($request->exchange_rate ?? 0),
            'quotation' => $request->hasFile('quotation') ? $this->upload_file($request->file('quotation'), 'sale-order/quotation') : null,
            'no_po_external' => $request->no_po_external ?? null,
            'drop_point' => $request->drop_point ?? null,
            'is_include_tax' => $request->is_include_tax ?? 0,
        ]);

        // * saving and make response
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        $sub_total = 0;
        $total = 0;

        // * create details data
        if ($request->item_id) {
            foreach ($request->item_id as $key => $value) {
                $price = thousand_to_float($request->final_price[$key] ?? $request->price[$key]);
                $single_sub_total = $price * thousand_to_float($request->amount[$key]);
                $sub_total += $single_sub_total;

                // * item
                $item = \App\Models\Item::find($request->item_id[$key]);

                // * detail
                $detail = new SaleOrderGeneralDetail();
                $detail->loadModel([
                    'sale_order_general_id' => $model->id,
                    'item_id' => $request->item_id[$key],
                    'unit_id' => $item->unit_id,
                    'price_before_discount' => thousand_to_float($request->price_before_discount[$key]),
                    'discount' => thousand_to_float($request->discount[$key]),
                    'price' => $price,
                    'amount' => thousand_to_float($request->amount[$key]),
                    'sub_total' => $single_sub_total,
                    'notes' => $request->notes[$key],
                ]);

                try {
                    $detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }

                // * tax
                $total_item_tax = 0;
                if ($request->tax_data) {
                    $tax_list = $request->tax_data;
                    foreach ($tax_list as $tax_key => $tax_value) {
                        $data_tax = Tax::find($tax_value);
                        if ($data_tax) {
                            $total_item_tax += $single_sub_total * $data_tax->value;
                            $tax = new SaleOrderGeneralDetailTax();
                            $tax->loadModel([
                                'so_general_detail_id' => $detail->id,
                                'tax_id' => $data_tax->id,
                                'value' => $data_tax->value,
                                'total' => $single_sub_total * $data_tax->value,
                            ]);

                            try {
                                $tax->save();
                            } catch (\Throwable $th) {
                                DB::rollBack();

                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                            }
                        }
                    }
                }

                $total += $single_sub_total + $total_item_tax;

                // * update total sale order general detail
                $detail->total = $total_item_tax + $single_sub_total;
                try {
                    $detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                }
            }
        }

        // * update total and sub total
        $model->total = $total;
        $model->sub_total = $sub_total;
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "SO General",
                subtitle: Auth::user()->name . " mengajukan SO General " . $model->kode,
                link: route('admin.sales-order-general.show', $model),
                update_status_link: route('admin.sales-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.sales.index")->with($this->ResponseMessageCRUD());
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
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
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
        $authorization_logs['can_void'] = $model->status == "approve" && $model->check_available_date;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button'));
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
        $tax_data = SaleOrderGeneralDetailTax::whereHas('saleOrderGeneralDetail', function ($query) use ($model) {
            $query->where('sale_order_general_id', $model->id);
        })
            ->groupBy('tax_id')
            ->get();
        if (!in_array($model->status, ['pending', 'revert'])) {
            abort(403);
        }

        if (!$model->check_available_date) {
            abort(403);
        }

        if ($request->ajax()) {
            $model->tax_data = $tax_data;

            return $this->ResponseJsonData($model);
        }


        return view("admin.$this->view_folder.edit", compact('model', 'tax_data'));
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

        // * validate
        $this->validate($request, model::rules());

        // Check available date closeing
        if (!checkAvailableDate($request->tanggal)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal yang anda pilih sudah close'));
        }

        // * update data
        $model = model::findOrFail($id);
        $model->customer_id = $request->customer_id;
        $model->currency_id = $request->currency_id;
        $model->tanggal = Carbon::parse($request->tanggal);
        $model->exchange_rate = thousand_to_float($request->exchange_rate ?? 0);
        if ($request->hasFile('quotation')) {
            $this->delete_file('sale-order/quotation' . $model->quotation);
            $model->quotation = $this->upload_file($request->file('quotation'), 'sale-order/quotation');
        }
        $model->no_po_external = $request->no_po_external ?? null;
        $model->drop_point = $request->drop_point ?? null;
        $model->is_include_tax = $request->is_include_tax ?? 0;

        // * saving and make response
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        $sub_total = 0;
        $total = 0;

        // * update details data
        $old_sale_order_general_detail_id = collect($request->sale_order_general_detail_id)->filter(function ($value) {
            return $value != null;
        })->toArray();
        $delete_sale_order_generals = SaleOrderGeneralDetail::where('sale_order_general_id', $model->id)
            ->when($old_sale_order_general_detail_id && is_array($old_sale_order_general_detail_id), function ($query) use ($old_sale_order_general_detail_id) {
                $query->whereNotIn('id', $old_sale_order_general_detail_id);
            })
            ->get();

        SaleOrderGeneralDetailTax::whereIn('so_general_detail_id', $delete_sale_order_generals->pluck('id'))->delete();
        SaleOrderGeneralDetail::whereIn('id', $delete_sale_order_generals->pluck('id'))->delete();

        if (is_array($request->item_id) && count($request->item_id) > 0) {
            foreach ($request->item_id as $key => $value) {
                $price = thousand_to_float($request->final_price[$key] ?? $request->price[$key]);
                $single_sub_total = $price * thousand_to_float($request->amount[$key]);
                $sub_total += $single_sub_total;

                $detail = SaleOrderGeneralDetail::find($request->sale_order_general_detail_id[$key] ?? null);

                // * item
                $item = \App\Models\Item::find($value);

                // * detail
                if ($detail) {
                    $detail->item_id = $request->item_id[$key];
                    $detail->unit_id = $item->unit_id;
                    $detail->price_before_discount = thousand_to_float($request->price_before_discount[$key]);
                    $detail->discount = thousand_to_float($request->discount[$key]);
                    $detail->price = $price;
                    $detail->amount = thousand_to_float($request->amount[$key]);
                    $detail->sub_total = $single_sub_total;
                    $detail->notes = $request->notes[$key] ?? null;

                    try {
                        $detail->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
                    }
                } else {
                    $detail = new SaleOrderGeneralDetail();
                    $detail->sale_order_general_id = $model->id;
                    $detail->item_id = $request->item_id[$key];
                    $detail->unit_id = $item->unit_id;
                    $detail->price_before_discount = thousand_to_float($request->price_before_discount[$key]);
                    $detail->discount = thousand_to_float($request->discount[$key]);
                    $detail->price = $price;
                    $detail->amount = thousand_to_float($request->amount[$key]);
                    $detail->sub_total = $single_sub_total;
                    $detail->notes = $request->notes[$key] ?? null;

                    try {
                        $detail->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
                    }
                }

                // * tax
                $total_item_tax = 0;
                if ($request->tax_data && $request->tax_data != ",") {
                    $tax_list = $request->tax_data;
                    foreach ($tax_list as $tax_key => $tax_value) {
                        $data_tax = Tax::find($tax_value);
                        if ($data_tax) {
                            $tax = SaleOrderGeneralDetailTax::where('so_general_detail_id', $detail->id)->where('tax_id', $tax_value)->first();
                            $total_item_tax += $single_sub_total * $data_tax->value;
                            if (is_null($tax)) {
                                $tax = new SaleOrderGeneralDetailTax();
                            }

                            $tax->loadModel([
                                'so_general_detail_id' => $detail->id,
                                'tax_id' => $data_tax->id,
                                'value' => $data_tax->value,
                                'total' => $single_sub_total * $data_tax->value,
                            ]);

                            try {
                                $tax->save();
                            } catch (\Throwable $th) {
                                DB::rollBack();

                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
                            }
                        }
                    }
                } else {
                    SaleOrderGeneralDetailTax::where('so_general_detail_id', $detail->id)->delete();
                }

                $total += $single_sub_total + $total_item_tax;
                // * update total sale order general detail
                $detail->total = $total_item_tax + $single_sub_total;
                try {
                    $detail->save();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
                }
            }
        } else {
            $detail = SaleOrderGeneralDetail::where('sale_order_general_id', $model->id);

            if ($detail->get()->count() > 0) {
                foreach ($detail->get() as $dt) {
                    $detailTax = SaleOrderGeneralDetailTax::where('so_general_detail_id', $dt->id);
                    $detailTax->delete();
                }
            }
            $detail->delete();
        }

        // * update total and sub total
        $model->total = $total;
        $model->sub_total = $sub_total;
        try {
            $model->save();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $model->id,
                amount: $model->total ?? 0,
                title: "SO General",
                subtitle: Auth::user()->name . " mengajukan SO General " . $model->kode,
                link: route('admin.sales-order-general.show', $model),
                update_status_link: route('admin.sales-order-general.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.sales.index")->with($this->ResponseMessageCRUD());

        // $model = model::findOrFail($id);
        // DB::beginTransaction();
        // // * validate
        // if ($request->ajax()) {
        //     $this->validate($request, model::rules());
        // } else {
        //     $this->validate_api($request->all(), model::rules());
        // }
        // // * update data
        // $model->loadModel($request->all());

        // // * saving and make reponse
        // try {
        //     $model->save();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     if ($request->ajax()) {
        //         return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
        //     }

        //     return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        // }

        // DB::commit();
        // if ($request->ajax()) {
        //     return $this->ResponseJsonMessageCRUD();
        // }

        // return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
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
            $model->delete();

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();
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

        return redirect()->route("admin.sales.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        if ($request->search) {
            $model = model::where('nama', 'like', "%$request->search%")->orderByDesc('created_at')->limit(10)->get();
        } else {
            $model = model::orderByDesc('created_at')->limit(10)->get();
        }

        return $this->ResponseJsonData($model);
    }

    /**
     * detail_edit
     *
     * @param $id
     * @return mixed
     */
    public function detail_edit($id)
    {
        $model = model::with([
            'customer',
            'currency',
        ])->findOrFail($id);

        $tax_data = SaleOrderGeneralDetailTax::with('tax')->whereHas('saleOrderGeneralDetail', function ($query) use ($model) {
            $query->where('sale_order_general_id', $model->id);
        })
            ->groupBy('tax_id')
            ->get();

        $model->tax_data = $tax_data;

        $items = $model->sale_order_general_details()->with([
            'item',
            'unit',
            'sale_order_general_detail_taxes.tax'
        ])->get();

        return $this->ResponseJsonData(compact('model', 'items'));
    }

    /**
     * update_status
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function update_status(Request $request, $id)
    {
        $model = model::findOrFail($id);

        // Check available date closing
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal yang anda pilih sudah close'));
        }

        DB::beginTransaction();

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

                // * update status
                $model->status = $request->status;
                if ($model->status == 'approve') {
                    $model->approved_by = Auth::user()->id;
                }
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

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update_status', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update_status'));
    }

    public function get_by_customer(Request $request)
    {
        $model = model::with(['customer'])
            ->whereHas('sale_order_general_details', function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('delivery_order_general_details', function ($query) {
                        $query
                            ->whereHas('delivery_order_general', function ($query) {
                                $query->whereIn('status', ['approve', 'done']);
                            })
                            ->whereDoesntHave('invoice_general_details', function ($query) {
                                $query->whereHas('invoice_general', function ($query) {
                                    $query->whereIn('status', ['approve', 'pending', 'revert', 'done']);
                                });
                            });
                    });
                });
            })
            ->when($request->sale_order_general_ids, function ($q) use ($request) {
                $q->whereIn('id', explode(',', $request->sale_order_general_ids));
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('kode', 'like', "%$request->search%");
            })
            ->whereIn(
                'status',
                ['approve', 'partial-sent', 'partial-approve', 'done']
            )
            ->where('customer_id', $request->customer_id)
            ->where('branch_id', $request->branch_id)
            ->where('currency_id', $request->currency_id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJsonData($model);
    }

    /**
     * select2 api for delivery order
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_for_delivery_order(Request $request)
    {
        $branch_id = $request->branch_id ?? get_current_branch_id();
        $branch = Branch::find($branch_id);

        if ($request->ajax()) {
            if ($request->search) {
                $model = model::with('customer')
                    ->whereIn('status', ['approve', 'partial-sent'])
                    ->when(!$branch->is_primary, function ($query) {
                        $query->where('branch_id', get_current_branch_id());
                    })
                    ->where(function ($query) use ($request) {
                        $query->where('kode', 'like', "%$request->search%")
                            ->orWhereHas('customer', function ($query) use ($request) {
                                $query->where('nama', 'like', "%$request->search%");
                            });
                    })->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
            } else {
                $model = model::with('customer')
                    ->when(!$branch->is_primary, function ($query) {
                        $query->where('branch_id', get_current_branch_id());
                    })
                    ->whereIn('status', ['approve', 'partial-sent'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();
            }

            return $this->ResponseJsonData($model);
        }
    }

    /**
     * detail api for delivery order
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail_for_delivery_order($id = null)
    {
        $model = model::findOrFail($id);

        $items = $model->sale_order_general_details()
            ->whereIn('status', ['approve', 'partial'])
            ->where('amount', '>', 'sended')
            ->with([
                'item.item_category.item_type',
                'unit',
            ])
            ->get();

        return $this->ResponseJsonData(compact('model', 'items'));
    }

    /**
     * detail api for invoice general
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail_for_invoice_general(Request $request)
    {
        $model = model::with(['currency', 'customer', 'customer.customer_banks.bank_internal'])->findOrFail($request->id);

        $items = $model->sale_order_general_details()
            ->whereIn('status', ['done', 'partial-send'])
            ->with([
                'sale_order_general_detail_taxes',
                'sale_order_general_detail_taxes.tax',
                'item',
                'unit',
            ])->get();

        return $this->ResponseJsonData(compact('model', 'items'));
    }

    /**
     * get_item_stocks
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_item_stocks($id)
    {
        $warehouse = \App\Models\WareHouse::with('branch')
            ->selectRaw('ware_houses.*,sum(COALESCE(sm.in,0)-COALESCE(sm.out,0)) as stock')
            ->leftJoin('stock_mutations as sm', function ($j) use ($id) {
                $j->on('sm.ware_house_id', '=', 'ware_houses.id');
                $j->where('sm.item_id', '=', $id);
                $j->whereNull('sm.deleted_at');
            })
            ->havingRaw('stock > 0')
            ->groupBy('ware_houses.id')
            ->get();

        $stock = [];

        $item = \App\Models\Item::find($id);
        foreach ($warehouse as $key => $value) {
            $main_stock = $item->mainStock($value->id);

            $stock[] = [
                'warehouse' => $value,
                'stock' => $main_stock,
            ];
        }

        return $this->ResponseJsonData($stock);
    }

    /**
     * export pdf
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function export($id, Request $request)
    {
        if (!$request->preview) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'sale_order_general',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('customer', 'sale_order_general_details', 'sale_order_general_details.item')->findOrFail(decryptId($id));
        $sale_order_taxes = SaleOrderGeneralDetailTax::with('tax')
            ->whereHas('saleOrderGeneralDetail', function ($query) use ($model) {
                $query->where('sale_order_general_id', $model->id);
            })->get();

        // group so taxes base on tax_id and value
        $taxes = $sale_order_taxes->unique('tax_id')
            ->unique('value')
            ->map(function ($tax) use ($sale_order_taxes) {
                $tax->grand_total = $sale_order_taxes->where('tax_id', $tax->tax_id)
                    ->where('value', $tax->value)
                    ->sum('total');

                return $tax;
            });

        $file = public_path('/pdf_reports/Report-Sales-Order-' . microtime(true) . '.pdf');
        $fileName = 'SALES-ORDER-' . microtime(true) . '.pdf';

        $qr_url = route('sales-order-general.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));
        $approval = Authorization::where('model', model::class)
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->where('model_id', $model->id)
            ->first();

        $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'taxes', 'approval'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
        $pdf->render();

        $canvas = $pdf->get_canvas();
        $h = $canvas->get_height();
        $w = $canvas->get_width();
        $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

        if ($request->ajax() || $request->preview) {
            $canvas->page_text($w / 5, $h / 1.7, 'PREVIEW ONLY', null, 60, array(0, 0, 0, 0.3), 0, 0, -30);
        }

        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_sale_order_general');
            $tmp_file_name = 'sale_order_' . time() . '.pdf';
            $path = 'tmp_sale_order_general/' . $tmp_file_name;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json($path);
        }

        return $pdf->stream($fileName);
    }

    /**
     * Check date for delivery order general
     *
     */
    public function check_date_so(Request $request, $id)
    {
        if ($request->ajax()) {
            $model = model::find($id);

            if ($model) {
                if (Carbon::parse($request->date)->lessThan(Carbon::parse($model->tanggal))) {
                    return $this->ResponseJsonData(false);
                }
                return $this->ResponseJsonData(true);
            } else {
                return $this->ResponseJsonData(false);
            }
        }
    }

    public function history($id, Request $request)
    {
        try {
            $sale_order_generals = DB::table('sale_order_general_details')
                ->join('sale_order_generals', 'sale_order_generals.id', '=', 'sale_order_general_details.sale_order_general_id')
                ->whereNull('sale_order_generals.deleted_at')
                ->where('sale_order_general_id', $id)
                ->select(
                    'sale_order_generals.id',
                    'sale_order_generals.kode as code',
                    'sale_order_generals.tanggal as date',
                    'sale_order_generals.status',
                )
                ->get();

            $sale_order_generals = $sale_order_generals->map(function ($item) {
                $item->link = route('admin.sales-order-general.show', $item->id);
                $item->menu = 'sales order general';
                return $item;
            });

            $delivery_order_generals = DeliveryOrderGeneral::whereHas('delivery_order_general_details', function ($query) use ($id) {
                $query->whereHas('sale_order_general_detail', function ($query) use ($id) {
                    $query->where('sale_order_general_id', $id);
                });
            })
                ->whereNotIn('status', ['rejected', 'void'])
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'code',
                    'date',
                    'status',
                )->get();

            $delivery_order_generals = $delivery_order_generals->map(function ($item) {
                $item->link = route('admin.delivery-order-general.show', $item->id);
                $item->menu = 'delivery order general';
                return $item;
            });

            $invoice_generals = InvoiceGeneral::whereHas('invoice_general_details', function ($query) use ($id) {
                $query->whereHas('sale_order_general_detail', function ($query) use ($id) {
                    $query->where('sale_order_general_id', $id);
                });
            })
                ->join('invoice_parents', function ($query) {
                    $query->on('invoice_generals.id', '=', 'invoice_parents.reference_id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceGeneral');
                })
                ->whereNotIn('invoice_generals.status', ['rejected', 'void'])
                ->whereNull('invoice_generals.deleted_at')
                ->select(
                    'invoice_generals.id',
                    'invoice_generals.code',
                    'invoice_generals.date',
                    'invoice_generals.status',
                    'invoice_parents.id as invoice_parent_id',
                )->get();

            $invoice_generals = $invoice_generals->map(function ($item) {
                $item->link = route('admin.invoice-general.show', $item->id);
                $item->menu = 'invoice general';
                return $item;
            });

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

            $invoice_returns = $invoice_returns->map(function ($item) {
                $item->link = route('admin.invoice-return.show', $item->id);
                $item->menu = 'invoice return';
                return $item;
            });

            $receivables_payments = DB::table('receivables_payment_details')
                ->where('invoice_parent_id', $invoice_generals->pluck('invoice_parent_id')->toArray())
                ->join('receivables_payments', 'receivables_payments.id', '=', 'receivables_payment_details.receivables_payment_id')
                ->leftJoin('bank_code_mutations', function ($query) {
                    $query->on('bank_code_mutations.ref_id', '=', 'receivables_payments.id')
                        ->where('bank_code_mutations.ref_model', '=', 'App\Models\ReceivablesPayment');
                })
                ->whereNull('receivables_payments.deleted_at')
                ->whereNotIn('receivables_payments.status', ['rejected', 'void'])
                ->select(
                    'receivables_payments.id',
                    'receivables_payments.code',
                    'bank_code_mutations.code as bank_code_mutation_code',
                    'receivables_payments.date',
                    'receivables_payments.status',
                )->get()
                ->map(function ($item) {
                    $item->code = $item->bank_code_mutation_code ?? $item->code;
                    return $item;
                });

            $receivables_payments = $receivables_payments->map(function ($item) {
                $item->link = route('admin.receivables-payment.show', $item->id);
                $item->menu = 'receivables payment';
                return $item;
            });

            $histories = $sale_order_generals->unique('id')
                ->merge($delivery_order_generals->unique('id'))
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
