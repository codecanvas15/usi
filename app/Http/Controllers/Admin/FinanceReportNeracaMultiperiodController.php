<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceReportNeracaMultiperiodController extends Controller
{
    public function get_data($request)
    {
        $default_balance[1] = 0;
        $default_balance[2] = 0;
        $default_balance[3] = 0;
        $default_balance[4] = 0;
        $default_balance[5] = 0;
        $default_balance[6] = 0;
        $default_balance[7] = 0;
        $default_balance[8] = 0;
        $default_balance[9] = 0;
        $default_balance[10] = 0;
        $default_balance[11] = 0;
        $default_balance[12] = 0;

        try {
            $data = [];
            // ! GET ACTIVA DATA
            $activa_accounts = Coa::where('account_category', 'activa')
                ->whereDoesntHave('parent')
                ->get();

            $return_activa_account = [];
            foreach ($activa_accounts as $key => $activa_account) {
                $push_data['indent'] = 0;
                $push_data['name'] = $activa_account->name;
                $push_data['code'] = $activa_account->account_code;
                $push_data['balance'] = $default_balance;
                $push_data['childs'] = $this->get_coa($activa_account, $request->period, $request->branch_id ?? null);

                array_push($return_activa_account, $push_data);
            }

            // ! GET PASIVA DATA
            $pasiva_accounts = Coa::where('account_category', 'pasiva')
                ->whereDoesntHave('parent')
                ->get();

            $return_pasiva_account = [];
            foreach ($pasiva_accounts as $key => $pasiva_account) {
                $push_data['indent'] = 0;
                $push_data['name'] = $pasiva_account->name;
                $push_data['code'] = $pasiva_account->account_code;
                $push_data['balance'] = $default_balance;
                $push_data['childs'] = $this->get_coa($pasiva_account, $request->period, $request->branch_id ?? null);

                array_push($return_pasiva_account, $push_data);
            }

            // ! GET EQUITY DATA
            $equity_accounts = Coa::where('account_category', 'equity')
                ->whereDoesntHave('parent')
                ->get();

            $return_equity_account = [];
            foreach ($equity_accounts as $key => $equity_account) {
                $push_data['indent'] = 0;
                $push_data['name'] = $equity_account->name;
                $push_data['code'] = $equity_account->account_code;
                $push_data['balance'] = $default_balance;
                $push_data['childs'] = $this->get_coa($equity_account, $request->period, $request->branch_id ?? null);

                array_push($return_equity_account, $push_data);
            }

            $data['aktiva']['childs'] = $return_activa_account;
            $data['kewajiban_dan_ekuitas']['childs'] = array_merge($return_pasiva_account, $return_equity_account);

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_coa($coa, $period, $indent = 1, $branch_id = null)
    {
        try {
            $main_query = DB::table('journal_details')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->join('coas', 'coas.id', 'journal_details.coa_id')
                ->where('journals.status', 'approve')
                ->whereIn('coa_id', $coa->childs->pluck('id')->toArray())
                ->whereNull('journals.deleted_at')
                ->when($branch_id, fn ($q) => $q->where('journals.branch_id', $branch_id))
                ->selectRaw('coas.id as coa_id,(CASE
                                        WHEN coas.normal_balance = "debit" THEN COALESCE(SUM(journal_details.debit_exchanged),0) - COALESCE(SUM(journal_details.credit_exchanged),0)
                                        ELSE COALESCE(SUM(journal_details.credit_exchanged),0) - COALESCE(SUM(journal_details.debit_exchanged),0)
                                    END) AS balance')
                ->whereYear('journals.date', '<=', $period)
                ->groupBy('coa_id');

            $data = [];
            $default_balance[1] = 0;
            $default_balance[2] = 0;
            $default_balance[3] = 0;
            $default_balance[4] = 0;
            $default_balance[5] = 0;
            $default_balance[6] = 0;
            $default_balance[7] = 0;
            $default_balance[8] = 0;
            $default_balance[9] = 0;
            $default_balance[10] = 0;
            $default_balance[11] = 0;
            $default_balance[12] = 0;
            foreach ($coa->childs->sortBy('account_code') as $key => $child) {
                $push['indent'] = $indent;
                if (count($child->childs) > 0) {
                    $push['name'] = $child->name;
                    $push['code'] = $child->account_code;
                    $push['balance'] = $default_balance;
                    $push['childs'] = $this->get_coa($child, $period, $indent + 1);
                    array_push($data, $push);
                } else {
                    $push['name'] = $child->name;
                    $push['code'] = $child->account_code;
                    $balances = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $loop_period = Carbon::parse($period . '-' . $i . '-01')->endOfMonth();
                        if (Carbon::now()->endOfMonth()->gte($loop_period)) {
                            $clone_query = clone $main_query;
                            $clone_query = $clone_query->whereDate('journals.date', '<=', $loop_period)
                                ->where('coa_id', $child->id)
                                ->first()->balance ?? 0;
                        } else {
                            $clone_query = 0;
                        }

                        $balances[$i] = $clone_query;
                    }

                    $push['balance'] = $balances;
                    $push['childs'] = [];

                    if (in_array(strtolower($child->name), [
                        "laba ditahan",
                        "laba bulan ini",
                        "laba tahun berjalan",
                    ])) {
                        if (strtolower($child->name) == "laba ditahan") {
                            $credit = JournalDetail::where('coa_id', $child->id)
                                ->join('journals', 'journals.id', 'journal_details.journal_id')
                                ->where('journals.status', 'approve')
                                ->whereNull('journals.deleted_at')
                                ->sum('credit_exchanged');
                        } else {
                            $credit = 0;
                        }

                        $balances = [];
                        for ($i = 1; $i <= 12; $i++) {
                            $loop_period = Carbon::parse($period . '-' . $i . '-01');
                            if (Carbon::now()->endOfMonth()->gte($loop_period)) {
                                $clone_query = $this->get_profit_loss($child, $loop_period, $branch_id) + $credit;
                            } else {
                                $clone_query = 0;
                            }

                            $balances[$i] = $clone_query;
                        }

                        $push['balance'] = $balances;
                    }

                    array_push($data, $push);
                }
            }

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_profit_loss($coa, $parse_date, $branch_id = null)
    {
        $profit_loss = 0;

        $profit_loss_details = DB::table('profit_loss_details')
            ->join('profit_loss_subcategories', 'profit_loss_subcategories.id', 'profit_loss_details.profit_loss_subcategory_id')
            ->whereNull('profit_loss_details.deleted_at')
            ->select('profit_loss_details.*', 'profit_loss_subcategories.type')
            ->get();

        $main_query =  DB::table('journal_details')
            ->join('journals', 'journals.id', 'journal_details.journal_id')
            ->join('coas', 'coas.id', 'journal_details.coa_id')
            ->when($branch_id, fn ($q) => $q->where('journals.branch_id', $branch_id))
            ->whereIn('coas.id', $profit_loss_details->pluck('coa_id')->toArray())
            ->where('journals.status', 'approve')
            ->whereNull('journals.deleted_at')
            ->selectRaw('coas.id as coa_id, (CASE
                                WHEN coas.normal_balance = "debit" THEN COALESCE(SUM(journal_details.debit_exchanged),0) - COALESCE(SUM(journal_details.credit_exchanged),0)
                                ELSE COALESCE(SUM(journal_details.credit_exchanged),0) - COALESCE(SUM(journal_details.debit_exchanged),0)
                            END) AS balance');

        if (strtolower($coa->name) == "laba ditahan") {
            $previous_year = Carbon::parse($parse_date)->format('Y') - 1;
            $main_query = $main_query->whereYear('journals.date', '<=', $previous_year);
        }
        if (strtolower($coa->name) == "laba bulan ini") {
            $main_query = $main_query->whereMonth('journals.date', Carbon::parse($parse_date))
                ->whereYear('journals.date', Carbon::parse($parse_date));
        }
        if (strtolower($coa->name) == "laba tahun berjalan") {
            $main_query = $main_query->whereMonth('journals.date', '<', Carbon::parse($parse_date))
                ->whereYear('journals.date', Carbon::parse($parse_date));
        }

        $main_query = $main_query->groupBy('coas.id')
            ->get();

        foreach ($profit_loss_details as $key => $profit_loss_detail) {
            $amount = $main_query->where('coa_id', $profit_loss_detail->coa_id)->first();

            if ($amount) {
                if ($profit_loss_detail->type == 'plus') {
                    $profit_loss += $amount->balance;
                } else {
                    $profit_loss -= $amount->balance;
                }
            }
        }

        return $profit_loss;
    }
}
