<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowanceSalary;
use App\Models\DeductionSalary;
use App\Models\Employee;
use App\Models\FeeSalary;
use App\Models\IncomeTax;
use App\Models\Leave;
use App\Models\PayrollPeriod;
use App\Models\PermissionLetterEmployee;
use App\Models\Salary as model;
use App\Models\Salary;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use ZipArchive;

class PayrollController extends Controller
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
    protected string $view_folder = 'payroll';

    /**
     * search  value
     *
     * @var string|null
     */
    protected string|null $search = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $salaries = model::with(['user', 'payrollPeriod'])
                ->when($request->from_date, fn($q) => $q->whereDate('salaries.created_at', '>=', Carbon::parse($request->from_date)))
                ->when($request->to_date, fn($q) => $q->whereDate('salaries.created_at', '>=', Carbon::parse($request->to_date)))
                ->orderByDesc('created_at');

            if ($request->period_id) {
                $salaries->where('payroll_period_id', $request->period_id);
            }

            return DataTables::of($salaries)
                ->addIndexColumn()
                ->addColumn('user', function (model $salary) {
                    return ucwords($salary->user->name);
                })
                ->addColumn('type', function (model $salary) {
                    return ucfirst($salary->payrollPeriod->type);
                })
                ->addColumn('date', function (model $salary) {
                    return Carbon::parse($salary->payrollPeriod->date)->format('Y-m-d');
                })
                ->addColumn('base_salary', function (model $salary) {
                    return 'Rp. ' . number_format($salary->base_salary, 0, ',', '.');
                })
                ->addColumn('brutto_salary', function (model $salary) {
                    return 'Rp. ' . number_format($salary->brutto_salary, 0, ',', '.');
                })
                ->addColumn('netto_salary', function (model $salary) {
                    return 'Rp. ' . number_format($salary->netto_salary, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => 'payroll',
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
                ->rawColumns(['action'])
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
        $users = \App\Models\Employee::all();
        $periods = PayrollPeriod::all();

        return view("admin.$this->view_folder.create", compact('users', 'periods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fee_total = 0;
        $allowance_total = 0;
        $deduction_total = 0;

        $user = Employee::find($request->user);

        DB::beginTransaction();
        try {
            $salary = new model();
            $salary->user_id = $user->id;
            $salary->branch_id = Auth::user()->branch_id;
            $salary->payroll_period_id = $request->period;
            $salary->work_days = $request->work_days;
            $salary->work_days_total = $request->work_days_total;
            $salary->absences_days = $request->absences_days;
            $salary->alpha_days = $request->alpha_days;
            $salary->leave_days = $request->leave_days;
            $salary->base_salary = thousand_to_float($request->base_salary);
            $salary->brutto_salary = thousand_to_float($request->brutto_salary);
            $salary->netto_salary = thousand_to_float($request->netto_salary);
            $salary->is_absence_calculated = $request->is_absence_calculated ?? 0;
            $salary->status = "approved";

            if (!$salary->check_available_date) {
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, 'Tanggal sudah closing'));
            }

            $salary->save();

            if ($request->fee_detail_name !== null) {
                for ($i = 0; $i < count($request->fee_detail_name); $i++) {
                    $fee_total += thousand_to_float($request->fee_detail_total[$i]);

                    $allowance = new FeeSalary();
                    $allowance->user_id = $user->id;
                    $allowance->salary_id = $salary->id;
                    $allowance->name =  $request->fee_detail_name[$i];
                    $allowance->type =  $request->fee_detail_type[$i];
                    $allowance->amount = thousand_to_float($request->fee_detail_amount[$i]);
                    $allowance->qty = thousand_to_float($request->fee_detail_qty[$i]);
                    $allowance->percentage = thousand_to_float($request->fee_detail_percentage[$i]);
                    $allowance->total = thousand_to_float($request->fee_detail_total[$i]);
                    $allowance->save();
                }
            }

            if ($request->allowance_detail_name !== null) {
                for ($i = 0; $i < count($request->allowance_detail_name); $i++) {
                    $allowance_total += thousand_to_float($request->allowance_detail_total[$i]);

                    $allowance = new AllowanceSalary();
                    $allowance->user_id = $user->id;
                    $allowance->salary_id = $salary->id;
                    $allowance->name =  $request->allowance_detail_name[$i];
                    $allowance->type =  $request->allowance_detail_type[$i];
                    $allowance->amount = thousand_to_float($request->allowance_detail_amount[$i]);
                    $allowance->qty = thousand_to_float($request->allowance_detail_qty[$i]);
                    $allowance->percentage = thousand_to_float($request->allowance_detail_percentage[$i]);
                    $allowance->total = thousand_to_float($request->allowance_detail_total[$i]);
                    $allowance->save();
                }
            }

            if ($request->deduction_detail_name !== null) {
                for ($i = 0; $i < count($request->deduction_detail_name); $i++) {
                    $deduction_total += thousand_to_float($request->deduction_detail_total[$i]);

                    $deduction = new DeductionSalary();
                    $deduction->user_id = $user->id;
                    $deduction->salary_id = $salary->id;
                    $deduction->name = $request->deduction_detail_name[$i];
                    $deduction->type = $request->deduction_detail_type[$i];
                    $deduction->amount = thousand_to_float($request->deduction_detail_amount[$i]);
                    $deduction->qty = thousand_to_float($request->deduction_detail_qty[$i]);
                    $deduction->percentage = thousand_to_float($request->deduction_detail_percentage[$i]);
                    $deduction->total = thousand_to_float($request->deduction_detail_total[$i]);
                    $deduction->save();
                }
            }

            $salary = model::find($salary->id);
            $salary->allowance_total = $fee_total + $allowance_total;
            $salary->deduction_total = $deduction_total;
            $salary->save();

            DB::commit();

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
        } catch (\Throwable $th) {
            DB::rollback();

            throw $th;
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }
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
        $periods = PayrollPeriod::all();
        $users = Employee::all();
        $salary = Salary::findOrFail($id);
        $auth = Employee::find($salary->user_id);

        return view("admin.$this->view_folder.edit", compact('periods', 'users', 'salary', 'auth'));
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
        $user = Employee::find($request->user);

        $fee_total = 0;
        $allowance_total = 0;
        $deduction_total = 0;

        DB::beginTransaction();
        try {
            $salary = model::find($id);
            $salary->user_id = $user->id;
            $salary->payroll_period_id = $request->period;
            $salary->work_days = $request->work_days;
            $salary->work_days_total = $request->work_days_total;
            $salary->absences_days = $request->absences_days;
            $salary->alpha_days = $request->alpha_days;
            $salary->leave_days = $request->leave_days;
            $salary->base_salary = thousand_to_float($request->base_salary);
            $salary->brutto_salary = thousand_to_float($request->brutto_salary);
            $salary->netto_salary = thousand_to_float($request->netto_salary);
            $salary->status = "approved";
            $salary->save();

            FeeSalary::where('salary_id', $salary->id)->forceDelete();

            if ($request->fee_detail_name !== null) {
                for ($i = 0; $i < count($request->fee_detail_name); $i++) {
                    $fee_total += thousand_to_float($request->fee_detail_total[$i]);

                    $fee = new FeeSalary();
                    $fee->user_id = $user->id;
                    $fee->salary_id = $salary->id;
                    $fee->name =  $request->fee_detail_name[$i];
                    $fee->type =  $request->fee_detail_type[$i];
                    $fee->amount = thousand_to_float($request->fee_detail_amount[$i]);
                    $fee->qty = thousand_to_float($request->fee_detail_qty[$i]);
                    $fee->percentage = thousand_to_float($request->fee_detail_percentage[$i]);
                    $fee->total = thousand_to_float($request->fee_detail_total[$i]);
                    $fee->save();
                }
            }

            AllowanceSalary::where('salary_id', $salary->id)->forceDelete();

            if ($request->allowance_detail_name !== null) {
                for ($i = 0; $i < count($request->allowance_detail_name); $i++) {
                    $allowance_total += thousand_to_float($request->allowance_detail_total[$i]);

                    $allowance = new AllowanceSalary();
                    $allowance->user_id = $user->id;
                    $allowance->salary_id = $salary->id;
                    $allowance->name =  $request->allowance_detail_name[$i];
                    $allowance->type =  $request->allowance_detail_type[$i];
                    $allowance->amount = thousand_to_float($request->allowance_detail_amount[$i]);
                    $allowance->qty = thousand_to_float($request->allowance_detail_qty[$i]);
                    $allowance->percentage = thousand_to_float($request->allowance_detail_percentage[$i]);
                    $allowance->total = thousand_to_float($request->allowance_detail_total[$i]);
                    $allowance->save();
                }
            }

            DeductionSalary::where('salary_id', $salary->id)->forceDelete();

            if ($request->deduction_detail_name !== null) {
                for ($i = 0; $i < count($request->deduction_detail_name); $i++) {
                    $deduction_total += thousand_to_float($request->deduction_detail_total[$i]);

                    $deduction = new DeductionSalary();
                    $deduction->user_id = $user->id;
                    $deduction->salary_id = $salary->id;
                    $deduction->name = $request->deduction_detail_name[$i];
                    $deduction->type = $request->deduction_detail_type[$i];
                    $deduction->amount = thousand_to_float($request->deduction_detail_amount[$i]);
                    $deduction->qty = thousand_to_float($request->deduction_detail_qty[$i]);
                    $deduction->percentage = thousand_to_float($request->deduction_detail_percentage[$i]);
                    $deduction->total = thousand_to_float($request->deduction_detail_total[$i]);
                    $deduction->save();
                }
            }

            $salary = model::find($salary->id);
            $salary->allowance_total = $fee_total + $allowance_total;
            $salary->deduction_total = $deduction_total;
            $salary->save();

            DB::commit();
            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit'));
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            FeeSalary::where('salary_id', $model->id)->forceDelete();
            AllowanceSalary::where('salary_id', $model->id)->forceDelete();
            DeductionSalary::where('salary_id', $model->id)->forceDelete();

            $model->forceDelete();
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

    public function getFee(Request $request)
    {
        $fees = FeeSalary::where('salary_id', $request->id)->get();
        return response()->json($fees);
    }

    public function getAllowance(Request $request)
    {
        $allowances = AllowanceSalary::where('salary_id', $request->id)->get();
        return response()->json($allowances);
    }

    public function getDeduction(Request $request)
    {
        $deductions = DeductionSalary::where('salary_id', $request->id)->get();
        return response()->json($deductions);
    }

    public function checkPayroll(Request $request)
    {
        $query = PayrollPeriod::find($request->payroll_period_id);
        return response()->json($query);
    }
    public function exportPdf(Request $request)
    {
        $salaries = model::where('payroll_period_id', $request->payroll_period_id)->get();
        $file = new Filesystem();

        $file->cleanDirectory('storage/public/payroll');

        Storage::delete('public/salary.zip');
        $zip = new ZipArchive();
        $fileName = 'salary.zip';
        if ($zip->open(storage_path('app/public/' . $fileName), ZipArchive::CREATE) === true) {
            foreach ($salaries as $key => $salary) {
                $salary_data['period'] = PayrollPeriod::find($request->payroll_period_id);
                $salary_data['salary'] = $salary;
                $qr_url = url('payroll/' . $salary->id);
                $salary_data['qr'] = base64_encode(QrCode::size(250)->generate($qr_url));
                $salary_data['hrga'] = null;

                $salary_data['mf'] =  null;
                $salary_data['ceo'] =  null;

                $pdf = PDF::loadview("admin.$this->view_folder.slip_gaji", $salary_data);
                $pdf->setPaper('a4', 'potrait');
                $content = $pdf->download()->getOriginalContent();
                $file =  Storage::put('public/payroll/' . $salary->user->name . '.pdf', $content);
                $path = storage_path('app/public/payroll/' . $salary->user->name . '.pdf');
                // return $path;
                $zip->addFile($path, $salary->user->name . '.pdf');
            }
            $zip->close();
        }
        return response()->download(storage_path('app/public/' . $fileName));
    }

    public function getCuti(Request $request)
    {
        $employee_id = $request->employee_id;

        $period = PayrollPeriod::find($request->period_id);

        $data = Leave::with(['employee', 'employee.division'])
            ->where('type', 'cuti')
            ->where('employee_id', $employee_id)
            ->where('status', 'approve')
            ->whereMonth('from_date', Carbon::parse($period->end_date))
            ->whereYear('from_date', Carbon::parse($period->end_date))
            // ->whereBetween(DB::raw('DATE(from_date)'), array($period->date, $period->end_date))
            // ->whereBetween(DB::raw('DATE(to_date)'), array($period->date, $period->end_date))
            ->get();

        foreach ($data as $d) {
            $d->from_date = Carbon::parse($d->from_date)->translatedFormat('d F Y');
            $d->to_date = Carbon::parse($d->to_date)->translatedFormat('d F Y');
        }

        return response()->json($data);
    }

    public function getIzin(Request $request)
    {
        $employee_id = $request->employee_id;

        $period = PayrollPeriod::find($request->period_id);

        $data = Leave::with(['employee', 'employee.division'])
            ->where('type', 'izin')
            ->where('employee_id', $employee_id)
            ->where('status', 'approve')
            ->whereMonth('from_date', Carbon::parse($period->end_date))
            ->whereYear('from_date', Carbon::parse($period->end_date))
            // ->whereBetween(DB::raw('DATE(from_date)'), array($period->date, $period->end_date))
            // ->whereBetween(DB::raw('DATE(to_date)'), array($period->date, $period->end_date))
            ->get();

        foreach ($data as $d) {
            $d->days = $d->day;
            $d->letter_date_start = Carbon::parse($d->from_date)->translatedFormat('d F Y');
            $d->letter_date_end = Carbon::parse($d->to_date)->translatedFormat('d F Y');
        }

        return response()->json($data);
    }

    public function calculateIncomeTax(Request $request)
    {
        try {
            $settings = Setting::where('type', 'payroll')
                ->get();

            $biaya_jabatan = $settings->where('name', 'biaya jabatan')->first();
            $max_biaya_jabatan = $settings->where('name', 'max biaya jabatan')->first();
            $non_npwp = $settings->where('name', 'non npwp')->first();

            $employee = Employee::find($request->employee_id);
            $base_salary = thousand_to_float($request->base_salary);

            $yearly_salary = $base_salary * 12;

            $non_taxable_income = $employee->non_taxable_income->amount ?? 0;
            $role_expense = $yearly_salary * preg_replace('/[^0-9]/', '', $biaya_jabatan->value) / 100 ?? 0;
            if ($role_expense > (preg_replace('/[^0-9]/', '', $max_biaya_jabatan->value) / 100 ?? 0)) {
                $role_expense = preg_replace('/[^0-9]/', '', $max_biaya_jabatan->value) / 100 ?? 0;
            }
            $taxable_income = $yearly_salary - $role_expense - $non_taxable_income;
            $income_tax_result = 0;
            $income_tax_percent = 0;
            if ($taxable_income > 0) {
                $income_tax_data = IncomeTax::where(function ($q) use ($taxable_income) {
                    $q->where('min', '<', $taxable_income)
                        ->where('max', '>=', $taxable_income);
                })
                    ->orWhere(function ($q) use ($taxable_income) {
                        $q->where('min', '<=', $taxable_income)
                            ->where('max', '=', 0);
                    })
                    ->first();

                $income_tax_percent = $income_tax_data->percentage;
                $income_taxes = IncomeTax::orderBy('min')
                    ->where(function ($q) use ($taxable_income) {
                        $q->where('min', '<', $taxable_income);
                    })
                    ->get();

                $taxable_amount = $taxable_income;
                foreach ($income_taxes as $income_tax) {
                    if ($income_tax->id != $income_tax_data->id) {
                        if ($income_tax->min < $income_tax->max) {
                            $source = $income_tax->max - $income_tax->min;
                        } else {
                            $source = $income_tax->min;
                        }
                    } else {
                        $source = $taxable_amount;
                    }
                    $result = $source * $income_tax->percentage / 100;
                    $income_tax_result += $result;

                    $taxable_amount -= $source;
                }

                if (!$employee->npwp) {
                    $income_tax_result += $income_tax_result * preg_replace('/[^0-9]/', '', $non_npwp->value) / 100;
                }
            }

            $return_data = [
                'base_salary' => $base_salary,
                'yearly_salary' => $yearly_salary,
                'non_taxable_income' => $non_taxable_income,
                'role_expense' => $role_expense,
                'income_tax_percent' => $income_tax_percent,
                'taxable_income' => $taxable_income,
                'income_tax_result' => $income_tax_result / 12,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json($return_data);
    }
}
