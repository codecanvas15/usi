<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INVOICE {{ $model->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 8pt;
            color: #000;
        }

        @page {
            margin: 28px;
        }

        #footer {
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
        <div class="row" style="max-width: 100%">
            <div class="text-center">
                <h2>INVOICE: {{ $model->bank_code_mutation }}</h2>
            </div>
        </div>

        <div>
            <table class="w-100">
                <tr>
                    <td class="valign-top p-0">
                        <table>
                            <tr>
                                <td class="p-0 text-bold" width="28%">Date</td>
                                <td class="p-0" width="2%">:</td>
                                <td class="p-0">{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 text-bold valign-top" width="28%">Reference</td>
                                <td class="p-0 valign-top" width="2%">:</td>
                                <td class="p-0 valign-top">{{ $model->reference }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 text-bold valign-top" width="28%">No. Faktur Pajak</td>
                                <td class="p-0 valign-top" width="2%">:</td>
                                <td class="p-0 valign-top">{{ $model->tax_number }}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="10%"></td>
                    <td class="valign-top p-0">
                        <table>
                            <tr>
                                <td class="valign-top p-0 text-right text-bold" width="25%">Kepada YTH</td>
                                <td class="valign-top p-0" width="2%">:</td>
                                <td class="valign-top p-0">{{ $model->to_name }}</td>
                            </tr>
                            <tr>
                                <td class="valign-top p-0 text-right text-bold">Alamat</td>
                                <td class="valign-top p-0">:</td>
                                <td class="valign-top p-0">{{ $model->model_reference->alamat ?? '' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row mt-1">
            <table class="table-bordered">
                <tr style="height: 50px;">
                    <th class="small-font">No. Perkiraan</th>
                    <th class="small-font">Keterangan</th>
                    @if (!$model->currency->is_local)
                        <th class="small-font">Nilai {{ $model->currency->kode }}</th>
                    @endif
                    <th class="small-font">Nilai</th>
                </tr>
                @foreach ($model->cash_advance_payment_details->where('type', '!=', 'cash_bank') ?? [] as $cash_advance_payment_detail)
                    <tr>
                        <td class="small-font text-center">{{ $cash_advance_payment_detail->coa->account_code }}</th>
                        <td class="small-font">{{ $cash_advance_payment_detail->note }}</td>
                        <td class="small-font p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cash_advance_payment_detail->debit ?? $cash_advance_payment_detail->credit, true) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                @php
                    $total = $model->cash_advance_payment_details->where('type', '!=', 'cash_bank')->sum('debit') - $model->cash_advance_payment_details->where('type', '!=', 'cash_bank')->sum('credit');
                @endphp
                <tr>
                    <td class="small-font text-right" colspan="2"><b>TOTAL</b></td>
                    <td class="small-font p-0">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <td class="border-0"> <b>{{ $model->currency->simbol }}</b></td>
                                    <td class="border-0 text-right"><b>{{ formatNumber($total, true) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <p class="font-small-1">
                <b>Terbilang : </b> {{ strtoupper(Terbilang::make($total)) }} {{ strtoupper(get_local_currency()->nama) }}
            </p>
        </div>
    </div>
    <table>
        <tbody>
            <tr>
                <td class="p-0 text-left valign-top" width="50%">
                    <img src="data:image/png;base64, {{ $qr }}" width="70px" class="mt-1">
                </td>
                <td width="10%"></td>
                <td class="border-0 p-0 valign-top text-center">
                    <p class="my-0">{{ $model->branch->name }}, {{ \Carbon\Carbon::parse($model->date)->translatedFormat('d F Y') }}</p>
                    <p class="my-0 text-bold">Hormat Kami</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p class="my-0">{{ getCompany()->name }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">

                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-center" id="footer">
        <p class="my-0">If you have any questions regarding this invoice, please contact us via:</p>
        <p class="my-0">Mobile/WhatsApp: {{ getCompany()->phone }} ; Email: {{ getCompany()->email }} </p>
    </div>
</body>

</html>
