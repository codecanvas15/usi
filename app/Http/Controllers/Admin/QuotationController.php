<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Quotation as model;
use App\Models\QuotationItem;
use App\Models\QuotationItemTax;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;

use function GuzzleHttp\Promise\all;

class QuotationController extends Controller
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
    protected string $view_folder = 'quotation';

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
            $data = model::orderByDesc('created_at')->with(['item', 'price'])->select('*');

            if ($request->from_date) {
                $data->whereDate('tanggal', '>=', Carbon::parse($request->form_date));
            }

            if ($request->to_date) {
                $data->whereDate('tanggal', '<=', Carbon::parse($request->to_date));
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('code', fn ($row) => view('components.datatable.detail-link', [
                    'field' => $row->code,
                    'row' => $row,
                    'main' => $this->view_folder,
                ]))
                ->addColumn('item', fn ($row) => $row->item?->nama)
                ->addColumn('total_main', fn ($row) => 'Rp. ' . formatNumber($row->sub_total_after_tax))
                ->addColumn('total_add', fn ($row) =>  'Rp. ' . formatNumber($row->additional_total))
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

        $quotation = new model();
        $kode = generate_code_with_cus_name(
            model: model::class,
            code: 'QUO',
            code2: null,
            date_column: 'date',
            date: \Carbon\Carbon::now()->format('Y-m-d'),
            code3: 'CUS',
            filter: [],
        );
        $currency = Currency::where('is_local', true)->first();

        return view("admin.$this->view_folder.create", compact('model', 'kode', 'currency'));
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
        $model->branch_id = get_current_branch_id();
        $model->customer_id = $request->customer_id;
        $model->date = Carbon::parse($request->date);
        $model->information = $request->keterangan;
        $model->currency_id = $request->currency_id;
        $model->exchange_rate = $request->exchange_rate;

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

        // * Saving Trading Items
        $trading_item = new QuotationItem();
        $trading_item->loadModel([
            'item_id' => $request->item_id,
            'quotation_id' => $model->id,
            'item_type' => 'trading',
            'type' => 'main',
            'price' => thousand_to_float($request->price),
            'quantity' => $request->quantity,
            'sub_total' => thousand_to_float($request->price) * thousand_to_float($request->quantity),
        ]);


        try {
            $trading_item->save();
        } catch (\Throwable $th) {
            dd($th->getMessage());
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
        }

        $trading_subtotal_after_tax = 0;
        // Condition if $request->tax_id not aray an length
        $countMainTax = 0;
        if (is_array($request->tax_id) && !empty($request->tax_id)) {
            QuotationItemTax::where('quotation_item_id', $trading_item->id)->whereNotIn('tax_id', $request->tax_id)->delete();
            foreach ($request->tax_id as $tax_id) {
                if (isset($tax_id) && !empty($tax_id)) {
                    $tax = Tax::find($tax_id);
                    $quotation_item_tax = new QuotationItemTax();
                    $quotation_item_tax->loadModel([
                        'quotation_item_id' => $trading_item->id,
                        'tax_id' => ($tax->id ?? null),
                        'total' => $trading_item->sub_total * $tax->value,
                        'value' => ($tax->value ?? 0),
                    ]);

                    try {
                        // Save QuotationItemTax
                        $quotation_item_tax->save();
                    } catch (\Throwable $th) {
                        dd('Error' . $th->getMessage());
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                    }

                    $countMainTax += $quotation_item_tax->total;
                }
            }
        }
        $trading_subtotal_after_tax = $trading_item->sub_total + $countMainTax;
        $trading_item->sub_total_after_tax = $trading_subtotal_after_tax;
        $trading_item->total = $trading_subtotal_after_tax;

        try {
            $trading_item->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }
            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()));
        }


        // * saving add on

        $additional_sub_total = 0;
        $additional_subtotal_after_tax = 0;
        $additional_total = 0;
        if (!is_null($request->quotation_add_on_type_id) && !is_null($request->additional_item) && !is_null($request->additional_price) && !is_null($request->additional_quantity) && !is_null($request->additional_tax_id)) {
            if (
                is_array($request->quotation_add_on_type_id) &&
                count($request->quotation_add_on_type_id) > 0 &&
                count($request->additional_item) > 0 &&
                count($request->additional_price) > 0 &&
                count($request->additional_quantity) > 0 &&
                count($request->additional_tax_id) > 0
            ) {
                foreach ($request->quotation_add_on_type_id as $key => $value) {
                    $add_on = new QuotationItem();
                    $add_on->loadModel([
                        'item_id' => $request->additional_item[$key],
                        'quotation_id' => $model->id,
                        'item_type' => $value,
                        'type' => 'additional',
                        'price' => thousand_to_float($request->additional_price[$key]),
                        'quantity' => thousand_to_float($request->additional_quantity[$key]),
                        'sub_total' => thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_quantity[$key]),
                    ]);

                    try {
                        // Save additional Item
                        $add_on->save();
                    } catch (\Throwable $th) {
                        dd($th->getMessage());
                        DB::rollBack();
                        if ($request->ajax()) {
                            return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                        }

                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
                    }



                    $explode_additional_tax_value = explode(',', $request->additional_tax_value[$key]);
                    $countAdditionalTotalTax = 0;
                    if (is_array($explode_additional_tax_value) && count($explode_additional_tax_value) > 0) {
                        foreach ($explode_additional_tax_value as $value) {
                            $tax = Tax::find($value);
                            if ($tax) {
                                $quotation_item_tax = new QuotationItemTax();
                                $quotation_item_tax->loadModel([
                                    'quotation_item_id' => $add_on->id,
                                    'tax_id' => $tax->id,
                                    'total' => $add_on->sub_total * $tax->value,
                                    'value' => $tax->value,
                                ]);

                                try {
                                    // Save QuotationItemTax
                                    $quotation_item_tax->save();
                                } catch (\Throwable $th) {
                                    dd($th->getMessage());
                                    DB::rollBack();
                                    if ($request->ajax()) {
                                        return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                                    }

                                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
                                }
                                $countAdditionalTotalTax += $quotation_item_tax->total;
                            }
                        }
                        $add_on->sub_total_after_tax = $add_on->sub_total + $countAdditionalTotalTax;
                        $add_on->total = $add_on->sub_total_after_tax;

                        try {
                            // Save QuotationItem
                            $add_on->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            if ($request->ajax()) {
                                return $this->ResponseJsonMessageCRUD(false, 'create', null, $th->getMessage(), 422);
                            }

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()))->withInput();
                        }

                        // Get Aditional Subtotal && Total && Subtotal Aftertax
                        $additional_sub_total += $add_on->sub_total;
                        $additional_subtotal_after_tax += $add_on->sub_total_after_tax;
                        $additional_total += $add_on->total;
                    }
                }
            }
        }

        $model->additional_subtotal = $additional_sub_total;
        $model->additional_sub_total_after_tax = $additional_subtotal_after_tax;
        $model->additional_total = $additional_total;
        $model->sub_total = $trading_item->sub_total;
        $model->total = $trading_item->total + $additional_subtotal_after_tax;
        $model->sub_total_after_tax = $trading_subtotal_after_tax;

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
        $currency = Currency::where('is_local', true)->first();
        if ($request->ajax()) {
            return $this->ResponseJsonData($model->quotationItems);
        }

        return view("admin.$this->view_folder.edit", compact('model', 'currency'));
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
        $model->branch_id = get_current_branch_id();
        $model->customer_id = $request->customer_id;
        $model->date = Carbon::parse($request->date);
        $model->information = $request->information;
        $model->currency_id = $request->currency_id;
        $model->exchange_rate = $request->exchange_rate;

        // * update and make reponse
        try {
            $model->save();
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
        }

        // * Saving Trading Items
        $trading_item = QuotationItem::find($request->id_main);
        $trading_item->item_id = $request->item_id;
        $trading_item->quotation_id = $model->id;
        $trading_item->item_type = 'trading';
        $trading_item->type = 'main';
        $trading_item->price = thousand_to_float($request->price);
        $trading_item->quantity = thousand_to_float($request->quantity);
        $trading_item->sub_total = thousand_to_float($request->price) * thousand_to_float($request->quantity);


        try {
            $trading_item->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
        }


        $trading_subtotal_after_tax = 0;
        // Condition if $request->tax_id not array an length
        $countMainTax = 0;
        if (is_array($request->tax_id_) && !empty($request->tax_id_)) {
            QuotationItemTax::where('quotation_item_id', $trading_item->id)->whereNotIn('tax_id', $request->tax_id_)->delete();
            foreach ($request->tax_id_ as $tax_id) {
                $tax = Tax::find($tax_id);
                if (isset($tax_id) && !empty($tax_id)) {
                    $quotation_item_tax = QuotationItemTax::where('quotation_item_id', $trading_item->id)->where('tax_id', $tax_id)->first();
                    if (is_null($quotation_item_tax)) {
                        $quotation_item_tax = new QuotationItemTax();
                        $quotation_item_tax->loadModel([
                            'quotation_item_id' => $trading_item->id,
                            'tax_id' => $tax->id,
                            'total' => $trading_item->sub_total * $tax->value,
                            'value' => ($tax->value ?? 0),
                        ]);

                        try {
                            // Save QuotationItemTax
                            $quotation_item_tax->save();
                        } catch (\Throwable $th) {
                            dd('Error' . $th->getMessage());
                            DB::rollBack();
                            if ($request->ajax()) {
                                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                            }

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                        }
                    } else {
                        $quotation_item_tax->quotation_item_id = $trading_item->id;
                        $quotation_item_tax->total = $trading_item->sub_total * $tax->value;
                        $quotation_item_tax->value = ($tax->value ?? 0);
                        $quotation_item_tax->tax_id = $tax->id;

                        try {
                            // Save QuotationItemTax
                            $quotation_item_tax->save();
                        } catch (\Throwable $th) {
                            dd('Error' . $th->getMessage());
                            DB::rollBack();
                            if ($request->ajax()) {
                                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                            }

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                        }
                    }

                    $countMainTax += $quotation_item_tax->total;
                }
            }

            $trading_item->sub_total_after_tax = $trading_item->sub_total + $countMainTax;
            $trading_item->total = $trading_item->sub_total + $countMainTax;
            try {
                $trading_item->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                if ($request->ajax()) {
                    return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                }
                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
            }
        } else {
            QuotationItemTax::where('quotation_item_id', $trading_item->id)->delete();
        }


        $trading_subtotal_after_tax = $trading_item->sub_total + $countMainTax;


        // Defined variable additional total dll.
        $additional_sub_total = 0;
        $additional_subtotal_after_tax = 0;
        $additional_total = 0;
        $additional_total_after_tax = 0;

        if (!is_null($request->quotation_add_on_type_id) && !is_null($request->additional_item) && !is_null($request->additional_price) && !is_null($request->additional_quantity)) {
            // * saving add on
            if (
                is_array($request->quotation_add_on_type_id) &&
                count($request->quotation_add_on_type_id) > 0
            ) {
                foreach ($request->quotation_add_on_type_id as $key => $value) {
                    // Check all additional form have value
                    if (
                        !empty($request->additional_item[$key]) && !empty($request->additional_price[$key]) &&
                        !empty($request->additional_quantity[$key]) &&
                        !empty($request->quotation_add_on_type_id[$key])
                    ) {
                        if (isset($request->id_add[$key])) {
                            $add_update = QuotationItem::find($request->id_add[$key]);
                            $add_update->item_id = $request->additional_item[$key];
                            $add_update->quotation_id = $model->id;
                            $add_update->item_type = $value;
                            $add_update->type = 'additional';
                            $add_update->price =  thousand_to_float($request->additional_price[$key]);
                            $add_update->quantity = thousand_to_float($request->additional_quantity[$key]);
                            $add_update->sub_total = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_quantity[$key]);
                            try {
                                // Save additional Item
                                $add_update->update();
                            } catch (\Throwable $th) {
                                dd('Error' . $th->getMessage());
                                DB::rollBack();
                                if ($request->ajax()) {
                                    return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                                }
                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                            }
                        } else {
                            $add_update = new QuotationItem();
                            $add_update->item_id = $request->additional_item[$key];
                            $add_update->quotation_id = $model->id;
                            $add_update->item_type = $value;
                            $add_update->type = 'additional';
                            $add_update->price =  thousand_to_float($request->additional_price[$key]);
                            $add_update->quantity = thousand_to_float($request->additional_quantity[$key]);
                            $add_update->sub_total = thousand_to_float($request->additional_price[$key]) * thousand_to_float($request->additional_quantity[$key]);

                            try {
                                // Save additional Item
                                $add_update->save();
                            } catch (\Throwable $th) {
                                dd('Error' . $th->getMessage());
                                DB::rollBack();
                                if ($request->ajax()) {
                                    return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                                }

                                return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                            }
                        }

                        $additional_tax_total = 0;
                        // Change Request Additional Value To Array
                        $value_tax = explode(',', $request->additional_tax_value[$key]);
                        foreach ($value_tax as $tax_id) {
                            // Condition if $tax_id is empty or undefined
                            if (isset($tax_id) && !empty($tax_id)) {
                                $tax = Tax::find($tax_id);
                                $quotation_item_tax = QuotationItemTax::where('quotation_item_id', $add_update->id)->where('tax_id', $tax_id)->first();
                                // Condition if QuotationItemTax have record same with $tax_id
                                if (is_null($quotation_item_tax)) {
                                    $quotation_item_tax = new QuotationItemTax();
                                    $quotation_item_tax->loadModel([
                                        'quotation_item_id' => $add_update->id,
                                        'tax_id' => $tax->id,
                                        'total' => $add_update->sub_total * $tax->value,
                                        'value' => $tax->value,
                                    ]);

                                    try {
                                        // Save QuotationItemTax
                                        $quotation_item_tax->save();
                                    } catch (\Throwable $th) {
                                        dd('Error' . $th->getMessage());
                                        DB::rollBack();
                                        if ($request->ajax()) {
                                            return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                                        }

                                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                                    }
                                } else {
                                    // $quotation_item_tax = QuotationItemTax::find();
                                    $quotation_item_tax->quotation_item_id = $add_update->id;
                                    $quotation_item_tax->tax_id = $tax->id;
                                    $quotation_item_tax->total = $add_update->sub_total * $tax->value;
                                    $quotation_item_tax->value = $tax->value;

                                    try {
                                        // Save QuotationItemTax
                                        $quotation_item_tax->save();
                                    } catch (\Throwable $th) {
                                        dd('Error' . $th->getMessage());
                                        DB::rollBack();
                                        if ($request->ajax()) {
                                            return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                                        }

                                        return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                                    }
                                }
                                $additional_tax_total += $quotation_item_tax->total;
                            }
                        }

                        $add_update->sub_total_after_tax = $add_update->sub_total + $additional_tax_total;
                        $add_update->total =  $add_update->sub_total + $additional_tax_total;

                        try {
                            $add_update->save();
                        } catch (\Throwable $th) {
                            DB::rollBack();
                            if ($request->ajax()) {
                                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
                            }

                            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
                        }

                        // Get Aditional Subtotal && Total && Subtotal Aftertax
                        $additional_sub_total += $add_update->sub_total;
                        $additional_total += $add_update->total;
                        $additional_subtotal_after_tax += $add_update->sub_total_after_tax;
                        $additional_total_after_tax += $additional_total * $additional_tax_total;
                        // Delete Tax if not exist on request additional Tax
                        $quotation_tax_action = QuotationItemTax::where('quotation_item_id', $add_update->id);
                        if (count($value_tax) > 0) {
                            $quotation_tax_action->whereNotIn('tax_id', $value_tax)->delete();
                        }
                    }
                }
            }
        }

        // Generate Subtotal value, Total Value and Additional All
        $model->additional_subtotal = $additional_sub_total;
        $model->additional_sub_total_after_tax = $additional_subtotal_after_tax;
        $model->additional_total = $additional_total;
        $model->sub_total = $trading_item->sub_total;
        $model->sub_total_after_tax = $trading_subtotal_after_tax;
        $model->total = $trading_item->total + $additional_subtotal_after_tax;

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'update', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', null, $th->getMessage()))->withInput();
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'update'));
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

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'delete'));
    }



    /**
     * Generate Code By Date
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateCodeByDate(Request $request)
    {
        $model = new model();
        if ($request->ajax()) {
            $code2 = $model->customer;
            if ($request->get('customer_id')) {
                $customer = Customer::find($request->get('customer_id'));
                $code2 = $customer;
                $code3 = null;
            } else {
                $code2 = null;
                $code3 = 'CUS';
            }
            $kode = generate_code_with_cus_name(
                model: model::class,
                code: 'QUO',
                code2: $code2,
                date_column: 'date',
                date: $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::now()->format('Y-m-d'),
                code3: $code3 ?? null,
                filter: [],
            );

            return $this->ResponseJsonData($kode, 'Success get code');
        }
    }
}
