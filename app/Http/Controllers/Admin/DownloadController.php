<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Download as model;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Str;
use Log;
use Carbon\Carbon;

class DownloadController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected string $view_folder = 'download';

    public function index(Request $request)
    {
        $data = model::where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc');


        if ($request->ajax()) {
            $ids = clone $data;
            $selected_ids = explode(',', $request->selected_ids ?? '');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) use ($selected_ids) {
                    $checked = '';
                    if (in_array($row->id, $selected_ids)) {
                        $checked = 'checked';
                    }
                    return "<input type='checkbox' name='download_id[]' style='opacity:1 !important; left:unset; position:static !important' value='$row->id' data-id='$row->id' onclick='downloadCheck($(this))' class='download-check' $checked />";
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'transaksi-jurnal') {
                        return 'Transaksi Jurnal';
                    } else if ($row->type == 'rekap-party-confirmation') {
                        return 'Rekap Konfirmasi Partai';
                    } else {
                        return Str::headline($row->type);
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                })
                ->editColumn('done_at', function ($row) {
                    return Carbon::parse($row->done_at)->format('d-m-Y H:i:s');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == "pending") {
                        return '<span class="badge bg-warning">Pending</span>';
                    } elseif ($row->status == "failed") {
                        return '<span class="badge bg-danger">Gagal</span>';
                    } elseif ($row->status == "expired") {
                        return '<span class="badge bg-dark">Expired</span>';
                    } elseif ($row->status == "done") {
                        $file = route('admin.download-report.get-file', ['id' => $row->id]);
                        return "<a href='$file' class='btn btn-success btn-sm' target='_blank'><i class='ni ni-download'></i> Unduh</a>";
                    }
                })
                ->rawColumns(['status', 'checkbox'])
                ->with([
                    'ids' => $ids->pluck('id')->toArray()
                ])
                ->make(true);
        }

        return view('admin.download.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //asd
    }

    public function getFile($id)
    {
        $download = model::findOrFail($id);
        $download->downloaded_at = Carbon::now();
        $download->save();

        return redirect()->away(asset('storage/' . $download->path));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        try {
            $downloads = model::whereIn('id', $ids);

            $downloads->each(function ($download) {
                Storage::disk('public')->delete($download->path);
            });
            model::whereIn('id', $ids)->delete();
        } catch (\Throwable $th) {
            return $this->AjaxResponse(false, 'delete', null, $th->getMessage(), 500);
        }

        return $this->AjaxResponse(true, 'delete');
    }
}
