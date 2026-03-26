<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\Customer;
use App\Http\Controllers\Controller;
use App\Imports\Admin\Customer as AdminCustomer;
use App\Models\Customer as model;
use App\Models\CustomerBank;
use App\Models\CustomerCoa;
use App\Models\ShNumber;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Datatables;

class CustomerController extends Controller
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
        $this->middleware("permission:edit $this->view_folder-coa", ['only' => ['update_customer_coa']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
        $this->middleware("permission:import $this->view_folder", ['only' => ['import']]);
        $this->middleware("permission:export $this->view_folder", ['only' => ['export']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'customer';

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
            $data = model::orderByDesc('created_at')->select('*');
            $customer_banks = CustomerBank::whereIn('customer_id', $data->pluck('id'))->get();
            return Datatables::of($data)
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
                ->addColumn('banks', function ($row) use ($customer_banks) {
                    $str = '';
                    $banks = $customer_banks->where('customer_id', $row->id);
                    foreach ($banks as $item) {
                        $str .= $item->bank_internal->nama_bank . ' - ' . $item->bank_internal->no_rekening . '<br>';
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
                ->rawColumns(['nama', 'action', 'banks'])
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

        // * create data customeer
        $model = new model();
        $model->loadModel(array_merge($request->all(), [
            'lost_tolerance' => $request->lost_tolerance && $request->lost_tolerance_type == 'percent' ? (thousand_to_float($request->lost_tolerance ?? 0) / 100) : thousand_to_float($request->lost_tolerance ?? ''),
        ]));

        // * saving
        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        // * create custmer coas
        if (Auth::user()->hasPermissionTo('create customer-coa')) {
            foreach (customer_coa_types() as $item) {
                $value = Str::snake($item);

                // * have request customer coas type
                if ($request->$value) {
                    $customer_coa = new CustomerCoa();
                    $customer_coa->loadModel([
                        'customer_id' => $model->id,
                        'coa_id' => $request->$value,
                        'tipe' => $item,
                    ]);

                    try {
                        $customer_coa->save();
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

        // * customer coa data is null or empty
        // if (CustomerCoa::where('customer_id', $model->id)->count() != count(customer_coa_types())) {
        //     foreach (customer_coa_types() as $item) {
        //         $customer_coa = CustomerCoa::where('customer_id', $model->id)->where('tipe', $item)->first();

        //         if (!$customer_coa) {
        //             $default_coa = \App\Models\DefaultCoa::where('type', 'customer')->where('name', $item)->first();

        //             $customer_coa = new CustomerCoa();
        //             $customer_coa->loadModel([
        //                 'customer_id' => $model->id,
        //                 'coa_id' => $default_coa->coa_id,
        //                 'tipe' => $item,
        //             ]);

        //             try {
        //                 $customer_coa->save();
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

        // * customer bank
        if ($request->customer_bank_id) {
            foreach ($request->customer_bank_id as $item) {
                $customer_bank = new CustomerBank();
                $customer_bank->loadModel([
                    'customer_id' => $model->id,
                    'bank_internal_id' => $item,
                ]);

                try {
                    $customer_bank->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
                }
            }
        }


        DB::commit();

        if ($model->customer_coas->count() != count(customer_coa_types())) {
            $model->is_complete = false;
            $model->save();
        }

        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route('admin.customer.index')->with($this->ResponseMessageCRUD());
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
        $model = model::with('sh_numbers')->findOrFail($id);
        foreach ($model->sh_numbers as $item) {
            $item->sh_number_details;
        }

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
            $this->validate_api($request->all(), model::rules('update', $id));
        } else {
            $this->validate($request, model::rules('update', $id));
        }
        // * update data
        $model->loadModel(array_merge($request->all(), [
            'lost_tolerance' => $request->lost_tolerance && $request->lost_tolerance_type == 'percent' ? (thousand_to_float($request->lost_tolerance ?? 0) / 100) : thousand_to_float($request->lost_tolerance ?? ''),
        ]));

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

        // * customer bank
        if ($request->customer_bank_id) {
            $customer_banks = CustomerBank::where('customer_id', $model->id)->get();
            foreach ($customer_banks as $item) {
                $item->delete();
            }

            foreach ($request->customer_bank_id as $item) {
                $customer_bank = new CustomerBank();
                $customer_bank->loadModel([
                    'customer_id' => $model->id,
                    'bank_internal_id' => $item,
                ]);

                try {
                    $customer_bank->save();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    if ($request->ajax()) {
                        return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                    }

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
                }
            }
        }

        if ($model->customer_coas->count() != count(customer_coa_types())) {
            $model->is_complete = false;
            $model->save();
        } else {
            $model->is_complete = true;
            $model->save();
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

        if ($model->so_tradings()->exists() || $model->sale_order_generals()->exists()) {
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'delete', null, 'Customer telah memiliki transaksi', 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'delete', null, 'Customer telah memiliki transaksi'));
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

    public function select(Request $request)
    {
        $data = model::when($request->search, function ($query, $search) {
            return $query->where('is_complete', true)
                ->where('nama', 'like', "%{$search}%");
        }, function ($query) {
            return $query->where('is_complete', true);
        })
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->ResponseJson($data);
    }

    public function export()
    {
        return Excel::download(new Customer(), 'customers.xlsx');
    }

    public function import_format()
    {
        return $this->ResponseDownload(public_path('import/admin/customer.xlsx'));
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
        // $file = $this->upload_file($request->file('file'), 'storage/app/public/import');

        // * import
        try {
            Excel::import(new AdminCustomer(), $request->file('file'));
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
     * update customer coass
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_customer_coa(Request $request, $id)
    {
        DB::beginTransaction();
        $customer = model::findOrFail($id);

        // * delete all customer coas
        foreach ($customer->customer_coas as $item) {
            $item->delete();
        }

        // * create new
        foreach (customer_coa_types() as $item) {
            $value = Str::snake($item);
            $customer_coa = new CustomerCoa();
            $customer_coa->loadModel([
                'customer_id' => $customer->id,
                'coa_id' => $request->$value,
                'tipe' => $item,
            ]);

            try {
                $customer_coa->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
            }
        }



        if ($customer->customer_coas->count() != count(customer_coa_types())) {
            $customer->is_complete = false;
            $customer->save();
        } else {
            $customer->is_complete = true;
            $customer->save();
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'edit', 'customer coa');
        }

        return redirect()->route('admin.customer.show', $customer)->with($this->ResponseMessageCRUD(true, 'edit', ' customer coa.'));
    }

    /**
     * select customer sh
     *
     * @param Request  $request
     * @param int|null $id
     * @return Response
     */
    public function select_customer_shs(Request $request, $id = null)
    {
        $model = ShNumber::where('customer_id', $id)
            ->whereNull('deleted_at')
            ->leftJoin('sh_number_details as dp', function ($q) {
                $q->on('dp.sh_number_id', 'sh_numbers.id')
                    ->where('dp.type', 'Drop Point');
            })
            ->leftJoin('sh_number_details as sp', function ($q) {
                $q->on('sp.sh_number_id', 'sh_numbers.id')
                    ->where('sp.type', 'Supply Point');
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('sh_numbers.kode', 'like', "%{$search}%")
                        ->orWhere('dp.alamat', 'like', "%{$search}%")
                        ->orWhere('sp.alamat', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('sh_numbers.created_at')
            ->select('sh_numbers.*', 'dp.alamat as drop_point', 'sp.alamat as supply_point')
            ->paginate(10);

        return $this->ResponseJson($model);
    }

    /**
     * detail
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $model = model::with(['sh_numbers.sh_number_details'])->findOrFail($id);
        return $this->ResponseJsonData($model);
    }

    public function customer_detail($id)
    {
        $data = model::with(['customer_banks', 'customer_banks.bank_internal'])->where("id", $id)->first();

        return $this->ResponseJsonData($data);
    }

    /**
     * select customer bank
     */
    public function selectCustomerBank(Request $request, $id)
    {
        $data = CustomerBank::leftJoin('bank_internals', 'customer_banks.bank_internal_id', 'bank_internals.id')
            ->where('customer_banks.customer_id', $id)
            ->selectRaw('
                customer_banks.id,
                customer_banks.bank_internal_id,
                bank_internals.nama_bank,
                bank_internals.no_rekening
            ')
            ->when($request->search, function ($query, $search) {
                $query->where('bank_internals.nama_bank', 'like', "%$search%")
                    ->orWhere('bank_internals.no_rekening', 'like', "%$search%");
            })
            ->get();

        return $this->ResponseJsonData($data);
    }

    public function find_vendor(Request $request, $id)
    {
        $model = model::findOrFail($id);
        $vendor = Vendor::where('nama', $model->nama)->first();

        if ($request->ajax()) {
            return $this->ResponseJsonData($vendor);
        }
    }
}
