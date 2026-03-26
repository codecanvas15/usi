<!DOCTYPE html>
<html>

<head>
    <title>RETUR PEMBELIAN - {{ $model->code }}</title>
    <style type="text/css">
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
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%">
            <div class="text-center">
                <h2 class="">RETUR PEMBELIAN</h2>
            </div>
        </div>
        <div>
            <table class="mt-1">
                <tr>
                    <td class=" p-0">
                        <table>
                            <tbody>
                                <tr>
                                    <td>No. Retur</td>
                                    <td>:</td>
                                    <td> {{ $model->code }}</td>
                                </tr>
                                <tr>
                                    <td>No. LPB</td>
                                    <td>:</td>
                                    <td> {{ $model->item_receiving_report?->kode }}</td>
                                </tr>
                                <tr>
                                    <td>Gudang</td>
                                    <td>:</td>
                                    <td> {{ $model->ware_house?->nama }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="20%"></td>
                    <td class=" p-0">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td> {{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <td>Vendor</td>
                                    <td>:</td>
                                    <td> {{ $model->vendor?->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Reference</td>
                                    <td>:</td>
                                    <td> {{ $model->reference }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row mt-2">
            <table class="table table-bordered">
                <tr>
                    <th class=""><b>Item</b></th>
                    <th class=""><b>Satuan</b></th>
                    <th class=""><b>Qty LPB</b></th>
                    <th class=""><b>Qty</b></th>
                    <th class=""><b>Harga</b></th>
                    <th class=""><b>Sub total</b></th>
                    <th class=""><b>Total pajak</b></th>
                    <th class=""><b>Total</b></th>
                </tr>
                @foreach ($model->purchase_return_details as $item)
                    <tr>
                        <td>{{ $item->item?->nama }} - {{ $item->item?->kode }}</td>
                        <td class="text-center">{{ $item->unit?->name }}</td>
                        <td class="text-right">{{ formatNumber($item->lpb_qty) }}</td>
                        <td class="text-right">{{ formatNumber($item->qty) }}</td>
                        <td class="text-right">{{ $model->currency->simbol }} {{ formatNumber($item->price) }}</td>
                        <td class="text-right">{{ $model->currency->simbol }} {{ formatNumber($item->subtotal) }}</td>
                        <td class="text-right">{{ $model->currency->simbol }} {{ formatNumber($item->tax_amount) }}</td>
                        <td class="text-right">{{ $model->currency->simbol }} {{ formatNumber($item->total) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4"></td>
                    <td class="text-right" style="font-weight: bolder">{{ $model->currency->simbol }} {{ formatNumber($model->purchase_return_details->sum('price')) }}</td>
                    <td class="text-right" style="font-weight: bolder">{{ $model->currency->simbol }} {{ formatNumber($model->purchase_return_details->sum('subtotal')) }}</td>
                    <td class="text-right" style="font-weight: bolder">{{ $model->currency->simbol }} {{ formatNumber($model->purchase_return_details->sum('tax_amount')) }}</td>
                    <td class="text-right" style="font-weight: bolder">{{ $model->currency->simbol }} {{ formatNumber($model->purchase_return_details->sum('total')) }}</td>
                </tr>
            </table>
            <p class=""><b>Terbilang : </b> {{ strtoupper(Terbilang::make($model->total)) }} {{ strtoupper($model->currency->nama) }}</p>
        </div>

        {{-- <div>
            <table class="table table-bordered">
                <tr>
                    <th width="25%"><span class="bold">Penerima</span></th>
                    <th width="25%"><span class="bold">Kasir</span></th>
                    <th width="25%"><span class="bold">Pembukuan</span></th>
                    <th width="25%"><span class="bold">Divisi Manager</span></th>
                </tr>
                <tr>
                    <td >
                        <div style="text-align: center; min-height: 80px;"></div>
                    </td>
                    <td >
                        <div style="text-align: center; min-height: 80px;"></div>
                    </td>
                    <td >
                        <div style="text-align: center; min-height: 80px;"></div>
                    </td>
                    <td >
                        <div style="text-align: center; min-height: 80px;"></div>
                    </td>
                </tr>
            </table>
        </div> --}}

    </div>
    <div id="footer">
        <div class="row">
            <table>
                <tr>
                    <td style="width: 25%">
                        <div>
                            <img src="data:image/png;base64, {{ $qr }}" width="80px">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
