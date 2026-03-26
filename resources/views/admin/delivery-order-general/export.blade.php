<!DOCTYPE html>
<html>

<head>
    <title>Delivery Order General {{ $model->code }}</title>
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
    <div style="max-width:100%;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top">
                    <h2 class="text-uppercase my-0">Delivery Order (DO)</h2>
                    <b class="doc-number-border font-small-3 text-bold">No. {{ $model->code }}</b>
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="max-width:100%;">
        <table style="width: 100%">
            <tr>
                <td style="width:50%;vertical-align: top;">
                    <table>
                        <tr class="mb-0">
                            <td class="p-0 text-bold mb-0" style="width: 75px;">Tanggal</td>
                            <td class="p-0 text-medium mb-0" style="width: 8px;">:</td>
                            <td class="p-0 text-regular mb-0" style="text-align: left;">{{ localDate($model->date) }}</td>
                        </tr>
                        <tr class="mb-0">
                            <td class="p-0 text-bold mb-0" style="width: 75px;">No. PO</td>
                            <td class="p-0 text-medium mb-0" style="width: 8px;">:</td>
                            <td class="p-0 text-regular mb-0" style="text-align: left;">{{ $model->sale_order_general->no_po_external }}</td>
                        </tr>
                        <tr class="mb-0">
                            <td class="p-0 text-bold mb-0" style="width: 75px;">No. DO External</td>
                            <td class="p-0 text-medium mb-0" style="width: 8px;">:</td>
                            <td class="p-0 text-regular mb-0" style="text-align: left;">{{ $model->external_code }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table>
                        <tr class="m-0">
                            <td class="p-0 text-bold pb-0" style="width: 75px;line-height: 1;">Sold To :</td>
                        </tr>
                        <tr class="m-0">
                            <td class="p-0 text-regular" style="width: 75px;">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="p-0 text-bold pb-0" style="width: 75px;">Alamat :</td>
                        </tr>
                        <tr class="m-0">
                            <td class="p-0 text-regular" style="width: 75px;">{{ $model->customer->alamat }}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="p-0 text-bold pb-0" style="width: 75px;">Telp :</td>
                        </tr>
                        <tr class="m-0">
                            <td class="p-0 text-regular" style="width: 75px;">{{ $model->customer->bussiness_phone }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="max-width: 100%">
        <table class="table mb-1 table-striped table-bordered">
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th width="25%">Jumlah</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($model->delivery_order_general_details as $item)
                    <tr>
                        <td>{{ $item->item?->nama }}</td>
                        <td class="text-right">{{ formatNumber($item->quantity) }}</td>
                        <td class="text-center">{{ $item->item->unit?->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="my-1" style="border-collapse: collapse">
            <tbody>
                <tr>
                    <td class="text-left border-0" rowspan="2" width="20%">
                        <img src="data:image/png;base64, {{ $qr }}" width="70px">
                    </td>
                    <td class="valign-bottom text-center font-xsmall-3 bordered">
                        <b>Maker:</b><br>
                        @if ($model->created_by_user)
                            @if ($model->created_by_user->employee)
                                <span>{{ Str::headline($model->created_by_user->employee->name) }}</span>
                            @else
                                <span>{{ $model->created_by_user->name }}</span>
                            @endif
                        @endif <br>{{ localDateTime($model->created_at) }}
                    </td>
                    <td width="25%" class="valign-top text-center font-xsmall-3 bordered" rowspan="2">
                        <b>Driver</b>
                    </td>
                    <td width="25%" class="valign-top text-center font-xsmall-3 bordered" rowspan="2">
                        <b>Penerima</b>
                    </td>
                </tr>
                <tr>
                    <td class="valign-bottom text-center font-xsmall-3 bordered">
                        <b>Mengetahui:</b><br>
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
