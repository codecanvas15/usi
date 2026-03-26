<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\NotificationHelper;
use App\Models\Asset as model;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetDocument;
use App\Models\Branch;
use App\Models\Fleet;
use App\Models\Item;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'asset';
    protected string $permission_name = 'master-asset|asset-document';

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
        return view('admin.' . $this->view_folder . '.index');
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data = model::leftJoin('item_categories', 'item_categories.id', 'assets.item_category_id')
                ->select('assets.*', 'item_categories.nama as item_category_name');

            if (!get_current_branch()->is_primary) {
                $data->where('assets.branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $data->where('assets.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('asset_name', function ($row) {
                    $str = $row->asset_name;
                    if (!$row->is_complete) {
                        $str .= '<br><span class="text-capitalize badge bg-' . complete_status()[$row->is_complete ?? 0]['color'] . '">' . complete_status()[$row->is_complete ?? 0]['text'] . '</span>';
                    }

                    return $str;
                })
                ->editColumn('value', function ($row) {
                    return floatDotFormat($row->value);
                })
                ->editColumn('purchase_date', fn($row) => localDate($row->purchase_date))
                ->addColumn('outstanding_value', function ($row) {
                    return floatDotFormat($row->outstanding_value);
                })
                ->editColumn('status', function ($row) {
                    $status = asset_status()[$row->status];
                    $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                                    ' . $status['text'] . '
                                </div>';

                    return $badge;
                })
                ->editColumn('is_fleet', function ($row) {
                    return $row->is_fleet ? '<div class="text-center"><i class="fa fa-check-square text-primary" aria-hidden="true" style="font-size: 24px;"></i></div>' : '';
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'permission_name' => $this->permission_name,
                        'is_multiple_permission' => true,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
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
                ->rawColumns(['action', 'status', 'is_fleet', 'asset_name'])
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
        $branch = Auth::user()->branch;
        $code = generate_code(Asset::class, 'code', 'created_at', 'AST', branch_sort: $branch->sort);
        return view('admin.' . $this->view_folder . '.create', compact('code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new model();
        $branch = Branch::find($request->branch_id);

        DB::beginTransaction();
        // * validate
        $request['depreciation_percentage'] = $request->depreciation_percentage;
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        $branch = Branch::find($request->branch_id ?? Auth::user()->branch_id);
        $model->code = generate_code(Asset::class, 'code', 'created_at', 'AKT', date: $request->purchase_date, branch_sort: $branch->sort);

        $data_request = $request->all();
        $data_request['status'] = 'active';
        $data_request['value'] = thousand_to_float($request->value);
        $data_request['residual_value'] = thousand_to_float($request->residual_value);
        $data_request['depreciated_value'] = thousand_to_float($request->value) - thousand_to_float($request->residual_value);
        $data_request['is_fleet'] = $request->is_fleet ?? 0;
        $data_request['purchase_date'] = Carbon::parse($request->purchase_date);
        $data_request['usage_date'] = Carbon::parse($request->usage_date);

        // * update data
        $model->loadModel($data_request);

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

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        if ($request->is_fleet) {
            try {
                $fleet = Fleet::where('asset_id', $model->id)->first();
                if (!$fleet) {
                    $fleet = new Fleet();
                    $fleet->asset_id = $model->id;
                    $fleet->name = $model->asset_name;
                    $fleet->quantity = 1;
                    $fleet->year = Carbon::parse($model->purchase_date)->format('Y');
                    $fleet->item_id = $model->item_id;
                }
                $fleet->type = $model->vehicle_type;
                $fleet->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }
        }

        // * create asset documents
        $data_documents = [];
        if (is_array($request->document_names)) {
            foreach ($request->document_names as $document_name_key => $document_name_value) {
                $data_documents[] = [
                    'asset_id' => $model->id,
                    'name' => $document_name_value ?? null,
                    'transaction_date' => $request->document_transaction_dates[$document_name_key] ?? null,
                    'effective_date' => $request->document_effective_dates[$document_name_key] ?? null,
                    'end_date' => $request->document_end_dates[$document_name_key] ?? null,
                    'due_date' => $request->document_dates[$document_name_key] ?? null,
                    'audit_result' => $request->document_audit_results[$document_name_key] ?? null,
                    'description' => $request->document_descriptions[$document_name_key] ?? null,
                    'file' => $request->hasFile('document_files.' . $document_name_key) ? $this->upload_file($request->file('document_files.' . $document_name_key), 'asset-documents') : null
                ];
            }

            // * saving asset documents
            try {
                $model->assetDocuments()->createMany($data_documents);
            } catch (\Throwable $th) {
                DB::rollBack();

                foreach ($data_documents as $key => $value) {
                    if (isset($value['file'])) {
                        $this->delete_file($value['file']);
                    }
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
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
        $model = model::with('depreciations')
            ->findOrFail($id);

        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.{$this->view_folder}.show", compact('model'));
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
        $readonly = $model->item_receiving_report_detail_id != null;

        return view("admin.{$this->view_folder}.edit", compact('model', 'readonly'));
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
        DB::beginTransaction();
        // * validate
        if (!$request->division_id) {
            $request['division_id'] = $model->division_id;
        }
        $request['depreciation_percentage'] = $request->depreciation_percentage;
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        $data_request = $request->all();
        if ($model->status == "pending") {
            $data_request['status'] = "active";
        }
        $data_request['asset_coa_id'] = $request->asset_coa_id ?? $model->asset_coa_id;
        $data_request['value'] = thousand_to_float($request->value);
        $data_request['residual_value'] = thousand_to_float($request->residual_value);
        $data_request['depreciated_value'] = thousand_to_float($request->value) - thousand_to_float($request->residual_value);
        $data_request['is_fleet'] = $request->is_fleet ?? 0;
        $data_request['purchase_date'] = Carbon::parse($request->purchase_date);
        $data_request['usage_date'] = Carbon::parse($request->usage_date);

        // * update data
        $model->loadModel($data_request);

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        if ($request->is_fleet) {
            try {
                $fleet = Fleet::where('asset_id', $model->id)->first();
                if (!$fleet) {
                    $fleet = new Fleet();
                    $fleet->asset_id = $model->id;
                    $fleet->name = $model->asset_name;
                    $fleet->quantity = 1;
                    $fleet->year = Carbon::parse($model->purchase_date)->format('Y');
                    $fleet->item_id = $model->item_id;
                }
                $fleet->type = $model->vehicle_type;
                $fleet->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                }
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
            }
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

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
        DB::beginTransaction();
        try {
            $asset = Asset::find($id);

            if ($asset->depreciations()->exists()) {
                DB::rollBack();
                throw new Exception('Tidak dapat mengahapus data karena sudah ada depresiasi');
            }

            if ($asset->fleet()->exists()) {
                DB::rollBack();
                throw new Exception('Tidak dapat mengahapus data karena sudah ditarik di armada');
            }
            $asset->delete();

            DB::commit();

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, $th->getMessage()));
        }
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = model::where(function ($query) use ($request) {
            $query->orWhere('code', 'like', "%$request->search%");
            $query->orWhere('asset_name', 'like', "%$request->search%");
        });

        if ($request->branch_id) {
            $model->where('branch_id', $request->branch_id);
        }

        if ($request->status) {
            $model->where('status', $request->status);
        }

        $model = $model->limit(10)->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * Asset document api for editing assets
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function asset_document(?string $id = null)
    {
        $model = model::findOrFail($id);
        $asset_documents = \App\Models\AssetDocument::where('asset_id', $model->id)->get();

        return $this->ResponseJsonData($asset_documents);
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

        if ($model->depreciations()->count() > 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Sudah ada penyusutan'));
        }

        validate_branch($model->branch_id);
        $this->create_activity_status_log(model::class, $id, $request->message ?? 'message not available', $model->status, $request->status);

        $model->status = $request->status;

        // * saving and make response
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
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
     * Set notification for reminder asset document most end date
     *
     * @return \Illuminate\Http\Response
     */
    public function setNotificationForReminderAssetDocument()
    {
        $asset_documents = AssetDocument::get();

        foreach ($asset_documents as $key => $asset_document) {
            if (Carbon::parse($asset_document->end_date)->subDays($asset_document->due_date)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d')) {
                $notification = new NotificationHelper();
                $notification->send_notification(
                    branch_id: get_current_branch_id(),
                    user_id: auth()->user()->id,
                    roles: [],
                    permissions: [],
                    title: 'Most Expired Aseet Document',
                    body: 'Most expired for assets document name ' . $asset_document->name . ' on Asset ' . $asset_document->asset?->code,
                    reference_model: \App\Models\AssetDocument::class,
                    reference_id: $asset_document->id,
                    link: route('admin.asset.show', $asset_document->asset_id)
                );
            }
        }
    }

    /**
     * Import asset view
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        return view('admin.' . $this->view_folder . '.import.index');
    }

    /**
     * Get the import format
     *
     * @return \Illuminate\Http\Response
     */
    public function importFormat()
    {
        return $this->ResponseDownload(public_path('import/admin/import-format-asset.xlsx'));
    }

    /**
     * Format date time to valid excel data
     *
     * @param string $date_time
     * @param string $format
     * @return null|date|string
     */
    private function formaDateTimeExcelData($date_time, $format = 'Y-m-d')
    {
        $date = null;
        try {
            $date = Date::excelToDateTimeObject($date_time)->format($format);
        } catch (\Exception $exception) {
            $date = null;
        }

        return $date;
    }

    /**
     * Process import asset
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function processImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $the_file = $request->file('file');

        // * Load the file
        $spreadsheet = IOFactory::load($the_file->getRealPath());

        // * Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // * Get the highest row and column
        $row_limit = $sheet->getHighestDataRow();
        $row_range = range(2, $row_limit);

        // * Prepare the data
        $data = array();

        $items = DB::table('items')
            ->whereNull('items.deleted_at')
            ->get();

        $asset_categories = DB::table('asset_categories')
            ->whereNull('asset_categories.deleted_at')
            ->get();

        $branches = DB::table('branches')
            ->whereNull('branches.deleted_at')
            ->get();

        $coas = DB::table('coas')
            ->whereNull('coas.deleted_at')
            ->get();

        $divisions = DB::table('divisions')
            ->whereNull('divisions.deleted_at')
            ->get();

        foreach ($row_range as $row) {
            $data[] = [
                'item_id' => $items->where('nama', $sheet->getCell('A' . $row)->getValue())->first()?->id,
                'item_data' => $items->where('nama', $sheet->getCell('A' . $row)->getValue())->first(),
                'asset_name' => $sheet->getCell('B' . $row)->getValue(),
                'asset_category_id' => $asset_categories->where('name', $sheet->getCell('C' . $row)->getValue())->first()?->id,
                'asset_category_data' => $asset_categories->where('name', $sheet->getCell('C' . $row)->getValue())->first(),
                'asset_category_name' => $sheet->getCell('C' . $row)->getValue(),
                'branch_id' =>  $branches->where('name', $sheet->getCell('D' . $row)->getValue())->first()?->id,
                'branch_data' => $branches->where('name', $sheet->getCell('D' . $row)->getValue())->first(),
                'purchase_date' =>  $this->formaDateTimeExcelData($sheet->getCell('E' . $row)->getValue()),
                'usage_date' =>  $this->formaDateTimeExcelData($sheet->getCell('F' . $row)->getValue()),
                'asset_coa_id' => $coas->where('account_code', $sheet->getCell('G' . $row)->getValue())->first()?->id,
                'asset_coa_data' => $coas->where('account_code', $sheet->getCell('G' . $row)->getValue())->first(),
                'acumulated_depreciation_coa_id' => $coas->where('account_code', $sheet->getCell('H' . $row)->getValue())->first()?->id,
                'acumulated_depreciation_coa_data' => $coas->where('account_code', $sheet->getCell('H' . $row)->getValue())->first(),
                'depreciation_coa_id' => $coas->where('account_code', $sheet->getCell('I' . $row)->getValue())->first()?->id,
                'depreciation_coa_data' => $coas->where('account_code', $sheet->getCell('I' . $row)->getValue())->first(),
                'value' => $sheet->getCell('J' . $row)->getValue(),
                'residual_value' =>  $sheet->getCell('K' . $row)->getValue(),
                'depreciation_percentage' =>  $sheet->getCell('L' . $row)->getValue(),
                'estimated_life' => round($sheet->getCell('M' . $row)->getValue()),
                'depreciation_value' =>  $sheet->getCell('N' . $row)->getValue(),
                'depreciation_end_date' =>  $this->formaDateTimeExcelData($sheet->getCell('O' . $row)->getValue()),
                'division_id' =>  $divisions->where('name', $sheet->getCell('P' . $row)->getValue())->first()?->id,
                'division_data' => $divisions->where('name', $sheet->getCell('P' . $row)->getValue())->first(),
                'initial_location' =>  $sheet->getCell('Q' . $row)->getValue(),
                'note' => "Saldo Awal Asset - " . $sheet->getCell('B' . $row)->getValue(),
                'status' => 'active',
                'acumulated_depreciation' =>  $sheet->getCell('R' . $row)->getValue(),
                'last_depreciation_date' =>  $this->formaDateTimeExcelData($sheet->getCell('S' . $row)->getValue()),

            ];
        }

        return view('admin.' . $this->view_folder . '.import.proccess', compact('data'));
    }

    /**
     * Store import asset
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeImport(Request $request)
    {
        $data = [];

        if (is_array($request->branch_id)) {
            foreach ($request->branch_id as $key => $item) {
                $data[] = [
                    'item_id' => $request->item_id[$key],
                    'asset_name' => $request->asset_name[$key],
                    'asset_category_id' => $request->asset_category_id[$key],
                    'asset_category_name' => $request->asset_category_name[$key],
                    'branch_id' => $request->branch_id[$key],
                    'purchase_date' => $request->purchase_date[$key],
                    'usage_date' => $request->purchase_date[$key],
                    'asset_coa_id' => $request->asset_coa_id[$key],
                    'acumulated_depreciation_coa_id' => $request->acumulated_depreciation_coa_id[$key],
                    'depreciation_coa_id' => $request->depreciation_coa_id[$key],
                    'value' => $request->value[$key],
                    'residual_value' => $request->residual_value[$key],
                    'depreciation_percentage' => $request->depreciation_percentage[$key],
                    'estimated_life' => $request->estimated_life[$key],
                    'depreciation_value' => $request->depreciation_value[$key],
                    'depreciation_end_date' => $request->depreciation_end_date[$key],
                    'division_id' => $request->division_id[$key],
                    'initial_location' => $request->initial_location[$key],
                    'note' => $request->note[$key],
                    'status' => $request->status[$key],
                    'acumulated_depreciation' => $request->acumulated_depreciation[$key],
                    'last_depreciation_date' => $request->last_depreciation_date[$key],
                ];
            }
        }


        DB::beginTransaction();

        try {
            foreach ($data as $key => $value) {
                $item = Item::find($value['item_id']);
                $asset_category = \App\Models\AssetCategory::find($value['asset_category_id']);
                if (!$asset_category) {
                    $asset_category = AssetCategory::updateOrCreate(
                        [
                            'name' => $value['asset_category_name'],
                        ],
                        [
                            'name' => $value['asset_category_name'],
                        ]
                    );
                }
                $value['asset_category_id'] = $asset_category->id;
                $value['depreciated_value'] = $value['value'] - $value['residual_value'];
                $value['item_category_id'] = $item->item_category_id ?? null;
                $model = new model();
                $model->loadModel($value);
                $model->save();

                if ($value['acumulated_depreciation'] > 0) {
                    $gapMonth = Carbon::parse($value['purchase_date'])->startOfMonth()->floatDiffInRealMonths(Carbon::parse($value['last_depreciation_date'])->endOfMonth());
                    $gapMonth = round($gapMonth);

                    if (Carbon::parse($value['purchase_date'])->lt(Carbon::parse($value['last_depreciation_date']))) {
                        $depreciation = new \App\Models\Depreciation();
                        $depreciation->branch_id = get_primary_branch()->id;
                        $depreciation->asset_id = $model->id;
                        $depreciation->date = $value['last_depreciation_date'];
                        $depreciation->from_date = $value['purchase_date'];
                        $depreciation->to_date = $value['last_depreciation_date'];
                        $depreciation->note = "Akumulasi Depresiasi Saldo Awal";
                        $depreciation->amount = $value['acumulated_depreciation'];
                        $depreciation->counter = $gapMonth;
                        $depreciation->save();
                    }
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.asset.import')->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }
}
