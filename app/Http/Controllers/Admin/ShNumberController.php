<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShNumber as model;
use App\Models\ShNumber;
use App\Models\ShNumberDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;

class ShNumberController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'sh-number';

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
            $data = model::orderByDesc('created_at')->with('customer')->select(['sh_numbers.*']);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', fn ($row) => $row->customer->nama)
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => true,
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

        return view("admin.$this->view_folder.index");
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
        $model->loadModel([
            'kode' => $request->sh_number,
            'customer_id' => $request->customer_id,
            'allowance' => (float) str_replace(['.', ','], ['', '.'], $request->allowance),
        ]);

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

        // * saving sh number detail
        foreach ($request->alamat as $key => $value) {
            // validate
            $arr = [
                'sh_number_id' => $model->id,
                'alamat' => $value,
                'allowance' => (float) str_replace(['.', ','], ['', '.'], $request->allowance),
                'longitude' => $request->longitude[$key],
                'latitude' => $request->latitude[$key],
                'type' => $request->type[$key],
            ];
            ShNumberDetail::rules($arr);

            // creating new data
            $sh_nunmber_detail = new ShNumberDetail();
            $sh_nunmber_detail->loadModel($arr);
            try {
                $sh_nunmber_detail->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
            }
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.customer.show", $model->customer)->with($this->ResponseMessageCRUD(true));
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
        //
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
        $model->loadModel(array_merge(
            $request->all(),
            [
                'kode' => $request->sh_number,
                'allowance' => (float) str_replace(['.', ','], ['', '.'], $request->allowance),
            ]
        ));

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

        // * update sh number details
        foreach ($request->type as $key => $value) {
            $sh_nunmber_detail = ShNumberDetail::where('sh_number_id', $model->id)->where('type', $value)->first();

            $sh_nunmber_detail->loadModel([
                'sh_number_id' => $model->id,
                'alamat' => $request->alamat[$key],
                'longitude' => $request->longitude[$key],
                'latitude' => $request->latitude[$key],
                'type' => $value,
            ]);

            try {
                $sh_nunmber_detail->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
            }
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'edit');
        }

        return redirect()->route("admin.customer.show", $model->customer)->with($this->ResponseMessageCRUD(true, 'edit'));
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
        $customer = $model->customer;
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

        return redirect()->route("admin.customer.show", $customer)->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $model = model::leftJoin('customers', 'customers.id', 'sh_numbers.customer_id')
            ->orWhere('sh_numbers.kode', 'like', "%$request->search%")
            ->orWhere('customers.nama', 'like', "%$request->search%")
            ->select(['sh_numbers.*'])
            ->with(['customer'])
            ->distinct('sh_numbers.id')
            ->orderByDesc('sh_numbers.created_at')->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * details api
     *
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_detail($id = null)
    {
        $model = model::with(['sh_number_details', 'customer'])->findOrFail($id);
        return $this->ResponseJsonData($model);
    }

    /**
     * select_by_customer
     *
     * @param int|null $id
     * @return mixed
     */
    public function select_by_customer(Request $request, $id = null)
    {
        $model = ShNumber::where('customer_id', $id)
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
}
