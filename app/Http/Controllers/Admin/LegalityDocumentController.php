<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\NotificationHelper;
use App\Models\LeaseDocument;
use App\Models\LegalityDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LegalityDocumentController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder|view asset-document|view lease-document", ['only' => ['index', 'show']]);
        $this->middleware("permission:create $this->view_folder", ['only' => ['create', 'store']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'legality-document';

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
            $data = LegalityDocument::when($request->type, fn ($query, $type) => $query->where('type', $type))
                ->select('legality_documents.*');

            $modifiedData = [];
            // Filter Data most expired
            if ($request->expired) {
                if ($data->count() > 0) {
                    foreach ($data->get() as $key => $value) {
                        if (Carbon::now()->gt(Carbon::parse($value->end_date)->subDays($value->due_date))) {
                            $modifiedData[] = $value;
                        }
                    }
                }
            } else {
                $modifiedData = $data;
            }

            return DataTables::of($modifiedData)
                ->addIndexColumn()
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

                    $delete_route = route('admin.legality-document.destroy', ['legality_document' => $row->id]);
                    $datatable_id = '#company_legality_table';
                    if ($row->type == 'finance') {
                        $datatable_id = '#finance_legality_table';
                    }

                    if (auth()->user()->can("edit $this->view_folder")) {
                        $button .= "<button class='btn btn-sm btn-warning' onclick='show_edit_modal(`$row->id`)'><i class='fas fa-edit'></i></button>";
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
            'name' => 'required|string',
            'type' => 'required|string',
            'transaction_date' => 'required',
            'effective_date' => 'required',
            'end_date' => 'required',
            'due_date' => 'required',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10048',
        ]);

        DB::beginTransaction();
        try {
            $legality_document = new LegalityDocument();
            $legality_document->name = $request->name;
            $legality_document->type = $request->type;
            $legality_document->transaction_date = Carbon::parse($request->transaction_date);
            $legality_document->effective_date = Carbon::parse($request->effective_date);
            $legality_document->end_date = Carbon::parse($request->end_date);
            $legality_document->due_date = thousand_to_float($request->due_date);
            $legality_document->description = $request->description ?? '';
            $legality_document->file = $request->file('file')->store('legality-document', 'public');
            $legality_document->save();

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
        $legality_document = LegalityDocument::findOrFail($id);

        return $this->ResponseJsonMessageCRUD(data: $legality_document);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'type' => 'required|string',
            'transaction_date' => 'required',
            'effective_date' => 'required',
            'end_date' => 'required',
            'due_date' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10048',
        ]);

        DB::beginTransaction();
        try {
            $legality_document = LegalityDocument::findOrFail($id);
            $legality_document->name = $request->name;
            $legality_document->type = $request->type;
            $legality_document->transaction_date = Carbon::parse($request->transaction_date);
            $legality_document->effective_date = Carbon::parse($request->effective_date);
            $legality_document->end_date = Carbon::parse($request->end_date);
            $legality_document->due_date = thousand_to_float($request->due_date);
            $legality_document->description = $request->description ?? '';
            if ($request->hasFile('file')) {
                Storage::delete($legality_document->file);
                $legality_document->file = $request->file('file')->store('legality-document', 'public');
            }
            $legality_document->save();

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
            $legality_document = LegalityDocument::findOrFail($id);
            Storage::delete($legality_document->file);
            $legality_document->delete();

            DB::commit();
            return $this->ResponseJsonMessageCRUD(true, 'delete', 'berhasil menghapus dokumen', null, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->ResponseJsonMessageCRUD(false, 'delete',  $th->getMessage(), null, 500);
            //throw $th;
        }
    }

    /**
     * Notification push if most due date
     */
    public function pushNotificationForMostExpiredDate()
    {
        $data = LegalityDocument::get();

        $arr = [];
        if ($data->count() > 0) {
            foreach ($data as $key => $legal) {
                if (Carbon::now()->gt(Carbon::parse($legal->end_date)->subDays($legal->due_date))) {
                    if (!Carbon::now()->gt($legal->end_date)) {
                        $notification = new NotificationHelper();
                        $notification->send_notification(
                            branch_id: get_current_branch_id(),
                            user_id: auth()->user()->id,
                            roles: [],
                            permissions: [],
                            title: 'Most Expired Ligality Document',
                            body: 'Most expired for legality document name ' . $legal->name,
                            reference_model: \App\Models\LegalityDocument::class,
                            reference_id: $legal->id,
                        );
                    }
                }
            }
        }
    }
}
