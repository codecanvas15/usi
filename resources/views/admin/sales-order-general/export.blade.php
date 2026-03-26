<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sale Order {{ $model->kode }}</title>
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
    <div style="max-width:100%;" class="mt-1">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top">
                    <h2 class="text-uppercase my-0">Sales Order</h2>
                    <span class="font-small-2 text-bold doc-number-border">No. {{ $model->kode }}</span>
                </td>
                <td style="width: 25%;text-align: right; vertical-align: top">
                    <b>Date :</b> {{ localDate($model->tanggal) }}
                </td>
            </tr>
        </table>
    </div>
    <div class="row small-font" style="max-width:100%;">
        <table style="width: 100%">
            <tr>
                <td style="width:50%;vertical-align: top;" class="p-0">
                    <table>
                        <tr class="mb-0">
                            <td class="p-0 text-bold mb-0" width="30%">Customer ID</td>
                            <td class="p-0 mb-0 text-right" width="5%">:</td>
                            <td class="p-0 mb-0" width="70%" style="text-align: left;">{{ $model->customer->code }}</td>
                        </tr>
                        <tr class="mb-0">
                            <td class="p-0 text-bold mb-0">No. Ref PO</td>
                            <td class="p-0 mb-0 text-right">:</td>
                            <td class="p-0 mb-0"><span class="text-capitalize">{{ $model->no_po_external }}</td>
                        </tr>
                        <tr class="mb-0">
                            <td class="p-0 text-bold mb-0">Term of Payment</td>
                            <td class="p-0 mb-0 text-right">:</td>
                            <td class="p-0 mb-0"><span class="text-capitalize">{{ $model->customer->term_of_payment }} - {{ $model->customer->top_days }}</td>
                        </tr>
                    </table>
                </td>
                <td width="10%"></td>
                <td style="width: 40%;">
                    <table>
                        <tr class="m-0">
                            <td class="valign-top text-bold pb-0" style="width: 75px;line-height: 1;">Sold To</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="valign-top text-bold pb-0">Alamat</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->customer->alamat }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="container" style="color: black">
        <div class="row showTable" style="margin-top: 6px;max-width: 100%">
            <table class="table table-bordered" width="100%">
                <tbody>
                    <tr style="background-color: black; color:white;">
                        <th style="text-align: center; width: 40%;"><span class="bold">Item</span></th>
                        <th style="text-align: center; width: 10%;"><span class="bold">Qty</span></th>
                        <th style="text-align: center; width: 10%;"><span class="bold">Satuan</span></th>
                        <th style="text-align: center; width: 20%;"><span class="bold">Price</span></th>
                        <th style="text-align: center; width: 20%;"><span class="bold">Amount</span></th>
                    </tr>

                    @foreach ($model->sale_order_general_details as $detail)
                        <tr style="color:white;">
                            <td style="text-align: left;color:black;">{{ $detail->item->nama }}</td>
                            <td style="text-align: right;color:black;">{{ formatNumber($detail->amount) }}</td>
                            <td style="text-align: center;color:black;">{{ $detail->item->unit->name }}</td>
                            <td style="text-align: right;color:black;" class="p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">
                                                {{ formatNumber($detail->price) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="text-align: right;color:black;" class="p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">
                                                {{ formatNumber($detail->amount * $detail->price) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">
                        </td>
                        <td class="text-right text-bold">Subtotal</td>
                        <td class="text-right text-bold p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">
                                            {{ formatNumber($model->sub_total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @foreach ($taxes as $tax)
                        <tr>
                            <td colspan="3">
                            </td>
                            <td class="text-right text-bold">{{ $tax->tax->name }} {{ $tax->value * 100 }}%</td>
                            <td class="text-right text-bold p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">
                                                {{ formatNumber($tax->grand_total) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">
                        </td>
                        <td class="text-right text-bold">Total</td>
                        <td class="text-right text-bold p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">
                                            {{ formatNumber($model->total) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <table class="my-1" style="border-collapse: collapse">
            <tbody>
                <tr>
                    <td class="text-left border-0" width="20%">
                        <img src="data:image/png;base64, {{ $qr }}" width="70px">
                    </td>
                    <td class="valign-bottom text-center font-xsmall-3 bordered">
                        <b>Maker:</b>
                        @if ($model->created_by_user)
                            @if ($model->created_by_user->employee)
                                <span>{{ Str::headline($model->created_by_user->employee->name) }}</span>
                            @else
                                <span>{{ $model->created_by_user->name }}</span>
                            @endif
                        @endif <br>{{ localDateTime($model->created_at) }}
                    </td>
                    <td class="valign-bottom text-center font-xsmall-3 bordered" width="40%">
                        <b>Mengetahui:</b>
                        @if ($approval?->details->last() ?? null)
                            @if ($approval->details->last()->user)
                                <span>{{ Str::headline($approval->details->last()->user->name) }}</span>
                            @else
                                <span>{{ $approval->details->last()->name }}</span>
                            @endif <br>{{ localDateTime($approval->details->last()->updated_at ?? null) }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
