<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Models\Fleet as model;
use App\Models\FleetDocument;
use App\Models\MarineFleet;
use App\Models\Project;
use App\Models\VechicleFleet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class FleetController extends Controller
{
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
    protected string $view_folder = 'fleet';

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
        $projects = Project::all();
        return view('admin.' . $this->view_folder . '.index', compact('projects'));
    }

    /**
     * get_data_by_type
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function get_data_by_type(Request $request, $type = null)
    {
        if ($request->ajax() && in_array($type, ['laut', 'darat'])) {
            if ($type == 'darat') {
                $data = model::where('type', $type)
                    ->orderByDesc('created_at')
                    ->with('vechicle_fleet')
                    ->select('*');

                if (!get_current_branch()->is_primary) {
                    $data->where('branch_id', get_current_branch_id());
                }

                if ($request->branch_id) {
                    $data->where('branch_id', $request->branch_id);
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('name', fn ($row) => view('components.datatable.detail-link', [
                        'row' => $row,
                        'field' => $row->name,
                        'main' => $this->view_folder
                    ]))
                    ->editColumn('status', function ($row) {
                        $status = fleet_status()[$row->status];
                        $badge = '<div class="badge badge-' . $status['color'] . '">
                                        ' . $status['text'] . '
                                    </div>';

                        return $badge;
                    })
                    ->editColumn('vechicle_fleet.type', function ($d) {
                        return $d->vechicle_fleet->type ?? '';
                    })
                    ->editColumn('vechicle_fleet.plat_nomor', function ($d) {
                        return $d->vechicle_fleet->type ?? '';
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
                                    'display' => true,
                                ],
                                'delete' => [
                                    'display' => true,
                                ],
                            ],
                        ]);
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }

            if ($type == 'laut') {
                $data = model::where('type', $type)
                    ->orderByDesc('created_at')
                    ->with('marine_fleet')
                    ->select('*');

                if (!get_current_branch()->is_primary) {
                    $data->where('branch_id', get_current_branch_id());
                }

                if ($request->branch_id) {
                    $data->where('branch_id', $request->branch_id);
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('name', fn ($row) => view('components.datatable.detail-link', [
                        'row' => $row,
                        'field' => $row->name,
                        'main' => $this->view_folder
                    ]))
                    ->editColumn('status', function ($row) {
                        $status = fleet_status()[$row->status];
                        $badge = '<div class="badge badge-' . $status['color'] . '">
                                        ' . $status['text'] . '
                                    </div>';

                        return $badge;
                    })
                    ->editColumn('marine_fleet.nomor_lambung', function ($d) {
                        return $d->marine_fleet->nomor_lambung ?? '';
                    })
                    ->editColumn('marine_fleet.panjang', function ($d) {
                        return $d->marine_fleet->panjang ?? '';
                    })
                    ->editColumn('marine_fleet.lebar', function ($d) {
                        return $d->marine_fleet->lebar ?? '';
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
                                    'display' => true,
                                ],
                                'delete' => [
                                    'display' => true,
                                ],
                            ],
                        ]);
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
        }

        return $this->ResponseJsonNotFound();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = [];
        $projects = Project::all();

        return view("admin.$this->view_folder.create", compact('model', 'projects'));
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
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }
        // * create data
        $model = new model();
        $model->loadModel([
            'name' => $request->name,
            'type' => $request->type,
            'merk' => $request->merk,
            'quantity' => $request->quantity,
            'year' => $request->year,
        ]);

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * create data vehicle or marine
        if ($request->type == 'darat') {
            $this->validate($request, VechicleFleet::rules());
            $vehicle = VechicleFleet::create([
                'fleet_id' => $model->id,
                'nama' => $request->name,
                'type' => $request->vehicle_type
            ]);
        } elseif ($request->type == 'laut') {
            $this->validate($request, MarineFleet::rules());
            $marine = MarineFleet::create([
                'fleet_id' => $model->id,
                "nomor_lambung" => $request->nomor_lambung,
                "panjang" => $request->panjang,
                "lebar" => $request->lebar,
                "gt" => $request->gt,
            ]);
        } else {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'type not found'));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

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
        $model = model::with(['deliveryOrders'])->findOrFail($id);

        $stockUsageDetails = \App\Models\StockUsageDetail::leftJoin('stock_usages', 'stock_usage_details.stock_usage_id', 'stock_usages.id')
            ->leftJoin('items', 'stock_usage_details.item_id', 'items.id')
            ->leftJoin('units', 'items.unit_id', 'units.id')
            ->where('stock_usages.type', 'kendaraan')
            ->where('stock_usages.fleet_id', $id)
            ->where('stock_usages.fleet_type', $model->type)
            ->where('stock_usages.status', 'approve')
            ->selectRaw('
                stock_usages.id as parent_id,
                stock_usages.code as code,
                stock_usages.date as date,
                items.kode as item_code,
                items.nama as item_name,
                units.name as unit_name,
                stock_usage_details.id as detail_id,
                stock_usage_details.quantity as quantity
            ')
            ->get();

        return view("admin.$this->view_folder.show", compact('model', 'stockUsageDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::with('fleetDocuments')->findOrFail($id);
        $projects = Project::all();
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.edit", compact('model', 'projects'));
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
        $model = model::findOrFail($id);
        $projects = Project::all();

        DB::beginTransaction();

        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }
        $model->loadModel([
            'name' => $request->name,
            'project_id' => $request->project_id ?? null,
            'type' => $request->type,
            'merk' => $request->merk,
            'quantity' => $request->quantity,
            'year' => $request->year,
        ]);

        // * saving and make reponse
        try {
            if ($model->status == "incomplete") {
                $model->status = "complete";
            }
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        // * create data vehicle or marine
        if ($request->type == 'darat') {
            $this->validate($request, VechicleFleet::rules('update', $model->vechicle_fleet->id ?? ''));

            VechicleFleet::updateOrCreate(
                [
                    'fleet_id' => $model->id,
                ],
                [
                    'employee_id' => $request->employee_id,
                    'nama' => $request->name,
                    'project_id' => $request->project_id ?? null,
                    'type' => $request->vehicle_type,
                ]
            );
        } elseif ($request->type == 'laut') {
            $this->validate($request, MarineFleet::rules());
            MarineFleet::updateOrCreate(
                [
                    'fleet_id' => $model->id,
                ],
                [
                    'fleet_id' => $model->id,
                    "nomor_lambung" => $request->nomor_lambung,
                    "panjang" => $request->panjang,
                    "lebar" => $request->lebar,
                    "project_id" => $request->project_id ?? null,
                    "gt" => $request->gt,
                ]
            );
        } else {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'type not found'));
        }

        // * updating and create fleet documents
        $ids = [];
        $deleted_files = [];

        if (is_array($request->document_names) && count($request->document_names) > 0) {
            $data_documents = [];
            foreach ($request->document_names as $document_name_key => $document_name_value) {
                // * create new data
                if (is_null($request->document_ids[$document_name_key]) or $request->document_ids[$document_name_key] == 'null') {
                    $data_documents[] = [
                        'fleet_id' => $model->id,
                        'name' => $document_name_value ?? null,
                        'project_id' => $request->project_id ?? null,
                        'transaction_date' => $request->document_transaction_dates[$document_name_key] ?? null,
                        'effective_date' => $request->document_effective_dates[$document_name_key] ?? null,
                        'end_date' => $request->document_end_dates[$document_name_key] ?? null,
                        'due_date' => $request->document_dates[$document_name_key] ?? null,
                        'audit_result' => $request->document_audit_results[$document_name_key] ?? null,
                        'description' => $request->document_descriptions[$document_name_key] ?? null,
                        'file' => $request->hasFile('document_files.' . $document_name_key) ? $this->upload_file($request->file('document_files.' . $document_name_key), 'fleet-documents') : ''
                    ];
                }

                // * update data
                if (!is_null($request->document_ids[$document_name_key]) and $request->document_ids[$document_name_key] != 'null') {
                    $old_file = null;

                    $fleet_document = \App\Models\FleetDocument::find($request->document_ids[$document_name_key]);

                    $ids[] = $fleet_document->id;
                    $old_file = $fleet_document->file;

                    $fleet_document->fill([
                        'name' => $document_name_value ?? null,
                        'project_id' => $request->project_id ?? null,
                        'transaction_date' => $request->document_transaction_dates[$document_name_key] ?? null,
                        'effective_date' => $request->document_effective_dates[$document_name_key] ?? null,
                        'end_date' => $request->document_end_dates[$document_name_key] ?? null,
                        'due_date' => $request->document_dates[$document_name_key] ?? null,
                        'audit_result' => $request->document_audit_results[$document_name_key] ?? null,
                        'description' => $request->document_descriptions[$document_name_key] ?? null,
                        'file' => $request->hasFile('document_files.' . $document_name_key) ? $this->upload_file($request->file('document_files.' . $document_name_key), 'fleet-documents') : $fleet_document->file
                    ]);

                    // * saving fleet document
                    try {
                        $fleet_document->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                    }

                    // * deleting old file
                    if ($request->hasFile('document_files.' . $document_name_key)) {
                        $deleted_files[] = $old_file;
                    }
                }
            }

            // * deleting old data
            if (count($ids) > 0) {
                Storage::disk('public')->delete($model->fleetDocuments()->whereNotIn('id', $ids)->get()->pluck('file')->toArray());
                $model->fleetDocuments()->whereNotIn('id', $ids)->delete();
            }

            // * create new data
            if (count($data_documents) > 0) {
                try {
                    $model->fleetDocuments()->createMany($data_documents);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }
        } else {
            Storage::delete($model->fleetDocuments()->get()->pluck('file')->toArray() ?? []);
            $model->fleetDocuments()->delete();
        }

        // * deleting old file
        if (count($deleted_files) > 0) {
            foreach ($deleted_files as $deleted_file) {
                $this->delete_file($deleted_file);
            }
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
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
    public function select(Request $request)
    {
        if ($request->search) {
            $model = model::where('name', 'like', "%$request->search%")
                ->where('branch_id', get_current_branch_id())
                ->orWhere('quantity', 'like', "%$request->search%")
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } else {
            $model = model::orderByDesc('created_at')
                ->where('branch_id', get_current_branch_id())
                ->limit(10)
                ->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function select_by_type(Request $request, $type = null)
    {
        if ($request->search) {
            $model = model::where('type', $type)
                ->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%$request->search%");
                    $q->orWhere('quantity', 'like', "%$request->search%");
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } else {
            $model = model::where('type', $type)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return $this->ResponseJsonData($model);
    }

    public function detail($id)
    {
        $model = model::findOrFail($id);

        return $this->ResponseJsonData($model);
    }

    /**
     * Set notification for reminder lease document most end date
     * 
     * @return \Illuminate\Http\Response
     */
    public function setNotificationForReminderFleetDocument()
    {
        $fleet_documents = FleetDocument::get();

        foreach ($fleet_documents as $key => $fleet_document)
        {
            $endDate = \Carbon\Carbon::parse($fleet_document->end_date)->subDays($fleet_document->due_date);
            $dateNow = \Carbon\Carbon::now()->format('Y-m-d');
            
            if ($endDate->format('Y-m-d') == $dateNow) {
                $notification = new NotificationHelper();
                $notification->send_notification(
                    branch_id: get_current_branch_id(),
                    user_id: auth()->user()->id,
                    roles: [],
                    permissions: [],
                    title: 'Most Expired Fleet Document',
                    body: 'Most expired for fleet document name ' . $fleet_document->name . ' on Fleet ' . $fleet_document->asset?->code,
                    reference_model: \App\Models\FleetDocument::class,
                    reference_id: $fleet_document->id,
                    link: route('admin.fleet.show', $fleet_document->fleet_id)
                );
            }
        }
    }
}
