<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Down Payment - {{ $model->code }}</title>
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
    <div style="max-width: 100%" class="mt-1">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top" class="p-0">
                    <h2 class="text-uppercase my-0">Purchase Down Payment</h2>
                    <span class="font-small-3 text-bold doc-number-border">NO. {{ $model->code }}</span>
                </td>
                <td class="text-right valign-top">
                    <span>Date : <b>{{ localDate($model->date) }}</b></span>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <tr>
            <td class="p-0 valign-top">
                <table class="small-font">
                    <tbody>
                        <tr>
                            <td class="p-0 valign-top" width="40%">No. Purchase Order</td>
                            <td class="p-0 valign-top" width="5%"> :</td>
                            <td class="p-0 valign-top text-uppercase">
                                @php
                                    $po_code = '';
                                    if ($model->purchase?->general) {
                                        $po_code = $model->purchase?->general->code;
                                    } elseif ($model->purchase?->trading) {
                                        $po_code = $model->purchase?->trading->nomor_po;
                                    } elseif ($model->purchase?->service) {
                                        $po_code = $model->purchase?->service->code;
                                    } elseif ($model->purchase?->transport) {
                                        $po_code = $model->purchase?->transport->kode;
                                    } else {
                                        $po_code = $model->purchase?->kode ?? '';
                                    }
                                @endphp
                                {{ $po_code }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="10%"></td>
            <td class="p-0 valign-top">
                <table>
                    <tbody>
                        <tr>
                            <td class=" valign-top" width="25%">Kepada YTH</td>
                            <td class=" valign-top" width="5%">:</td>
                            <td class=" valign-top">{{ $model->vendor->nama }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top">Alamat</td>
                            <td class=" valign-top">:</td>
                            <td class=" valign-top">{{ $model->vendor->alamat }}</td>
                        </tr>

                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    {{-- <hr style="border: 1px solid grey;"> --}}
    <div class=" mt-1">
        <table class="table table-striped table-bordered">
            <thead>
                <th>No.</th>
                <th>Keterangan</th>
                <th>Harga</th>
                <th>Total</th>
            </thead>
            <tbody>
                <tr>
                    <td style="vertical-align: top" width="5%" class="text-center">1. </td>
                    <td style="vertical-align: top" width="60%">{{ $model->note }}</td>
                    <td style="vertical-align: top" width="10%" class="text-end"></td>
                    <td style="vertical-align: top" class="text-end">{{ formatNumber($model->total_amount) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td style="vertical-align: top" class="text-center"></td>
                    <td style="vertical-align: top"></td>
                    <td style="vertical-align: top"><b>Total</b></td>
                    <td style="vertical-align: top" class="text-end"><b>{{ formatNumber($model->subtotal) }}</b></td>
                </tr>
                @foreach ($model->purchase_down_payment_taxes as $purchase_down_payment_tax)
                    <tr>
                        <td style="vertical-align: top" class="text-center"></td>
                        <td style="vertical-align: top"></td>
                        <td style="vertical-align: top"><b>{{ $purchase_down_payment_tax->tax->tax_name_with_percent }}</b></td>
                        <td style="vertical-align: top" class="text-end"><b>{{ formatNumber($purchase_down_payment_tax->amount) }}</b></td>
                    </tr>
                @endforeach
                <tr>
                    <td style="vertical-align: top" class="text-center"></td>
                    <td style="vertical-align: top"></td>
                    <td style="vertical-align: top"><b>Grand Total</b></td>
                    <td style="vertical-align: top" class="text-end"><b>{{ formatNumber($model->grand_total) }}</b></td>
                </tr>
            </tfoot>
        </table>
        <p class="text-uppercase"><b>Terbilang : </b> {{ Terbilang::make($model->grand_total) }} {{ $model->currency->nama }}</p>
    </div>
    @if ($related_down_payments->count() > 0)
        <h2 class="text-uppercase font-small-3 mt-2">Payment History</h2>
        <table class="table table-bordered mb-2">
            <tbody>
                <tr>
                    <th class="small-font">{{ Str::headline('no. dokumen') }}</th>
                    <th class="small-font">{{ Str::headline('tanggal') }}</th>
                    <th class="small-font">{{ Str::headline('total') }}</th>
                    <th class="small-font">{{ Str::headline('bayar') }}</th>
                </tr>
                <tr>
                    <td><b>{{ $model->purchase->kode }}</b></td>
                    <td class="text-center">{{ localDate($model->purchase->tanggal) }}</td>
                    <td class="text-end">{{ formatNumber($model->purchase->reference->total) }}</td>
                    <td></td>
                </tr>
                @foreach ($related_down_payments as $purchase_down_payment)
                    <tr>
                        <td>
                            <b>{{ $purchase_down_payment->code }}</b>
                            <br>
                            {{ $purchase_down_payment->note }}
                        </td>
                        <td class="text-center">{{ localDate($purchase_down_payment->date) }}</td>
                        <td></td>
                        <td class="text-end">{{ formatNumber($purchase_down_payment->grand_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
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
                <td class="valign-bottom text-center font-xsmall-3 bordered">
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

</body>

</html>
