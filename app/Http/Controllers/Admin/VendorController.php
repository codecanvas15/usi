<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\Vendor;
use App\Http\Controllers\Controller;
use App\Imports\Admin\Vendor as AdminVendor;
use App\Models\Customer;
use App\Models\User;
use App\Models\Vendor as model;
use App\Models\VendorCoa;
use App\Models\VendorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
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
    protected string $view_folder = 'vendor';

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
            $data = model::with('business_field')
                ->leftJoin('business_fields', 'business_fields.id', 'vendors.business_field_id')
                ->orderByDesc('vendors.created_at')
                ->select('vendors.*');

            if ($request->business_field) {
                $data->where('business_field_id', $request->business_field);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('nama', function ($row) {
                    $str = $row->nama;
                    if (!$row->is_complete) {
                        $str .= '<br><span class="text-capitalize badge bg-' . complete_status()[$row->is_complete]['color'] . '">' . complete_status()[$row->is_complete]['text'] . '</span>';
                    }

                    return $str;
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
                ->editColumn('business_field', function ($row) {
                    return $row->business_field?->name;
                })
                ->rawColumns(['nama', 'action'])
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

        return view("admin.$this->view_folder.create", compact('model'));
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
        $request_all = $request->all();
        $request_all['top_days'] = $request->top_days ?? 0;
        $request_all['loss_tolerance'] = thousand_to_float($request->loss_tolerance ?? 0);
        $model->loadModel($request_all);

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

        // ! COA #######################

        // * create vendor coas
        if (Auth::user()->hasPermissionTo('create vendor-coa')) {
            foreach (vendor_coa_types() as $item) {
                $value = Str::snake($item);
                if ($request->$value) {
                    $vendor_coa = new VendorCoa();
                    $vendor_coa->loadModel([
                        'vendor_id' => $model->id,
                        'coa_id' => $request->$value,
                        'type' => $item,
                    ]);

                    try {
                        $vendor_coa->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
                    }
                }
            }
        }

        // if (VendorCoa::where('vendor_id', $model->id)->count() != count(vendor_coa_types())) {
        //     foreach (vendor_coa_types() as $item) {
        //         $vendor_coa = VendorCoa::where('vendor_id', $model->id)->where('type', $item)->first();

        //         if (!$vendor_coa) {
        //             $default_coa = \App\Models\DefaultCoa::where('type', 'vendor')->where('name', $item)->first();
        //             $vendor_coa = new VendorCoa();
        //             $vendor_coa->loadModel([
        //                 'vendor_id' => $model->id,
        //                 'coa_id' => $default_coa->coa_id,
        //                 'type' => $item,
        //             ]);

        //             try {
        //                 $vendor_coa->save();
        //             } catch (\Throwable $th) {
        //                 DB::rollBack();
        //                 if ($request->ajax()) {
        //                     return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
        //                 }

        //                 return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        //             }
        //         }
        //     }
        // }

        // ! COA #######################

        // ! BANK #######################

        $vendor_banks = [];
        if (is_array($request->bank_name)) {
            foreach ($request->bank_name as $key => $item) {
                $vendor_banks[] = [
                    'vendor_id' => $model->id,
                    'name' => $request->bank_name[$key],
                    'account_number' => $request->bank_account_number[$key],
                    'behalf_of' => $request->bank_behalf_of[$key],
                ];
            }
        }

        if (count($vendor_banks) > 0) {
            $model->vendor_banks()->createMany(
                $vendor_banks
            );
        }

        // ! BANK #######################

        DB::commit();

        if ($model->vendor_coas->count() != count(vendor_coa_types())) {
            $model->is_complete = false;
            $model->save();
        }

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
        $model = model::with('vendor_banks')
            ->findOrFail($id);
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
        $model = model::findOrFail($id);
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate($request, model::rules());
        } else {
            $this->validate_api($request->all(), model::rules());
        }

        // * update data
        $request_all = $request->all();
        $request_all['loss_tolerance'] = thousand_to_float($request->loss_tolerance ?? 0);
        $request_all['top_days'] = $request->top_days ?? 0;
        $model->loadModel($request_all);

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

        // * update vendor coas
        if (Auth::user()->hasPermissionTo('edit vendor-coa')) {
            // * delete all vendor coas
            $model->vendor_coas()->delete();

            // * create new
            foreach (vendor_coa_types() as $item) {
                $value = Str::snake($item);
                if ($request->$value) {
                    $vendor_coa = new VendorCoa();
                    $vendor_coa->loadModel([
                        'vendor_id' => $model->id,
                        'coa_id' => $request->$value,
                        'type' => $item,
                    ]);

                    try {
                        $vendor_coa->save();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
                    }
                }
            }
        }

        // if (VendorCoa::where('vendor_id', $model->id)->count() != count(vendor_coa_types())) {
        //     foreach (vendor_coa_types() as $item) {
        //         $vendor_coa = VendorCoa::where('vendor_id', $model->id)->where('type', $item)->first();

        //         if (!$vendor_coa) {
        //             $default_coa = \App\Models\DefaultCoa::where('type', 'vendor')->where('name', $item)->first();

        //             $vendor_coa = new VendorCoa();
        //             $vendor_coa->loadModel([
        //                 'vendor_id' => $model->id,
        //                 'coa_id' => $default_coa->coa_id,
        //                 'type' => $item,
        //             ]);

        //             try {
        //                 $vendor_coa->save();
        //             } catch (\Throwable $th) {
        //                 DB::rollBack();
        //                 if ($request->ajax()) {
        //                     return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
        //                 }

        //                 return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        //             }
        //         }
        //     }
        // }

        DB::commit();

        if ($model->vendor_coas->count() != count(vendor_coa_types())) {
            $model->is_complete = false;
            $model->save();
        } else {
            $model->is_complete = true;
            $model->save();
        }

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
        if ($model->purchases()->exists()) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, 'Vendor telah memiliki transaksi', 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Vendor telah memiliki transaksi'));
        }
        DB::beginTransaction();
        try {
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

    /**
     * export data
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return Excel::download(new Vendor(), 'vendors.xlsx');
    }

    public function import_format()
    {
        return $this->ResponseDownload(public_path('import/admin/vendor.xlsx'));
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
        // $file = $this->upload_file($request->file('file'), 'import');

        // * import
        try {
            Excel::import(new AdminVendor(), $request->file('file'));
        } catch (\Throwable $th) {
            // $this->delete_file($file);

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'import', 'failed import data', $th->getMessage());
            }

            return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(false, 'import', 'failed import data', $th->getMessage()));
        }

        // $this->delete_file($file);
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'import', 'success import data');
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'import', 'success import data'));
    }

    /**
     * update vendor coass
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_vendor_coa(Request $request, $id)
    {
        if (Auth::user()->hasPermissionTo('update vendor-coa')) {
            DB::beginTransaction();
            $model = model::findOrFail($id);

            // * delete all vendor coas
            foreach ($model->vendor_coas as $item) {
                $item->delete();
            }

            // * create new
            foreach (vendor_coa_types() as $item) {
                $value = Str::snake($item);
                $vendor_coa = new VendorCoa();
                $vendor_coa->loadModel([
                    'vendor_id' => $model->id,
                    'coa_id' => $request->$value,
                    'type' => $item,
                ]);

                try {
                    $vendor_coa->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
                }
            }

            DB::commit();

            if ($model->vendor_coas->count() != count(vendor_coa_types())) {
                $model->is_complete = false;
                $model->save();
            } else {
                $model->is_complete = true;
                $model->save();
            }

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(true, 'edit', 'vendor coa');
            }
            return redirect()->route('admin.vendor.show', $model)->with($this->ResponseMessageCRUD(true, 'edit', ' vendor coa.'));
        }

        return abort(403);
    }

    /**
     * select api
     *
     * @param Request  $request
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $model = model::where('is_complete', true)
            ->when($request->search, function ($query, $search) {
                return $query->where('nama', 'like', "%$search%");
            })
            ->when($request->has_unpaid_supplier_invoice, function ($query) {
                return $query->whereHas('supplier_invoice_parents', function ($si) {
                    $si->where('payment_status', '!=', 'paid');
                });
            })
            ->when($request->has_cash_advance_payment, function ($query) {
                return $query->whereHas('cash_advance_payments');
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * vendor_user
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function vendor_coa_with_type(Request $request)
    {
        $vendor_coa = VendorCoa::where('vendor_id', $request->vendor_id)->where('type', $request->type)->first();
        return $this->ResponseJsonData($vendor_coa);
    }

    /**
     * vendor_alamat
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function vendor_alamat($id)
    {
        $alamat = model::findOrFail($id)->alamat;
        return $this->ResponseJsonData($alamat);
    }

    /**
     * vendor_user
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function vendor_users($id)
    {
        $vendor = model::findOrFail($id);

        return Datatables::of($vendor->users)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($id) {
                return view("admin.vendor.datatable.user", ['row' => $row, 'id' => $id]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * store_user
     *
     * @param Request $request,
     * @param $id
     * @return mixed
     */
    public function store_user(Request $request, $id)
    {
        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), User::rules());
        } else {
            $this->validate($request, User::rules());
        }
        // * create data
        $model = new User();
        $model->loadModel($request->all());
        $model->password = Hash::make($request->password);

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        try {
            $model->assignRole('partner-transport');
        } catch (\Throwable $th) {
        }

        // * attach vendor
        try {
            $model->vendor()->attach($id);
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.vendor.show", $id)->with($this->ResponseMessageCRUD());
    }

    /**
     * edit_user
     *
     * @param arguments
     * @return mixed
     */
    public function edit_user($vendor_id, $user_id)
    {
        $vendor = model::findOrFail($vendor_id);
        $user = User::findOrFail($user_id);
        $vendor_user = VendorUser::where('vendor_id', $vendor_id)->where('user_id', $user_id)->first();

        if (!$vendor_user) {
            return abort(404);
        }

        return view("admin.$this->view_folder.edit_user", compact('vendor', 'user'));
    }

    /**
     * update_user
     *
     * @param Request $request,
     * @param $id
     * @return mixed
     */
    public function update_user(Request $request, $vendor_id, $user_id)
    {
        $vendor = model::findOrFail($vendor_id);
        $model = User::findOrFail($user_id);
        $vendor_user = VendorUser::where('vendor_id', $vendor_id)->where('user_id', $user_id)->first();

        DB::beginTransaction();
        // * validate
        if ($request->ajax()) {
            $this->validate_api($request->all(), User::rules('update'));
        } else {
            $this->validate($request, User::rules());
        }

        // * create data
        $model->loadModel($request->all());
        $model->password = Hash::make($request->password);

        // * saving and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.vendor.show", $vendor_id)->with($this->ResponseMessageCRUD());
    }

    public function create_vendor_bank(Request $request, $vendor_id)
    {
        $vendor = model::findOrFail($vendor_id);

        DB::beginTransaction();

        $model = new \App\Models\VendorBank();
        $model->fill([
            'vendor_id' => $vendor_id,
            'name' => $request->bank_name,
            'account_number' => $request->bank_account_number,
            'behalf_of' => $request->bank_behalf_of,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', 'vendor bank', $th->getMessage()))->withInput();
        }

        DB::commit();

        return redirect()->route("admin.vendor.show", $vendor)->with($this->ResponseMessageCRUD(true, 'create', 'vendor bank'));
    }

    public function update_vendor_bank(Request $request, $vendor_id, $bank_id)
    {
        $vendor = model::findOrFail($vendor_id);
        $model = \App\Models\VendorBank::findOrFail($bank_id);

        if ($vendor_id != $model->vendor_id) {
            return abort(404);
        }

        DB::beginTransaction();
        $model->fill([
            'name' => $request->bank_name,
            'account_number' => $request->bank_account_number,
            'behalf_of' => $request->bank_behalf_of,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->route("admin.vendor.show", $vendor)->with($this->ResponseMessageCRUD(false, "edit", "vendor bank", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.vendor.show", $vendor)->with($this->ResponseMessageCRUD(true, "edit", 'vendor bank'));
    }

    public function destroy_vendor_bank($vendor_id, $bank_id)
    {
        $vendor = model::findOrFail($vendor_id);
        $model = \App\Models\VendorBank::findOrFail($bank_id);

        if ($vendor_id != $model->vendor_id) {
            return abort(404);
        }

        DB::beginTransaction();
        try {
            $model->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->route("admin.vendor.show", $vendor)->with($this->ResponseMessageCRUD(false, "delete", "vendor bank", $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.vendor.show", $vendor)->with($this->ResponseMessageCRUD(true, "delete", 'vendor bank'));
    }

    public function vendor_detail($id)
    {
        $data = model::with('vendor_banks')
            ->findOrFail($id);

        return $this->ResponseJsonData($data);
    }

    public function find_customer(Request $request, $id)
    {
        $model = model::findOrFail($id);
        $customer = Customer::where('nama', $model->nama)->first();

        if ($request->ajax()) {
            return $this->ResponseJsonData($customer);
        }
    }
}
