<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coa;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceReportNeracaController extends Controller
{
    public function get_data($request)
    {
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
                $push_data['balance'] = 0;
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
                $push_data['balance'] = 0;
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
                $push_data['balance'] = 0;
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

    public function get_coa($coa, $date, $indent = 1, $branch_id = null)
    {
        try {
            $parse_date =  '01-' . $date;
            $prev_period =  Carbon::parse($parse_date)->subMonth(1)->format('d-m-Y');

            $main_query = DB::table('journal_details')
                ->join('journals', 'journals.id', 'journal_details.journal_id')
                ->join('coas', 'coas.id', 'journal_details.coa_id')
                ->where('journals.status', 'approve')
                ->whereIn('coa_id', $coa->childs->pluck('id')->toArray())
                ->whereNull('journals.deleted_at')
                ->when($branch_id, fn($q) => $q->where('journals.branch_id', $branch_id))
                ->selectRaw('coas.id as coa_id,(CASE
                                        WHEN coas.normal_balance = "debit" THEN COALESCE(SUM(journal_details.debit_exchanged),0) - COALESCE(SUM(journal_details.credit_exchanged),0)
                                        ELSE COALESCE(SUM(journal_details.credit_exchanged),0) - COALESCE(SUM(journal_details.debit_exchanged),0)
                                    END) AS balance');

            $current_month = clone $main_query;
            $current_month = $current_month
                ->whereDate('journals.date', '<=', Carbon::parse($parse_date)->endOfMonth())
                ->groupBy('coas.id')
                ->get();

            $prev_month = clone $main_query;
            $prev_month = $prev_month
                ->whereDate('journals.date', '<=', Carbon::parse($prev_period)->endOfMonth())
                ->groupBy('coas.id')
                ->get();


            $data = [];
            foreach ($coa->childs->sortBy('account_code') as $key => $child) {
                $push['indent'] = $indent;
                if (count($child->childs) > 0) {
                    $push['name'] = $child->name;
                    $push['code'] = $child->account_code;
                    $push['balance'] = 0;
                    $push['childs'] = $this->get_coa($child, $date, $indent + 1);
                    array_push($data, $push);
                } else {
                    $push['name'] = $child->name;
                    $push['code'] = $child->account_code;
                    $push['balance'] = $current_month->where('coa_id', $child->id)->first()->balance ?? 0;
                    $push['prev_balance'] = $prev_month->where('coa_id', $child->id)->first()->balance ?? 0;
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
                                ->whereDate('journals.date', '<=', Carbon::parse($parse_date)->endOfMonth())
                                ->sum('credit_exchanged');

                            $credit_prev = JournalDetail::where('coa_id', $child->id)
                                ->join('journals', 'journals.id', 'journal_details.journal_id')
                                ->where('journals.status', 'approve')
                                ->whereNull('journals.deleted_at')
                                ->whereDate('journals.date', '<=', Carbon::parse($prev_period)->endOfMonth())
                                ->sum('credit_exchanged');
                        } else {
                            $credit = 0;
                            $credit_prev = 0;
                        }
                        $push['balance'] = $this->get_profit_loss($child, $parse_date, $branch_id) + $credit;
                        $push['prev_balance'] = $this->get_profit_loss($child, $prev_period, $branch_id) + $credit_prev;
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
            ->when($branch_id, fn($q) => $q->where('journals.branch_id', $branch_id))
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
