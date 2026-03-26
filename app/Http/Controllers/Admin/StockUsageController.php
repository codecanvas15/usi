<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\StockUsage;
use Illuminate\Http\Request;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\StockUsageDetail;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StockUsageController extends Controller
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
    protected string $view_folder = 'stock-usage';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StockUsage::with(['ware_house'])
                ->when(!get_current_branch()->is_primary, fn($q) => $q->where('branch_id', get_current_branch_id()))
                ->when($request->from_date, fn($q) => $q->whereDate('date', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('date', '<=', Carbon::parse($request->to_date)));

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    $route = route('admin.stock-usage.show', $row->id);
                    return "<a href='{$route}' class='text-primary text-decoration-underline hover_text-dark'>{$row->code}</a>";
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('ware_house', function ($row) {
                    return $row->ware_house->nama;
                })
                ->editColumn('status', function ($row) {
                    $badge = '<div class="badge badge-lg badge-' . stock_usage_status()[$row->status]['color'] . '">
                                ' . stock_usage_status()[$row->status]['label'] . ' - ' . stock_usage_status()[$row->status]['text'] . '
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
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                            'delete' => [
                                'display' => $row->check_available_date ? in_array($row->status, ['pending', 'revert']) : false,
                            ],
                        ],
                    ]);
                })
                ->editColumn('export', function ($row) {
                    $link = route('stock-usage.export', ['id' => encryptId($row->id)]);
                    $export = '<a target="_blank" href="' . $link . '" class="btn btn-sm btn-info-light" onclick="show_print_out_modal(event)"><i class="fa fa-file-pdf"></i></a>';

                    return $export;
                })
                ->rawColumns(['code', 'status', 'action', 'export'])
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
        $this->validate($request, [
            'type' => 'required|in:divisi,pegawai,kendaraan',
            'date' => 'required|date',
            'warehouse_id' => 'nullable|exists:ware_houses,id',
            'branch_id' => 'nullable|exists:branches,id',
            'division_id' => 'nullable|exists:divisions,id',
            'employee_id' => 'nullable|exists:employees,id',
            'fleet_type' => 'nullable|in:darat,laut',
            'fleet_id' => 'nullable|exists:fleets,id',

            'item_id' => 'required|array',
            'item_id.*' => 'required|exists:items,id',
            'quantity' => 'required|array',
            'quantity.*' => 'nullable',
        ]);

        DB::beginTransaction();

        // * create stock usage
        $model = new StockUsage();
        $model->fill([
            'ware_house_id' => $request->ware_house_id,
            'branch_id' => $request->branch_id ?? Auth::user()->branch_id,
            'employee_id' => $request->employee_id ?? null,
            'division_id' => $request->division_id ?? null,
            'fleet_id' => $request->fleet_id ?? null,
            'coa_id' => $request->coa_id ?? null,
            'project_id' => $request->project_id ?? null,
            'fleet_type' => $request->fleet_type ?? null,
            'date' => Carbon::parse($request->date),
            'type' => $request->type,
            'note' => $request->note ?? "",
        ]);


        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->save();

            if ($request->purchase_request_id && is_array($request->purchase_request_id)) {
                foreach ($request->purchase_request_id as $key => $purchase_request_id) {
                    $model->stock_usage_purchase_requests()->create([
                        'purchase_request_id' => $purchase_request_id,
                        'stock_usage_id' => $model->id,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()));
        }

        // ! create stock usage detail

        // * set data
        $data_details = [];
        if (is_array($request->item_id)) {
            foreach ($request->item_id as $key => $item_id) {
                $item = Item::find($item_id);
                $value = $item->getCurrentValue();

                if (thousand_to_float($request->quantity[$key]) > 0) {
                    $data_details[] = [
                        'coa_detail_id' => $request->coa_detail_id[$key] ?? null,
                        'stock_usage_id' => $model->id,
                        'item_id' => $request->item_id[$key] ?? null,
                        'unit_id' => $item->unit_id,
                        'price_id' => $request->price_id[$key] ?? null,
                        'stock' => thousand_to_float($request->stock_left[$key] ?? 0),
                        'quantity' => thousand_to_float($request->quantity[$key] ?? 0),
                        'necessity' => $request->necessity[$key] ?? null,
                        'price_unit' => $value,
                    ];
                }
            }
        }

        // * create data details
        try {
            $model->stock_usage_details()->createMany($data_details);

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: StockUsage::class,
                model_id: $model->id,
                amount: 0,
                title: "Pemakaian Stock",
                subtitle: Auth::user()->name . " mengajukan Pemakaian Stock " . $model->code,
                link: route('admin.stock-usage.show', $model),
                update_status_link: route('admin.stock-usage.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "creating details data", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.stock-usage.index')->with($this->ResponseMessageCRUD(true, "create"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = StockUsage::with([
            'ware_house',
            'branch',
            'employee',
            'division',
            'fleet',
            'stock_usage_details.item.unit',
        ])->findOrFail($id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        $authorization_helper = new \App\Http\Helpers\AuthorizationHelper();
        $authorization_logs = $authorization_helper->get_authorization_logs(
            model: StockUsage::class,
            model_id: $model->id,
            user_id: Auth::user()->id,
        );

        $authorization_log_view = view('components.authorization_log', $authorization_logs)->render();

        $authorization_logs['can_revert_request'] = $authorization_logs['approved_count'] > 0 && $model->check_available_date && $model->status == 'approve';
        $authorization_logs['can_void_request'] = $authorization_logs['approved_count'] > 0 && $model->check_available_date && $model->status == 'approve';
        $auth_revert_void_button = view('components.auth_revert_void_button', $authorization_logs)->render();
        $can_edit_or_delete = $authorization_logs['approved_count'] == 0;

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs', 'authorization_log_view', 'auth_revert_void_button', 'can_edit_or_delete'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = StockUsage::findOrFail($id);
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
            'type' => 'required|in:divisi,pegawai,kendaraan',
            'date' => 'required|date',
            'warehouse_id' => 'nullable|exists:ware_houses,id',
            'branch_id' => 'nullable|exists:branches,id',
            'division_id' => 'nullable|exists:divisions,id',
            'employee_id' => 'nullable|exists:employees,id',
            'fleet_type' => 'nullable|in:darat,laut',
            'fleet_id' => 'nullable|exists:fleets,id',

            'item_id' => 'required|array',
            'item_id.*' => 'required|exists:items,id',
            'quantity' => 'required|array',
            'quantity.*' => 'nullable',
        ]);

        DB::beginTransaction();

        // * create stock usage
        $model = StockUsage::with(['stock_usage_details'])->findOrFail($id);
        $model->fill([
            'ware_house_id' => $request->ware_house_id,
            'branch_id' => $request->branch_id ?? Auth::user()->branch_id,
            'employee_id' => $request->employee_id ?? null,
            'division_id' => $request->division_id ?? null,
            'fleet_id' => $request->fleet_id ?? null,
            'coa_id' => $request->coa_id ?? null,
            'project_id' => $request->project_id ?? null,
            'fleet_type' => $request->fleet_type ?? null,
            'date' => Carbon::parse($request->date),
            'type' => $request->type,
            'note' => $request->note ?? "",
        ]);


        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
        }

        try {
            $model->stock_usage_purchase_requests()->delete();
            if ($request->purchase_request_id && is_array($request->purchase_request_id)) {
                foreach ($request->purchase_request_id as $key => $purchase_request_id) {
                    $model->stock_usage_purchase_requests()->create([
                        'purchase_request_id' => $purchase_request_id,
                        'stock_usage_id' => $model->id,
                    ]);
                }
            }

            $model->save();
        } catch (\Throwable $th) {
            DB::rollback();

            throw $th;
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", null, $th->getMessage()));
        }

        // ! create stock usage detail

        // * set data
        $model->stock_usage_details()->delete();
        if (is_array($request->item_id)) {
            foreach ($request->item_id as $key => $item_id) {
                $item = Item::find($item_id);
                $value = $item->getCurrentValue();

                if (thousand_to_float($request->quantity[$key]) > 0) {
                    $stock_usage_details = StockUsageDetail::where('stock_usage_id', $model->id)->where('item_id', $item_id)->first();
                    if (!$stock_usage_details) {
                        $stock_usage_details = new StockUsageDetail();
                    }
                    $stock_usage_details->stock_usage_id = $model->id;
                    $stock_usage_details->item_id = $request->item_id[$key] ?? null;
                    $stock_usage_details->coa_detail_id = $request->coa_detail_id[$key] ?? null;
                    $stock_usage_details->unit_id = $item->unit_id;
                    $stock_usage_details->price_id = $request->price_id[$key] ?? null;
                    $stock_usage_details->stock = thousand_to_float($request->stock_left[$key] ?? 0);
                    $stock_usage_details->quantity = thousand_to_float($request->quantity[$key] ?? 0);
                    $stock_usage_details->necessity = $request->necessity[$key] ?? null;
                    $stock_usage_details->price_unit = $value;
                    $stock_usage_details->save();
                }
            }
        }

        // * create data details
        try {
            Authorization::where('model', StockUsage::class)
                ->where('model_id', $model->id)
                ->delete();

            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization->init(
                branch_id: $model->branch_id,
                user_id: auth()->user()->id,
                model: StockUsage::class,
                model_id: $model->id,
                amount: 0,
                title: "Pemakaian Stock",
                subtitle: Auth::user()->name . " mengajukan Pemakaian Stock " . $model->code,
                link: route('admin.stock-usage.show', $model),
                update_status_link: route('admin.stock-usage.update-status', ['id' => $model->id]),
                division_id: auth()->user()->division_id ?? null
            );
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "create", "creating details data", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route('admin.stock-usage.index')->with($this->ResponseMessageCRUD(true, "create"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = StockUsage::findOrFail($id);
        if (!in_array($model->status, ['pending', 'revert']) || !$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Data sudah closing'));
        }

        DB::beginTransaction();
        try {
            $model->stock_usage_details()->delete();
            $model->stock_usage_purchase_requests()->delete();
            $model->delete();

            Authorization::where('model', StockUsage::class)
                ->where('model_id', $model->id)
                ->delete();

            DB::commit();

            return redirect()->route('admin.stock-usage.index')->with($this->ResponseMessageCRUD(true, 'delete'));
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
    }

    /**
     * Get stock left
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function get_stock_left(Request $request)
    {
        $item_data = Item::with('unit')->findOrFail($request->item_id);

        $data = StockMutation::where('item_id', $request->item_id)
            ->where('ware_house_id', $request->ware_house_id)
            ->get();

        $stock_in = $data->sum('in');
        $stock_out = $data->sum('out');

        $stock_left = $stock_in - $stock_out;

        return $this->ResponseJsonData([
            'stock_left' => $stock_left,
            'item' => $item_data,
            'coa_expense' => $item_data->item_category->item_category_coas->filter(function ($q) {
                return $q->type == 'Expense';
            })->first()->coa ?? null,
        ]);
    }

    /**
     * Update status the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        // * get model
        $model = \App\Models\StockUsage::findOrFail($id);

        // * validate
        validate_branch($model->branch_id);

        // * check available date closing
        if (!$model->check_available_date) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, 'Tanggal sudah closing'));
        }

        // * update status
        DB::beginTransaction();

        try {
            $authorization = new \App\Http\Helpers\AuthorizationHelper();
            $authorization_response = $authorization->info(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );

            if ($authorization_response['is_last_level'] || $request->status != 'approve') {
                $this->create_activity_status_log(StockUsage::class, $id, $request->note ?? $request->message ?? 'message not available', $model->status, $request->status);
                $model->update([
                    'status' => $request->status == 'revert' ? 'pending' : $request->status,
                ]);
            } else {
                $this->create_activity_status_log(StockUsage::class, $id, $request->note ?? $request->message ?? 'message not available', null, $request->status);
            }

            $authorization->authorize(
                authorization_detail_id: $request->authorization_detail_id,
                status: $request->status,
                note: $request->message,
            );
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, "update", null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->back()->with($this->ResponseMessageCRUD(true, "update"));
    }

    /**
     * Get data for creating stock usage for division
     */
    public function get_data_for_division(Request $request)
    {
        // * get purchase request
        $purchase_request = PurchaseRequest::with(['division', 'project'])
            ->when($request->division_id, function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->whereIn('status', ['done', 'partial'])
            ->findOrFail($request->purchase_request_id);


        // * get purchase request details
        $purchase_request_details = $purchase_request
            ->purchase_request_details()
            ->with(['item_data.unit'])
            ->where('jumlah_diapprove', '>', 'quantity_used')
            ->whereIn('status', ['done', 'partial'])
            ->get();


        // * get stock left
        $stock_mutations = StockMutation::when($request->warehouse_id, function ($query) use ($request) {
            return $query->where('ware_house_id', $request->warehouse_id);
        })
            // ->when($request->branch_id, function ($query) use ($request) {
            //     return $query->where('branch_id', $request->branch_id);
            // })
            ->whereIn('item_id', $purchase_request_details->pluck('item_id')->toArray())
            ->get();

        $stock_left = [];
        foreach ($stock_mutations as $stock_mutation) {
            $stock_left[$stock_mutation->item_id] = ($stock_left[$stock_mutation->item_id] ?? 0) + $stock_mutation->in - $stock_mutation->out;
        }

        // * return data
        $data = [
            'purchase_request' => $purchase_request,
            'purchase_request_details' => $purchase_request_details,
            'stock_left' => $stock_left,
        ];

        return $this->ResponseJsonData($data);
    }

    /**
     * Get data for creating stock usage for employee
     */
    public function get_data_for_employee(Request $request)
    {
        // * get purchase request
        $purchase_request = PurchaseRequest::with(['division'])
            // ->when($request->division_id, function ($q) use ($request) {
            //     $q->where('division_id', $request->division_id);
            // })
            ->when($request->branch_id, function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->whereIn('status', ['done', 'partial'])
            ->findOrFail($request->purchase_request_id);

        // * get purchase request details
        $purchase_request_details = $purchase_request
            ->purchase_request_details()
            ->with(['item_data.unit'])
            ->whereIn('status', ['done', 'partial'])
            ->get();

        // * get stock left
        $stock_mutations = StockMutation::when($request->warehouse_id, function ($query) use ($request) {
            return $query->where('ware_house_id', $request->warehouse_id);
        })
            ->when($request->branch_id, function ($query) use ($request) {
                return $query->where('branch_id', $request->branch_id);
            })
            ->whereIn('item_id', $purchase_request_details->pluck('item_id')->toArray())
            ->get();

        $stock_left = [];
        foreach ($stock_mutations as $stock_mutation) {
            $stock_left[$stock_mutation->item_id] = ($stock_left[$stock_mutation->item_id] ?? 0) + $stock_mutation->in - $stock_mutation->out;
        }

        // * return data
        $data = [
            'purchase_request' => $purchase_request,
            'purchase_request_details' => $purchase_request_details,
            'stock_left' => $stock_left,
        ];

        return $this->ResponseJsonData($data);
    }

    /**
     * Export pdf stock usages
     */
    public function export(string $id, Request $request)
    {
        $model = StockUsage::with([
            'branch',
            'division',
            'ware_house',
            'employee',
            'fleet',
            'purchaseRequest',
            'stock_usage_details',
            'stock_usage_details.item.unit',
        ])->findOrFail(decryptId($id));

        $qr_url = route('stock-usage.export', ['id' => $id]);
        $qr = base64_encode(QrCode::size(250)->generate($qr_url));

        $pdf = Pdf::loadView('admin.stock-usage.pdf.export', compact('model', 'qr'))
            ->setPaper($request->paper ?? 'a4', $request->orientation ?? 'potrait');

        return $pdf->stream('stock_usage_' . $model->code . '.pdf');
    }

    public function upload(Request $request, $id)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:5000',
        ]);

        try {
            $model = StockUsage::findOrFail($id);
            if ($request->hasFile('file')) {
                Storage::delete($model->file);
                $model->update([
                    'file' => $this->upload_file($request->file('file'), 'stock-usage'),
                ]);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(true, "edit"));
        } catch (\Throwable $th) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, "edit", null, $th->getMessage()));
        }
    }

    public function get_purchase_request_item(Request $request)
    {
        $purchase_request_detail = PurchaseRequestDetail::with('item_data.item_category.item_category_coas.coa')
            ->whereIn('purchase_request_id', $request->purchase_request_id)
            ->get()
            ->map(function ($q) {
                return $q->item_data;
            })
            ->unique('id');

        return $this->ResponseJsonData($purchase_request_detail);
    }

    public function coa_expense(Request $request)
    {
        $model = StockUsage::findOrFail($request->id);
        $model->coa_id = $request->coa_id;
        $model->save();

        return $this->ResponseJsonData($model);
    }
}
