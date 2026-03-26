<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kas/Bank Keluar {{ $model->code }}</title>
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
                <h3 style="margin-bottom: 0px">BUKTI {{ ($model->cash_advance_cash_bank->coa->bank_internal->type ?? '') == 'bank' ? 'BANK' : 'KAS' }} KELUAR</h3>
                <b>{{ $model->bank_code_mutation }}</b>
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
                                        @if ($model->cash_advance_cash_bank->coa->bank_internal)
                                            {{ $model->cash_advance_cash_bank->coa?->bank_internal?->nama_bank }} {{ $model->cash_advance_cash_bank->coa?->bank_internal?->no_rekening }}
                                        @else
                                            {{ $model->cash_advance_cash_bank->coa->name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">No. C/BG</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font"> {{ $model->fund_submission->send_payment->cheque_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="valign-top small-font">Tgl. JT</td>
                                    <td class="valign-top small-font">:</td>
                                    <td class="valign-top small-font">
                                        {{ $model->fund_submission->is_giro ? localDate($model->fund_submission->send_payment->due_date) : '' }}
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
            <table class="table-bordered">
                <tr>
                    <th class="small-font"><b>No. Perkiraan</b></th>
                    <th class="small-font"><b>No. LPB</b></th>
                    <th class="small-font"><b>No. Invoice</b></th>
                    <th class="small-font"><b>Keterangan</b></th>
                    <th class="small-font"><b>TOP</b></th>
                    @if (!$model->currency->is_local)
                        <th class="small-font"><b>Nilai {{ $model->currency->kode }}</b></th>
                    @endif
                    <th class="small-font"><b>Nilai</b></th>
                </tr>
                @php
                    $down_payment_total = $model
                        ->cash_advance_payment_details()
                        ->whereIn('type', ['cash_advance', 'tax'])
                        ->sum('debit');
                @endphp
                <tr>
                    <td class="small-font text-center"></td>
                    <td class="small-font">
                        @if ($model->fund_submission->purchase_down_payment)
                            {{ $model->fund_submission->purchase_down_payment->code }}
                            <br>
                        @endif
                        {{ $model->purchase->kode }}
                    </td>
                    <td class="small-font text-center"></td>
                    <td class="small-font">
                        {{ $model->cash_advance_cash_advance->note ?? '' }}
                    </td>
                    <td class="small-font text-center"></td>
                    @if (!$model->currency->is_local)
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
                    @endif
                    <td class="small-font p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($down_payment_total * $model->exchange_rate, true) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                @foreach ($model->cash_advance_payment_details()->whereNotIn('type', ['cash_advance', 'tax'])->where('debit', '!=', 0)->get() as $item)
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
                        @if (!$model->currency->is_local)
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
                        @endif
                        <td class="small-font p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($item->debit * $model->exchange_rate, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                @php
                    $total = $model->cash_advance_cash_bank->credit;
                @endphp
                <tr>
                    <td class="small-font"></td>
                    <td class="small-font"></td>
                    <td class="small-font"></td>
                    <td class="small-font text-right" colspan="2"><b>TOTAL</b></td>
                    @if (!$model->currency->is_local)
                        <td class="small-font p-0">
                            <b>
                                <table class="border-0">
                                    <tbody>
                                        <tr class="border-0">
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($total, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </b>
                        </td>
                    @endif
                    <td class="small-font p-0">
                        <b>
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($total * $model->exchange_rate, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </b>
                    </td>
                </tr>
            </table>
            <p class="font-small-1">
                <b>Terbilang : </b> {{ strtoupper(Terbilang::make($total)) }} {{ strtoupper(get_local_currency()->nama) }}
            </p>
        </div>
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
