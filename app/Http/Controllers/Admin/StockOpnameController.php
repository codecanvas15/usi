<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Price;
use App\Models\StockOpname;
use App\Models\StockOpname as model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Models\Authorization;
use App\Models\Journal;
use App\Models\WareHouse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    use ActivityStatusLogHelper;
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'stock-adjustment';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockOpname::with(['creator'])
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->when($request->from_date, fn($q) => $q->whereDate('date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('date', '<=', Carbon::parse($request->to_date)))
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    $route = route('admin.stock-adjustment.show', $row->id);
                    return "<a href='{$route}' class='text-primary text-decoration-underline hover_text-dark'>{$row->code}</a>";
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('d-m-Y');
                })
                ->editColumn('employee', function ($row) {
                    return $row->creator->name;
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . stock_usage_status()[$row->status]['color'] . '">
                                ' . stock_usage_status()[$row->status]['label'] . ' - ' . stock_usage_status()[$row->status]['text'] . '
                            </div>';

                    return $badge;
                })
                ->addColumn('notes', function ($row) {
                    return $row->details->pluck('note')->filter()->implode('<br>');
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
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['code', 'status', 'action'])
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
        $model = [];
        $now = Carbon::now()->format('Y-m-d');
        $authorization = new \App\Http\Helpers\AuthorizationHelper();
        if (!$authorization->is_authoirization_exist(model::class)) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "Silahkan setting otorisasi terlebih dahulu"));
        }

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
        try {
            $warehouse = WareHouse::find($request->ware_house_id);

            $stock_opname = new StockOpname();
            $stock_opname->code = 'SOP-' . date('Ymd') . Str::random(4);
            $stock_opname->ware_house_id = $warehouse->id;
            $stock_opname->branch_id = $warehouse->branch_id;
            $stock_opname->coa_id = $request->coa_id;
            $stock_opname->date = Carbon::parse($request->date);
            $stock_opname->created_by = Auth::user()->id;
            $stock_opname->status = 'pending';

            if (!$stock_opname->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $stock_opname->save();
            foreach ($request->item_id as $key => $item_id) {
                $item = Item::find($item_id);
                $difference =  thousand_to_float($request->real_stock[$key]) - $item->mainStock($warehouse->id);
                $price_unit = thousand_to_float($request->price_unit[$key]) ?? $item->getCurrentValue();
                $value = $difference * $price_unit;

                $stock_opname_detail = new StockOpnameDetail();
                $stock_opname_detail->stock_opname_id = $stock_opname->id;
                $stock_opname_detail->item_id = $item_id;
                $stock_opname_detail->stock = $item->mainStock($warehouse->id);
                $stock_opname_detail->real_stock = thousand_to_float($request->real_stock[$key]);
                $stock_opname_detail->difference = $difference;
                $stock_opname_detail->note = $request->note[$key];
                $stock_opname_detail->price_unit = $price_unit;
                $stock_opname_detail->value = $value;
                $stock_opname_detail->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $stock_opname->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $stock_opname->id,
                amount: $stock_opname->details->sum('value') ?? 0,
                title: "Stock Adjustment",
                subtitle: Auth::user()->name . " mengajukan Stock Adjustment " . $stock_opname->code,
                link: route('admin.stock-adjustment.show', $stock_opname),
                update_status_link: route('admin.stock-adjustment.update-status', ['id' => $stock_opname->id]),
                division_id: auth()->user()->division_id ?? null
            );

            DB::commit();

            return redirect()->route("admin.stock-adjustment.show", ['stock_adjustment' => $stock_opname->id])->with($this->ResponseMessageCRUD(true, 'create', "Berhasil menyimpan stock adjustment"));
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', "Gagal menyimpan stock adjustment " . $th->getMessage()));
        }
    }

    public function update_status(Request $request, $id)
    {
        Db::beginTransaction();
        $model = StockOpname::findOrfail($id);

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
                $this->create_activity_status_log(model::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);

                if ($request->status == 'approve') {
                    $total = $this->createStockMutation($id);
                    if ($total < 0) {
                        $model->less_difference = $total;
                    } else {
                        $model->more_difference = $total;
                    }
                }
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = StockOpname::findOrFail($id);
        $data['model'] = $model;

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: model::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_logs['can_revert'] = $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void'] = $model->check_available_date && $model->status == 'approve';
        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();
        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_logs['can_revert_request'] =  $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $model->check_available_date && $model->status == 'approve';

        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();

        $data['authorization_log_view'] = $authorization_log_view;
        $data['status_logs'] = $status_logs;
        $data['activity_logs'] = $activity_logs;
        $data['auth_revert_void_button'] = $auth_revert_void_button;


        return view("admin.$this->view_folder.show", $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = StockOpname::with(['warehouse', 'details.item', 'details.price'])->findOrFail($id);

        if (!in_array($model->status, ['pending', 'revert'])) {
            return abort(403);
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
        $stock_opname = StockOpname::with(['details'])->findOrFail($id);

        if (!in_array($stock_opname->status, ['pending', 'revert'])) {
            return abort(403);
        }

        DB::beginTransaction();
        try {
            $warehouse = WareHouse::find($request->ware_house_id);

            $stock_opname->ware_house_id = $warehouse->id;
            $stock_opname->branch_id = $warehouse->branch_id;
            $stock_opname->coa_id = $request->coa_id;
            $stock_opname->date = Carbon::parse($request->date);
            $stock_opname->save();

            $stock_opname->details()->delete();
            foreach ($request->item_id as $key => $item_id) {
                $item = Item::find($item_id);
                $difference =  thousand_to_float($request->real_stock[$key]) - $item->mainStock($warehouse->id);
                $price_unit = thousand_to_float($request->price_unit[$key]) ?? $item->getCurrentValue();
                $value = $difference * $price_unit;

                $stock_opname_detail = new StockOpnameDetail();
                $stock_opname_detail->stock_opname_id = $stock_opname->id;
                $stock_opname_detail->item_id = $item_id;
                $stock_opname_detail->stock = $item->mainStock($warehouse->id);
                $stock_opname_detail->real_stock = thousand_to_float($request->real_stock[$key]);
                $stock_opname_detail->difference = $difference;
                $stock_opname_detail->note = $request->note[$key];
                $stock_opname_detail->price_unit = $price_unit;
                $stock_opname_detail->value = $value;
                $stock_opname_detail->save();
            }

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $stock_opname->branch_id,
                user_id: auth()->user()->id,
                model: model::class,
                model_id: $stock_opname->id,
                amount: $stock_opname->details->sum('value') ?? 0,
                title: "Stock Adjustment",
                subtitle: Auth::user()->name . " mengajukan Stock Adjustment " . $stock_opname->code,
                link: route('admin.stock-adjustment.show', $stock_opname),
                update_status_link: route('admin.stock-adjustment.update-status', ['id' => $stock_opname->id]),
                division_id: auth()->user()->division_id ?? null
            );

            DB::commit();

            return redirect()->route("admin.stock-adjustment.show", ['stock_adjustment' => $stock_opname->id])->with($this->ResponseMessageCRUD(true, 'update', "Berhasil memperbarui stock adjustment"));
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', "Gagal memperbarui stock adjustment " . $th->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $model = StockOpname::findOrFail($id);

            if (!in_array($model->status, ['pending', 'revert'])) {
                return abort(403);
            }

            $model->details()->delete();
            $model->delete();

            StockMutation::where('document_model', StockOpname::class)
                ->where('document_id', $model->id)
                ->delete();

            Journal::where('reference_id', $model->id)
                ->where('reference_model', StockOpname::class)
                ->delete();

            Authorization::where('model', StockOpname::class)
                ->where('model_id', $model->id)
                ->delete();

            DB::commit();

            return redirect()->route("admin.stock-adjustment.index")->with($this->ResponseMessageCRUD(true, 'delete', "Berhasil menghapus stock adjustment"));
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route("admin.stock-adjustment.index")->with($this->ResponseMessageCRUD(false, 'delete', "Gagal menghapus stock adjustment"));
        }
    }

    public function priceSelect(Request $request)
    {
        $branch_id = Auth::user()->branch_id ?? Auth::user()->temp_branch_id;
        $item = Item::find($request->item_id);
        $item_value = $item->getCurrentValue() ?? 0;

        return response()->json(
            [
                'item_value' => $item_value,
                'stock' => $item->mainStock($request->ware_house_id),
            ]
        );
    }

    public function priceDetail(Request $request)
    {
        $item = Price::find($request->price_id);
        $item->main_stock = $item->mainStock($request->ware_house_id);

        return response()->json($item);
    }

    public function createStockMutation($id)
    {
        $stock_opname = StockOpname::find($id);
        $details = StockOpnameDetail::where('stock_opname_id', $id)->get();
        if (count($details) > 0) {
            $total = 0;
            foreach ($details as $key => $detail) {
                $stock_before = StockMutation::where('item_id', $detail->item_id)
                    ->orderBy('ordering', 'desc')
                    ->whereNull('is_return')
                    // ->whereValue('>', 0)
                    ->first();

                $stocks_before = StockMutation::where('item_id', $detail->item_id)
                    ->orderBy('ordering', 'desc')
                    ->whereNull('is_return')
                    ->get();

                $in = $stocks_before->sum('in');
                $out = $stocks_before->sum('out');
                $total_stock = $in + $out;

                $price_unit = $detail->price_unit ?? $stock_before->value ?? 0;

                $mutation = new StockMutation();
                $mutation->ware_house_id = $stock_opname->ware_house_id;
                $mutation->branch_id = $stock_opname->branch_id;
                $mutation->item_id = $detail->item_id;
                $mutation->price_id = $detail->price_id;
                $mutation->document_id = $id;
                $mutation->document_model = \App\Models\StockOpname::class;
                $mutation->document_code = $stock_opname->code;
                $mutation->date = $stock_opname->date;
                $mutation->type = 'stock opname';
                $mutation->price_unit = $price_unit ?? 0;
                $mutation->subtotal = ($price_unit ?? 0) * abs($detail->difference);

                if ($detail->difference > 0) {
                    $mutation->in = $detail->difference;
                    $total_stock += $detail->difference;
                    $total = ($stock_before->total ?? 0) + $mutation->subtotal;
                } else {
                    $mutation->out = abs($detail->difference);
                    $total_stock -= abs($detail->difference);
                    $total = ($stock_before->total ?? 0) - $mutation->subtotal;
                }
                $mutation->total = $total;
                $mutation->value = $total != 0 ? $total / $total_stock : 0;
                $mutation->note = "Stock Opname" . " (" . $detail->note . ")";
                $mutation->created_at = $detail->created_at;
                $mutation->save();

                $total += $detail->value;
            }
        }

        return $total;
    }
}
