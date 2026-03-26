<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceReportProfitLossController extends Controller
{
    public function get_data($type, $request, $multiperiod = false, $year = null)
    {
        $data = [];
        $parse_date =  '01-' . ($request['period'] ?? '');

        try {
            $parents = DB::table('profit_loss_categories')
                ->whereNull('deleted_at')
                ->get();

            foreach ($parents as $key => $parent) {
                $parent_data = [];

                $categories = DB::table('profit_loss_subcategories')
                    ->orderBy('order')
                    ->whereNull('deleted_at')
                    ->where('profit_loss_category_id', $parent->id)
                    ->get();

                foreach ($categories as $key => $category) {
                    $category_data = [];

                    $details = DB::table('profit_loss_details')
                        ->join('coas', 'coas.id', 'profit_loss_details.coa_id')
                        ->whereExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('journal_details')
                                ->whereColumn('journal_details.coa_id', 'coas.id');
                        })
                        ->whereNull('profit_loss_details.deleted_at')
                        ->where('profit_loss_details.profit_loss_subcategory_id', $category->id)
                        ->orderBy('coas.account_code', 'asc')
                        ->get();

                    foreach ($details as $key => $detail) {
                        $coa = DB::table('coas')->where('id', $detail->coa_id)
                            ->first();

                        if ($multiperiod) {
                            $push_category['code'] = $coa->account_code;
                            $push_category['coa'] = $coa->name;
                            for ($i = 1; $i <= 12; $i++) {
                                $parse_date = '01-' . $i . '-' . $year;
                                $push_category['data'][$i] = $this->get_coa($detail->coa_id, $parse_date, false, isset($request['branch_id']), $i);
                            }

                            array_push($category_data, $push_category);
                        } else {
                            $current_balance = $this->get_coa($detail->coa_id, $parse_date, false, isset($request['branch_id']));

                            $prev_balance = $this->get_coa($detail->coa_id, $parse_date, true, isset($request['branch_id']));
                            $push_category['code'] = $coa->account_code;

                            if ($current_balance != 0 || $prev_balance != 0) {
                                $push_category['coa'] = $coa->name;
                                $push_category['current_period'] = $current_balance;
                                $push_category['prev_period'] = $prev_balance;

                                array_push($category_data, $push_category);
                            }
                        }
                    }

                    $parent_data[$category->name]['data'] = $category_data;
                    $parent_data[$category->name]['type'] = $category->type;
                }
                $data[$parent->name] = $parent_data;
            }

            $throw_data['type'] = $type;
            if ($multiperiod) {
                $throw_data['type'] = 'laba rugi multiperiod';
                $throw_data['period'] = $year;
            } else {
                $throw_data['period'] = Carbon::parse('01-' . $request->period)->translatedFormat('F Y');
            }
            $throw_data['data'] = $data;
            $throw_data['branch'] = Branch::find(isset($request['branch_id']));

            return $throw_data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_coa($coa, $date, $calculate_from_beginning_year = false, $branch_id = null)
    {
        try {
            $main_query = DB::table('journal_details')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->join('coas', 'coas.id', 'journal_details.coa_id')
                ->where('journals.status', 'approve')
                ->when($branch_id, function ($query) use ($branch_id) {
                    return $query->where('journals.branch_id', $branch_id);
                })
                ->where('coa_id', $coa)
                ->whereNull('journals.deleted_at')
                ->selectRaw('(CASE
                                WHEN coas.normal_balance = "debit" THEN COALESCE(SUM(journal_details.debit_exchanged),0) - COALESCE(SUM(journal_details.credit_exchanged),0)
                                ELSE COALESCE(SUM(journal_details.credit_exchanged),0) - COALESCE(SUM(journal_details.debit_exchanged),0)
                            END) AS balance');

            if ($calculate_from_beginning_year) {
                $main_query = $main_query->whereMonth('journals.date', '<=', Carbon::parse($date));
            } else {
                $main_query = $main_query->whereMonth('journals.date', Carbon::parse($date));
            }

            $main_query = $main_query->whereYear('journals.date', Carbon::parse($date))
                ->first();

            return $main_query->balance;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
