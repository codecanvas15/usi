<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Role;
use App\Models\Employee;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ApiAuthorizationController extends Controller
{
    public function index(Request $request)
    {
        $branch_id = Auth::user()->branch_id ?? $request->branch_id;

        try {
            $dataAuth = [];

            $query = Authorization::where('status', $request->status)
                ->where('user_id', Auth::user()->id);

            if ($branch_id) {
                $query->where('branch_id', $branch_id);
            }

            if ($request->limit) {
                $query->limit($request->limit);
            }

            if ($request->start) {
                $query->offset($request->start);
            }
            $query->orderBy('updated_at', 'desc');
            $query = $query->get();

            foreach ($query as $q) {
                $auth['id'] = $q->id;
                $auth['date'] = $q->formatted_date;
                $auth['time'] = $q->time;
                $auth['type'] = $q->type;
                $row = $q->model::find($q->subject_id);
                if ($q->type == 'customer') {
                    $auth['title'] = 'Pengajuan Plafon';
                } elseif ($q->type == 'sales order') {
                    $auth['title'] = $row->salesOrder->code ?? '';
                } else {
                    $auth['title'] = $row->code ?? $q->title;
                }
                $auth['subtitle'] = $q->subtitle;
                switch ($q->type) {
                    case "customer":
                        $auth['subject_name'] = $row->customer->name ?? '';
                        break;
                    case "purchase order":
                        $auth['subject_name'] = $row->supplier->name ?? '';
                        break;
                    case "purchase return":
                        $auth['subject_name'] = $row->supplier->name ?? '';
                        break;
                    case "employee debt":
                        $auth['subject_name'] = $row->user->nickname ?? $row->user->name ?? '';
                        break;
                    case "quotation":
                        $auth['subject_name'] = $row->customer->name ?? '';
                        break;
                    case "sales order":
                        $auth['subject_name'] = $row->item->name ?? '';
                        break;
                    default:
                        $auth['subject_name'] = $row->customer->name ?? '';
                }
                array_push($dataAuth, $auth);
            }



            return response()->json([
                'success' => true,
                'data' => $dataAuth,
            ], 200);
        } catch (\Throwable $th) {
            throw $th;

            return response()->json([
                'success' => false,
                'data' => 'Internal Server Error',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $authorization = Authorization::find($id);
            if ($authorization->type == "leave") {
                $data = $authorization->model::find($authorization->subject_id);

                $response['id'] = $data->id;
                $response['user_name'] = $data->employee?->name;
                $response['title'] = $authorization->title;
                $response['subtitle'] = $authorization->subtitle;
                $response['note'] = $data->note;
                $response['from_date'] = Carbon::parse($data->from_date)->translatedFormat('l, d F Y');
                $response['to_date'] = Carbon::parse($data->to_date)->translatedFormat('l, d F Y');
                $response['rest'] = $data->employee?->leave + $data->day . " Hari";
            }

            $response['date'] = $authorization->formatted_date;
            $response['time'] = $authorization->time;
            $response['type'] = $authorization->type;

            return response()->json([
                'success' => true,
                'data' => $response,
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'success' => false,
                'data' => 'Internal Server Error',
            ], 200);
        }

        return response()->json($data);
    }

    public function counter(Request $request)
    {
        $branch_id = Auth::user()->branch_id ?? $request->branch_id;
        try {
            $query = Authorization::where('user_id', Auth::user()->id);

            if ($branch_id) {
                $query->where('branch_id', $branch_id);
            }

            $pending = clone $query;
            $pending = $pending->where('status', 'pending')->count();
            $approved = clone $query;
            $approved = $approved->where('status', 'approved')->count();
            $rejected = clone $query;
            $rejected = $rejected->where('status', 'rejected')->count();

            $data['pending'] = $pending;
            $data['approved'] = $approved;
            $data['rejected'] = $rejected;

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'success' => false,
                'data' => 'Internal Server Error',
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $authorization = Authorization::find($id);
            if ($authorization->status == "pending") {
                $authorization->note = $request->note;
                $authorization->status = $request->approval;
                $authorization->save();

                if ($authorization->type == "leave") {
                    $leave = $authorization->model::find($authorization->subject_id);
                    $this->authorizeLeave($leave, $request->approval, $request->note);
                }
            }
            $authorization->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $authorization,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json([
                'success' => true,
                'data' => 'Internal Server Error',
            ], 200);
        }
    }

    public function authorizeLeave($data, $status, $note = null, $request = null)
    {
        DB::beginTransaction();
        try {
            if ($status == 'approved') {
                $log = Auth::user()->name . " menyetujui pengajuan cuti " . $data->employee?->name;
            } else {
                $log = Auth::user()->name . " menolak permintaan pengajuan cuti " . $data->employee?->name;
            }

            $title = "Pengajuan Cuti " . $data->employee?->name;
            $link = route('admin.leave.show', ['leave' => $data->id]);

            $leave = Leave::where('id', $data->id)->first();
            if ($status == 'approved') {
                $updateLeave = Employee::where('id', $leave->employee_id)->first();
                $updateLeave->leave = ($updateLeave->leave - $leave->day);
                $updateLeave->save();
            }

            $role = Role::where('id', Auth::user()->role_id)->first();

            if ($status == 'approved') {
                $data->status = 'approved';
            } else {
                $data->status = 'rejected';
            }
            $data->save();

            $this->notifToRole($data->branch_id, 'hrd', $title, $log, 'leave', '', $link, false, Leave::class, $data->id);


            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;

            return response()->json([
                'success' => false,
                'data' => 'Internal Server Error',
            ], 500);
        }
    }
}
