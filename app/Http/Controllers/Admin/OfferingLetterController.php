<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\OfferingLetter as model;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OfferingLetterController extends Controller
{
    use \App\Http\Helpers\ActivityStatusLogHelper;

    /**
     * where the view will be displayed
     *
     * @var string
     */
    public $view_folder = "offering-letter";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = model::join('labor_applications', 'labor_applications.id', '=', 'offering_letters.labor_application_id')
                ->when(!get_current_branch()->is_primary, fn ($q) => $q->where('offering_letters.branch_id', get_current_branch_id()))
                ->when($request->from_date, fn ($q) => $q->whereDate('offering_letters.created_at', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn ($q) => $q->whereDate('offering_letters.created_at', '<=', Carbon::parse($request->to_date)))
                ->select([
                    'offering_letters.*',
                    'labor_applications.name',
                ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('reference', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->reference,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d F Y');
                })
                ->editColumn('updated_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d F Y');
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
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
                ->addColumn('copy_link', function ($row) {
                    $hash_offering_letter_id = Hashids::encode($row->id);
                    $link = route('guest.offering-letter.show', ['id' => $hash_offering_letter_id]);

                    $color = offering_letter_status()[$row->applicant_status]['color'];
                    $text = offering_letter_status()[$row->applicant_status]['text'];
                    $button = "<span class='badge bg-$color text-capitalize'>$text</span>";
                    if ($row->applicant_status == 'pending') {
                        $button .= "<button class='btn btn-sm btn-primary' onclick='copyToClipBoard(\"$link\")'>Share Link</button>";
                    }

                    return $button;
                })
                ->addColumn('export', function ($row) {
                    return view('components.datatable.export-button', [
                        "route" => route('offering-letter.export', ['id' => encryptId($row->id)]),
                        "onclick" => "show_print_out_modal(event)",
                    ]);
                })
                ->rawColumns(['reference', 'employee_id', 'copy_link'])
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
        return view("admin.$this->view_folder.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $created_at = Carbon::now()->format('Y-m-d');
        $labor_application = \App\Models\LaborApplication::findOrFail($request->labor_application_id);
        $code = generate_code(model::class, 'reference', 'created_at', 'HRDOL', date: $created_at, branch_sort: $labor_application->branch->sort ?? null);

        $branch = Branch::find($labor_application->branch_id);

        $code = generate_code(model::class, 'reference', 'created_at', 'HRDOL', branch_sort: $branch->sort ?? null, date: $created_at);

        // * create data
        $model = new model();
        $model->fill([
            'branch_id' => $labor_application->branch->id ?? null,
            'labor_application_id' => $request->labor_application_id,
            'created_by' => auth()->user()->id,
            'reference' => $code,
            'salary' => thousand_to_float($request->salary),
            'nik' => $request->nik,
            'offering_letter' => $request->offering_letter,
        ]);

        // * saving and make reponse
        DB::beginTransaction();
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = \App\Models\OfferingLetter::with([
            'branch',
            'laborApplication',
            'laborApplication.laborDemandDetail',
            'created_by_data',
        ])->findOrFail($id);

        validate_branch($model->branch_id);

        $status_logs = $model->logs_data['status_logs'] ?? [];
        $activity_logs = $model->logs_data['activity_logs'] ?? [];

        return view("admin.$this->view_folder.show", compact('model', 'status_logs', 'activity_logs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = \App\Models\OfferingLetter::with([
            'branch',
            'laborApplication',
            'created_by_data',
        ])->findOrFail($id);

        validate_branch($model->branch_id);

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
        $labor_application = \App\Models\LaborApplication::findOrFail($request->labor_application_id);
        $branch = Branch::find($labor_application->branch_id);

        $model = \App\Models\OfferingLetter::findOrFail($id);

        $model->fill([
            'branch_id' => $branch->id ?? null,
            'labor_application_id' => $request->labor_application_id,
            'created_by' => auth()->user()->id,
            'reference' => $model->reference,
            'nik' => $request->nik,
            'salary' => thousand_to_float($request->salary),
        ]);

        try {
            $model->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
    }

    public function export($id, Request $request)
    {
        $model = model::findOrFail(decryptId($id));
        $file = public_path('/pdf_reports/Offering-Letter-' . microtime(true) . '.pdf');
        $fileName = 'Offering-Letter-' . microtime(true) . '.pdf';

        $pdf = Pdf::loadview("admin/.$this->view_folder./export", compact('model'))
            ->setPaper($request->paper ?? 'a4', $request->landscape ?? 'portrait');
        $pdf->render();

        return $pdf->stream($fileName);
    }
}
