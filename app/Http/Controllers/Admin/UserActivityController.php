<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ActivityStatusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserActivityController extends Controller
{
    /**
     * initial
     *
     */
    public function __construct()
    {
        $this->middleware('permission:view user-activity', ['only' => ['index', 'activity_logs', 'status_logs']]);
    }

    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.user-activity.index');
    }

    /**
     * activity_logs
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function activity_logs(Request $request)
    {
        if ($request->ajax()) {
            $data = ActivityLog::with('user');

            if ($request->user_id) {
                $data->where('causer_id', $request->user_id);
            }

            if ($request->from_date && $request->to_date) {
                $data->whereBetween('created_at', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)]);
            }

            if ($request->from_date && !$request->to_date) {
                $data->whereDate('created_at', Carbon::parse($request->from_date));
            }

            if (!$request->from_date && $request->to_date) {
                $data->whereDate('created_at', Carbon::parse($request->to_date));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('code', function ($row) {
                    return $row->referece?->code ?? $row->referece?->kode ?? $row->referece?->nomor_so ?? $row->referece?->nomor_po ?? $row->referece?->code;
                })
                ->editColumn('user.email', function ($row) {
                    return $row->user->email ?? 'User tidak ditemukan';
                })
                ->addColumn('action', fn ($row) => view("admin.user-activity.action.activity", compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * status_logs
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function status_logs(Request $request)
    {
        if ($request->ajax()) {
            $data = ActivityStatusLog::with('user');

            if ($request->user_id) {
                $data->where('user_id', $request->user_id);
            }

            if ($request->from_date && $request->to_date) {
                $data->whereBetween('created_at', [Carbon::parse($request->from_date), Carbon::parse($request->to_date)]);
            }

            if ($request->from_date && !$request->to_date) {
                $data->whereDate('created_at', Carbon::parse($request->from_date));
            }

            if (!$request->from_date && $request->to_date) {
                $data->whereDate('created_at', Carbon::parse($request->to_date));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('code', function ($row) {
                    return $row->reference?->code ?? $row->reference?->kode ?? $row->reference?->nomor_so ?? $row->reference?->nomor_po ?? $row->reference?->code;
                })
                ->addColumn('action', fn ($row) => view("admin.user-activity.action.status", compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * show activity
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show_activity(Request $request, $id)
    {
        $activity_log = ActivityLog::with('user')->find($id);

        return view('admin.user-activity.show.activity', compact('activity_log'));
    }

    /**
     * show status
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show_status($id)
    {
        $activity_status_log = ActivityStatusLog::with('user')->find($id);
        return view('admin.user-activity.show.status', compact('activity_status_log'));
    }
}
