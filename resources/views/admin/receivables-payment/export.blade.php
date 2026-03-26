<!DOCTYPE html>
<html>

<head>
    <title>Pembayaran Customer - {{ $model->code }}</title>
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
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @include('components.print_out_header')
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%;  margin-bottom: 5px">
            <div class="text-center">
                <h1 class="font-medium-1" style="margin-bottom: 0px">BUKTI {{ ($model->coa->bank_internal->type ?? '') == 'bank' ? 'BANK' : 'KAS' }} MASUK</h1>
                <b>{{ $model->bank_code_mutation }}</b>
            </div>
        </div>
        <div>
            <table>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Bank & Rekening</td>
                                <td>:</td>
                                <td>
                                    @if ($model->coa->bank_internal)
                                        {{ $model->coa?->bank_internal?->nama_bank }} {{ $model->coa?->bank_internal?->no_rekening }}
                                    @else
                                        {{ $model->coa->name }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="small-font">No. C/BG</td>
                                <td class="small-font">:</td>
                                <td class="small-font"> {{ '' }}</td>
                            </tr>
                            <tr>
                                <td class="small-font">Tgl. JT</td>
                                <td class="small-font">:</td>
                                <td class="small-font">
                                    {{ '' }}
                                </td>
                            </tr>
                            <tr>
                                <td>No. Reff</td>
                                <td>:</td>
                                <td>{{ $model->reference }}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="10%"></td>
                    <td>
                        <table>
                            <tr>
                                <td width="25%">Tanggal</td>
                                <td width="2%">:</td>
                                <td>{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <td>Diterima Dari</td>
                                <td>:</td>
                                <td>{{ $model->customer?->nama }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row mt-2">
            <table class="table-bordered">
                <tr style="height: 50px;">
                    <th class="font-small-1" width="5%">No. Perkiraan</th>
                    <th class="font-small-1" width="10%">No. Invoice</th>
                    <th class="font-small-1" width="20%">Keterangan</th>
                    <th class="font-small-1" width="7%">Top</th>
                    @if (!$model->currency->is_local)
                        <th class="font-small-1" width="10%">Nilai {{ $model->currency->kode }}</th>
                    @endif
                    <th class="font-small-1" width="10%">Nilai</th>
                </tr>
                @foreach ($model->receivables_payment_details ?? [] as $detail)
                    @php
                        $receive_amount = $detail->total_foreign;
                        if ($detail->is_clearing) {
                            if ($detail->receive_amount_gap_foreign > 0) {
                                $receive_amount = $detail->total_foreign;
                                $clearing_amount = $detail->receive_amount_gap_foreign * -1;
                            } else {
                                $receive_amount = $detail->total_foreign;
                                $clearing_amount = $detail->receive_amount_gap_foreign * -1;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1">{{ $detail->invoice_parent->code }}</td>
                        <td class="font-small-1">{{ $detail->note ?? '' }}</td>
                        <td class="font-small-1 text-center">{{ localDate($detail->invoice_parent->due_date) }}</td>
                        @if (!$model->currency->is_local)
                            <td class="font-small-1 p-0">
                                <table class="border-0">
                                    <tbody>
                                        <tr class="border-0">
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($receive_amount, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        @endif
                        <td class="font-small-1 p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($receive_amount * $model->exchange_rate, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @if ($detail->is_clearing)
                        <tr>
                            <td class="font-small-1 text-center">{{ $detail->coa->account_code }}</td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1">{{ $detail->clearing_note }} </td>
                            <td class="font-small-1"></td>
                            @if (!$model->currency->is_local)
                                <td class="font-small-1 p-0">
                                    <table class="border-0">
                                        <tbody>
                                            <tr class="border-0">
                                                <td class="border-0">{{ $model->currency->simbol }}</td>
                                                <td class="border-0 text-right">{{ formatNumber($clearing_amount, true) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            @endif
                            <td class="font-small-1 p-0">
                                <table class="border-0">
                                    <tbody>
                                        <tr class="border-0">
                                            <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($clearing_amount * $model->exchange_rate, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach ($model->receivables_payment_invoice_returns ?? [] as $item)
                    <tr>
                        <td class="font-small-1"></td>
                        <td class="font-small-1">{{ $item->invoice_return->reference_data->code ?? '' }}</td>
                        <td class="font-small-1">{{ $item->invoice_return->code }}</td>
                        <td class="font-small-1">{{ localDate($item->invoice_return->date) }}</td>
                        @if (!$model->currency->is_local)
                            <td class="font-small-1 p-0">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($item->amount * -1, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        @endif
                        <td class="font-small-1 p-0">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($item->amount * -1 * $model->exchange_rate, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                @foreach ($model->receivables_payment_vendors ?? [] as $detail)
                    @php
                        $amount = $detail->total_foreign;
                        if ($detail->is_clearing) {
                            if ($detail->amount_gap_foreign > 0) {
                                $amount = $detail->total_foreign;
                                $clearing_amount = $detail->amount_gap_foreign * -1;
                            } else {
                                $amount = $detail->total_foreign;
                                $clearing_amount = $detail->amount_gap_foreign * -1;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="font-small-1"></td>
                        <td class="font-small-1 p-0">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="font-small-1 p-0 border-0 text-left">{{ implode(',', $detail->lpb_reference ?? []) }}</td>
                                        <td class="font-small-1 p-0 border-0 text-left">{{ $detail->supplier_invoice_parent->reference ?? ($detail->supplier_invoice_parent->code ?? '') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="font-small-1">
                            {{ $detail->note ?? '' }}
                        </td>
                        <td class="font-small-1 text-center">
                            {{ localDate($detail->supplier_invoice_parent->due_date) }}</td>
                        </td>
                        @if (!$model->currency->is_local)
                            <td class="font-small-1 p-0">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($amount * -1, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        @endif
                        <td class="font-small-1 p-0">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($amount * $model->exchange_rate * -1, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @if ($detail->is_clearing)
                        <tr>
                            <td class="font-small-1 text-center">{{ $detail->coa->account_code }}</td>
                            <td class="font-small-1 text-center"></td>
                            <td class="font-small-1">
                                {{ $detail->clearing_note }}
                            </td>
                            @if (!$model->currency->is_local)
                                <td class="font-small-1 p-0">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="border-0">{{ $model->currency->simbol }}</td>
                                                <td class="border-0 text-right">{{ formatNumber($clearing_amount * -1, true) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            @endif
                            <td class="font-small-1 text-center"></td>
                            <td class="font-small-1 p-0">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($clearing_amount * $model->exchange_rate * -1, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach ($model->receivables_payment_others ?? [] as $detail)
                    <tr>
                        <td class="font-small-1 text-center">{{ $detail->coa->account_code }}</td>
                        <td class="font-small-1"></td>
                        <td class="font-small-1">{{ $detail->note }}</td>
                        <td class="font-small-1"></td>
                        @if (!$model->currency->is_local)
                            <td class="font-small-1 p-0">
                                <table class="border-0">
                                    <tbody>
                                        <tr class="border-0">
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($detail->credit, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        @endif
                        <td class="font-small-1 p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($detail->credit * $model->exchange_rate, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                @php
                    $total = $model->receivables_payment_others->sum('credit') + $model->receivables_payment_details->sum('receive_amount') - $model->receivables_payment_vendors->sum('amount') - $model->receivables_payment_invoice_returns->sum('amount');
                @endphp
                <tr>
                    <td colspan="4" class="font-small-1 text-right"><b>TOTAL</b></td>
                    @if (!$model->currency->is_local)
                        <td class="font-small-1 p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($total, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    @endif
                    <td class="font-small-1 p-0">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($total * $model->exchange_rate, true) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <p class="font-small-1">
                <b>Terbilang : </b> {{ strtoupper(Terbilang::make($total)) }} {{ strtoupper($model->currency->nama) }}
            </p>
        </div>
        @if ($is_payment_history)
            <h2 class="text-uppercase font-small-3 mt-2">Payment Information</h2>
            <table class="table table-bordered mb-2">
                <tbody>
                    @foreach ($model->receivables_payment_details ?? [] as $receivables_payment_detail)
                        @if ($receivables_payment_detail->is_payment_history)
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Ket.</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Bayar</th>
                            </tr>
                            @foreach ($receivables_payment_detail->payment_informations as $payment_information)
                                <tr>
                                    <td class="text-center">{{ localDate($payment_information->date) }}</td>
                                    <td>{{ $payment_information->note }}</td>
                                    <td class="text-end">
                                        @if ($payment_information->amount_to_receive != 0)
                                            {{ $model->invoice_currency->simbol }}
                                            {{ formatNumber($payment_information->amount_to_receive, true) }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($payment_information->receive_amount != 0)
                                            {{ $model->invoice_currency->simbol }}
                                            {{ formatNumber($payment_information->receive_amount, true) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <th>TOTAL</th>
                                <th></th>
                                <th class="text-end">{{ $model->invoice_currency->simbol }}
                                    {{ formatNumber($receivables_payment_detail->payment_informations->sum('amount_to_receive'), true) }}
                                </th>
                                <th class="text-end">{{ $model->invoice_currency->simbol }}
                                    {{ formatNumber($receivables_payment_detail->payment_informations->sum('receive_amount'), true) }}
                                </th>
                            </tr>
                            <tr>
                                <th>SISA</th>
                                <th></th>
                                <th></th>
                                <th class="text-end">{{ $model->invoice_currency->simbol }}
                                    {{ formatNumber($receivables_payment_detail->payment_informations->sum('amount_to_receive') - $receivables_payment_detail->payment_informations->sum('receive_amount'), true) }}
                                </th>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <div id="footer" class="mt-1">
        <table style="width: 100%;">
            <tr>
                <td style="width: 25%">
                    <div>
                        <img src="data:image/png;base64, {{ $qr }}" width="70px">
                    </div>
                </td>
                <td style="vertical-align: top" class="border-0 p-0">
                    <div class="row">
                        <table class="table-bordered">
                            <tr>
                                <th width="25%" class="font-small-1"><span class="bold">Kasir</span></th>
                                <th width="25%" class="font-small-1"><span class="bold">Pembukuan</span></th>
                                <th width="25%" class="font-small-1"><span class="bold">Divisi Manager</span></th>
                            </tr>
                            <tr>
                                <td class="text-medium">
                                    <div style="text-align: center; min-height: 40px;"></div>
                                </td>
                                <td class="text-medium">
                                    <div style="text-align: center; min-height: 40px;"></div>
                                </td>
                                <td class="text-medium">
                                    <div style="text-align: center; min-height: 40px;"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
