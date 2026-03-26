<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->type as $key => $type) {
                $is_numeric = $request->is_numeric ? true : false;
                Setting::updateOrCreate(
                    [
                        'type' => $type,
                        'name' => $request->name[$key],
                    ],
                    [
                        'type' => $type,
                        'name' => $request->name[$key],
                        'value' => $is_numeric ? thousand_to_float($request->value[$key]) : $request->value[$key],
                    ]
                );
            }
        } catch (\Throwable $th) {
            DB::rollback();

            return redirect()->back()->with($this->ResponseMessageCRUD(false, 'create', null, $th->getMessage()));
        }

        DB::commit();
        return redirect()->back()->with($this->ResponseMessageCRUD());
    }
}
