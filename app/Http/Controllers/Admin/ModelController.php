<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModelAuthorization;
use App\Models\ModelAuthorizationBranch;
use App\Models\ModelAuthorizationDivision;
use App\Models\ModelTable as model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Str;

class ModelController extends Controller
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
    protected string $view_folder = 'model';

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
        $groups = model::select('group')->groupBy('group')->get()->pluck('group')->toArray();
        if ($request->ajax()) {
            $data = model::select('*')
                ->when($request->group, function ($query, $group) {
                    return $query->where('group', $group);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('alias', function ($row) {
                    return Str::headline($row->alias);
                })
                ->addColumn('is_complete', function ($row) {
                    if (count($row->model_authorizations) > 0) {
                        return '<span class="badge bg-success">Complete</span>';
                    } else {
                        return '<span class="badge bg-danger">Not Complete</span>';
                    }
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
                                'display' => false,
                            ],
                        ],
                    ]);
                })
                ->rawColumns(['name', 'action', 'is_complete'])
                ->make(true);
        }

        return view('admin.' . $this->view_folder . '.index', compact('groups'));
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $model = model::findOrFail($id);

        $activity_logs = [];
        foreach ($model->model_authorizations as $key => $value) {
            array_push($activity_logs, $value->logs_data['activity_logs'] ?? []);
        }
        if ($request->ajax()) {
            return $this->ResponseJsonData($model);
        }

        $activity_logs = collect($activity_logs)->flatten()->sortByDesc('created_at');

        return view("admin.$this->view_folder.edit", compact('model', 'activity_logs'));
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
        $model->need_to_check_amount = $request->need_to_check_amount ? 1 : 0;
        DB::beginTransaction();

        // * saving and make reponse
        try {
            $model->save();
            $saved_model_authorizations = collect($request->model_authorization_id ?? [])
                ->filter(function ($model_authorization_id) {
                    return $model_authorization_id != null;
                })->toArray();

            $deleted_auth = ModelAuthorization::where('model_id', $model->id)
                ->when($saved_model_authorizations, function ($query, $model_authorization_id) {
                    return $query->whereNotIn('id', $model_authorization_id);
                })
                ->get();

            ModelAuthorizationBranch::whereIn('model_authorization_id', $deleted_auth->pluck('id')->toArray())
                ->delete();

            ModelAuthorizationDivision::whereIn('model_authorization_id', $deleted_auth->pluck('id')->toArray())
                ->delete();

            ModelAuthorization::whereIn('id', $deleted_auth->pluck('id')->toArray())
                ->delete();

            foreach ($request->user_id ?? [] as $key => $user_id) {
                $model_auth = ModelAuthorization::updateOrCreate(
                    [
                        'model_id' => $model->id,
                        'user_id' => $user_id,
                    ],
                    [
                        'model_id' => $model->id,
                        'user_id' => $user_id,
                        'minimum_value' => thousand_to_float($request->minimum_value[$key]),
                        'level' => $request->level[$key],
                        'role' => $request->role[$key],
                    ],
                );

                ModelAuthorizationBranch::where('model_authorization_id', $model_auth->id)
                    ->when(count($request->branch_id[$key] ?? []) > 0, function ($query) use ($request, $key) {
                        return $query->whereNotIn('branch_id', $request->branch_id[$key]);
                    })
                    ->delete();

                foreach ($request->branch_id[$key] ?? [] as $branch_id) {
                    ModelAuthorizationBranch::updateOrCreate(
                        [
                            'model_authorization_id' => $model_auth->id,
                            'branch_id' => $branch_id,
                        ],
                        [
                            'model_authorization_id' => $model_auth->id,
                            'branch_id' => $branch_id,
                        ]
                    );
                }

                ModelAuthorizationDivision::where('model_authorization_id', $model_auth->id)
                    ->when(count($request->division_id[$key] ?? []) > 0, function ($query) use ($request, $key) {
                        return $query->whereNotIn('division_id', $request->division_id[$key]);
                    })
                    ->delete();

                foreach ($request->division_id[$key] ?? [] as $division_id) {
                    ModelAuthorizationDivision::updateOrCreate(
                        [
                            'model_authorization_id' => $model_auth->id,
                            'division_id' => $division_id,
                        ],
                        [
                            'model_authorization_id' => $model_auth->id,
                            'division_id' => $division_id,
                        ]
                    );
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            if ($request->ajax()) {
                return $this->ResponseJsonMessageCRUD(false, 'edit', null, $th->getMessage(), 422);
            }

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'edit', null, $th->getMessage()));
        }

        DB::commit();
        if ($request->ajax()) {
            return $this->ResponseJsonMessageCRUD();
        }

        return redirect()->back()->with($this->ResponseMessageCRUD(true, 'edit'));
    }
}
