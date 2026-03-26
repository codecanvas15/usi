<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\LeaseDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LeaseDocumentController extends Controller
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
    protected string $view_folder = 'lease-document';

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
            $data = LeaseDocument::join('leases', 'leases.id', 'lease_documents.lease_id')
                ->select('lease_documents.*', 'leases.lease_name', 'leases.code as lease_code');

            // If Request Expired data
            $expiredData = [];
            if ($request->expired) {
                if ($data->count() > 0) {
                    foreach ($data->get() as $key => $value) {
                        if (Carbon::now()->gt(Carbon::parse($value->end_date)->subDays($value->due_date))) {
                            array_push($expiredData, $value);
                        }
                    }
                }
            } else {
                $expiredData = $data;
            }

            return DataTables::of($expiredData)
                ->addIndexColumn()
                ->editColumn('lease_name', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->lease_name,
                    'row' => $row,
                    'main' => 'lease',
                ]))
                ->editColumn('effective_date', fn ($row) => localDate($row->effective_date))
                ->editColumn('end_date', fn ($row) => localDate($row->end_date))
                ->addColumn('status', function ($row) {
                    $badge = '';
                    $expired_text = '';
                    $expired_in = Carbon::now()->diffForHumans($row->end_date, true);
                    if (Carbon::now()->gt($row->end_date)) {
                        $expired_text = "berakhir $expired_in yang lalu";
                        $badge .= '<span class="badge bg-danger">Expired</span>';
                    } else {
                        if (Carbon::now()->gt(Carbon::parse($row->end_date)->subDays($row->due_date))) {
                            $expired_text = "berakhir $expired_in lagi";
                        }
                    }

                    $badge .= "<span class='text-capitalize badge bg-dark'>$expired_text</span>";

                    return $badge;
                })
                ->editColumn('export', function ($row) {
                    $button = '';
                    $file = asset('storage/' . $row->file);
                    $button .= "<a href='$file' class='btn btn-sm btn-primary' target='_blank'><i class='fas fa-download'></i></a>";

                    $delete_route = route('admin.lease-document.destroy', ['lease_document' => $row->id]);
                    $datatable_id = '#lease_legality_table';

                    if (auth()->user()->can("edit $this->view_folder")) {
                        $button .= "<button class='btn btn-sm btn-warning' onclick='show_lease_edit_modal(`$row->id`)'><i class='fas fa-edit'></i></button>";
                    }

                    if (auth()->user()->can("delete $this->view_folder")) {
                        $button .= "<button class='btn btn-sm btn-danger' onclick='show_delete_confirmation(`$delete_route`, `$datatable_id`)'><i class='fas fa-trash'></i></button>";
                    }

                    return $button;
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'lease_id' => 'required|exists:leases,id',
            'name' => 'required|string',
            'transaction_date' => 'required',
            'effective_date' => 'required',
            'end_date' => 'required',
            'due_date' => 'required',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10048',
        ]);

        DB::beginTransaction();
        try {
            $lease_document = new LeaseDocument();
            $lease_document->lease_id = $request->lease_id;
            $lease_document->name = $request->name;
            $lease_document->transaction_date = Carbon::parse($request->transaction_date);
            $lease_document->effective_date = Carbon::parse($request->effective_date);
            $lease_document->end_date = Carbon::parse($request->end_date);
            $lease_document->due_date = thousand_to_float($request->due_date);
            $lease_document->description = $request->description ?? '';
            $lease_document->file = $request->file('file')->store('lease-document', 'public');
            $lease_document->save();

            DB::commit();
            return $this->ResponseJsonMessageCRUD(true, 'create', 'berhasil menyimpan dokumen', null, 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $this->ResponseJsonMessageCRUD(false, 'create', $th->getMessage(), null, 500);
        }
    }

    public function edit($id)
    {
        $lease_document = LeaseDocument::findOrFail($id);

        return $this->ResponseJsonMessageCRUD(data: $lease_document);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lease_id' => 'required|exists:leases,id',
            'name' => 'required|string',
            'transaction_date' => 'required',
            'effective_date' => 'required',
            'end_date' => 'required',
            'due_date' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10048',
        ]);

        DB::beginTransaction();
        try {
            $lease_document = LeaseDocument::findOrFail($id);
            $lease_document->name = $request->name;
            $lease_document->lease_id = $request->lease_id;
            $lease_document->transaction_date = Carbon::parse($request->transaction_date);
            $lease_document->effective_date = Carbon::parse($request->effective_date);
            $lease_document->end_date = Carbon::parse($request->end_date);
            $lease_document->due_date = thousand_to_float($request->due_date);;
            $lease_document->description = $request->description ?? '';
            if ($request->hasFile('file')) {
                Storage::delete($lease_document->file);
                $lease_document->file = $request->file('file')->store('lease-document', 'public');
            }
            $lease_document->save();

            DB::commit();
            return $this->ResponseJsonMessageCRUD(true, 'create', 'berhasil menyimpan dokumen', null, 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $this->ResponseJsonMessageCRUD(false, 'create', $th->getMessage(), null, 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $lease_document = LeaseDocument::findOrFail($id);
            Storage::delete($lease_document->file);
            $lease_document->delete();

            DB::commit();
            return $this->ResponseJsonMessageCRUD(true, 'delete', 'berhasil menghapus dokumen', null, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->ResponseJsonMessageCRUD(false, 'delete',  $th->getMessage(), null, 500);
            //throw $th;
        }
    }

    public function lease(Request $request)
    {
        if ($request->ajax()) {
            $data = Lease::select('leases.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('lease_name', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->lease_name,
                    'row' => $row,
                    'main' => 'lease',
                ]))
                ->editColumn('export', function ($row) {
                    $button = '';

                    if (auth()->user()->can("create $this->view_folder")) {
                        $button .= "<button class='btn btn-sm btn-info' onclick='show_create_modal_lease(`$row->id`)'><i class='fas fa-plus'></i></button>";
                    }

                    return $button;
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index');
    }
}
