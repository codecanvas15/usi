<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPrintAuthorization;
use Illuminate\Http\Request;

class MasterPrintController extends Controller
{
    private $routeView = 'admin.master-print-authorization';
    private $routeName = 'admin.master-print-authorization';

    public function index() 
    {
        return view($this->routeView . '.index');
    }

    public function data()
    {
        $data = MasterPrintAuthorization::query();

        return datatables($data)
            ->addIndexColumn()
            ->editColumn('can_print', function ($data)  {
                $type = request()->type;
                $html = view('admin.master-print-authorization.checkbox', compact('data', 'type'))->render();
                return $html;
            })
            ->rawColumns(['can_print'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'can_print' => 'required|array',
            'can_print.*' => 'required',
        ]);

        foreach ($request->can_print as $key => $value) {
            $model = MasterPrintAuthorization::find($value['id']);
            $model->can_print = $value['can_print'] == 'true' ? 1 : 0;
            $model->save();
        }

        return response()->json(['message' => 'Data berhasil disimpan']);
    }
}
