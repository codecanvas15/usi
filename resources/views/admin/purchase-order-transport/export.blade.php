<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order Transport - {{ $model->kode }}</title>
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
                <td style="width: 75%; vertical-align: top">
                    <h2 class="text-uppercase my-0">Purchase Order</h2>
                    <b style="border-top: 1px solid #000; padding-top: 2px" class="font-small-3">No. {{ $model->kode }}</b>
                </td>
            </tr>
        </table>
    </div>

    <div>
        <table>
            <tr>
                <td style="width:50%; vertical-align: top;" class="p-0">
                    <table class="small-font p-0">
                        @if ($model->so_trading)
                            <tr>
                                <td class="valign-top p-0">No. SO </td>
                                <td class="valign-top p-0">: </td>
                                <td class="valign-top p-0" style="text-align: left;">{{ $model->so_trading->nomor_so }} </td>
                            </tr>
                            <tr>
                                <td class="valign-top p-0">No. Referensi PO </td>
                                <td class="valign-top p-0">: </td>
                                <td class="valign-top p-0" style="text-align: left;">{{ $model->so_trading->nomor_po_external ?? '' }} </td>
                            </tr>
                            <tr>
                                <td class="valign-top p-0" width="12%">Customer </td>
                                <td class="valign-top p-0" width="2%">: </td>
                                <td class="valign-top p-0" width="35%" style="text-align: left;">{{ $model->so_trading->customer->nama }} </td>
                            </tr>
                            <tr>
                                <td class="valign-top p-0">SH No. </td>
                                <td class="valign-top p-0">: </td>
                                <td class="valign-top p-0" style="text-align: left;">{{ $model->so_trading->sh_number->kode }} </td>
                            </tr>
                            @foreach ($model->so_trading->sh_number->sh_number_details as $item)
                                @php
                                    $type = $item->type;
                                @endphp

                                @if ($item->type == 'Drop Point')
                                    @php
                                        $type = 'Ship To';
                                    @endphp
                                @endif
                                <tr>
                                    <td class="valign-top p-0">{{ $type }} </td>
                                    <td class="valign-top p-0">: </td>
                                    <td class="valign-top p-0" style="text-align: left;">{{ $item->alamat }} </td>
                                </tr>
                            @endforeach
                        @else
                        @endif
                    </table>
                </td>
                <td width="7%"></td>
                <td style="width: 45%; vertical-align: top;">
                    <table class="small-font">
                        <tr class="m-0">
                            <td class="valign-top" width="20%">Date </td>
                            <td class="valign-top" width="2%">: </td>
                            <td class="valign-top" style="text-align: left;">{{ localDate($model->purchase->tanggal) }} </td>
                        </tr>
                        <tr class="m-0">
                            <td class="valign-top">Kepada Yth </td>
                            <td class="valign-top">: </td>
                            <td class="valign-top" style="text-align: left;">{{ $model->vendor->nama }} </td>
                        </tr>
                        <tr class="m-0">
                            <td class="valign-top">Alamat </td>
                            <td class="valign-top">: </td>
                            <td class="valign-top" style="text-align: left;">{{ $model->vendor->alamat }} </td>
                        </tr>
                        <tr class="m-0">
                            <td class="valign-top">Warehouse </td>
                            <td class="valign-top">: </td>
                            <td class="valign-top" style="text-align: left;">{{ $model->ware_house?->nama }} </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row showTable" style="max-width: 100%">
        <table class="table table-responsive table-striped table-bordered">
            <thead>
                <th>Jumlah DO</th>
                <th>Jumlah</th>
                <th>DO Dibuat</th>
                <th>Harga</th>
                <th>Total</th>
            </thead>
            <tbody>
                @foreach ($model->purchase_transport_details as $item)
                    <tr>
                        <td class="text-right">{{ formatNumber($item->jumlah_do) }}</td>
                        <td class="text-right">{{ formatNumber($item->jumlah) }}</td>
                        <td class="text-right">{{ formatNumber($item->delivery_count) }}</td>
                        <td class="p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->harga) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->harga * ($item->jumlah * $item->jumlah_do)) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-end">{{ Str::headline('subtotal') }}</td>
                    <td class="p-0">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->sub_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                @foreach ($model->purchase_transport_taxes as $item)
                    <tr>
                        <td colspan="4" class="text-right">{{ $item->tax->tax_name_without_percent }} {{ $item->value * 100 }}%</td>
                        <td class="p-0">
                            <table class="border-0">
                                <tbody>
                                    <tr class="border-0">
                                        <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($item->value * $model->sub_total) }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="4" class="text-end">{{ Str::headline('total') }}</th>
                    <td class="p-0 text-bold">
                        <table class="border-0">
                            <tbody>
                                <tr class="border-0">
                                    <td class="border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="text-uppercase"><b>Terbilang : </b>{{ Terbilang::make($model->total) }} {{ $model->currency->nama }}</p>
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
