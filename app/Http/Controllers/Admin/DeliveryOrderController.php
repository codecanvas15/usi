<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\PrintHelper;
use App\Models\Authorization;
use App\Models\DeliveryOrder as model;
use App\Models\DeliveryOrder;
use App\Models\InvoiceTradingDetail;
use App\Models\ItemReceivingReport;
use App\Models\PairingSoToPo;
use App\Models\PoTrading;
use App\Models\PurchaseTransport;
use App\Models\SoTrading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDFMerger;

class DeliveryOrderController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'delivery-order';

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
            $query = SoTrading::where(function ($query) {
                $query->orWhere('status', 'done');
                $query->orWhere('status', 'not_yet_send');
                $query->orWhere('status', 'partial_sent');
                $query->orWhere('status', 'delivery_complete');
                $query->orWhere('status', 'approve');
                $query->orWhere('status', 'paired');
                $query->orWhere('status', 'ready');
            });

            if (!get_current_branch()->is_primary) {
                $query->where('branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->from_date) {
                $query->whereDate('tanggal', '>=', Carbon::parse($request->from_date));
            }
            if ($request->to_date) {
                $query->whereDate('tanggal', '<=', Carbon::parse($request->to_date));
            }

            $data = $query;

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nomor_so', function ($row) {
                    $code = '<a href="' . route("admin.delivery-order.show", $row) . '" target="_blank" class="text-primary text-decoration-underline hover_text-dark">' . $row->nomor_so . '</a>';
                    return $code;
                })
                ->editColumn('customer', function ($row) {
                    return ucwords($row->customer->nama);
                })
                ->editColumn('jumlah_do', fn($row) => count($row->delivery_orders))
                ->addColumn('status', function ($row) {
                    $color = status_sale_orders()[$row->status]['color'];
                    $badge = '<div class="badge badge-lg badge-' . $color . '">
                                ' . status_sale_orders()[$row->status]['label'] . '
                            </div>';

                    return $badge;
                })
                ->addColumn('delivery_orders', function ($row) {
                    $delivery_orders = $row->delivery_orders;
                    foreach ($delivery_orders as $data) {
                        $data->sh_number;
                    }

                    return json_decode($delivery_orders);
                })
                ->rawColumns(['nomor_so', 'status', 'export'])
                ->make(true);
        }
        abort(403);
    }

    /**
     * data
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $checkAuthorizePrint = authorizePrint('delivery_order_trading');
            $models = \App\Models\DeliveryOrder::with('so_trading')
                ->leftJoin('sale_orders', 'sale_orders.id', 'delivery_orders.so_trading_id')
                ->join('customers', 'customers.id', 'sale_orders.customer_id')
                ->when($request->status, fn($q) => $q->where('delivery_orders.status', $request->status))
                ->when($request->sh_number_id, fn($q) => $q->where('delivery_orders.sh_number_id', $request->sh_number_id))
                ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
                ->when($request->from_date, fn($q) => $q->whereDate('delivery_orders.target_delivery', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('delivery_orders.target_delivery', '<=', Carbon::parse($request->to_date)))
                ->when($request->is_done, function ($q) use ($request) {
                    $q->when($request->is_done == "true", function ($q) {
                        $q->where('delivery_orders.status', 'done')
                            ->where(function ($q) {
                                $q->where('delivery_orders.is_invoice_created', 1)
                                    ->orWhere('delivery_orders.type', 'delivery-order-2');
                            });
                    });

                    $q->when($request->is_done == "false", function ($q) {
                        $q->where(function ($q) {
                            $q->where('delivery_orders.status', '!=', 'done')
                                ->orWhere(function ($q) {
                                    $q->where('delivery_orders.is_invoice_created', 0)
                                        ->where('delivery_orders.type', '!=', 'delivery-order-2');
                                });
                        });
                    });
                })
                ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('delivery_orders.branch_id', $request->branch_id))
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('delivery_orders.branch_id', get_current_branch()->id))
                ->select('delivery_orders.*', 'sale_orders.nomor_so', 'customers.nama as customer_name');

            return DataTables::of($models)
                ->addIndexColumn()
                ->editColumn('target_delivery', fn($row) => $row->target_delivery ? localDate($row->target_delivery) : "-")
                ->editColumn('load_date', fn($row) => $row->load_date ? localDate($row->load_date) : "-")
                ->editColumn('unload_date', fn($row) => $row->unload_date ? localDate($row->unload_date) : "-")
                ->editColumn('load_quantity', function ($row) {
                    $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                    return formatNumber($row->load_quantity) . " " . $unit;
                })
                ->editColumn('unload_quantity_realization', function ($row) {
                    $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                    return formatNumber($row->unload_quantity_realization) . " " . $unit;
                })
                ->editColumn('code', fn($row) => view("admin.$this->view_folder.detail-link", [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('nomor_so', function ($row) {
                    $link = route('admin.sales-order.show', ['sales_order' => $row->so_trading_id]);

                    return "<a href='{$link}' target='_blank'>{$row->nomor_so}</a>";
                })
                ->editColumn('status', fn($row) => view('admin.delivery-order.status', compact('row')))
                ->editColumn('export', function ($row) use ($checkAuthorizePrint) {
                    $link = route("delivery-order.export.id", ['id' => encryptId($row->id)]);
                    $linkDetail = route("admin.delivery-order.show", ['delivery_order' => $row->id]);
                    $export = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="showPrintOption(event)" ' . ($checkAuthorizePrint ? 'data-model="' . \App\Models\DeliveryOrder::class . '" data-id="' . $row->id . '" data-print-type="delivery_order_trading" data-link="' . $linkDetail . '" data-code="' . $row->code . '"' : '') . '>Export</a>';

                    return $export;
                })
                ->addColumn('moda_transport', function ($row) {
                    $transport = '';

                    if ($row->purchase_transport && $row->delivery_order_ship_id) {
                        $transport = "Transportir + Kapal";
                    } elseif ($row->purchase_transport && $row->delivery_order_id) {
                        $transport = "Transportir + Kendaraan";
                    } elseif ($row->purchase_transport) {
                        $transport = "Transportir";
                    } else {
                        $transport = "Own Use";
                    }

                    return $transport;
                })
                ->addColumn('action', function ($row) {
                    return view("admin.$this->view_folder.button-datatable", [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'model' => $row,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => !in_array($row->status, ['reject', 'void', 'cancel', 'done']) && $row->check_available_date && $row->is_can_edit_data,
                            ],
                            'delete' => [
                                'display' => $row->status == "pending",
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['action', 'status', 'export', 'checkbox', 'nomor_so'])
                ->make(true);
        }

        abort(403);
    }

    /**
     * List delivery order purchase transport where status receved is true
     */
    public function list_delivery_order_where_status_received_is_true(Request $request)
    {
        // if ($request->ajax()) {
        $models = \App\Models\DeliveryOrder::with('so_trading')
            ->leftJoin('sale_orders', 'sale_orders.id', 'delivery_orders.so_trading_id')
            ->join('customers', 'customers.id', 'sale_orders.customer_id')
            ->when($request->status, fn($q) => $q->where('delivery_orders.status', $request->status))
            ->when($request->sh_number_id, fn($q) => $q->where('delivery_orders.sh_number_id', $request->sh_number_id))
            ->when($request->customer_id, fn($q) => $q->where('sale_orders.customer_id', $request->customer_id))
            ->when($request->from_date, fn($q) => $q->whereDate('delivery_orders.target_delivery', '>=', Carbon::parse($request->from_date)))
            ->when($request->to_date, fn($q) => $q->whereDate('delivery_orders.target_delivery', '<=', Carbon::parse($request->to_date)))
            ->when(get_current_branch()->is_primary && $request->branch_id, fn($q) => $q->where('delivery_orders.branch_id', $request->branch_id))
            ->when(!get_current_branch()->is_primary, fn($q) => $q->where('delivery_orders.branch_id', get_current_branch()->id))
            ->whereNotNull('purchase_transport_id')
            ->where('is_item_receiving_report_created', true)
            ->select('delivery_orders.*', 'sale_orders.nomor_so', 'customers.nama as customer_name');

        $checkAuthorizePrint = authorizePrint('delivery_order_trading');

        return DataTables::of($models)
            ->addIndexColumn()
            ->editColumn('target_delivery', fn($row) => $row->target_delivery ? localDate($row->target_delivery) : "-")
            ->editColumn('load_date', fn($row) => $row->load_date ? localDate($row->load_date) : "-")
            ->editColumn('unload_date', fn($row) => $row->unload_date ? localDate($row->unload_date) : "-")
            ->editColumn('load_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->load_quantity) . " " . $unit;
            })
            ->editColumn('unload_quantity_realization', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                return formatNumber($row->unload_quantity_realization) . " " . $unit;
            })
            ->editColumn('code', fn($row) => view("admin.$this->view_folder.detail-link", [
                'field' => $row->code,
                'row' => $row,
                'main' => $this->view_folder,
            ]))
            ->editColumn('nomor_so', function ($row) {
                $link = route('admin.sales-order.show', ['sales_order' => $row->so_trading_id]);

                return "<a href='{$link}' target='_blank'>{$row->nomor_so}</a>";
            })
            ->editColumn('status', fn($row) => view('admin.delivery-order.status', compact('row')))
            ->editColumn('export', function ($row) use ($checkAuthorizePrint) {
                $link = route("delivery-order.export.id", ['id' => encryptId($row->id)]);
                $linkDetail = route("admin.delivery-order.show", ['delivery_order' => $row->id]);

                $export = '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" target="_blank" onclick="showPrintOption(event)" ' . ($checkAuthorizePrint ? 'data-model="' . \App\Models\DeliveryOrder::class . '" data-id="' . $row->id . '" data-print-type="delivery_order_trading" data-link="' . $linkDetail . '" data-code="' . $row->code . '"' : '') . '>Export</a>';

                return $export;
            })
            ->addColumn('moda_transport', function ($row) {
                $transport = '';

                if ($row->purchase_transport && $row->delivery_order_ship_id) {
                    $transport = "Transportir + Kapal";
                } elseif ($row->purchase_transport && $row->delivery_order_id) {
                    $transport = "Transportir + Kendaraan";
                } elseif ($row->purchase_transport) {
                    $transport = "Transportir";
                } else {
                    $transport = "Own Use";
                }

                return $transport;
            })
            ->addColumn('action', function ($row) {
                return view("admin.$this->view_folder.button-datatable", [
                    'row' => $row,
                    'main' => $this->view_folder,
                    'model' => $row->so_trading,
                    'btn_config' => [
                        'detail' => [
                            'display' => false,
                        ],
                        'edit' => [
                            'display' => true,
                        ],
                        'delete' => [
                            'display' => false,
                        ],
                    ],
                ]);
            })
            ->rawColumns(['action', 'status', 'export', 'checkbox', 'nomor_so'])
            ->make(true);
        // }

        abort(403);
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

        $model = [];
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
        if ($request->option_type_input == 'so') {
            $this->validate($request, [
                'so_trading_id' => 'required|exists:sale_orders,id',
                'sh_number_id' => 'required|exists:sh_numbers,id',
                'target_delivery.*' => 'required|date',
                'kapasitas.*' => 'required',
                'jumlah.*' => 'required',
            ]);
        } else if ($request->option_type_input == 'potp') {
            $this->validate($request, [
                'potp_trading_id' => 'required|exists:purchase_transports,id',
                'so_trading_potp_id' => 'required|exists:sale_orders,id',
                'target_delivery.*' => 'required|date',
                'kapasitas.*' => 'required',
                'jumlah.*' => 'required',
            ]);
        }

        DB::beginTransaction();
        try {
            if ($request->option_type_input == 'so') {
                $so = SoTrading::findOrfail($request->so_trading_id);
                // * creating delivery order

                foreach ($request->kapasitas as $key => $value) {
                    for ($j = 0; $j < thousand_to_float($request->jumlah[$key]); $j++) {
                        // * create delivery order
                        $model = new model();
                        $model->loadModel([
                            'branch_id' => $so->branch_id,
                            'so_trading_id' => $request->so_trading_id,
                            'sh_number_id' => $so->sh_number_id,
                            'target_delivery' => Carbon::parse($request->target_delivery[$key]),
                            'load_quantity' => thousand_to_float($request->kapasitas[$key]),
                            'created_by' => Auth::user()->id,
                            'hpp' => $so->so_trading_detail->item->getCurrentValue(),
                        ]);

                        if (!$model->check_available_date) {
                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
                        }

                        try {
                            $model->save();

                            $authorization = new \App\Http\Helpers\AuthorizationHelper();
                            $authorization->init(
                                branch_id: $model->branch_id,
                                user_id: auth()->user()->id,
                                model: model::class,
                                model_id: $model->id,
                                amount: 0,
                                title: "DO Trading",
                                subtitle: Auth::user()->name . " mengajukan DO Trading " . $model->code,
                                link: route('admin.delivery-order.list-delivery-order.show', ['delivery_order_id' => $model->id, 'sale_order_id' => $model->so_trading_id]),
                                update_status_link: route('admin.delivery-order.update_status', ['id' => $model->id]),
                                division_id: auth()->user()->division_id ?? null
                            );
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                        }
                    }
                }

                $sale_order = SoTrading::findOrfail($request->so_trading_id);
                if (in_array($sale_order->status, ['do_not_created'])) {
                    $sale_order->status = 'not_yet_send';
                    $sale_order->save();
                }
            } else if ($request->option_type_input == 'potp') {
                $so = SoTrading::findOrfail($request->so_trading_potp_id);

                // * creating delivery order
                $potp = PurchaseTransport::findOrfail($request->potp_trading_id);
                $main_delivery = null;
                if ($potp->type == 'double_handling') {
                    $main_delivery = DeliveryOrder::where('purchase_transport_id', $potp->purchase_transport_id)
                        ->where('type', 'delivery-order-2')
                        ->first();
                }

                foreach ($request->kapasitas as $key => $value) {
                    for ($j = 0; $j < thousand_to_float($request->jumlah[$key]); $j++) {
                        // * create delivery order
                        $model = new model();
                        $model->loadModel([
                            'so_trading_id' => $so->id,
                            'purchase_transport_id' => $request->potp_trading_id,
                            'purchase_transport_detail_id' => null,
                            'item_receiving_report_id' => $request->item_receiving_report_id,
                            'sh_number_id' => $so->sh_number_id,
                            'target_delivery' => Carbon::parse($request->target_delivery[$key]),
                            'hpp' => $so->so_trading_detail->item->getCurrentValue(),
                            'delivery_order_ship_id' => $potp->delivery_order_ship_id,
                            'delivery_order_id' => $main_delivery ? $main_delivery->id : null,
                            'ware_house_id' => $potp->ware_house_id,
                            'load_quantity' => thousand_to_float($request->kapasitas[$key]),
                            'load_quantity_realization' => 0,
                            'unload_quantity' => 0,
                            'quantity_used' => 0,
                            'created_by' => Auth::user()->id,
                            'approved_by'  => $potp->approved_by,
                            'status' => 'approve',
                            'type' => 'delivery-order',
                        ]);

                        if (!$model->check_available_date) {
                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal yang anda pilih sudah close'));
                        }

                        try {
                            $model->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                        }
                    }
                }

                $sale_order = SoTrading::findOrfail($request->so_trading_potp_id);
                if (in_array($sale_order->status, ['do_not_created'])) {
                    $sale_order->status = 'not_yet_send';
                    $sale_order->save();
                }
            }

            DB::commit();
            return redirect()->route("admin.delivery.index")->with($this->ResponseMessageCRUD(true, 'create'));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
        $model = SoTrading::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        $data_quantity = model::select('delivery_orders.load_quantity')->distinct('delivery_orders.load_quantity')->where('type', "delivery-order")->where('so_trading_id', $id)->get();
        $data_quantity_delivery_2 = model::select('delivery_orders.load_quantity')->distinct('delivery_orders.load_quantity')->where('type', 'delivery-order-2')->where('so_trading_id', $id)->get();

        return view("admin.$this->view_folder.show", compact('model', 'data_quantity', 'data_quantity_delivery_2'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
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
        //
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
        try {
            $model = model::findOrFail($id);
            $model->delete();

            if ($model->delivery_order_id) {
                $delivery_order = DeliveryOrder::findOrFail($model->delivery_order_id);
                $delivery_order->calculateQtyUsed();
            }

            Authorization::where('model', model::class)->where('model_id', $model->id)->delete();

            DB::commit();
            return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
    }

    /**
     * list delivery order for a sales order
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function list_delivery_order(Request $request, $id)
    {
        $model = SoTrading::findOrFail($id);
        $data = model::where('so_trading_id', $id)
            ->where('load_quantity', $request->load_quantity)
            ->when(is_null($request->is_double), fn($q) => $q->where('type', "delivery-order-2"))
            ->when($request->is_double == "Y", fn($q) => $q->where('type', "delivery-order"))
            ->when($request->is_double == "N", fn($q) => $q->where('type', "delivery-order-2"));

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', fn($row) => $row->created_at ? localDate($row->created_at) : "-")
            ->editColumn('target_delivery', fn($row) => $row->target_delivery ? localDate($row->target_delivery) : "-")
            ->editColumn('load_date', fn($row) => $row->load_date ? localDate($row->load_date) : "-")
            ->editColumn('unload_date', fn($row) => $row->unload_date ? localDate($row->unload_date) : "-")
            ->editColumn('load_quantity', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                formatNumber($row->load_quantity) . " " . $unit;
            })
            ->editColumn('unload_quantity_realization', function ($row) {
                $unit = $row->so_trading->so_trading_detail->item->unit->name ?? '';
                formatNumber($row->unload_quantity_realization) . " " . $unit;
            })
            ->editColumn('code', fn($row) => view("admin.$this->view_folder.detail-link", [
                'field' => $row->code,
                'row' => $row,
                'main' => $this->view_folder,
            ]))
            ->editColumn('status', fn($row) => view('admin.delivery-order.status', compact('row')))
            ->editColumn('export', function ($row) {
                $export = '<a href="' . route("admin.$this->view_folder.export") . '/' . encryptId($row->id) . '" class="btn btn-sm btn-flat btn-info">Export</a>';

                return $export;
            })
            ->addColumn('kapasitas_do', fn($row) => $row->purchase_transport_detail ? formatNumber($row->purchase_transport_detail->jumlah) : formatNumber($row->kuantitas_kirim))
            ->addColumn('moda_transport', function ($row) {
                $transport = '';

                if ($row->purchase_transport && $row->delivery_order_ship_id) {
                    $transport = "Transportir + Kapal";
                } elseif ($row->purchase_transport && $row->delivery_order_id) {
                    $transport = "Transportir + Kendaraan";
                } elseif ($row->purchase_transport) {
                    $transport = "Transportir";
                } else {
                    $transport = "Own Use";
                }

                return $transport;
            })
            ->addColumn('action', function ($row) use ($model) {
                return view("admin.$this->view_folder.button-datatable", [
                    'row' => $row,
                    'main' => $this->view_folder,
                    'model' => $model,
                    'btn_config' => [
                        'detail' => [
                            'display' => false,
                        ],
                        'edit' => [
                            'display' => true,
                        ],
                        'delete' => [
                            'display' => false,
                        ],
                    ],
                ]);
            })
            ->escapeColumns([])
            ->rawColumns(['action', 'status', 'export', 'checkbox'])
            ->make(true);
    }

    /**
     * detail sale order
     *
     * @param SoTrading $sale_order_id
     * @param model $id
     * @return \Illuminate\Http\Response
     */
    public function show_delivery_order($sale_order_id, $id)
    {
        $so = SoTrading::findOrFail($sale_order_id);
        $model = model::findOrFail($id);

        $is_has_invoice = InvoiceTradingDetail::where('delivery_order_id', $model->id)
            ->whereHas('invoice_trading', function ($invoice_trading) {
                $invoice_trading->whereIn('status', ['approve', 'done']);
            })
            ->exists();

        $is_has_delivery_order = DeliveryOrder::where('delivery_order_id', $model->id)
            ->whereNotIn('status', ['reject', 'void'])
            ->exists();

        $is_can_edit_data = $model->is_can_edit_data && !$is_has_invoice;

        if ($model->so_trading_id != $so->id) {
            return abort(404);
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

        return view("admin.$this->view_folder.show-do", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'is_can_edit_data', 'is_has_delivery_order'));
    }

    /**
     * detail sale order
     *
     * @param SoTrading $sale_order_id
     * @param model $id
     * @return \Illuminate\Http\Response
     */
    public function edit_delivery_order($sale_order_id, $id)
    {
        $so = SoTrading::findOrFail($sale_order_id);
        $unit = $so->so_trading_detail->item->unit->name ?? '';
        $model = model::findOrFail($id);

        if (in_array($model->status, ['reject', 'void', 'cancel'])) {
            abort(403);
        }

        if ($model->so_trading_id != $so->id) {
            return abort(404);
        }

        if ($model->delivery_order_id) {
            $delivery_order = $model->delivery_order;

            if ($delivery_order->status != 'done') {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'view', "selesaikan delivery order pertama {$model->delivery_order?->code}"));
            }
        }

        if (!$model->check_available_date || !$model->is_can_edit_data) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "access denied"));
        }

        return view("admin.$this->view_folder.edit-do", compact('model', 'so', 'unit'));
    }

    /**
     * detail sale order
     *
     * @param Request $request
     * @param SoTrading $sale_order_id
     * @param model $id
     * @return \Illuminate\Http\Response
     */
    public function update_delivery_order(Request $request, $sale_order_id, $id)
    {
        $so = SoTrading::findOrFail($sale_order_id);
        $model = model::findOrFail($id);

        if (in_array($model->status, ['reject', 'void', 'cancel']) || !$model->check_available_date || !$model->is_can_edit_data) {
            abort(403);
        }

        if ($model->so_trading_id != $so->id) {
            return abort(404);
        }

        $this->validate($request, [
            'fleet_id' => 'nullable|exists:fleets,id',
            'employee_id' => 'nullable|exists:employees,id',
            'target_delivery' => 'nullable|date',
            'load_date' => 'nullable|date',
            'unload_date' => 'nullable|date',
            'load_quantity' => 'nullable',
            'load_quantity_realization' => 'nullable',
            'unload_quantity' => 'nullable',
            'unload_quantity_realization' => 'nullable',
            'file' => 'nullable|mimes:pdf,png,jpg,jpeg|max:6000',
            'description' => 'nullable|string|max:255',
            'top_seal' => 'nullable|string|max:60',
            'bottom_seal' => 'nullable|string|max:60',
            'temperature' => 'nullable|string|max:60',
            'initial_meter' => 'nullable|string|max:60',
            'initial_final' => 'nullable|string|max:60',
            'sg_meter' => 'nullable|string|max:60',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:24',
            'vehicle_information' => 'nullable|string|max:100',
            'external_number' => 'nullable|string|max:100',
        ]);

        $model->loadModel([
            'target_delivery' => $request->target_delivery ? Carbon::parse($request->target_delivery) : $model->target_delivery,
            'item_receiving_report_id' => $request->item_receiving_report_id,
            'ware_house_id' => $request->ware_house_id ?? $model->ware_house_id,
            'fleet_id' => $request->fleet_id,
            'employee_id' => $request->employee_id,
            'load_date' => Carbon::parse($request->load_date),
            'unload_date' => Carbon::parse($request->unload_date),
            'load_quantity' => thousand_to_float($request->load_quantity ?? 0),
            'load_quantity_realization' => thousand_to_float($request->load_quantity_realization ?? 0),
            'unload_quantity' => thousand_to_float($request->unload_quantity ?? 0),
            'unload_quantity_realization' => thousand_to_float($request->unload_quantity_realization ?? 0),
            'description' => $request->description,
            'top_seal' => $request->top_seal,
            'bottom_seal' => $request->bottom_seal,
            'temperature' => $request->temperature,
            'initial_meter' => $request->initial_meter,
            'initial_final' => $request->initial_final,
            'sg_meter' => $request->sg_meter,
            'sale_order_transport_address_id' => $request->sale_order_transport_address_id,
            'driver_name' => $request->driver_name,
            'driver_phone' => $request->driver_phone,
            'vehicle_information' => $request->vehicle_information,
            'external_number' => $request->external_number,
        ]);

        if ($request->hasFile('file')) {
            $this->delete_file($model->file ?? '');
            $model->file = $request->file('file')->store("$this->view_folder", ['disk' => 'public']);
        }

        try {
            $model->save();

            if ($model->delivery_order_id) {
                $delivery_order = DeliveryOrder::findOrFail($model->delivery_order_id);
                $delivery_order->calculateQtyUsed();
            }

            if (!in_array($model->status, ['done', 'approve'])) {
                $authorization = new \App\Http\Helpers\AuthorizationHelper();
                $authorization->init(
                    branch_id: $model->branch_id,
                    user_id: auth()->user()->id,
                    model: model::class,
                    model_id: $model->id,
                    amount: 0,
                    title: "DO Trading",
                    subtitle: Auth::user()->name . " mengajukan DO Trading " . $model->code,
                    link: route('admin.delivery-order.list-delivery-order.show', ['delivery_order_id' => $model->id, 'sale_order_id' => $model->so_trading_id]),
                    update_status_link: route('admin.delivery-order.update_status', ['id' => $model->id]),
                    division_id: auth()->user()->division_id ?? null
                );
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'delivery order.', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.delivery-order.list-delivery-order.show", ['delivery_order_id' => $model->id, 'sale_order_id' => $so->id])->with($this->ResponseMessageCRUD(true, 'update',));
    }

    /**
     * update status delivery order
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required'
        ]);

        DB::beginTransaction();

        $model = model::findOrFail($id);
        $is_purchase_item = $model->so_trading->so_trading_detail->item->item_category->item_type->nama == 'purchase item';
        if (!$model->check_available_date) {
            abort(403);
        }

        try {
            // * if done (close)
            if ($request->status == 'done') {
                if (is_null($model->ware_house_id) && is_null($model->delivery_order_id) && $is_purchase_item) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', "please select warehouse before closing your delivery order"));
                }

                if (is_null($model->load_quantity) or $model->load_quantity <= 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field load quantity before closing your delivery order'));
                }

                if (is_null($model->load_quantity_realization) or $model->load_quantity_realization <= 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field load quantity realization before closing your delivery order'));
                }

                if (is_null($model->unload_quantity_realization) or $model->unload_quantity_realization <= 0) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field unload quantity realization before closing your delivery order'));
                }

                if (is_null($model->load_date)) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field load date before closing your delivery order'));
                }

                if (is_null($model->unload_date)) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field unload date before closing your delivery order'));
                }

                if (is_null($model->target_delivery)) {
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field target delivery before closing your delivery order'));
                }

                // ! if not using purchase transport
                if (!is_null($model->purchase_transport_id) && $is_purchase_item) {
                    if (is_null($model->driver_name) || is_null($model->driver_phone)) {
                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field driver name and driver phone before closing your delivery order'));
                    }

                    if (is_null($model->vehicle_information)) {
                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'please fill field vehicle information before closing your delivery order'));
                    }
                }
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                // * if approve
                if ($request->status == 'approve') {
                    if (is_null($model->target_delivery)) {
                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'target delivery is required'));
                    }

                    // load quantity
                    if ($model->load_quantity <= 0) {
                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'quantity is required'));
                    }

                    // ! if not using purchase transport
                    // if (is_null($model->purchase_transport_id)) {
                    //     // fleet
                    //     if (is_null($model->fleet_id)) {
                    //         return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', 'fleet is required'));
                    //     }
                    // }
                }

                $model->status = $request->status;
                $model->approved_by = Auth::user()->id;
                $model->save();
            } else {
                $this->create_activity_status_log(model::class, $id, $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', $th->getMessage()));
        }

        // if model status is close and request is approve
        if ($model->status == 'done' && $request->status == 'approve') {
            $model->status = 'approve';

            try {
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status.', $th->getMessage()));
            }
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'update status.'));
    }

    /**
     * update_status_details
     *
     * @param int
     * @return mixed
     */
    public function approve_status_detail($id, $detail_id)
    {
        DB::beginTransaction();
        $model = SoTrading::findOrFail($id);
        $detail = model::findOrFail($detail_id);

        if ($detail->so_trading_id != $model->id) {
            return abort(404);
        }

        $detail->status = 'done';
        try {
            $detail->save();
        } catch (\Throwable $th) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status detail.', $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'update status detail.'));
    }

    /**
     * update_status_details
     *
     * @param int
     * @return mixed
     */
    public function reject_status_detail($id, $detail_id)
    {
        $model = SoTrading::findOrFail($id);
        $detail = model::findOrFail($detail_id);

        if ($detail->so_trading_id != $model->id) {
            return abort(404);
        }

        $detail->status = 'submit-rejected';
        try {
            $detail->save();
        } catch (\Throwable $th) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update status detail.', $th->getMessage()));
        }

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'update status detail.'));
    }

    /**
     * set delivery done
     * ! this is for development / testing only
     *
     * @param int $id
     */
    public function set_delivery_done($id)
    {
        DB::beginTransaction();
        $sale_order = SoTrading::findOrFail($id);
        $delivery_orders = $sale_order->delivery_orders()->where('status', 'done')->get();

        foreach ($delivery_orders as $key => $delivery_order) {
            $realisasi_muat = $delivery_order->delivery_order_detail->delivery_order_type()->where('tipe', 'supplier')->first()->realisasi;
            $realisasi_bongkar = $delivery_order->delivery_order_detail->delivery_order_type()->where('tipe', 'drop')->first()->realisasi;

            $delivery_order->kuantitas_kirim = $realisasi_muat;
            $delivery_order->kuantitas_diterima = $realisasi_bongkar;

            $delivery_order->save();
        }

        // * make delivery order un complete to void
        $delivery_orders = $sale_order->delivery_orders()->where('status', '!=', 'done')->get();
        foreach ($delivery_orders as $key => $delivery_order) {
            $delivery_order->status = 'void';
            $delivery_order->save();
        }

        $sale_order->status = 'delivery_complete';
        $sale_order->save();

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'set done'));
    }

    public function approve_all_request_print($id)
    {
        DB::beginTransaction();
        $sale_order = SoTrading::findOrFail($id);
        $delivery_orders = $sale_order->delivery_orders->where('status', 'request-print');

        foreach ($delivery_orders as $key => $model) {
            $model->status = 'approve-request-print';
            try {
                $model->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'approve all request print', $th->getMessage()));
            }
        }

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'approve'));
    }

    public function approve_all_submitted($id)
    {
        DB::beginTransaction();
        $sale_order = SoTrading::findOrFail($id);
        if ($sale_order->is_have_any_submitted) {
            $delivery_orders = $sale_order->delivery_orders->where('status', 'submitted');

            foreach ($delivery_orders as $key => $model) {
                $model->status = 'done';
                try {
                    $model->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'approve all request print', $th->getMessage()));
                }
            }
        }


        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'approve'));
    }

    public function update_request_print(Request $request, $sale_order_id, $id)
    {
        DB::beginTransaction();

        $sale_order = SoTrading::findOrFail($sale_order_id);
        $model = model::findOrFail($id);
        $old_status = $model->status;

        if ($model->so_trading_id != $sale_order->id) {
            return abort(404);
        }

        $this->create_activity_status_log(model::class, $id, 'message not available', $old_status, $request->status);

        $model->status = $request->status;
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'approve request print', $th->getMessage()));
        }

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'update', 'approve'));
    }

    public function checkWarehouseStock(Request $request, $sale_order_id, $id)
    {
        DB::beginTransaction();

        $sale_order = SoTrading::findOrFail($sale_order_id);
        $model = model::findOrFail($id);

        $item = $sale_order->so_trading_detail->item;

        // * get stock in
        $in = \App\Models\StockMutation::where('ware_house_id', $request->warehouse_id)
            ->where('item_id', $item->id)
            ->whereNull('is_return')
            ->sum('in');

        // * get stock out
        $out = \App\Models\StockMutation::where('ware_house_id', $request->warehouse_id)
            ->where('item_id', $item->id)
            ->whereNull('is_return')
            ->sum('out');

        return $this->ResponseJsonData([
            'stock' => $in - $out,
            'warehouse' => $request->warehouse_id,
        ]);
    }

    /**
     * check delivery stock
     *
     */
    public function checkDeliveryStock($id)
    {
        DB::beginTransaction();

        $model = model::findOrFail($id);
        $left = $model->unload_quantity_realization - $model->quantity_used;

        return $this->ResponseJsonData([
            'stock' => $left,
        ]);
    }

    /**
     * delivery order export
     *
     *
     *
     */
    public function export($id, Request $request)
    {
        if (!$request->preview && authorizePrint('delivery_order_trading')) {
            $document_print = new PrintHelper();
            $result = $document_print->check_available_for_print(
                model::class,
                decryptId($id),
                'delivery_order_trading',
            );

            if (!$result) {
                return abort(403);
            }
        }

        $model = model::with('so_trading', 'created_by_user', 'approved_by_user')->findOrFail(decryptId($id));

        $file = public_path('/pdf_reports/Report-Invoice-Trading-' . microtime(true) . '.pdf');
        $fileName = 'Report-Invoice-Trading-' . microtime(true) . '.pdf';

        if (!$model->status_print) {
            $model->status_print = true;
            $model->save();
        }

        $approval = Authorization::where('model', model::class)
            ->where('model_id', decryptId($id))
            ->with(['details' => function ($q) {
                $q->where('status', 'approve')
                    ->where('note', 'not like', '%otomatis%');
            }])
            ->first();

        if (!$approval) {
            $approval = Authorization::where('model', PurchaseTransport::class)
                ->where('model_id', $model->purchase_transport_id)
                ->with(['details' => function ($q) {
                    $q->where('status', 'approve')
                        ->where('note', 'not like', '%otomatis%');
                }])
                ->first();
        }

        $qr_url = route('delivery-order.export.id', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $generate_unique_temp_code = time() . mt_rand(100, 999);
        $generate_printout = [];
        if ($request->original == "true") {
            $generate_printout[] = 'asli';
        }

        for ($i = 0; $i < $request->copies ?? 0; $i++) {
            $generate_printout[] = 'copy-' . $i + 1;
        }

        foreach ($generate_printout as $key => $generate_print_out) {
            $document_stamp = $generate_print_out;
            $pdf = PDF::loadview("admin/.$this->view_folder./export", compact('model', 'qr', 'approval', 'document_stamp'))
                ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'portrait');
            $canvas = $pdf->get_canvas();
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $canvas->page_text($w - 80, $h - 20, "Page: {PAGE_NUM}/{PAGE_COUNT}", '', 8);

            $path = "delivery-generate/$generate_unique_temp_code/$document_stamp.pdf";
            Storage::disk('public')->put($path, $pdf->output());
        }

        $files = Storage::disk('public')->allFiles("delivery-generate/$generate_unique_temp_code");
        $pdf_merger = PDFMerger::init();
        foreach ($files as $key => $file) {
            $pdf_merger->addPDF(public_path('storage/' . $file));
        }
        $pdf_merger->merge();
        $result = $pdf_merger
            ->setFileName("DO-$model->code.pdf")
            ->output();

        Storage::disk('public')->deleteDirectory("delivery-generate/$generate_unique_temp_code");
        if ($request->ajax()) {
            Storage::disk('public')->deleteDirectory('tmp_delivery_order');
            $tmp_file_name = 'delivery_order_' . time() . '.pdf';
            $path = 'tmp_delivery_order/' . $tmp_file_name;
            Storage::disk('public')->put($path, $result);

            return response()->json($path);
        }
        return response($result)
            ->header('Content-type', 'application/pdf');
    }

    public function history($id, Request $request)
    {
        try {
            $delivery_orders = DB::table('delivery_orders')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->select(
                    'delivery_orders.id',
                    'delivery_orders.code',
                    'delivery_orders.target_delivery as date',
                    'delivery_orders.status',
                    'delivery_orders.so_trading_id'
                )->get();

            $sale_orders = DB::table('sale_orders')
                ->whereNull('deleted_at')
                ->whereIn('id', $delivery_orders->pluck('so_trading_id')->toArray())
                ->select(
                    'id',
                    'sale_orders.nomor_so as code',
                    'sale_orders.tanggal as date',
                    'status',
                )->get();

            $invoice_tradings = DB::table('invoice_trading_details')
                ->join('invoice_tradings', 'invoice_tradings.id', '=', 'invoice_trading_details.invoice_trading_id')
                ->join('invoice_parents', function ($query) {
                    $query->on('invoice_parents.reference_id', '=', 'invoice_tradings.id')
                        ->where('invoice_parents.model_reference', '=', 'App\Models\InvoiceTrading');
                })
                ->whereIn('invoice_trading_details.delivery_order_id', $delivery_orders->pluck('id')->toArray())
                ->whereNotIn('invoice_tradings.status', ['rejected', 'void'])
                ->whereNull('invoice_tradings.deleted_at')
                ->select(
                    'invoice_tradings.id',
                    'invoice_tradings.kode as code',
                    'invoice_tradings.date',
                    'invoice_tradings.status',
                    'invoice_parents.id as invoice_parent_id',
                )->get();

            $receivables_payments = DB::table('receivables_payment_details')
                ->where('invoice_parent_id', $invoice_tradings->pluck('invoice_parent_id')->toArray())
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

            $delivery_orders = $delivery_orders->map(function ($item) {
                $item->link = route('admin.delivery-order.show', $item->id);
                $item->menu = 'delivery order trading';
                return $item;
            });

            $sale_orders = $sale_orders->map(function ($item) {
                $item->link = route('admin.sales-order.show', $item->id);
                $item->menu = 'sales order trading';
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
                ->merge($delivery_orders->unique('id'))
                ->merge($invoice_tradings->unique('id'))
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

    public function select_potp_for_do(Request $request)
    {
        $purchas_transport = PurchaseTransport::query()
            ->leftJoin('sale_orders', 'sale_orders.id', '=', 'purchase_transports.so_trading_id')
            ->leftJoin('customers', 'customers.id', '=', 'sale_orders.customer_id')
            ->whereNotIn('purchase_transports.status', ['pending', 'reject', 'void', 'done'])
            ->whereColumn('purchase_transports.total_qty', '>', 'purchase_transports.delivered_qty')
            ->where(function ($query) {
                $query->where('purchase_transports.type', 'not_double_handling')
                    ->orWhere(function ($query) {
                        $query->where('purchase_transports.type', 'double_handling')
                            ->where('purchase_transports.purchase_transport_id', '!=', null);
                    });
            })
            ->where('purchase_transports.delivery_destination', 'to_customer')
            ->when(!get_current_branch()->is_primary, function ($q) {
                $q->where('purchase_transports.branch_id', auth()->user()->branch_id);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($q) use ($request) {
                $q->where('purchase_transports.branch_id', $request->branch_id);
            })
            ->when(request('search'), function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('purchase_transports.kode', 'like', '%' . request('search') . '%')
                        ->orWhere('customers.nama', 'like', '%' . request('search') . '%');
                });
            })
            ->groupBy('purchase_transports.id')
            ->select('purchase_transports.*', 'customers.nama as customer_name')
            ->paginate(10, [], 'page', request('page'));

        return response()->json($purchas_transport);
    }

    public function get_so($potp)
    {
        $potp = PurchaseTransport::query()
            ->with(['purchase_transport_details'])
            ->findOrFail($potp);

        $so = SoTrading::query()
            ->findOrFail($potp->so_trading_id);

        // validate_branch($so->branch_id);

        $potp_modified = [];

        foreach ($potp->purchase_transport_details as $key => $value) {
            $potp_modified[$key] = [
                "id" => $value->id,
                "target_delivery" => Carbon::parse($potp->target_delivery)->format('d-m-Y'),
                "jumlah" => $value->jumlah,
                "jumlah_do" => $value->jumlah_do,
            ];
        }

        $unit = $so->so_trading_detail->item->unit->name ?? '';

        return $this->ResponseJsonData([
            'potp_data' => $potp_modified,
            'potp_qty' => $potp->purchase_transport_details->map(function ($item) {
                return $item->jumlah * $item->jumlah_do;
            })
                ->sum(),
            'potp_quota' => $potp->total_qty - $potp->delivered_qty,
            'jumlah' => $so->so_trading_detail->jumlah - $so->so_trading_detail->sudah_dikirim . " " . $unit,
            'jumlah_int' => $so->jumlah_number,
            'jumlah_dikirim' => $so->so_trading_detail->sudah_dikirim,
            'tanggal' => $so->tanggal,
            'id' => $so->id,
            'kode' => $so->nomor_so,
            'customer' => $so->customer,
            'unit' => $unit,
        ]);
    }

    public function item_receiving_report_select(Request $request)
    {
        $model = model::with('so_trading', 'created_by_user', 'approved_by_user')
            ->where('id', $request->id)
            ->first();

        $pairings = PairingSoToPo::with('po_trading_detail.po_trading')
            ->whereHas('so_trading_detail', function ($q) use ($model) {
                $q->whereHas('so_trading', function ($q) use ($model) {
                    $q->where('id', $model->so_trading_id);
                });
            })
            ->get()
            ->map(fn($q) => $q->po_trading_detail->po_trading->id);

        $item_receiving_reports = ItemReceivingReport::where('reference_model', PoTrading::class)
            ->join('item_receiving_po_tradings', 'item_receiving_po_tradings.item_receiving_report_id', '=', 'item_receiving_reports.id')
            ->whereIn('reference_id', $pairings)
            ->select('item_receiving_reports.*', 'item_receiving_po_tradings.loading_order')
            ->when(request('search'), function ($query) {
                $query->where('item_receiving_reports.kode', 'like', '%' . request('search') . '%')
                    ->orWhere('item_receiving_po_tradings.loading_order', 'like', '%' . request('search') . '%');
            })
            ->paginate(10);

        return $this->ResponseJson($item_receiving_reports);
    }
}
