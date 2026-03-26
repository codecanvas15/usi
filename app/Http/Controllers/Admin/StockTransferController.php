<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Price;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\StockTransferDetail;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class StockTransferController extends Controller
{
    use ActivityStatusLogHelper;
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'stock-transfer';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockTransfer::with('creator', 'fromWarehouse', 'toWarehouse')
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->when($request->from_date, fn($q) => $q->whereDate('date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('date', '<=', Carbon::parse($request->to_date)));

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y');
                })
                ->editColumn('code', function ($row) {
                    $route = route('admin.stock-transfer.show', [
                        'stock_transfer' => $row->id,
                    ]);
                    return "<a href='{$route}' class='text-primary text-decoration-underline hover_text-dark'>{$row->code}</a>";
                })
                ->editColumn('fromWarehouse.nama', function ($row) {
                    return $row->fromWarehouse->nama ?? '-';
                })
                ->editColumn('toWarehouse.nama', function ($row) {
                    return $row->toWarehouse->nama ?? '-';
                })
                ->editColumn('creator.name', function ($row) {
                    return $row->creator->name ?? '-';
                })
                ->editColumn('status', function ($row) {
                    return '<div class="badge badge-lg badge-' . stock_usage_status()[$row->status]['color'] . '">
                                ' . stock_usage_status()[$row->status]['label'] . ' - ' . stock_usage_status()[$row->status]['text'] . '
                            </div>';
                })
                ->editColumn('export', function ($row) {
                    $link = route('stock-transfer.export-pdf', ['id' => encryptId($row->id)]);
                    return '<a href="' . $link . '" class="btn btn-sm btn-flat btn-info" onclick="show_print_out_modal(event)">Export</a>';
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' =>
                        [
                            'detail' => [
                                'display' => true,
                            ],
                            'edit' => [
                                'display' => in_array($row->status, ['pending', 'revert']) ? true : false,
                            ],
                            'delete' => [
                                'display' => in_array($row->status, ['pending', 'revert']) ? true : false,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['code', 'status', 'from', 'to', 'export', 'action'])
                ->make(true);
        }

        $warehouse = WareHouse::first();

        return view('admin.' . $this->view_folder . '.index', compact('warehouse'));
    }

    public function receiving(Request $request)
    {
        if ($request->ajax()) {
            $data = StockTransfer::with('creator', 'fromWarehouse', 'toWarehouse')
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->whereNotNull('receiving_status')
                ->when($request->from_date, fn($q) => $q->whereDate('date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('date', '<=', Carbon::parse($request->to_date)));

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    $route = route('admin.stock-transfer.show.receiving', [
                        'id' => $row->id,
                    ]);
                    return "<a href='{$route}' class='text-primary text-decoration-underline hover_text-dark'>{$row->code}</a>";
                })
                ->editColumn('fromWarehouse.nama', function ($row) {
                    return $row->fromWarehouse->nama;
                })
                ->editColumn('toWarehouse.nama', function ($row) {
                    return $row->toWarehouse->nama;
                })
                ->editColumn('creator.name', function ($row) {
                    return $row->creator->name;
                })
                ->editColumn('status', function ($row) {
                    return '<div class="badge badge-lg badge-' . stock_usage_status()[$row->receiving_status]['color'] . '">
                                ' . stock_usage_status()[$row->receiving_status]['label'] . ' - ' . stock_usage_status()[$row->receiving_status]['text'] . '
                            </div>';
                })
                ->rawColumns(['code', 'status', 'from', 'to'])
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
        if (!$authorization->is_authoirization_exist(StockTransfer::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

        $model = [];
        $now = Carbon::now()->format('Y-m-d');
        return view("admin.$this->view_folder.create", compact('model', 'now'));
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

        $ware_house = WareHouse::find($request->from);

        if (!$ware_house) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Gudang tidak ditemukan'));
        }

        $branch = $ware_house->branch->sort;

        if (!checkAvailableDate($request->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tidak tersedia / sudah tutup buku'));
        }

        try {
            // * create stock transfer
            $model = new StockTransfer();

            $model->fill([
                'branch_id' => $ware_house->branch_id ?? get_current_branch_id(),
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'code' => generate_code(StockTransfer::class, 'code', 'date', 'STR', $branch, Carbon::parse($request->date)->format('Y-m-d')),
                'from' => $request->from,
                'to' => $request->to,
                'note' => $request->note,
                'created_by' => Auth::user()->id,
                'status' => "pending",
            ]);
            $model->save();

            // * creating data detail stock transfer
            $data_details = [];
            if (is_array($request->item_id)) {
                // * get last price each item
                $prices = Price::whereIn('item_id', $request->item_id)->orderByDesc('id')->get();

                foreach ($request->item_id as $key => $item_id) {
                    $price = $prices->where('item_id', $item_id)->first();

                    $data_details[] = [
                        'stock_transfer_id' => $model->id,
                        'item_id' => $item_id,
                        'price_id' => $price?->id,
                        'qty' => thousand_to_float($request->qty[$key]),
                        'stock' => thousand_to_float($request->stock[$key]),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            StockTransferDetail::insert($data_details);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: StockTransfer::class,
                model_id: $model->id,
                amount: 0,
                title: "Transfer Stock",
                subtitle: Auth::user()->name . " mengajukan transfer stock " . $model->code,
                link: route('admin.stock-transfer.show', $model),
                update_status_link: route('admin.stock-transfer.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route('admin.stock-transfer.index')->with(['message' => 'Berhasil menyimpan transfer stock']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator'])->findOrFail($id);
        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: StockTransfer::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );
        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = true;
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_logs['can_revert_request'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        return view("admin.$this->view_folder.show", compact('model', 'authorization_log_view', 'status_logs', 'activity_logs', 'auth_revert_void_button'));
    }

    public function showReceiving($id)
    {
        $model = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator'])->findOrFail($id);

        return view('admin.stock-transfer.show-receiving', compact('model'));
    }

    public function updateReceiving(Request $request, $id)
    {
        $stock_transfer = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator'])->findOrFail($id);

        if (!$stock_transfer->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        foreach ($request->detail_id as $key => $detail_id) {
            $detail = StockTransferDetail::find($detail_id);
            $detail->receiving_qty = $request->receiving_qty[$key];
            $detail->save();
        }

        $stock_transfer->receiving_status = 'approve';
        $stock_transfer->save();

        return redirect()->back();
    }

    public function update_status(Request $request, $id)
    {
        Db::beginTransaction();

        $model = StockTransfer::findOrfail($id);
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
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
                $this->create_activity_status_log(StockTransfer::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                // * saving and make response
                $model->status = $request->status;

                if ($request->status == 'approve') {
                    foreach ($model->details as $detail) {
                        $this->createInStockMutation($detail->id);
                        $this->createOutStockMutation($detail->id);
                    }
                } elseif ($request->status == 'void') {
                    foreach ($model->details as $detail) {
                        $this->createInStockMutation($detail->id, true);
                        $this->createOutStockMutation($detail->id, true);
                    }
                }
                $model->save();
            } else {
                $this->create_activity_status_log(StockTransfer::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            DB::commit();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD();
            }

            return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }

    // * create stok mutasi masuk ke gudang
    public function createOutStockMutation($id, $revert = false)
    {
        try {
            $stock_transfer_detail = StockTransferDetail::with(['stockTransfer.fromWarehouse'])->find($id);
            $stock_mutations = StockMutation::where('item_id', $stock_transfer_detail->item_id)
                ->get();
            $lastStock = $stock_mutations->sortByDesc('id')
                ->first();
            $sumInStock = $stock_mutations
                ->sum('in');
            $sumOutStock = $stock_mutations
                ->sum('out');
            $totalStock = $sumInStock - $sumOutStock;
            $current_item_value = $lastStock->value ?? $stock_transfer_detail->item->getCurrentValue();

            $warehouse = $stock_transfer_detail->stockTransfer->from;
            if ($revert) {
                $warehouse = $stock_transfer_detail->stockTransfer->to;
            }
            $warehouse_data = WareHouse::find($warehouse);

            $get_current_warehouse_stock = $stock_mutations
                ->where('ware_house_id', $warehouse)
                ->sum('in') - $stock_mutations->where('ware_house_id', $warehouse)->sum('out');

            if ($get_current_warehouse_stock < $stock_transfer_detail->qty) {
                throw new \Exception("Stok {$stock_transfer_detail->item->nama} tidak mencukupi");
            }

            $mutation = new StockMutation();
            $mutation->branch_id = $warehouse_data->branch_id;
            $mutation->ware_house_id = $warehouse;
            $mutation->item_id = $stock_transfer_detail->item_id;
            $mutation->price_id = $stock_transfer_detail->price_id;
            $mutation->type = 'stock transfer';
            $mutation->document_id = $stock_transfer_detail->stock_transfer_id;
            $mutation->document_code = $stock_transfer_detail->stockTransfer->code;
            $mutation->date = $stock_transfer_detail->stockTransfer->date;
            $mutation->document_model = \App\Models\StockTransfer::class;
            $mutation->out = $stock_transfer_detail->qty;
            $mutation->price_unit = $current_item_value;
            $mutation->subtotal = $current_item_value * $stock_transfer_detail->qty;
            $mutation->total = $lastStock->total - $mutation->subtotal;
            $mutation->value = ($totalStock - $mutation->out) != 0 ? ($lastStock->total - $mutation->subtotal) / ($totalStock - $mutation->out) : 0;
            $mutation->note = 'Transfer Stock ' . $stock_transfer_detail->stockTransfer->note;
            $mutation->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // * create stok mutasi keluar dari gudang
    public function createInStockMutation($id, $revert = false)
    {
        try {
            $stock_transfer_detail = StockTransferDetail::with(['stockTransfer.fromWarehouse'])->find($id);

            $lastStock = StockMutation::where('item_id', $stock_transfer_detail->item_id)
                ->where('ware_house_id', $stock_transfer_detail->stockTransfer->from)
                ->orderBy('id', 'desc')
                ->first();

            $sumInStock = StockMutation::where('item_id', $stock_transfer_detail->item_id)
                ->where('ware_house_id', $stock_transfer_detail->stockTransfer->from)
                ->sum('in');

            $sumOutStock = StockMutation::where('item_id', $stock_transfer_detail->item_id)
                ->where('ware_house_id', $stock_transfer_detail->stockTransfer->from)
                ->sum('out');

            $totalStock = $sumInStock - $sumOutStock;
            $current_item_value = $lastStock->value ?? $stock_transfer_detail->item->getCurrentValue();

            $warehouse = $stock_transfer_detail->stockTransfer->to;
            if ($revert) {
                $warehouse = $stock_transfer_detail->stockTransfer->from;
            }
            $warehouse_data = WareHouse::find($warehouse);

            $mutation = new StockMutation();
            $mutation->branch_id = $warehouse_data->branch_id;
            if ($revert) {
                $mutation->ware_house_id = $warehouse;
            } else {
                $mutation->ware_house_id = $warehouse;
            }
            $mutation->item_id = $stock_transfer_detail->item_id;
            $mutation->price_id = $stock_transfer_detail->price_id;
            $mutation->type = 'stock transfer';
            $mutation->document_id = $stock_transfer_detail->stock_transfer_id;
            $mutation->document_model = \App\Models\StockTransfer::class;
            $mutation->document_code = $stock_transfer_detail->stockTransfer->code;
            $mutation->date = $stock_transfer_detail->stockTransfer->date;
            $mutation->in = $stock_transfer_detail->qty;
            $mutation->price_unit = $current_item_value;
            $mutation->subtotal = $current_item_value * $stock_transfer_detail->qty;
            $mutation->total = $lastStock->total + $mutation->subtotal;
            $mutation->value = ($totalStock + $mutation->out) != 0 ? ($lastStock->total + $mutation->subtotal) / ($totalStock + $mutation->out) : 0;
            $mutation->note = 'Transfer Stock ' . $stock_transfer_detail->stockTransfer->note;
            $mutation->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function priceSelect(Request $request)
    {
        $prices = StockMutation::with('item')->with(['price' => function ($i) use ($request) {
            $i->where('item_id', $request->item_id);
        }])
            ->where('item_id', $request->item_id)
            ->where('ware_house_id', $request->ware_house_id)
            ->groupBy('price_id')
            ->get();

        $data_prices = [];
        foreach ($prices as $price) {
            $price->date = Carbon::parse($price->created_at)->format('d/m/Y');
            if ($price->price) {
                array_push($data_prices, $price);
            }
        }

        $data = $data_prices;

        return response()->json($data);
    }

    public function priceDetail(Request $request)
    {
        $item = Price::find($request->price_id);
        $item->main_stock = $item->mainStock($request->ware_house_id);

        return response()->json($item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = StockTransfer::with('details.item')
            ->findOrFail($id);

        if ($request->ajax()) {
            return $this->ResponseJson($model);
        }
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
        DB::beginTransaction();

        $ware_house = WareHouse::find($request->from);

        if (!$ware_house) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Gudang tidak ditemukan'));
        }

        if (!checkAvailableDate($request->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tidak tersedia / sudah tutup buku'));
        }

        try {
            // * create stock transfer
            $model = StockTransfer::findOrFail($id);

            $model->fill([
                'branch_id' => $ware_house->branch_id ?? get_current_branch_id(),
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'from' => $request->from,
                'to' => $request->to,
                'note' => $request->note,
                'status' => "pending",
            ]);
            $model->save();

            // * creating data detail stock transfer
            if (is_array($request->item_id)) {
                // * get last price each item
                $prices = Price::whereIn('item_id', $request->item_id)->orderByDesc('id')->get();
                StockTransferDetail::where('stock_transfer_id', $model->id)
                    ->whereNotIn('item_id', $request->item_id)
                    ->delete();

                foreach ($request->item_id as $key => $item_id) {
                    $price = $prices->where('item_id', $item_id)->first();
                    StockTransferDetail::updateOrCreate(
                        [
                            'stock_transfer_id' => $model->id,
                            'item_id' => $item_id,
                        ],
                        [
                            'stock_transfer_id' => $model->id,
                            'item_id' => $item_id,
                            'price_id' => $price?->id,
                            'qty' => thousand_to_float($request->qty[$key]),
                            'stock' => thousand_to_float($request->stock[$key]),
                        ]
                    );
                }
            }


            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: StockTransfer::class,
                model_id: $model->id,
                amount: 0,
                title: "Transfer Stock",
                subtitle: Auth::user()->name . " mengajukan transfer stock " . $model->code,
                link: route('admin.stock-transfer.show', $model),
                update_status_link: route('admin.stock-transfer.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->route('admin.stock-transfer.index')->with(['message' => 'Berhasil menyimpan transfer stock']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = StockTransfer::findOrFail($id);
        if (!checkAvailableDate($model->date)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tidak tersedia / sudah tutup buku'));
        }

        DB::beginTransaction();
        try {
            StockTransferDetail::where('stock_transfer_id', $model->id)->delete();
            $model->delete();
            DB::commit();
            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
    }

    /**
     * Export pdf
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function export_pdf($id, Request $request)
    {
        $model = StockTransfer::findOrFail(decryptId($id));

        $qr_url = url('stock-transfer/' . encryptId($model->id));
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = Pdf::loadView("admin.$this->view_folder.export", compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'landscape');

        return $pdf->stream($model->code . '.pdf');
    }

    /**
     * Stock transfer check stock
     */
    public function checkStock(Request $request)
    {
        $item = \App\Models\Item::with(['unit'])->find($request->item_id);
        $ware_house = \App\Models\WareHouse::find($request->ware_house_id);

        // * if item or warehouse not found
        if (!$item || !$ware_house) {
            return $this->ResponseJsonData([], "Empty", 204);
        }

        // * get stock
        $stocks = \App\Models\StockMutation::where('item_id', $request->item_id)
            ->where('ware_house_id', $request->ware_house_id)
            ->orderBy('ordering', 'desc')
            ->get();

        $stock_in = $stocks->sum('in');
        $stock_out = $stocks->sum('out');

        $stock_final = $stock_in - $stock_out;

        return $this->ResponseJsonData([
            'item_unit' => $item->unit,
            'stock_final' => $stock_final,
        ], 200);
    }

    public function checkItemStockTransfer(Request $request, $id_from, $id_item)
    {
        $model = StockTransfer::query()
            ->where('from', $id_from)
            ->where('status', 'pending')
            ->when($request->id, function ($query) use ($request) {
                $query->where('id', '!=', $request->id);
            })
            ->get();

        if ($model->count() > 0) {
            $qty_stock_transfer = 0;
            foreach ($model as $key => $stock_transfer) {
                foreach ($stock_transfer->details as $key_2 => $detail) {
                    if ($detail->item_id == $id_item) {
                        $qty_stock_transfer += $detail->qty;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'taken_qty' => $qty_stock_transfer,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'taken_qty' => 0,
            ], 404);
        }
    }
}
