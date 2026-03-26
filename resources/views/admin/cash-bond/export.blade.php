<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
    <style type="text/css">
        body {
            font-size: 14px;
            color: #000;
        }

        table {
            border-spacing: 0px;
        }

        span {
            font-size: 12px;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        #footer .page:after {
            content: counter(page, upper-roman);
        }

        td {
            vertical-align: top;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    <table style="width: 100%">
        <tr>
            <td style="width: 65%; vertical-align: top">
                <h1 class="font-medium-1 text-uppercase mb-0">{{ getCompany()->name }}</h1>
            </td>
            <td style="width: 25%">
            </td>
        </tr>
    </table>
    <h2 style="text-align: center; text-decoration: underline" class="mb-0">BON SEMENTARA</h2>
    <p class="text-center my-0">{{ $model->bank_code_mutation ?? $model->code }}</p>

    <table class="mt-2" width="100%">
        <tr>
            <td class="font-small-2" width="30%">
                <table width="100%">
                    <tr>
                        <td class="font-small-2" width="31.7%">
                            Tanggal
                        </td>
                        <td width="1%"> : </td>
                        <td class="font-small-2 border-bottom valign-bottom">
                            {{ localDate($model->date ?? $model->created_at) }}
                        </td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td>
            <td class="font-small-2" width="30%">
                <table width="100%">
                    <tr>
                        <td class="font-small-2">
                            Bagian
                        </td>
                        <td> : </td>
                        <td class="font-small-2 border-bottom valign-bottom">{{ $model->branch->name }}</td>
                    </tr>
                    <tr>
                        <td class="font-small-2">
                            Peminjam
                        </td>
                        <td> : </td>
                        <td class="font-small-2 border-bottom valign-bottom">{{ $model->employee->name }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="font-small-2 pt-1" colspan="3">
                <table width="100%">
                    <tr>
                        <td class="font-small-2" width="11.7%">
                            Keperluan
                        </td>
                        <td width="1%"> : </td>
                        <td class="font-small-2 {{ $model->cashBondDetails->where('type', 'cash_advance')->first()->note ?? '' ? 'border-bottom' : '' }} valign-bottom">{{ $model->cashBondDetails->where('type', 'cash_advance')->first()->note ?? '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="table-bordered mt-2">
        <tr>
            <td class="font-small-2 text-center" width="25%">
                <b>KETERANGAN</b>
            </td>
            <td class="font-small-2 text-center" width="25%">
                <b>TERIMA</b>
            </td>
            <td class="font-small-2 text-center" width="25%">
                <b>PENYELESAIAN</b>
            </td>
            <td class="font-small-2 text-center" width="25%">
                <b>SELISIH</b>
            </td>
        </tr>
        <tr>
            <td class="font-small-2 valign-top" style="vertical-align: middle">
                TANGGAL
            </td>
            <td class="text-center valign-top">
                {{ localDate($detail->date ?? $model->date) }}
            </td>
            <td class="text-center valign-top">
                @foreach ($model->cashBondReturnDetails as $cashBondReturnDetail)
                    <p class="my-0">{{ localDate($cashBondReturnDetail->cash_bond_return->date) }}</p>
                @endforeach
            </td>
            @php
                $balance = $model->cashBondDetails->where('type', 'cash_bank')->first()->credit ?? 0;
                $balance -= $model->cashBondReturnDetails->sum('amount_to_return');
            @endphp
            <td class="font-small-2" rowspan="2" style="vertical-align: bottom">
                <table class="table-borderless">
                    <tbody>
                        <tr>
                            <td>{{ $model->currency->simbol }}</td>
                            <td class="text-end">{{ formatNumber($balance) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="font-small-2">
                NOMINAL
            </td>
            <td class="font-small-2">
                <table class="table-borderless">
                    <tbody>
                        <tr>
                            <td>{{ $model->currency->simbol }}</td>
                            <td class="text-end">{{ formatNumber($model->cashBondDetails->where('type', 'cash_bank')->first()->credit ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td class="font-small-2">
                <table class="table-borderless">
                    <tbody>
                        <tr>
                            <td>{{ $model->currency->simbol }}</td>
                            <td class="text-end">{{ formatNumber($model->cashBondReturnDetails->sum('amount_to_return')) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

    </table>
    <table class="table-bordered mt-2">
        <tr>
            <td class="font-small-2 text-center" width="25%">
                Penerima
            </td>
            <td class="font-small-2 text-center" width="25%">
                Kasir
            </td>
            <td class="font-small-2 text-center" width="25%">
                Menyetujui
            </td>
            <td class="font-small-2 text-center" width="25%">
                Mengetahui
            </td>
        </tr>
        <tr>
            <td class="font-small-2">
                <br><br><br><br>
            </td>
            <td class="font-small-2">
                <br><br><br><br>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="font-small-1">
                NAMA:
            </td>
            <td class="font-small-1">
                NAMA:
            </td>
            <td class="font-small-1">
                NAMA:
            </td>
            <td class="font-small-1">
                NAMA:
            </td>

        </tr>

    </table>
</body>

</html>
