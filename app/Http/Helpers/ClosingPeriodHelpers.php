<?php

namespace App\Http\Helpers;

use App\Models\ClosingPeriod;
use App\Models\ClosingPeriodCurrency;
use App\Models\Coa;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClosingPeriodHelpers
{
    /**
     * List of coa ids
     */
    private array $coaIds = [];

    /**
     * Closing period data
     */
    private $closingPeriodData;

    public function __construct() {}

    public function execute($id)
    {
        $this->getClosingPeriod($id);
        $this->getCoaWhereCurrencyNotNull();
        $this->getJournalsBalance($id);
    }

    private function getClosingPeriod($id)
    {
        $this->closingPeriodData = \App\Models\ClosingPeriod::findOrFail($id);
    }

    private function getCoaWhereCurrencyNotNull(): void
    {
        $coas = Coa::whereHas('currency', function ($query) {
            $query->where('is_local', 0);
        })
            ->where('account_type', 'Cash & Bank')
            ->where('is_parent', 0)
            ->whereNotNull('currency_id')
            ->get(['id']);

        $this->coaIds = $coas->pluck('id')->toArray();
    }

    private function getJournalsBalance($id)
    {
        $local_currency = get_local_currency();
        $journal_details =  DB::table('journal_details')
            ->join('journals', 'journals.id', 'journal_details.journal_id')
            ->join('coas', 'coas.id', 'journal_details.coa_id')
            ->join('currencies', 'currencies.id', 'coas.currency_id')
            ->where('journal_details.currency_id', '!=', $local_currency->id)
            ->select(
                'journal_details.*',
                'journals.date',
                'journals.status',
                'coas.currency_id',
                'coas.normal_balance',
                'currencies.nama as currency_name',
                'currencies.simbol as currency_symbol',
                'journal_details.exchange_rate'
            )
            ->whereIn('coas.id', $this->coaIds)
            ->where('journals.status', 'approve')
            ->whereDate('journals.date', '>=', Carbon::parse($this->closingPeriodData->from_date))
            ->whereDate('journals.date', '<=', Carbon::parse($this->closingPeriodData->to_date))
            ->whereNull('journals.deleted_at')
            ->get();

        $closing = ClosingPeriod::find($id);
        $closing_currencies = ClosingPeriodCurrency::where('closing_period_id', $id)->get();

        $data_mutation = [];
        $journal_details->map(function ($journal_detail) use ($closing_currencies, &$data_mutation) {
            if ($journal_detail->debit != 0) {
                if ($journal_detail->normal_balance == 'debit') {
                    $amount = $journal_detail->debit;
                    $local_amount = $journal_detail->debit * $journal_detail->exchange_rate;
                } else {
                    $amount = $journal_detail->debit * -1;
                    $local_amount = $journal_detail->debit * $journal_detail->exchange_rate * -1;
                }
            } else {
                if ($journal_detail->normal_balance == 'credit') {
                    $amount = $journal_detail->credit;
                    $local_amount = $journal_detail->credit * $journal_detail->exchange_rate;
                } else {
                    $amount = $journal_detail->credit * -1;
                    $local_amount = $journal_detail->credit * $journal_detail->exchange_rate * -1;
                }
            }

            $closing_exchange_rate = $closing_currencies->where('currency_id', $journal_detail->currency_id)->first()->exchange_rate;
            $exchange_rate_gap = $closing_exchange_rate - $journal_detail->exchange_rate;
            $data_mutation[] = [
                'coa_id' => $journal_detail->coa_id,
                'currency_id' => $journal_detail->currency_id,
                'normal_balance' => $journal_detail->normal_balance,
                'exchange_rate' => $journal_detail->exchange_rate,
                'closing_exchange_rate' => $closing_exchange_rate,
                'exchange_rate_gap' => $exchange_rate_gap,
                'journal_amount' => $amount,
                'journal_local_amount' => $local_amount,
                'closing_local_amount' => $amount * $closing_exchange_rate,
                'closing_gap_local_amount' => $exchange_rate_gap * $amount,
                'closing_gap_amount' => number_format($exchange_rate_gap * $amount / $closing_exchange_rate, 2, '.', ''),
                'amount_after_gap' => number_format($amount + ($exchange_rate_gap * $amount / $closing_exchange_rate), 2, '.', ''),
            ];
        });

        $exchange_rate_gap_coa = get_default_coa('finance', 'Exchange Rate Gap');

        // sum data mutation group by coa_id
        $data_mutation = collect($data_mutation);
        // select unique coa id from data mutation
        $unique_coa_id = $data_mutation->unique('coa_id')->pluck('coa_id')->toArray();
        $final_data_mutation = [];

        // sum data mutation group by coa_id
        collect($unique_coa_id)->map(function ($coa) use ($data_mutation, &$final_data_mutation) {
            if ($data_mutation->where('coa_id', $coa)->first()['closing_gap_local_amount'] != 0) {
                $final_data_mutation[] = [
                    'coa_id' => $coa,
                    'currency_id' => $data_mutation->where('coa_id', $coa)->first()['currency_id'],
                    'normal_balance' => $data_mutation->where('coa_id', $coa)->first()['normal_balance'],
                    'closing_exchange_rate' => $data_mutation->where('coa_id', $coa)->first()['closing_exchange_rate'],
                    'exchange_rate_gap' => $data_mutation->where('coa_id', $coa)->sum('exchange_rate_gap'),
                    'journal_amount' => $data_mutation->where('coa_id', $coa)->sum('journal_amount'),
                    'journal_local_amount' => $data_mutation->where('coa_id', $coa)->sum('journal_local_amount'),
                    'closing_local_amount' => $data_mutation->where('coa_id', $coa)->sum('closing_local_amount'),
                    'closing_gap_local_amount' => $data_mutation->where('coa_id', $coa)->sum('closing_gap_local_amount'),
                    'closing_gap_amount' => $data_mutation->where('coa_id', $coa)->sum('closing_gap_amount'),
                    'amount_after_gap' => $data_mutation->where('coa_id', $coa)->sum('amount_after_gap')
                ];
            }
        });

        foreach ($final_data_mutation as $key => $final_mutation) {
            $journal = new Journal();
            $exchange_rate = $final_mutation['closing_exchange_rate'];
            $journal->loadModel([
                'branch_id' => Auth::user()->branch_id,
                'date' => Carbon::parse($closing->to_date),
                'remark' => "closing " . Carbon::parse($closing->to_date)->translatedFormat('F Y'),
                'document_reference' => [
                    'id' => $closing->id,
                    'model' => get_class($closing),
                    'code' => '-',
                    'link' => route('admin.closing-period.show', ['closing_period' => $closing->id]),
                ],
                'journal_type' => "Closing Period",
                'exchange_rate' => $exchange_rate,
                'currency_id' => $final_mutation['currency_id'],
                'created_by' => auth()->user()->id,
                'reference_model' => ClosingPeriod::class,
                'reference_id' => $closing->id,
                'is_generated' => true,
                'status' => 'approve',
            ]);
            $journal->save();

            $journal->journal_details()->create([
                'reference_id' => $journal->id,
                'reference_model' => ClosingPeriod::class,
                'coa_id' => $final_mutation['coa_id'],
                'debit' => $final_mutation['normal_balance'] == 'credit' ? ($final_mutation['journal_local_amount'] / $exchange_rate) : 0,
                'credit' => $final_mutation['normal_balance'] == 'debit' ? ($final_mutation['journal_local_amount'] / $exchange_rate) : 0,
                'remark' => "closing " . Carbon::parse($closing->to_date)->translatedFormat('F Y'),
            ]);

            $journal->journal_details()->create([
                'reference_id' => $journal->id,
                'reference_model' => ClosingPeriod::class,
                'coa_id' => $final_mutation['coa_id'],
                'debit' => $final_mutation['normal_balance'] == 'debit' ? ($final_mutation['closing_local_amount'] / $exchange_rate) : 0,
                'credit' => $final_mutation['normal_balance'] == 'credit' ? ($final_mutation['closing_local_amount'] / $exchange_rate) : 0,
                'remark' => "closing " . Carbon::parse($closing->to_date)->translatedFormat('F Y'),
            ]);

            $credit = $final_mutation['closing_gap_local_amount'];
            $journal->journal_details()->create([
                'reference_id' => $journal->id,
                'reference_model' => ClosingPeriod::class,
                'coa_id' => $exchange_rate_gap_coa->coa_id,
                'debit' => $final_mutation['normal_balance'] == "credit" ? ($credit / $exchange_rate) : 0,
                'credit' => $final_mutation['normal_balance'] == "debit" ? ($credit / $exchange_rate) : 0,
                'remark' => "closing " . Carbon::parse($closing->to_date)->translatedFormat('F Y'),
            ]);

            $journal->update([
                'debit_total' => $journal->journal_details()->sum('debit'),
                'credit_total' => $journal->journal_details()->sum('credit'),
            ]);
        }

        return $final_data_mutation;
    }
}
