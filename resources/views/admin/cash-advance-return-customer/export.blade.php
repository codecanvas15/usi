<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pengembalian Uang Muka {{ $model->code }}</title>
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
            padding: 2px 4px !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @include('components.print_out_header')
    <div class="container" style="color: black">
        <div class="text-center">
            <h3>PENGEMBALIAN UANG MUKA CUSTOMER</h3>
        </div>
        <div class="mt-2">
            <table width="100%">
                <tr>
                    <td class="small-font" width="15%"><b>Kode Transaksi</b></td>
                    <td class="small-font" width="25%">: {{ $model->code }}</td>
                    <td class="small-font" width="10%"></td>
                    <td class="small-font text-right" width="15%"><b>Tanggal</b></td>
                    <td class="small-font" width="25%">: {{ localDate($model->date) }}</td>
                </tr>
                <tr>
                    <td class="small-font"><b>Branch</b></td>
                    <td class="small-font">: {{ $model->branch->name }}</td>
                    <td class="small-font"></td>
                    <td class="small-font text-right"><b>Customer</b></td>
                    <td class="small-font">: {{ $model->reference?->nama }}</td>
                </tr>
            </table>
        </div>
        <div class="row mt-2">
            <table class="table-bordered">
                <tr style="height: 50px;">
                    <th class="small-font" width="10%">No. Perkiraan</th>
                    <th class="small-font" width="36%">Keterangan</th>
                    <th class="small-font" width="18%">Sisa</th>
                    <th class="small-font" width="18%">Dibayarkan</th>
                    <th class="small-font" width="18%">Saldo</th>
                </tr>
                @foreach ($model->cashAdvancedReturnDetails ?? [] as $cashAdvanceReturnDetail)
                    <tr>
                        <td class="small-font text-center">{{ $cashAdvanceReturnDetail->reference->cash_advance_cash_advance->coa->account_code ?? '-' }}</th>
                        <td class="font-xsmall-3">{{ $cashAdvanceReturnDetail->reference->bank_code_mutation ?? $cashAdvanceReturnDetail->reference->code }}</td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvanceReturnDetail->outstanding_amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvanceReturnDetail->amount_to_return) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvanceReturnDetail->outstanding_amount - $cashAdvanceReturnDetail->amount_to_return) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                @foreach ($model->cashAdvancedReturnInvoices ?? [] as $cashAdvanceReturnInvoices)
                    <tr>
                        <td class="small-font text-center">
                            </th>
                        <td class="font-xsmall-3">{{ $cashAdvanceReturnInvoices->transaction_code }}</td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->invoice_currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvanceReturnInvoices->outstanding_amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->invoice_currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvanceReturnInvoices->amount_to_paid_or_return) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->invoice_currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvanceReturnInvoices->outstanding_amount - $cashAdvanceReturnInvoices->amount_to_paid_or_return) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                @foreach ($model->cashAdvancedReturnTransactions ?? [] as $cashAdvancedReturnTransaction)
                    <tr>
                        <td class="small-font text-center">{{ $cashAdvancedReturnTransaction->coa->account_code }}</th>
                        <td class="font-xsmall-3">{{ $cashAdvancedReturnTransaction->description }}</td>
                        <td></td>
                        <td class="small-font">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($cashAdvancedReturnTransaction->credit - $cashAdvancedReturnTransaction->debit) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <td class="small-font text-end " colspan="4"><b class="text-capitalize">total uang muka</b></th>
                    <td class="small-font">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <th class="border-0 text-left">{{ $model->currency->simbol }}</th>
                                    <th class="border-0 text-right">{{ formatNumber($model->cashAdvancedReturnDetails->sum('amount_to_return')) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="small-font text-end " colspan="4"><b class="text-capitalize">total invoice</b></th>
                    <td class="small-font">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <th class="border-0 text-left">{{ $model->currency->simbol }}</th>
                                    <th class="border-0 text-right">{{ formatNumber($model->cashAdvancedReturnInvoices->sum('amount_to_paid_or_return_convert')) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="small-font text-end " colspan="4"><b class="text-capitalize">total lain - lain</b></th>
                    <td class="small-font">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <th class="border-0 text-left">{{ $model->currency->simbol }}</th>
                                    <th class="border-0 text-right">{{ formatNumber($model->cashAdvancedReturnTransactions->sum('credit') - $model->cashAdvancedReturnTransactions->sum('debit')) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
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
