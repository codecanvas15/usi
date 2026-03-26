<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kas Masuk {{ $model->nomor_so }}</title>
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
                <h2 style="margin-bottom: 0px">BUKTI {{ ($model->coa->bank_internal->type ?? '') == 'bank' ? 'BANK' : 'KAS' }} MASUK</h2>
                <b>{{ $model->bank_code_mutation }}</b>
            </div>
        </div>
        <div>

            <table>
                <tr>
                    <td class="p-0" width="45%">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="small-font">Bank & Rekening</td>
                                    <td class="small-font">:</td>
                                    <td class="small-font">
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
                                    <td class="small-font"> {{ $model->send_payment->cheque_no ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="small-font">Tgl. JT</td>
                                    <td class="small-font">:</td>
                                    <td class="small-font">
                                        {{ $model->is_giro ? localDate($model->send_payment->due_date) : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="small-font">
                                        No. Reff
                                    </td>
                                    <td class="small-font">:</td>
                                    <td class="small-font">
                                        {{ $model->reference ?? '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="small-font" width="10%"></td>
                    <td class="p-0 valign-top" width="45%">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="small-font" width="25%">Tanggal</td>
                                    <td class="small-font" width="2%">:</td>
                                    <td class="small-font" width="73%"> {{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <td class="small-font">Diterima Dari</td>
                                    <td class="small-font">:</td>
                                    <td class="small-font"> {{ $model->from_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row">
            <table class="table-bordered">
                <tr style="height: 50px;">
                    <th class="small-font">No. Perkiraan</th>
                    <th class="small-font">No. Invoice</th>
                    <th class="small-font">Keterangan</th>
                    <th class="small-font">Top</th>
                    @if (!$model->currency->is_local)
                        <th class="small-font">Nilai {{ $model->currency->kode }}</th>
                    @endif
                    <th class="small-font">Nilai</th>
                </tr>
                @foreach ($model->incoming_payment_details ?? [] as $incoming_payment_detail)
                    <tr>
                        <td class="small-font text-center">{{ $incoming_payment_detail->coa->account_code }}</th>
                        <td class="small-font"></td>
                        <td class="small-font">{{ $incoming_payment_detail->note }}</td>
                        <td class="small-font"></td>
                        @if (!$model->currency->is_local)
                            <td class="small-font p-0">
                                <table class="border-0">
                                    <tbody>
                                        <tr class="border-0">
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($incoming_payment_detail->credit) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        @endif
                        <td class="small-font p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0">{{ get_local_currency()->simbol }}</td>
                                        @php
                                            // Format the number using your formatNumber function
                                            $formattedNumber = formatNumber($incoming_payment_detail->credit_local);

                                            // Check if the number is negative
                                            if ($incoming_payment_detail->credit_local < 0) {
                                                // Remove the negative sign and wrap the number in parentheses
                                                $formattedNumber = '(' . formatNumber(abs($incoming_payment_detail->credit_local)) . ')';
                                            }
                                        @endphp
                                        {{-- if credit local is negative (-), then make value have (), so for example if value is -884.589,00, then make it become (884.589,00) --}}
                                        <td class="border-0 text-right">{{ $formattedNumber }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="small-font text-right" colspan="4"><b>TOTAL</b></td>
                    @if (!$model->currency->is_local)
                        <td class="small-font p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0"> {{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->credit_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    @endif
                    <td class="small-font p-0">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <td class="border-0"> {{ get_local_currency()->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->local_credit_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <p class="font-small-1">
                <b>Terbilang : </b> {{ strtoupper(Terbilang::make($model->local_credit_total)) }} {{ strtoupper(get_local_currency()->nama) }}
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
                    <td style="vertical-align: top" class="border-0 p-0">
                        <div class="row">
                            <table class="table-bordered">
                                <tr>
                                    <th width="25%" class="small-font"><span class="bold">Kasir</span></th>
                                    <th width="25%" class="small-font"><span class="bold">Pembukuan</span></th>
                                    <th width="25%" class="small-font"><span class="bold">Divisi Manager</span></th>
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
            </tbody>
        </table>
    </div>
</body>

</html>
