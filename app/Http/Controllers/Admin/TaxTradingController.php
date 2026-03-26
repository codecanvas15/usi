<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxTradingController extends Controller
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
    protected string $view_folder = 'tax-trading';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = \App\Models\TaxTrading::orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        return view("admin.$this->view_folder.index", compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $model = \App\Models\TaxTrading::findOrFail($id);

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
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'coa_sale_id' => 'required|exists:coas,id',
            'coa_purchase_id' => 'required|exists:coas,id',
            'value' => 'required'
        ]);

        $model = \App\Models\TaxTrading::findOrFail($id);

        DB::beginTransaction();

        $model->fill([
            'name' => $request->name,
            'coa_sale_id' => $request->coa_sale_id,
            'coa_purchase_id' => $request->coa_purchase_id,
            'value' => thousand_to_float($request->value ?? 0) / 100,
            'type' => $request->type,
            'is_show_percent' => $request->is_show_percent ? 1 : 0,
        ]);

        try {
            $model->save();
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', 'Tax Trading', $th->getMessage()));
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'edit', 'Tax Trading'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
