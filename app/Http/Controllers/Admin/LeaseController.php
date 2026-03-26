<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityStatusLogHelper;
use App\Http\Helpers\NotificationHelper;
use App\Models\Branch;
use App\Models\Lease as model;
use App\Models\Lease;
use App\Models\LeaseDocument;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class LeaseController extends Controller
{
    use ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'lease';
    protected string $permission_name = 'lease|lease-document';

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
            $data = model::select('leases.*');

            if (!get_current_branch()->is_primary) {
                $data->where('leases.branch_id', get_current_branch_id());
            }

            if ($request->branch_id) {
                $data->where('leases.branch_id', $request->branch_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('date', fn($row) => localDate($row->date))
                ->editColumn('value', function ($row) {
                    return floatDotFormat($row->value);
                })
                ->editColumn('lease_name', function ($row) {
                    $str = $row->lease_name;

                    $required_column = ['branch_id', 'date', 'from_date', 'to_date', 'asset_coa_id', 'acumulated_depreciation_coa_id', 'depreciation_coa_id', 'lease_name', 'value', 'month_duration', 'depreciation_value'];

                    $is_complete = true;
                    foreach ($required_column as $column) {
                        if (blank($row->{$column})) {
                            $is_complete = false;
                            break;
                        }
                    }

                    if (!$is_complete) {
                        $str .= '<br><span class="text-capitalize badge bg-' . complete_status()[$is_complete ?? 0]['color'] . '">' . complete_status()[$is_complete ?? 0]['text'] . '</span>';
                    }

                    return $str;
                })
                ->addColumn('outstanding_value', function ($row) {
                    return floatDotFormat($row->outstanding_value);
                })
                ->editColumn('status', function ($row) {
                    // $status = asset_status()[$row->status];
                    // $badge = '<div class="badge badge-lg badge-' . $status['color'] . '">
                    //                 ' . $status['text'] . '
                    //             </div>';

                    // return $badge;
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
                ->rawColumns(['action', 'status', 'lease_name'])
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
        $branch = get_current_branch();
        $code = generate_code(Lease::class, 'code', 'created_at', 'BDM', branch_sort: $branch->sort);
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
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        $branch = Branch::find($request->branch_id);
        $model->code = generate_code(Lease::class, 'code', 'created_at', 'BDM', branch_sort: $branch->sort ?? null, date: $request->date);

        $data_request = $request->all();
        $data_request['value'] = thousand_to_float($request->value);
        $data_request['date'] = Carbon::parse($request->date)->format('Y-m-d');
        $data_request['from_date'] = Carbon::parse($request->from_date)->format('Y-m-d');
        $data_request['to_date'] = Carbon::parse($request->to_date)->format('Y-m-d');
        $data_request['status'] = 'active';

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

        // * create lease documents
        $data_documents = [];
        if (is_array($request->document_names)) {
            foreach ($request->document_names as $document_name_key => $document_name_value) {
                $data_documents[] = [
                    'lease_id' => $model->id,
                    'name' => $document_name_value ?? null,
                    'transaction_date' => $request->document_transaction_dates[$document_name_key] ?? null,
                    'effective_date' => $request->document_effective_dates[$document_name_key] ?? null,
                    'end_date' => $request->document_end_dates[$document_name_key] ?? null,
                    'due_date' => $request->document_dates[$document_name_key] ?? null,
                    'audit_result' => $request->document_audit_results[$document_name_key] ?? null,
                    'description' => $request->document_descriptions[$document_name_key] ?? null,
                    'file' => $request->hasFile('document_files.' . $document_name_key) ? $this->upload_file($request->file('document_files.' . $document_name_key), 'lease-documents') : null
                ];
            }

            // * saving lease documents
            try {
                $model->leaseDocuments()->createMany($data_documents);
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
        $model = model::with('amortizations')
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
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        $data_request = $request->all();
        if ($model->status == "pending") {
            $data_request['status'] = "active";
        }
        $data_request['value'] = thousand_to_float($request->value);
        $data_request['date'] = Carbon::parse($request->date)->format('Y-m-d');
        $data_request['from_date'] = Carbon::parse($request->from_date)->format('Y-m-d');
        $data_request['to_date'] = Carbon::parse($request->to_date)->format('Y-m-d');

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

        $deleted_files = [];
        $ids = [];

        // * create lease documents
        $data_documents = [];
        if (is_array($request->document_names)) {
            foreach ($request->document_names as $document_name_key => $document_name_value) {
                // * creating new data
                if (is_null($request->document_ids) or $request->document_ids[$document_name_key] == 'null') {
                    $data_documents[] = [
                        'lease_id' => $model->id,
                        'name' => $document_name_value ?? null,
                        'transaction_date' => $request->document_transaction_dates[$document_name_key] ?? null,
                        'effective_date' => $request->document_effective_dates[$document_name_key] ?? null,
                        'end_date' => $request->document_end_dates[$document_name_key] ?? null,
                        'due_date' => $request->document_dates[$document_name_key] ?? null,
                        'audit_result' => $request->document_audit_results[$document_name_key] ?? null,
                        'description' => $request->document_descriptions[$document_name_key] ?? null,
                        'file' => $request->hasFile('document_files.' . $document_name_key) ? $this->upload_file($request->file('document_files.' . $document_name_key), 'lease-documents') : ''
                    ];
                }

                // * updating old data
                if (!is_null($request->document_ids) and $request->document_ids[$document_name_key] != 'null') {
                    $old_file = null;

                    $lease_document = \App\Models\LeaseDocument::findOrFail($request->document_ids[$document_name_key]);
                    $ids[] = $lease_document->id;
                    $old_file = $lease_document->file;

                    $lease_document->fill([
                        'name' => $document_name_value ?? null,
                        'transaction_date' => $request->document_transaction_dates[$document_name_key] ?? null,
                        'effective_date' => $request->document_effective_dates[$document_name_key] ?? null,
                        'end_date' => $request->document_end_dates[$document_name_key] ?? null,
                        'due_date' => $request->document_dates[$document_name_key] ?? null,
                        'audit_result' => $request->document_audit_results[$document_name_key] ?? null,
                        'description' => $request->document_descriptions[$document_name_key] ?? null,
                        'file' => $request->hasFile('document_files.' . $document_name_key) ? $this->upload_file($request->file('document_files.' . $document_name_key), 'lease-documents') : $lease_document->file
                    ]);

                    // * saving lease document
                    try {
                        $lease_document->save();
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

            // * deleting un used lease document data
            try {
                $model->leaseDocuments()->whereNotIn('id', $ids)->delete();
            } catch (\Throwable $th) {
                DB::rollBack();


                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
            }

            // * saving lease documents
            if (count($data_documents) > 0) {
                try {
                    $model->leaseDocuments()->createMany($data_documents);
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
        } else {
            // *  delete all lease document
            $model->leaseDocuments()->delete();
        }


        // * deleting old files
        foreach ($deleted_files as $key => $value) {
            $this->delete_file($value ?? '');
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit'));
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
            $lease = Lease::find($id);

            if ($lease->amortizations()->exists()) {
                DB::rollBack();
                throw new Exception('Tidak dapat mengahapus data karena sudah ada amortisasi');
            }
            $lease->delete();

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
            $query->orWhere('lease_name', 'like', "%$request->search%");
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
     * Lease document api for editing leases
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function lease_document(?string $id = null)
    {
        $model = model::findOrFail($id);
        $lease_documents = \App\Models\LeaseDocument::where('lease_id', $model->id)->get();

        return $this->ResponseJsonData($lease_documents);
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

        if ($model->amortizations()->count() > 0) {
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Sudah ada amortisasi'));
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
     * Set notification for reminder lease document most end date
     *
     * @return \Illuminate\Http\Response
     */
    public function setNotificationForReminderLeaseDocument()
    {
        $lease_documents = LeaseDocument::get();

        foreach ($lease_documents as $key => $lease_document) {
            $endDate = Carbon::parse($lease_document->end_date)->subDays($lease_document->due_date);
            $dateNow = \Carbon\Carbon::now()->format('Y-m-d');

            if ($endDate->format('Y-m-d') == $dateNow) {
                $notification = new NotificationHelper();
                $notification->send_notification(
                    branch_id: get_current_branch_id(),
                    user_id: auth()->user()->id,
                    roles: [],
                    permissions: [],
                    title: 'Most Expired Lease Document',
                    body: 'Most expired for lease document name ' . $lease_document->name . ' on Lease ' . $lease_document->asset?->code,
                    reference_model: \App\Models\LeaseDocument::class,
                    reference_id: $lease_document->id,
                    link: route('admin.lease.show', $lease_document->lease_id)
                );
            }
        }
    }

    /**
     * Import lease view
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
        return $this->ResponseDownload(public_path('import/admin/import-format-bdm.xlsx'));
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
            $date = Date::excelToDateTimeObject(trim($date_time))->format($format);
        } catch (\Exception $exception) {
            $date = null;
        }

        return $date;
    }

    /**
     * Process import lease
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
        $row_range = range(3, $row_limit);

        // * Prepare the data
        $data = array();

        $coas = DB::table('coas')
            ->whereNull('coas.deleted_at')
            ->get();
        $divisions = DB::table('divisions')
            ->whereNull('divisions.deleted_at')
            ->get();

        $items = DB::table('items')
            ->whereNull('items.deleted_at')
            ->get();

        $branches = DB::table('branches')
            ->whereNull('branches.deleted_at')
            ->get();

        foreach ($row_range as $row) {
            $data[] = [
                'branch_id' => $branches->where('name', $sheet->getCell('B' . $row)->getValue())->first()?->id,
                'branch_data' => $branches->where('name', $sheet->getCell('B' . $row)->getValue())->first(),
                'division_id' => $divisions->where('name', $sheet->getCell('J' . $row)->getValue())->first()?->id,
                'division_data' => $divisions->where('name', $sheet->getCell('J' . $row)->getValue())->first(),
                'item_id' => $items->where('nama', $sheet->getCell('A' . $row)->getValue())->first()?->id,
                'item_data' => $items->where('nama', $sheet->getCell('A' . $row)->getValue())->first(),
                'asset_coa_id' => $coas->where('account_code', $sheet->getCell('G' . $row)->getValue())->first()?->id,
                'asset_coa_data' => $coas->where('account_code', $sheet->getCell('G' . $row)->getValue())->first(),
                'acumulated_depreciation_coa_id' => $coas->where('account_code', $sheet->getCell('H' . $row)->getValue())->first()?->id,
                'acumulated_depreciation_coa_data' => $coas->where('account_code', $sheet->getCell('H' . $row)->getValue())->first(),
                'depreciation_coa_id' => $coas->where('account_code', $sheet->getCell('I' . $row)->getValue())->first()?->id,
                'depreciation_coa_data' => $coas->where('account_code', $sheet->getCell('I' . $row)->getValue())->first(),
                'lease_name' => $sheet->getCell('A' . $row)->getValue(),
                'date' => $this->formaDateTimeExcelData($sheet->getCell('C' . $row)->getValue()),
                'from_date' => $this->formaDateTimeExcelData($sheet->getCell('D' . $row)->getValue()),
                'to_date' => $this->formaDateTimeExcelData($sheet->getCell('E' . $row)->getValue()),
                'value' => $sheet->getCell('F' . $row)->getValue(),
                'note' => "Saldo Awal - " . $sheet->getCell('A' . $row)->getValue(),
                'status' => 'active',
                'month_duration' => $sheet->getCell('K' . $row)->getValue(),
                'depreciation_value' => $sheet->getCell('L' . $row)->getValue(),
                'counter' => $sheet->getCell('M' . $row)->getValue(),
                'acumulated_depreciation_value' => $sheet->getCell('N' . $row)->getValue(),
                'last_depreciation_date' => $this->formaDateTimeExcelData($sheet->getCell('O' . $row)->getValue()),
                'lease_data' => Lease::where('lease_name', $sheet->getCell('A' . $row)->getValue())->first(),
            ];
        }

        return view('admin.' . $this->view_folder . '.import.proccess', compact('data'));
    }

    /**
     * Store import lease
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
                    'lease_id' => $request->lease_id[$key],
                    'branch_id' => $request->branch_id[$key],
                    'division_id' => $request->division_id[$key] ?? null,
                    'item_id' => $request->item_id[$key] ?? null,
                    'asset_coa_id' => $request->asset_coa_id[$key],
                    'acumulated_depreciation_coa_id' => $request->acumulated_depreciation_coa_id[$key],
                    'depreciation_coa_id' => $request->depreciation_coa_id[$key],
                    'lease_name' => $request->lease_name[$key],
                    'date' => Carbon::parse($request->date[$key]),
                    'from_date' => Carbon::parse($request->from_date[$key]),
                    'to_date' => Carbon::parse($request->to_date[$key]),
                    'value' => thousand_to_float($request->value[$key]),
                    'note' => $request->note[$key],
                    'status' => $request->status[$key],
                    'month_duration' => $request->month_duration[$key],
                    'depreciation_value' => thousand_to_float($request->depreciation_value[$key]),
                    'counter' => $request->counter[$key],
                    'acumulated_depreciation_value' => thousand_to_float($request->acumulated_depreciation_value[$key]),
                    'last_depreciation_date' => $request->last_depreciation_date[$key],
                ];
            }
        }


        DB::beginTransaction();

        try {
            foreach ($data as $key => $value) {
                if ($value['lease_id']) {
                    $model = model::find($value['lease_id']);
                } else {
                    $model = new model();
                }
                $model->loadModel($value);
                $model->save();

                if ($value['acumulated_depreciation_value'] > 0) {
                    $gapMonth = Carbon::parse($value['from_date'])->startOfMonth()->floatDiffInRealMonths(Carbon::parse($value['last_depreciation_date'])->endOfMonth());
                    $gapMonth = round($gapMonth);

                    if (Carbon::parse($value['from_date'])->lt(Carbon::parse($value['last_depreciation_date']))) {
                        if ($value['lease_id']) {
                            $amortization = \App\Models\Amortization::where('lease_id', $value['lease_id'])->where('note', "Akumulasi Amortisasi Saldo Awal")->first();
                        } else {
                            $amortization = new \App\Models\Amortization();
                        }

                        if ($amortization) {
                            $amortization->branch_id = get_primary_branch()->id;
                            $amortization->lease_id = $model->id;
                            $amortization->date = $value['last_depreciation_date'];
                            $amortization->from_date = $value['from_date'];
                            $amortization->to_date = $value['last_depreciation_date'];
                            $amortization->note = "Akumulasi Amortisasi Saldo Awal";
                            $amortization->amount = $value['acumulated_depreciation_value'];
                            $amortization->counter = $value['counter'];
                            $amortization->save();
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('admin.lease.import')->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'create'));
    }
}
