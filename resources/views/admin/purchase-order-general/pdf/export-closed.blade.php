<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order General Close - {{ $model->code }}</title>
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
                    <h2 class="text-uppercase my-0">Purchase Order Close</h2>
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
                            <td class="p-0 valign-top" width="30%">{{ $model->type == 'purchase-request' ? 'No. Purchase Request' : 'No. Sales Order' }}</td>
                            <td class="p-0 valign-top" width="5%"> :</td>
                            <td class="p-0 valign-top text-uppercase">
                                @if ($model->type == 'purchase-request')
                                    @foreach ($model->purchaseOrderGeneralDetails as $item)
                                        <p class="my-0">{{ $item->purchase_request->kode ?? '' }}</p>
                                    @endforeach
                                @else
                                    @foreach ($model->purchaseOrderGeneralDetails as $item)
                                        <p class="my-0">{{ $item->sale_order_general->kode ?? '' }}</p>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class=" valign-top">Term of Payment</td>
                            <td class=" valign-top">:</td>
                            <td class=" valign-top text-uppercase">{{ $model->term_of_payment == 'cash' ? 'cash' : $model->term_of_payment_days . ' hari' }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top">Vendor ID</td>
                            <td class=" valign-top">:</td>
                            <td class=" valign-top text-uppercase">{{ $model->vendor->code }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top">Ket. Pembayaran</td>
                            <td class=" valign-top">:</td>
                            <td class=" valign-top text-uppercase">{{ $model->payment_description }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top">Ket. Close</td>
                            <td class=" valign-top">:</td>
                            <td class=" valign-top text-uppercase">{{ $model->close_note }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top">Project</td>
                            <td class=" valign-top">:</td>
                            <td class=" valign-top">{{ implode(', ', $projects->pluck('name')->toArray()) }}</td>
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
                <th>Item</th>
                <th>Qty PO</th>
                <th>Qty LPB</th>
                <th>Sisa</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Total</th>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $all_total = 0;

                    $dpp = 0;
                    $tax_total = 0;
                    $grand_total = 0;
                    $disc_total = 0;
                @endphp
                @foreach ($model->purchaseOrderGeneralDetails()->get() as $key => $purchaseOrderGeneralDetail)
                    @foreach ($purchaseOrderGeneralDetail->purchase_order_general_detail_items->where('status', '!=', 'done') as $purchase_order_general_detail_item)
                        @php
                            $outstanding_qty = $purchase_order_general_detail_item->quantity - $purchase_order_general_detail_item->quantity_received;
                            $subtotal = $outstanding_qty * ($purchase_order_general_detail_item->price_display - $purchase_order_general_detail_item->discount);
                            $all_total += $subtotal;

                            $dpp += $subtotal;
                            $disc_total += $purchase_order_general_detail_item->discount * $outstanding_qty;
                        @endphp
                        <tr class="{{ $purchase_order_general_detail_item->quantity_received != $purchase_order_general_detail_item->quantity ? 'bg-warning' : '' }}">
                            <td style="vertical-align: top" width="5%" class="text-center">{{ $no++ }}.</td>
                            <td style="vertical-align: top" width="35%">{{ $purchase_order_general_detail_item?->item?->nama }}</td>
                            <td style="vertical-align: top" width="10%" class="text-end">{{ formatNumber($purchase_order_general_detail_item->quantity) }} {{ $purchase_order_general_detail_item->unit?->name }}</td>
                            <td style="vertical-align: top" width="10%" class="text-end">{{ formatNumber($purchase_order_general_detail_item->quantity_received) }} {{ $purchase_order_general_detail_item->unit?->name }}</td>
                            <td style="vertical-align: top" width="10%" class="text-end">{{ formatNumber($outstanding_qty) }} {{ $purchase_order_general_detail_item->unit?->name }}</td>
                            <td style="vertical-align: top" class="text-end p-0">
                                <table>
                                    <tr>
                                        <td style="vertical-align: top" class="border-0"></td>
                                        <td style="vertical-align: top" class="text-right border-0">{{ formatNumber($purchase_order_general_detail_item->price_display) }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align: top" class="text-end p-0">
                                <table>
                                    <tr>
                                        <td style="vertical-align: top" class="border-0"></td>
                                        <td style="vertical-align: top" class="text-right border-0">{{ formatNumber($purchase_order_general_detail_item->discount) }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align: top" class="text-end p-0">
                                <table>
                                    <tr>
                                        <td style="vertical-align: top" class="border-0"></td>
                                        <td style="vertical-align: top" class="text-right border-0">{{ formatNumber($subtotal) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-end" colspan="7"><b>Subtotal Close</b></td>
                    <td class="text-end fw-bold p-0">
                        <table>
                            <tr>
                                <td class="border-0"></td>
                                <td class="text-right border-0">{{ formatNumber($dpp) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if ($disc_total)
                    <tr>
                        <td class="text-end" colspan="7"><b>Diskon</b></td>
                        <td class="text-end fw-bold p-0">
                            <table>
                                <tr>
                                    <td class="border-0"></td>
                                    <td class="text-right border-0">{{ formatNumber($disc_total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
                @foreach ($tax_data as $tax)
                    @php
                        $tax_amount = $dpp * $tax->value;
                        $tax_total += $tax_amount;
                    @endphp
                    <tr>
                        <td class="text-end" colspan="7"><b>{{ $tax->tax->name }} {{ $tax->value * 100 }}%</b></td>
                        <td class="text-end fw-bold p-0">
                            <table>
                                <tr>
                                    <td class="border-0"></td>
                                    <td class="text-right border-0">{{ formatNumber($tax_amount) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="text-end fw-bolder" colspan="7"><b>Total PO</b></td>
                    <td class="text-end fw-bold p-0">
                        <table>
                            <tr>
                                <td class="border-0"><b></b></td>
                                <td class="text-right border-0"><b>{{ formatNumber($model->total) }}</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="text-end fw-bolder" colspan="7"><b>Sisa PO</b></td>
                    <td class="text-end fw-bold p-0">
                        <table>
                            <tr>
                                <td class="border-0"><b></b></td>
                                <td class="text-right border-0"><b>{{ formatNumber($model->total - ($dpp + $tax_total)) }}</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
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
                    @if ($model->create)
                        @if ($model->create->employee)
                            <span>{{ Str::headline($model->create->employee->name) }}</span>
                        @else
                            <span>{{ $model->create->name }}</span>
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
