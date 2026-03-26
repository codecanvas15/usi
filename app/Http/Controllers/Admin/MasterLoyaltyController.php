<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MasterLoyaltys;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class MasterLoyaltyController extends Controller
{
    public function index()
    {
        return view('admin.master-loyalty.index');
    }

    public function data(Request $request)
    {
        $data = MasterLoyaltys::with('branch');

        return datatables($data)
            ->addColumn('branch', function ($row) {
                return ucfirst($row->branch->name);
            })
            ->addColumn('new_tab', function ($row) {
                $button = '<a class="btn btn-primary btn-sm" href="' . route('admin.master-loyalty.show', ['master_loyalty' => $row->id]) . '" target="_blank"><i class="fa fa-external-link"></i></a>';

                return $button;
            })
            ->escapeColumns([])
            ->make();
    }


    public function create()
    {
        return view('admin.master-loyalty.create');
    }


    public function store(Request $request)
    {
        $validated_input = $request->validate([
            'nilai_bawah' => ['required'],
            'nilai_atas' => ['required'],
            'bonus' => ['required'],
        ]);
        DB::beginTransaction();
        try {
            $master_loyaltys = new MasterLoyaltys();
            $master_loyaltys->branch_id = Auth::user()->branch_id ?? Auth::user()->temp_branch_id;
            $master_loyaltys->nilai_bawah = $request->nilai_bawah;
            $master_loyaltys->nilai_atas = $request->nilai_atas;
            $master_loyaltys->bonus = replaceComma($request->bonus);
            $master_loyaltys->save();

            DB::commit();

            return redirect()->route('admin.master-loyalty.index')->with(['message' => 'Berhasil menambahkan data loyalty reward']);
        } catch (\Throwable $th) {
            throw $th;
            return redirect()->route('admin.master-loyalty.index')->with(['message' => 'Gagal menambahkan data loyalty reward']);
        }
    }


    public function show($id)
    {
        $data = MasterLoyaltys::where('id', $id)->first();
        return view('admin.master-loyalty.show', compact('data'));
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $master_loyaltys = MasterLoyaltys::find($id);
            $master_loyaltys->branch_id = Auth::user()->branch_id ?? Auth::user()->temp_branch_id;
            $master_loyaltys->nilai_bawah = $request->nilai_bawah;
            $master_loyaltys->nilai_atas = $request->nilai_atas;
            $master_loyaltys->bonus = replaceComma($request->bonus);
            $master_loyaltys->save();


            $log = Auth::user()->name . " mengubah master Loyalty  " . $master_loyaltys->nilai_atas . " - " . $master_loyaltys->nilai_bawah . " Tahun";
            DB::commit();

            return redirect()->route('admin.master-loyalty.index')->with(['message' => 'Berhasil memperabarui data loyalty reward']);
        } catch (\Throwable $th) {
            throw $th;
            return redirect()->route('admin.master-loyalty.index')->with(['message' => 'Gagal memperabarui data loyalty reward']);
        }
    }


    public function destroy($id)
    {
        try {
            $master_loyaltys = MasterLoyaltys::find($id);

            $log = Auth::user()->name . " menhapus master Loyalty  " . $master_loyaltys->nilai_atas . " - " . $master_loyaltys->nilai_bawah . " Tahun";

            $master_loyaltys->delete();

            return redirect()->route('admin.master-loyalty.index')->with(['message' => 'Berhasil menghapus data loyalty reward.']);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => 'Gagal menghapus loyalty reward.']);
        }
    }
}
