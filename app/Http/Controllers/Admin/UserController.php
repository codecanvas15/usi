<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\User;
use App\Http\Controllers\Controller;
use App\Imports\Admin\User as AdminUser;
use App\Models\EmployeeBranchHistory;
use App\Models\EmployeeRoleHistory;
use App\Models\NonEmployee;
use App\Models\User as model;
use App\Models\VendorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Datatables;

class UserController extends Controller
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
        $this->middleware("permission:import $this->view_folder", ['only' => ['import']]);
        $this->middleware("permission:export $this->view_folder", ['only' => ['export']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'user';

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
            $data = model::when($request->user_type, fn($query, $user_type) => $query->where('user_type', $user_type))
                ->orderByDesc('created_at')->select('*');

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('username', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->username,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->addColumn('employee', function ($row) {
                    return $row->employee ? '<a href="' . route("admin.employee.show", $row->employee?->id) . '" class="text-primary text-decoration-underline hover_text-dark" target="_blank">' . ucwords($row->employee?->name) . '</a>' : '';
                })
                ->addColumn('roles', function ($row) {
                    return $row->roles->pluck('name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
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
                ->rawColumns(['employee', 'action'])
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
        $model = [];

        if (Auth::user()->hasRole('super_admin')) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'admin')->where('name', '!=', 'super_admin')->get();
        }

        return view("admin.$this->view_folder.create", compact('model', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules());
        } else {
            $this->validate($request, model::rules());
        }
        // * create data
        $model = new model();
        $model->loadModel($request->all());
        $model->password = Hash::make($request->password);

        // * saving and make
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        if ($request->user_type == 'vendor') {
            try {
                $model->assignRole('partner-transport');;
                $vendor_user = $model->vendor()->attach($request->vendor_id);
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create vendor user', $th->getMessage()))->withInput();
            }
        } else {
            try {
                $model->syncRoles($request->role);
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
            }
        }

        if ($request->user_type == 'non-employee') {
            $non_employee = new \App\Models\NonEmployee();
            $non_employee->user_id = $model->id;
            $non_employee->name = $request->name;
            $non_employee->gender = $request->gender;
            $non_employee->phone = $request->phone;
            $non_employee->agency = $request->agency;
            $non_employee->address = $request->address;
            $non_employee->identity_number = $request->identity_number;
            $non_employee->role = $request->role_name;
            try {
                $non_employee->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create non employee', $th->getMessage()))->withInput();
            }
        }


        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD());
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
        $model = model::findOrFail($id);
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        return view("admin.$this->view_folder.show", compact('model'));
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
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        if (Auth::user()->hasRole('super_admin')) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'admin')->where('name', '!=', 'super-admin')->get();
        }

        return view("admin.$this->view_folder.edit", compact('model', 'roles'));
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
        if ($request->ajax()) {
            $this->validate_api($request->all(), model::rules('update', $id));
        } else {
            $this->validate($request, model::rules('update', $id));
        }

        $request_data = $request->all();
        $request_data['project_id'] = $request->project_id;
        // * update data
        $model->loadModel($request_data);

        if ($request->role) {
            if ($model->getRoleNames()[0] !== $request->role) {
                $from = Role::where('name', $model->getRoleNames()[0])->first();
                $to = Role::where('name', $request->role)->first();

                if (auth()->user()->employee) {
                    if ($model->employee) {
                        $erh = new EmployeeRoleHistory();
                        $erh->causer_id = auth()->user()->employee->id;
                        $erh->employee_id = $model->employee->id;
                        $erh->from_role_id = $from->id;
                        $erh->to_role_id = $to->id;
                        $erh->save();
                    }
                }
            }
        }

        if ($request->branch_id) {
            if ($request->branch_id !== $model->branch?->id) {
                if (auth()->user()->employee) {
                    if ($model->employee) {
                        $ebh = new EmployeeBranchHistory();
                        $ebh->causer_id = auth()->user()->employee->id;
                        $ebh->employee_id = $model->employee->id;
                        $ebh->from_branch_id = $model->branch?->id;
                        $ebh->to_branch_id = $request->branch_id;
                        $ebh->save();
                    }
                }
            }
        }

        if (Auth::user()->hasRole('super_admin')) {
            if (!is_null($request->password) && !is_null($request->password_confirmation)) {
                if ($request->password != $request->password_confirmation) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, 'Password and password confirmation not match'))->withInput();
                } else {
                    $model->password = Hash::make($request->password);
                }
            }
        }

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

        if ($model->user_type == 'vendor') {
            try {
                $model->syncRoles('partner-transport');;
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create vendor user', $th->getMessage()))->withInput();
            }
        } else {
            try {
                $model->syncRoles($request->role);
            } catch (\Throwable $th) {
                DB::rollBack();
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
            }
        }

        if ($model->user_type == 'non-employee') {
            $non_employee = \App\Models\NonEmployee::where('user_id', $model->id)->first();
            if (!$non_employee) {
                $non_employee = new \App\Models\NonEmployee();
            }
            $non_employee->user_id = $model->id;
            $non_employee->name = $request->name;
            $non_employee->gender = $request->gender;
            $non_employee->phone = $request->phone;
            $non_employee->agency = $request->agency;
            $non_employee->address = $request->address;
            $non_employee->identity_number = $request->identity_number;
            $non_employee->role = $request->role_name;
            try {
                $non_employee->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'create non employee', $th->getMessage()))->withInput();
            }
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'edit');
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
        $model = model::findOrFail($id);
        DB::beginTransaction();
        try {
            VendorUser::where('user_id', $model->id)->delete();
            NonEmployee::where('user_id', $model->id)->delete();
            $model->delete();
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

    public function export()
    {
        return Excel::download(new User(), 'users.xlsx');
    }

    public function import_format()
    {
        return $this->ResponseDownload(public_path('import/admin/user.xlsx'));
    }

    public function import(Request $request)
    {
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), [
                'file' => 'required|file',
            ]);
        } else {
            $this->validate($request, [
                'file' => 'required|file',
            ]);
        }

        // * store file
        $file = $this->upload_file($request->file('file'), 'import');

        // * import
        try {
            Excel::import(new AdminUser(), 'storage/' . $file);
        } catch (\Throwable $th) {
            $this->delete_file($file);

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'import', 'failed import data', $th->getMessage());
            }

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(false, 'import', 'failed import data', $th->getMessage()));
        }

        $this->delete_file($file);
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'import', 'success import data');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'import', 'success import data'));
    }

    public function select(Request $request)
    {
        $model = model::leftJoin('employees', 'employees.id', '=', 'users.employee_id')
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('users.name', 'like', "%$request->search%")
                        ->orWhere('users.username', 'like', "%$request->search%")
                        ->orWhere('users.email', 'like', "%$request->search%")
                        ->orWhere('positions.nama', 'like', "%$request->search%");
                });
            })
            ->select('users.*', 'employees.name as employee_name', 'positions.nama as position_name')
            ->limit(10)
            ->get();

        $model->each(function ($item) {
            $item->position_name = $item->position_name ?? 'Tidak Ada';
        });

        return $this->ResponseJsonData($model);
    }

    public function store_token(Request $request)
    {
        Auth::user()->update(['device_token' => $request->token]);
        return response()->json(['Token successfully stored.']);
    }

    /**
     * create user get employee email
     */
    public function getEmployeeEmail(Request $request)
    {
        $employee = \App\Models\Employee::findOrFail($request->employee_id);
        $email = $employee?->email;

        return response()->json(['data' => $email]);
    }
}
