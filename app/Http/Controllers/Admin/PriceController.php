<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Period;
use App\Models\Price as model;
use App\Models\PriceCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;

class PriceController extends Controller
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
        $this->middleware("permission:update $this->view_folder", ['only' => ['edit', 'update']]);
        $this->middleware("permission:delete $this->view_folder", ['only' => ['destroy']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'price';

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
            $data = model::orderByDesc('created_at')->with(['item', 'period'])->select('*')->whereNotNull('period_id');

            if ($request->tahun) {
                $data->whereHas('period', function ($p) use ($request) {
                    $p->where('tahun', $request->tahun);
                });
            }

            if ($request->period_id) {
                $data->where('period_id', $request->period_id);
            }

            if ($request->item_id) {
                $data->where('item_id', $request->item_id);
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('periode', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->period?->value ?? 'Periode Null',
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->editColumn('harga_beli', fn ($row) => formatNumber($row->harga_beli))
                ->editColumn('harga_jual', fn ($row) => formatNumber($row->harga_jual))
                ->addColumn('action', function ($row) {
                    return view('components.datatable.button-datatable', [
                        'row' => $row,
                        'main' => $this->view_folder,
                        'btn_config' => [
                            'detail' => [
                                'display' => false,
                            ],
                            'edit' => [
                                'display' => false,
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
            'item_id' => $request->item_id,
            'nama' => $request->nama,
            'harga_beli' => thousand_to_float($request->harga_beli),
            'harga_jual' => thousand_to_float($request->harga_jual),
            'period_id' => $request->period_id,
        ]);

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

        // * creating price customer
        foreach ($request->sh_number_id as $key => $value) {
            // * check if have old price in same period and if find delete the old data
            $old_price = model::where('period_id', $request->period_id)->first();
            $price_customer = PriceCustomer::where('price_id', $old_price->id)->where('sh_number_id', $value)->first();
            if ($old_price) {
                try {
                    $price_customer->delete();
                    $old_price->delete();
                } catch (\Throwable $th) {
                }
            }

            $price_customer = new PriceCustomer();
            $price_customer->loadmodel([
                'sh_number_id' => $value,
                'price_id' => $model->id,
            ]);

            // * save data
            try {
                $price_customer->save();
            } catch (\Throwable $th) {
                DB::rollBack();

                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                }

                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
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
        $model->loadModel([
            'item_id' => $request->item_id,
            'nama' => $request->nama,
            'harga_beli' => thousand_to_float($request->harga_beli),
            'harga_jual' => thousand_to_float($request->harga_jual),
            'period_id' => $request->period_id
        ]);

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

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD(true, 'edit');
        }

        return redirect()->route("admin.$this->view_folder.show", $model)->with($this->ResponseMessageCRUD(true, 'edit'));
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

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * select 2 form search api
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function select(Request $request, $id = null)
    {
        $model = model::when($id, fn ($q) => $q->where('item_id', $id))
            ->when($request->search, fn ($q) => $q->where('harga_jual', 'like', "%$request->search%")->orWhere('harga_beli', 'like', "%$request->search%"))
            ->orderByDesc('created_at')
            ->limit(10);

        return $this->ResponseJsonData($model->get());
    }

    /**
     * search harga jual with customer id and period id
     *
     * @param  Request  $request
     * @param  int  $period
     * @param  int  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_with_period_and_customer_and_search_harga_jual(Request $request, $item = null, $customer = null, $date = null)
    {
        $period = Period::whereYear('tahun', ($date ? explode('-', $date)[0] : date('Y')))
            ->whereDate('tanggal_mulai', '<=', $date ?? Carbon::today()->format('Y-m-d'))
            ->whereDate('tanggal_akhir', '>=', $date ?? Carbon::today()->format('Y-m-d'))
            ->first();

        if ($period == null) {
            return $this->ResponseJsonData([]);
        }

        $model = model::leftjoin('price_customers', 'price_customers.price_id', 'prices.id')
            ->leftJoin('customers', 'customers.id', 'price_customers.customer_id')
            ->where('item_id', $item)
            ->where('customers.id', $customer)
            ->where('period_id', $period->id)
            ->where('prices.harga_jual', 'like', "%$request->search%")
            ->select('prices.*')
            ->distinct('prices.id')
            ->orderByDesc('prices.created_at')->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * search harga beli with customer id and period id
     *
     * @param  Request  $request
     * @param  int  $period
     * @param  int  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_with_period_and_customer_and_search_harga_beli(Request $request, $item = null, $customer = null, $date = null)
    {
        $period = Period::whereYear('tahun', $date ? explode('-', $date)[0] : date('Y'))
            ->whereDate('tanggal_mulai', '<=', $date ??  Carbon::today()->format('Y-m-d'))
            ->whereDate('tanggal_akhir', '>=', $date ??  Carbon::today()->format('Y-m-d'))
            ->first();

        if ($period == null) {
            return $this->ResponseJsonData([]);
        }

        $model = model::leftjoin('price_customers', 'price_customers.price_id', 'prices.id')
            ->leftJoin('customers', 'customers.id', 'price_customers.customer_id')
            ->where('item_id', $item)
            ->where('customers.id', $customer)
            ->where('period_id', $period->id)
            ->where('prices.harga_beli', 'like', "%$request->search%")
            ->select('prices.*')
            ->distinct('prices.id')
            ->orderByDesc('prices.created_at')->limit(10)
            ->get();

        return $this->ResponseJsonData($model);
    }

    /**
     * search harga jual with customer id and period id
     *
     * @param  Request  $request
     * @param  int  $period
     * @param  int  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_with_period_and_sh_number_and_search_harga_jual(Request $request, $item = null, $sh_number = null, $date = null)
    {
        $period = Period::where('tahun', $date ? explode('-', $date)[0] : date('Y'))
            ->whereDate('tanggal_mulai', '<=', $date ??  Carbon::today()->format('Y-m-d'))
            ->whereDate('tanggal_akhir', '>=', $date ??  Carbon::today()->format('Y-m-d'))
            ->first();

        if ($period == null) {
            return $this->ResponseJsonData([]);
        }

        $model = model::leftjoin('price_customers', 'price_customers.price_id', 'prices.id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', 'price_customers.sh_number_id')
            ->where('item_id', $item)
            ->where('sh_numbers.id', $sh_number)
            ->where('period_id', $period->id)
            ->where('prices.harga_jual', 'like', "%$request->search%")
            ->select('prices.*')
            ->distinct('prices.id')
            ->orderByDesc('prices.created_at')->limit(10)
            ->first();

        if (!is_null($model)) {
            $model['period'] = $period;
        }

        return $this->ResponseJsonData($model);
    }

    /**
     * search harga beli with customer id and period id
     *
     * @param  Request  $request
     * @param  int  $period
     * @param  int  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function select_with_period_and_sh_number_and_search_harga_beli(Request $request, $item = null, $sh_number = null, $date = null)
    {
        $period = Period::whereYear('tahun', Carbon::parse($date)->format('Y'))
            ->whereDate('tanggal_mulai', '<=', $date ??  Carbon::today()->format('Y-m-d'))
            ->whereDate('tanggal_akhir', '>=', $date ??  Carbon::today()->format('Y-m-d'))
            ->first();

        if ($period == null) {
            return $this->ResponseJsonData([]);
        }

        $model = model::leftjoin('price_customers', 'price_customers.price_id', 'prices.id')
            ->leftJoin('sh_numbers', 'sh_numbers.id', 'price_customers.sh_number_id')
            ->where('item_id', $item)
            ->where('sh_numbers.id', $sh_number)
            ->where('period_id', $period->id)
            ->where('prices.harga_beli', 'like', "%$request->search%")
            ->select('prices.*')
            ->distinct('prices.id')
            ->orderByDesc('prices.created_at')->limit(10)
            ->first();

        return $this->ResponseJsonData($model);
    }

    /**
     * data price customers
     *
     * @param  Request  $request
     * @param  int|null  $id
     * @return \Illuminate\Http\Response
     */
    public function data_price_customers(Request $request, $id = null)
    {
        $price_customer = PriceCustomer::where('price_id', $id)->orderByDesc('created_at')->with(['price', 'sh_number', 'customer']);

        return Datatables::of($price_customer)
            ->addIndexColumn()
            ->addColumn('drop', function ($price_customer) {
                return $price_customer->sh_number->sh_number_details()->where('type', 'Drop Point')->first()->alamat;
            })
            ->addColumn('supply', function ($price_customer) {
                return $price_customer->sh_number->sh_number_details()->where('type', 'Supply Point')->first()->alamat;
            })
            ->addColumn('action', function ($row) {
                return view('components.datatable.button-datatable', [
                    'row' => $row,
                    'main' => 'price.customers',
                    'btn_config' => [
                        'detail' => [
                            'display' => false,
                        ],
                        'edit' => [
                            'display' => false,
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

    /**
     * add data price customers
     *
     * @param  Request  $request
     * @param  int|null  $id
     * @return \Illuminate\Http\Response
     */
    public function add_price_customer(Request $request, $id)
    {
        $price = model::findOrFail($id);

        DB::beginTransaction();
        $model = new PriceCustomer();
        $model->loadModel([
            'sh_number_id' => $request->sh_number_id,
            'price_id' => $id,
        ]);

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

        return redirect()->back()->with($this->ResponseMessageCRUD());
    }

    /**
     * destroy data price customers
     *
     * @param  Request  $request
     * @param  int|null  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_price_customer(Request $request, $id)
    {
        $model = PriceCustomer::findOrFail($id);

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

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'delete'));
    }

    /**
     * get detail price
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function get_detail_price($id = null)
    {
        $model = model::findOrFail($id);
        $model->load(['item', 'period', 'price_customers.sh_number', 'price_customers.customer']);

        return $this->ResponseJsonData($model);
    }

    /**
     * detail
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id = null)
    {
        $model = model::findOrFail($id);
        $model->harga_jual = floatDotThreeDigitsFormat($model->harga_jual);
        $model->harga_beli = floatDotThreeDigitsFormat($model->harga_beli);

        return $this->ResponseJsonData($model);
    }
}
