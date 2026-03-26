<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceReportNeracaSaldoController extends Controller
{
    public function get_data($request)
    {
        try {
            $data = [];

            // ! GET ACCOUNT
            $q_accounts = Coa::whereDoesntHave('parent')
                ->where('deleted_at', null)
                ->orderBy('account_code')
                ->get();

            $accounts = [];
            foreach ($q_accounts as $key => $account) {
                $push_data['indent'] = 0;
                $push_data['name'] = $account->name;
                $push_data['code'] = $account->account_code;
                $push_data['debit'] = 0;
                $push_data['credit'] = 0;
                $push_data['balance'] = 0;
                $push_data['childs'] = $this->get_coa($account, $request->period, $request->branch_id ?? null);

                array_push($accounts, $push_data);
            }

            $data['account']['childs'] = $accounts;

            return $data;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_coa($coa, $date, $indent = 1, $branch_id = null)
    {
        try {
            $parse_date =  '01-' . $date;

            $main_query = DB::table('journal_details')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->join('coas', 'coas.id', 'journal_details.coa_id')
                ->where('journals.status', 'approve')
                ->whereIn('coa_id', $coa->childs->pluck('id')->toArray())
                ->whereNull('journals.deleted_at')
                ->when($branch_id, fn ($q) => $q->where('journals.branch_id', $branch_id))
                ->selectRaw('coas.id as coa_id,
                    COALESCE(SUM(journal_details.debit_exchanged),0) as total_debit,
                    COALESCE(SUM(journal_details.credit_exchanged),0) as total_credit,
                    (CASE
                        WHEN coas.normal_balance = "debit" THEN COALESCE(SUM(journal_details.debit_exchanged),0) - COALESCE(SUM(journal_details.credit_exchanged),0)
                        ELSE COALESCE(SUM(journal_details.credit_exchanged),0) - COALESCE(SUM(journal_details.debit_exchanged),0)
                    END) AS balance
                ');

            $current_month = clone $main_query;
            $current_month = $current_month
                ->whereDate('journals.date', '>=', Carbon::parse($parse_date)->startOfMonth())
                ->whereDate('journals.date', '<=', Carbon::parse($parse_date)->endOfMonth())
                ->groupBy('coas.id')
                ->get();

            $data = [];
            foreach ($coa->childs->sortBy('account_code') as $key => $child) {
                $push['indent'] = $indent;
                if (count($child->childs) > 0) {
                    $push['name'] = $child->name;
                    $push['code'] = $child->account_code;
                    $push['debit'] = 0;
                    $push['credit'] = 0;
                    $push['balance'] = 0;
                    $push['childs'] = $this->get_coa($child, $date, $indent + 1);
                    array_push($data, $push);
                } else {
                    $push['name'] = $child->name;
                    $push['code'] = $child->account_code;
                    $push['debit'] = $current_month->where('coa_id', $child->id)->first()->total_debit ?? 0;
                    $push['credit'] = $current_month->where('coa_id', $child->id)->first()->total_credit ?? 0;
                    $push['balance'] = $current_month->where('coa_id', $child->id)->first()->balance ?? 0;
                    $push['childs'] = [];

                    array_push($data, $push);
                }
            }

            return $data;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
