<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SPK - {{ $model->spk_number }}</title>
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
    <div style="max-width: 100%">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top" class="p-0">
                    <h2 class="text-uppercase my-0">SURAT PERINTAH KERJA</h2>
                    <span class="font-small-3 text-bold doc-number-border">NO. {{ $model->spk_number }}</span>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <table>
            <tr>
                <td class="p-0 valign-top">
                    <table>
                        <tbody>
                            <tr>
                                <td class="p-0 valign-top">Tanggal</td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 valign-top">Kepada Yth</td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model->vendor->nama }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 valign-top"></td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model->vendor->alamat }}</td>
                            </tr>
                            <tr>
                                <td class="p-0 valign-top">PIC</td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model->pic }}</td>
                            </tr>
                            <tr>
                                <td class=" valign-top">Keterangan</td>
                                <td class=" valign-top">:</td>
                                <td class=" valign-top">{{ $model->payment_description }}</td>
                            </tr>
                            <tr>
                                <td class=" valign-top">Project</td>
                                <td class=" valign-top">:</td>
                                <td class=" valign-top">{{ implode(', ', $projects->pluck('name')->toArray()) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    {{-- <hr style="border: 1px solid grey;"> --}}
    <div class="mt-1">
        <table class="table table-striped table-bordered">
            <thead>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Jumlah </th>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($model->purchaseOrderServiceDetails()->get() as $key => $purchaseOrderServiceDetail)
                    @foreach ($purchaseOrderServiceDetail->purchase_order_service_detail_items as $purchase_order_service_detail_item)
                        <tr>
                            <td class="text-center">{{ $no++ }}.</td>
                            <td>{{ $purchase_order_service_detail_item?->item?->kode }} - {{ $purchase_order_service_detail_item?->item?->nama }}</td>
                            <td class="text-end">{{ formatNumber($purchase_order_service_detail_item->quantity) }}</td>
                            <td class="text-center">{{ $purchase_order_service_detail_item->unit?->name }}</td>
                            <td class="text-end p-0">
                                <table>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td style="vertical-align: top" class="text-right border-0">{{ formatNumber($purchase_order_service_detail_item->price_display) }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="text-end p-0">
                                <table>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="text-right border-0">{{ formatNumber($purchase_order_service_detail_item->discount) }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="text-end p-0">
                                <table>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="text-right border-0">{{ formatNumber($purchase_order_service_detail_item->price_display * $purchase_order_service_detail_item->quantity) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-end" colspan="6"><b>Subtotal</b></td>
                    <td class="text-end fw-bold p-0">
                        <table>
                            <tr>
                                <td class="border-0"></td>
                                <td class="text-right border-0">{{ formatNumber($before_discount) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if ($discount_total)
                    <tr>
                        <td class="text-end" colspan="6"><b>Diskon</b></td>
                        <td class="text-end fw-bold p-0">
                            <table>
                                <tr>
                                    <td class="border-0"></td>
                                    <td class="text-right border-0">{{ formatNumber($discount_total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
                @foreach ($tax_data as $tax)
                    <tr>
                        <td class="text-end" colspan="6"><b>{{ $tax->tax->name }} {{ $tax->value * 100 }}%</b></td>
                        <td class="text-end fw-bold p-0">
                            <table>
                                <tr>
                                    <td class="border-0"></td>
                                    <td class="text-right border-0">{{ formatNumber($tax->total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="text-end fw-bolder" colspan="6"><b>Total</b></td>
                    <td class="text-end fw-bold p-0">
                        <table>
                            <tr>
                                <td class="border-0"><b></b></td>
                                <td class="text-right border-0"><b>{{ formatNumber($model->total) }}</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
        <p class="mt-1">No. PR : {{ implode(', ', $purchase_request_code) }}</p>
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
