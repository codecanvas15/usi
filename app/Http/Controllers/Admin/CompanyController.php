<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Supports\CompanyProfileSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index()
    {
        $company = Company::first();

        // if (is_null($company)) {
        //     Artisan::call('db:seed', ['--class' => 'CompanySeeder']);
        //     $company = Company::first();
        // }

        return view('admin.company.index', [
            'company' => $company,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:255|string',
            'short_name' => 'required|max:255|string',
            'address' => 'required|max:255|string',
            'phone' => 'required|max:255|string',
            'fax' => 'required|max:255|string',
            'email' => 'nullable|max:255|string',
            'website' => 'nullable|max:255|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'secondary_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $oldFile = $company->logo;
        $oldFileSecondary = $company->secondary_logo;

        DB::beginTransaction();

        $company->fill($request->except('logo'));

        if ($request->hasFile('logo')) {
            $company->logo = $this->upload_file($request->file('logo'), 'company');
        }

        if ($request->hasFile('secondary_logo')) {
            $company->secondary_logo = $this->upload_file($request->file('secondary_logo'), 'company');
        }

        try {
            $company->save();
        } catch (\Exception $e) {
            DB::rollback();

            if ($request->hasFile('logo')) {
                $this->delete_file($company->logo ?? '');
            }

            if ($request->hasFile('secondary_logo')) {
                $this->delete_file($company->secondary_logo ?? '');
            }

            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($request->hasFile('logo')) {
            $this->delete_file($oldFile);
        }

        if ($request->hasFile('secondary_logo')) {
            $this->delete_file($oldFileSecondary ?? '');
        }

        // update company cache
        $companySupport = new CompanyProfileSupport();
        $companySupport->renewCompany();

        DB::commit();

        return redirect()->route('admin.company.index')->with('success', 'Company updated successfully');
    }
}
