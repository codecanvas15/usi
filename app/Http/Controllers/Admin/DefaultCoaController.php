<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DefaultCoaController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("permission:view $this->view_folder", ['only' => ['index']]);
        $this->middleware("permission:edit $this->view_folder", ['only' => ['edit', 'update']]);
    }

    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'default-coa';

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
    public function index()
    {
        $data = \App\Models\DefaultCoa::get()->groupBy('type');

        return view("admin.$this->view_folder.index", compact('data'));
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
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $data = \App\Models\DefaultCoa::get()->groupBy('type');

        return view("admin.$this->view_folder.edit", compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'type.*' => 'required|string:100',
            'name.*' => 'required|string:100',
            'coa_id.*' => 'required|exists:coas,id',
        ]);

        DB::beginTransaction();

        foreach ($request->type as $key => $value) {
            // * get data
            $data = \App\Models\DefaultCoa::where('type', $value)
                ->where('name', $request->name[$key])
                ->first();

            // * if data exist and coa changing
            if ($data->coa_id != $request->coa_id[$key] && !is_null($data)) {
                // * create log data
                try {
                    \App\Models\DefaultCoaLog::create([
                        'default_coa_id' => $data->id,
                        'from' => $request->coa_id[$key],
                        'to' => $data->coa_id ?? $request->coa_id[$key],
                        'user_id' => auth()->user()->id,
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'create data log', $th->getMessage()));
                }

                // * update data
                $data->fill([
                    'coa_id' => $request->coa_id[$key],
                ]);

                try {
                    $data->save();
                } catch (\Throwable $th) {
                    DB::rollBack();

                    return redirect()->back()->with($this->ResponseMessageCRUD(false, 'update', 'update data', $th->getMessage()));
                }
            }
        }

        DB::commit();

        return redirect()->route("admin.$this->view_folder.index")->with($this->ResponseMessageCRUD(true, 'update'));
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
