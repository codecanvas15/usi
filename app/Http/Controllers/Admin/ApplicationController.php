<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application as model;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * where the view will be displayed
     *
     * @var string
     */
    protected string $view_folder = 'application';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = '';
        $is_expiry = false;
        $date_now = Carbon::now()->format('Y-m-d');
        $model = null;

        if ($request->id) {
            $id = $request->id;

            $model = model::where('token', $id)->first();

            if ($model) {
                return Carbon::parse(Carbon::now())->diffInDays($model->expiry);
            }
        }

        return view("admin.$this->view_folder.index", compact('id', 'is_expiry', 'model'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view("$this->view_folder.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'branch_id' => 'nullable|exists:branches,id',
        //     'employee_id' => 'nullable|exists:employees,id',
        //     'labor_demand_detail_id' => 'required|exists:labor_demand_details,id',
        //     'date' => 'required|date',
        //     'name' => 'nullable|string|max:255',
        //     'email' => 'nullable|email|max:255',
        //     'address' => 'nullable|string|max:255',
        //     'phone' => 'nullable|string|max:255',
        //     'date_of_birth' => 'nullable|date',
        //     'place_of_birth' => 'nullable',
        //     'religion' => 'nullable|string|max:60',
        //     'gender' => 'nullable',
        //     'marital_status' => 'nullable',
        //     'identity_card_number' => 'nullable',

        //     'file_type' => 'nullable|array',
        //     'file_path' => 'nullable|array',
        //     'file_type.*' => 'nullable|string|max:255',
        //     'file_path.*' => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',

        //     'emergency_contact_names' => 'nullable|array',
        //     'emergency_contact_relationships' => 'nullable|array',
        //     'emergency_contact_phones' => 'nullable|array',
        //     'emergency_contact_addresses' => 'nullable|array',

        //     'emergency_contact_names.*' => 'nullable|string|max:100',
        //     'emergency_contact_relationships.*' => 'nullable|string|max:100',
        //     'emergency_contact_phones.*' => 'nullable|string|max:100',
        //     'emergency_contact_addresses.*' => 'nullable|string|max:100',
        // ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = [];

        // return view("$this->view_folder.show", compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = [];

        // return view("$this->view_folder.show", compact('model'));
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
    }
}
