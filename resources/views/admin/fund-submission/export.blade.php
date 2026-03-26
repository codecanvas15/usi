<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDK {{ $model->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 8pt;
            color: #000;
        }

        @page {
            margin: 28px;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        .table tr th,
        .table tr td {
            padding: 2px 4px;
        }
    </style>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @include('components.print_out_header')
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%; margin-bottom: 5px">
            <div class="text-center">
                <h3 style="margin-bottom: 0px">PERMINTAAN DANA KELUAR</h3>
                <b>{{ $model->code }}</b>
            </div>
        </div>
        <div>
            <table>
                <tr>
                    <td class="p-0" width="43%">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="valign-top small-font">Bank & Rekening</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font">
                                        @if ($model->item != 'dp')
                                            {{ $model->coa?->name }}
                                        @else
                                            {{ $model->cash_advance_cash_bank->coa->name ?? '' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">No. C/BG</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font"> {{ $model->send_payment->cheque_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">Tgl. JT</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font">
                                        {{ $model->is_giro ? localDate($model->send_payment->due_date) : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">
                                        No. Reff
                                    </td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font">
                                        {{ $model->reference ?? '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="valign-top small-font" width="10%"></td>
                    <td class="valign-top p-0" width="45%">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="valign-top small-font" width="25%">Tanggal</td>
                                    <td class="valign-top small-font" width="2%">:</td>
                                    <td class="valign-top small-font" width="73%"> {{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">Dibayar Kpd</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font"> {{ $model->to_name }}</td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">Bank Vendor</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font">
                                        @foreach ($vendor->vendor_banks ?? [] as $vendor_bank)
                                            <p class="mt-0 mb-0">
                                                {{ $vendor_bank->name }} {{ $vendor_bank->account_number }}<br>
                                                a/n {{ $vendor_bank->behalf_of }}
                                            </p>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row">
            <table class="table table-bordered">
                <tr>
                    <th class="small-font"><b>No. Perkiraan</b></th>
                    <th class="small-font"><b>No. LPB</b></th>
                    <th class="small-font"><b>No. Invoice</b></th>
                    <th class="small-font"><b>Keterangan</b></th>
                    <th class="small-font"><b>TOP</b></th>
                    <th class="small-font"><b>Nilai</b></th>
                </tr>
                @if ($model->item == 'general')
                    @foreach ($model->fund_submission_generals as $item)
                        <tr>
                            <td class="small-font text-center">{{ $item->coa->account_code }}</td>
                            <td class="small-font text-center">{{ '' }}</td>
                            <td class="small-font text-center">{{ '' }}</td>
                            <td class="small-font">{{ $item->note }}</td>
                            <td class="small-font text-center">{{ '' }}</td>
                            <td class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($item->debit, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    @php
                        $total = $model->fund_submission_generals()->sum('debit');
                    @endphp
                @elseif($model->item == 'dp')
                    @php
                        $down_payment_total = $model
                            ->fund_submission_cash_advances()
                            ->whereIn('type', ['cash_advance', 'tax'])
                            ->sum('debit');
                    @endphp
                    <tr>
                        <td class="small-font text-center"></td>
                        <td class="small-font">
                            @if ($model->purchase_down_payment)
                                {{ $model->purchase_down_payment->code }}
                                <br>
                            @endif
                            {{ $model->purchase->kode ?? '-' }}
                        </td>
                        <td class="small-font text-center"></td>
                        <td class="small-font">
                            {{ $model->fund_submission_cash_advances()->where('type', 'cash_advance')->first()->note ?? '' }}
                        </td>
                        <td class="small-font text-center"></td>
                        <td class="small-font p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($down_payment_total, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @foreach ($model->fund_submission_cash_advances()->whereNotIn('type', ['cash_advance', 'tax'])->where('debit', '!=', 0)->get() as $item)
                        <tr>
                            <td class="small-font text-center">{{ $item->coa->account_code }}</td>
                            <td class="small-font text-center"></td>
                            <td class="small-font text-center"></td>
                            <td class="small-font">
                                {{ $item->note }}
                                @if ($item->type == 'cash_advance')
                                    <br>
                                    @foreach ($get_all_dp_with_related_po ?? [] as $dp)
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="p-0 border-0 font-xsmall-3">- {{ $dp->reference }}</td>
                                                    <td class="p-0 border-0 font-xsmall-3 text-center">TGL {{ localDate($dp->date) }}</td>
                                                    <td class="p-0 border-0 font-xsmall-3 text-right">{{ formatNumber($dp->total, true) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endforeach
                                @endif
                            </td>
                            <td class="small-font text-center"></td>
                            <td class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($item->debit, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    @php
                        $d_total = $model->fund_submission_cash_advances->sum('debit');
                        $c_total = $model->fund_submission_cash_advances->sum('credit');
                    @endphp
                @elseif($model->item == 'lpb')
                    @foreach ($model->fund_submission_supplier_details as $item)
                        @php
                            $amount = $item->total_foreign;
                            if ($item->is_clearing) {
                                if ($item->amount_gap_foreign > 0) {
                                    $amount = $item->total_foreign;
                                    $clearing_amount = $item->amount_gap_foreign * -1;
                                } else {
                                    $amount = $item->total_foreign;
                                    $clearing_amount = $item->amount_gap_foreign * -1;
                                }
                            }
                        @endphp
                        <tr>
                            <td class="small-font"></td>
                            <td class="small-font">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td width="30%" class="small-font p-0 border-0 text-left">{{ implode(',', $item->lpb_reference ?? []) }}</td>
                                            <td width="10%" class="small-font p-0 border-0 text-left">{{ $item->supplier_invoice_parent->reference ?? ($item->supplier_invoice_parent->code ?? '') }}</td>
                                            <td width="40%" class="small-font p-0 border-0 text-left">{{ $item->note ?? '' }}</td>
                                            <td width="10%" class="small-font p-0 border-0 text-right" style="white-space: nowrap;">{{ localDate($item->supplier_invoice_parent->due_date) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($amount, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @if ($item->is_clearing)
                            <tr>
                                <td class="small-font text-center">{{ $item->coa->account_code }}</td>
                                <td class="small-font">
                                    {{ $item->clearing_note }}
                                </td>
                                <td class="small-font p-0">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="border-0">{{ $model->currency->simbol }}</td>
                                                <td class="border-0 text-right">{{ formatNumber($clearing_amount, true) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($model->fund_submission_customers ?? [] as $item)
                        @php
                            $receive_amount = $item->total_foreign;
                            if ($item->is_clearing) {
                                if ($item->receive_amount_gap_foreign > 0) {
                                    $receive_amount = $item->total_foreign;
                                    $clearing_amount = $item->receive_amount_gap_foreign * -1;
                                } else {
                                    $receive_amount = $item->total_foreign - $item->receive_amount_gap_foreign;
                                    $clearing_amount = $item->receive_amount_gap_foreign * -1;
                                }
                            }
                        @endphp
                        <tr>
                            <td class="small-font"></td>
                            <td class="small-font">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="small-font p-0 border-0 text-left">{{ $item->invoice_parent->code }}</td>
                                            <td class="small-font p-0 border-0 text-left">{{ $item->note ?? '' }}</td>
                                            <td class="small-font p-0 border-0 text-right">{{ localDate($item->invoice_parent->due_date) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">-{{ formatNumber($receive_amount * -1, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @if ($item->is_clearing)
                            <tr>
                                <td class="small-font text-center">{{ $item->coa->account_code }}</td>
                                <td class="small-font">
                                    {{ $item->clearing_note }}
                                </td>
                                <td class="small-font p-0">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="border-0">{{ $model->currency->simbol }}</td>
                                                <td class="border-0 text-right">{{ formatNumber($clearing_amount * -1, true) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($model->fund_submission_supplier_others ?? [] as $detail)
                        <tr>
                            <td class="small-font text-center">{{ $detail->coa->account_code }}</td>
                            <td class="small-font">{{ $detail->note }}</td>
                            <td class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($detail->debit, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    @php
                        $total = $model->total;
                    @endphp
                @endif
                <tr>
                    <td class="small-font"></td>
                    <td class="small-font"></td>
                    <td class="small-font"></td>
                    <td class="small-font text-right" colspan="2"><b>TOTAL</b></td>
                    @if ($model->item != 'dp')
                        <th class="small-font p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($total, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </th>
                    @else
                        <th class="small-font p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($d_total, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </th>
                    @endif
                </tr>
            </table>
            <p class="small-font"><b>Terbilang : </b> {{ strtoupper(Terbilang::make($total ?? $d_total)) }} {{ strtoupper($model->currency->nama) }}</p>
        </div>

        @if ($model->item == 'lpb' && $is_payment_history)
            <h2 class="text-uppercase font-small-3 mt-2">Payment Information</h2>
            @foreach ($model->fund_submission_supplier_details ?? [] as $fund_submission_supplier_detail)
                <table class="table table-bordered mb-2">
                    <tbody>
                        <tr class="bg-dark">
                            <th colspan="4" class="text-center small-font">
                                {{ $fund_submission_supplier_detail->supplier_invoice_parent->code }}
                            </th>
                        </tr>
                        <tr>
                            <th class="small-font">TANGGAL</th>
                            <th class="small-font">KET.</th>
                            <th class="text-end small-font">JUMLAH</th>
                            <th class="text-end small-font">BAYAR</th>
                        </tr>
                        @if ($fund_submission_supplier_detail->supplier_invoice_parent->type == 'general')
                            @foreach ($fund_submission_supplier_detail->payment_informations as $payment_information)
                                <tr>
                                    <td class="small-font text-center">{{ localDate($payment_information->date) }}</td>
                                    <td class="small-font">{{ $payment_information->note }}</td>
                                    <td class="text-end small-font">
                                    <td class="small-font p-0">
                                        @if ($payment_information->amount_to_pay != 0)
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                                        <td class="border-0 text-right">{{ floatDotFormat($payment_information->amount_to_pay) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endif
                                    </td>
                                    </td>
                                    <td class="small-font p-0">
                                        @if ($payment_information->pay_amount != 0)
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                                        <td class="border-0 text-right">{{ floatDotFormat($payment_information->pay_amount) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach ($fund_submission_supplier_detail->supplier_invoice_parent->reference_model->detail as $detail)
                                <tr class="table-warning">
                                    <th colspan="2" class="small-font text-left">
                                        {{ $detail->item_receiving_report->kode }}
                                    </th>
                                    <th class="small-font p-0">
                                        @if ($detail->item_receiving_report->total != 0)
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                                        <td class="border-0 text-right">{{ floatDotFormat($detail->item_receiving_report->total) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endif
                                    </th>
                                    <th></th>
                                </tr>
                                @foreach ($detail->item_receiving_report->payment_informations as $payment_information)
                                    <tr>
                                        <td class="small-font text-center">{{ localDate($payment_information->date) }}</td>
                                        <td class="small-font">{{ $payment_information->note }}</td>
                                        <td class="small-font p-0">
                                            @if ($payment_information->amount_to_pay != 0)
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                                            <td class="border-0 text-right">{{ floatDotFormat($payment_information->amount_to_pay) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                        </td>
                                        <td class="small-font p-0">
                                            @if ($payment_information->pay_amount != 0)
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                                            <td class="border-0 text-right">{{ floatDotFormat($payment_information->pay_amount) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                        <tr>
                            <th class="small-font">TOTAL</th>
                            <th class="small-font"></th>
                            <th class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ floatDotFormat($fund_submission_supplier_detail->payment_informations->sum('amount_to_pay')) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </th>
                            </td>
                            <th class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ floatDotFormat($fund_submission_supplier_detail->payment_informations->sum('pay_amount')) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th class="small-font">SISA</th>
                            <th class="small-font"></th>
                            <th class="small-font"></th>
                            <th class="small-font p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->fund_submission_supplier->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ floatDotFormat($fund_submission_supplier_detail->payment_informations->sum('amount_to_pay') - $fund_submission_supplier_detail->payment_informations->sum('pay_amount')) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </th>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @elseif ($model->item == 'dp')
            @if (count($related_down_payments) > 0)
                <h2 class="text-uppercase font-small-3 mt-2">Payment History</h2>
                <table class="table table-bordered mb-2">
                    <tbody>
                        <tr>
                            <th class="small-font">{{ Str::headline('no. dokumen') }}</th>
                            <th class="small-font">{{ Str::headline('tanggal') }}</th>
                            <th class="small-font">{{ Str::headline('jumlah') }}</th>
                            <th class="small-font">{{ Str::headline('bayar') }}</th>
                        </tr>
                        @foreach ($related_down_payments as $purchase_down_payment)
                            <tr>
                                <td>{{ $purchase_down_payment->bank_code_mutation }}</td>
                                <td>{{ localDate($purchase_down_payment->date) }}</td>
                                <td></td>
                                <td>{{ $model->currency->simbol }} {{ formatNumber($purchase_down_payment->cash_advance_cash_advance->debit ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    </div>
    <div id="footer" class="mt-1">
        <table class="table table-responsive">
            <tbody>
                <tr>
                    <td class="border-0 p-0">
                        <img src="data:image/png;base64, {{ $qr }}" width="70px">
                    </td>
                    <td style="vertical-align: bottom" class="border-0 p-0">
                        <div>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="25%"><span class="bold">Penerima</span></th>
                                    <th width="25%"><span class="bold">Kasir</span></th>
                                    <th width="25%"><span class="bold">Pembukuan</span></th>
                                    <th width="25%"><span class="bold">Divisi Manager</span></th>
                                </tr>
                                <tr>
                                    <td class="small-font">
                                        <div style="text-align: center; min-height: 40px;"></div>
                                    </td>
                                    <td class="small-font">
                                        <div style="text-align: center; min-height: 40px;"></div>
                                    </td>
                                    <td class="small-font">
                                        <div style="text-align: center; min-height: 40px;"></div>
                                    </td>
                                    <td class="small-font">
                                        <div style="text-align: center; min-height: 40px;"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
