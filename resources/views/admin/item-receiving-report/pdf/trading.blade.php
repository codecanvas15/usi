<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Penerimaan Barang {{ Str::headline($model->tipe) }} - {{ $model->kode }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 8pt;
            color: #000;
        }

        @page {
            margin: 28px;
        }

        .black-column {
            background-color: #000;
            color: #fff;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        .table tr th,
        .table tr td {
            border: 0.5px solid black;
        }
    </style>
</head>

<body>
    @include('components.print_out_header_center')

    <div style="max-width:100%;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h2 class="text-uppercase my-0">Penerimaan Barang NO. {{ $model->kode }}</h2>
                </td>
                <td class="text-right">
                    <b>Date: {{ localDate($model->date_receive) }}</b>
                </td>
            </tr>
            <tr>
                <td width="60%">
                    <table>
                        <tbody>
                            <tr>
                                <td class="p-0 valign-top" width="18%">Vendor</td>
                                <td class="p-0 valign-top" width="2%">:</td>
                                <td class="p-0 valign-top">{{ $model->vendor?->nama }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 valign-top">No. Ref. PO</td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model?->reference->nomor_po ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 valign-top">No. SO</td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model?->reference->sale_confirmation ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 valign-top">No. LO</td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model?->item_receiving_report_po_trading->loading_order ?? '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td class="valign-top">
                    <table>
                        <tr>
                            <td class="p-0 valign-top text-right" style="width:20%;">Customer </td>
                            <td class="p-0 valign-top" style="width:2%"> : </td>
                            <td class="p-0 valign-top"> {{ $model->reference->customer->nama ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 valign-top text-right" style="width:20%;">Drop Point </td>
                            <td class="p-0 valign-top"> : </td>
                            @if ($model->reference->sale_order ?? '')
                                <td class="p-0 valign-top"> {{ $model->reference->sale_order->sh_number->sh_number_details[1]->alamat ?? '' }}</td>
                            @else
                                <td class="p-0 valign-top"> {{ $model->reference->sh_number->sh_number_details[1]->alamat ?? '' }}</td>
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="row mt-1" style="max-width: 100%">
            <table class="table" style="border: 0.5px solid black;">
                <tbody>
                    <tr>
                        <th style="text-align: center; width: 60%;" class="black-column text-semi-bold" rowspan="2">Item</th>
                        <th style="text-align: center; width: 60%;" class="black-column text-semi-bold" colspan="2">Qty</th>
                        <th style="text-align: center; width: 15%;" class="black-column text-semi-bold" rowspan="2">Satuan</th>
                    </tr>
                    <tr>
                        <th style="text-align: center; width: 15%;" class="black-column text-semi-bold">Liter 15</th>
                        <th style="text-align: center; width: 15%;" class="black-column text-semi-bold">Liter Obs</th>
                    </tr>
                    <tr>
                        <td style="text-align: left;">{{ $model->item_receiving_report_po_trading->item->kode }} / {{ $model->item_receiving_report_po_trading->item->nama }}</td>
                        <td style="text-align: center;">{{ formatNumber($model->item_receiving_report_po_trading->liter_15) }}</td>
                        <td style="text-align: center;">{{ formatNumber($model->item_receiving_report_po_trading->liter_obs) }}</td>
                        <td style="text-align: center;">{{ $model->item_receiving_report_po_trading->item?->unit->name }}</td>
                    </tr>
                    @foreach ($model?->item_receiving_po_trading_additionals as $additional)
                        <tr>
                            <td style="text-align: left;">{{ $additional->purchase_order_additional_items->item->nama }}</td>
                            <td style="text-align: center;">{{ formatNumber($additional->receive_qty) }}</td>
                            <td style="text-align: center;">{{ formatNumber($additional->receive_qty) }}</td>
                            <td style="text-align: center;">{{ $additional->purchase_order_additional_items->item->unit->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
