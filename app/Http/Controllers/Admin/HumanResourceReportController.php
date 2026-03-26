<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HumanResourceReport\EmployeePermissionReportExport;
use App\Exports\HumanResourceReport\PaidLeaveReportExport;
use App\Exports\HumanResourceReport\PeriodOfEmploymentReport;
use App\Http\Controllers\Controller;
use App\Models\ResetLeave;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class HumanResourceReportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'human-resource-report';

    /**
     * where the route will be defined
     *
     * @var string
     */
    protected string $route = 'human-resource-report';

    public function index()
    {
        return view('admin.human-resource-report.index');
    }

    public function show(Request $request)
    {
        $data = [];

        switch ($request->type) {
            case "paid-leaves":
                $data = $this->paidLeaveReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PaidLeaveReportExport::class;
                break;
            case "employee-permission":
                $data = $this->employeePermissionReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = EmployeePermissionReportExport::class;
                break;
            case "period-of-employment":
                $data = $this->periodOfEmploymentReport($request);
                $orientation = 'landscape';
                $paper_size = 'a3';
                $excel_export = PeriodOfEmploymentReport::class;
                break;
            default:
                return redirect()->route("admin.$this->route.index")->with($this->ResponseMessageCRUD(false, "report", "selected report type was not found"));
        }

        $file_path = "admin.$this->view_folder.$request->type.$request->format";

        if ($request->format == 'preview') {
            return view($file_path, $data);
        } elseif ($request->format == 'pdf') {
            $pdf = Pdf::loadView($file_path, $data)
                ->setPaper($paper_size ?? 'a4', $orientation ?? 'potrait');

            return $pdf->stream($request->type . '.pdf');
        } elseif ($request->format == 'excel') {
            return Excel::download(new $excel_export($file_path, $data), $request->type . '.xlsx');
        } else {
            return redirect()->route("admin.$this->route.index")->with($this->ResponseMessageCRUD(false, "report", "selected export format was not found"));
        }
    }

    public function paidLeaveReport(Request $request)
    {
        $reset_leave = ResetLeave::find($request->reset_leave_id);

        $results = DB::table('leaves')
            ->where('leaves.type', 'cuti')
            ->leftJoin('employees', 'leaves.employee_id', '=', 'employees.id')
            ->leftJoin('divisions', 'employees.division_id', '=', 'divisions.id')
            ->leftJoin('branches', 'leaves.branch_id', '=', 'branches.id')
            ->select(
                'leaves.id',
                'leaves.code',
                'leaves.from_date',
                'leaves.to_date',
                'leaves.day',
                'leaves.leave_remaining',
                'leaves.note',
                'leaves.status',
                'employees.id as employee_id',
                'employees.name as employee_name',
                'employees.NIK as employee_nik',
                'employees.leave as employee_leave',
                'divisions.name as employee_division',
                'branches.name as branch_name'
            )
            ->when($reset_leave, function ($query) use ($reset_leave) {
                return $query->where('leaves.from_date', '>=', Carbon::parse($reset_leave->from_date))
                    ->where('leaves.to_date', '<=', Carbon::parse($reset_leave->to_date));
            })
            ->where('status', 'approve')
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query, $branch_id) {
                return $query->where('leaves.branch_id', $branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('leaves.branch_id', get_current_branch()->id);
            })
            ->orderBy('leaves.from_date', 'asc')
            ->get();

        $employees = DB::table('employees')
            ->whereIn('employees.id', $results->pluck('employee_id'))
            ->select('id', 'leave')
            ->get();

        $results = $results->map(function ($result) use (&$employees) {
            $leave_allowance = $employees->where('id', $result->employee_id)->first();
            $result->leave_remaining = $leave_allowance->leave - $result->day;

            $leave_allowance->leave -= $result->day;
            return $result;
        });

        return [
            'type' => $request->type,
            'data' => $results,
            'from_date' => Carbon::parse($reset_leave->from_date),
            'to_date' => Carbon::parse($reset_leave->to_date),
            'title' => 'Cuti Karyawan',
        ];
    }

    public function employeePermissionReport(Request $request)
    {
        $results = DB::table('permission_letter_employees')
            ->leftJoin('employees', 'permission_letter_employees.employee_id', '=', 'employees.id')
            ->leftJoin('divisions', 'employees.division_id', '=', 'divisions.id')
            ->leftJoin('branches', 'permission_letter_employees.branch_id', '=', 'branches.id')
            ->when($request->from_date, function ($query, $from_date) {
                return $query->where('permission_letter_employees.created_at', '>=', Carbon::parse($from_date));
            })
            ->when($request->to_date, function ($query, $to_date) {
                return $query->where('permission_letter_employees.created_at', '<=', Carbon::parse($to_date));
            })
            ->where('permission_letter_employees.letter_status', 'approve')
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query, $branch_id) {
                return $query->where('permission_letter_employees.branch_id', $branch_id);
            })
            ->when(!get_current_branch()->is_primary, function ($query) {
                return $query->where('permission_letter_employees.branch_id', get_current_branch()->id);
            })
            ->select(
                'permission_letter_employees.id',
                'permission_letter_employees.letter_number',
                'permission_letter_employees.letter_type',
                'permission_letter_employees.letter_reason',
                'permission_letter_employees.letter_date_start',
                'permission_letter_employees.letter_date_end',
                'permission_letter_employees.letter_status',
                'permission_letter_employees.letter_note',
                'employees.name as employee_name',
                'employees.NIK as employee_nik',
                'divisions.name as employee_division',
                'branches.name as branch_name'
            )
            ->get();

        $letter_type = [
            'came too late' => 'Datang Terlambat',
            'leave during working hours' => 'Izin Pada Jam Kerja',
            'leave early' => 'Pulang Lebih Awal',
            'not work' => 'Tidak Masuk Kerja'
        ];

        $results = $results->map(function ($result) use ($letter_type) {
            $result->letter_type_alias = $letter_type[$result->letter_type] ?? $result->letter_type;

            return $result;
        });

        return [
            'type' => $request->type,
            'data' => $results,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
            'title' => 'Izin Karyawan',
        ];
    }

    public function periodOfEmploymentReport(Request $request)
    {
        $employee_data = DB::table('employees')
            ->leftJoin('divisions', 'employees.division_id', '=', 'divisions.id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->when($request->division_id, function ($query, $division_id) {
                return $query->where('employees.division_id', $division_id);
            })
            ->when($request->position_id, function ($query, $position_id) {
                return $query->where('employees.position_id', $position_id);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('employees.employee_status', $status);
            })
            ->when(get_current_branch()->is_primary && $request->branch_id, function ($query, $branch_id) {
                return $query->where('employees.branch_id', $branch_id);
            })
            ->selectRaw('
                employees.id,
                employees.NIK,
                employees.name,
                employees.employee_status,
                employees.join_date,
                employees.end_date,
                divisions.name as employee_division,
                positions.nama as employee_position
            ')
            ->groupBy('employees.id')
            ->get();

        $employee_data = $employee_data->map(function ($item) {
            $join_date = Carbon::parse($item->join_date);
            $end_date = Carbon::parse($item->end_date);

            $item->work_period = $end_date ? Carbon::parse($join_date)->diffInMonths($end_date) : Carbon::parse($join_date)->diffInMonths(Carbon::now());

            if (DateTime::createFromFormat('Y-m-d', $item->end_date)) {
                $item->end_date = localDate($item->end_date);
            } else {
                $item->end_date = null;
            }

            if (DateTime::createFromFormat('Y-m-d', $item->join_date)) {
                $item->join_date = localDate($item->join_date);
            } else {
                $item->join_date = null;
                $item->work_period = 0;
            }

            $item->employee_status = $item->employee_status == 'active' ? 'Aktif' : 'Tidak Aktif';
            return $item;
        });

        return [
            'type' => $request->type,
            'data' => $employee_data,
            'from_date' => Carbon::now(),
            'title' => 'Laporan Masa Kerja Karyawan',
        ];
    }
}
