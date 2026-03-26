<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pemakaian Stock {{ $model->code }}</title>
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
    <table style="width: 100%;">
        <tr>
            <td style="width: 75%; vertical-align: top">
                <u>
                    <h2 class="mt-0 text-uppercase">PEMAKAIAN STOCK</h2>
                </u>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td class="p-0">
                <table>
                    <tbody>
                        <tr>
                            <td>No.</td>
                            <td>:</td>
                            <td> {{ $model->code }}</td>
                        </tr>
                        <tr>
                            <td>Divisi</td>
                            <td>:</td>
                            <td> {{ $model->division?->name ?? '-' }}</td>
                        </tr>
                        @if ($model->employee)
                            <tr>
                                <td>Pegawai</td>
                                <td>:</td>
                                <td> {{ $model->employee?->name ?? '-' }} / {{ $model->employee?->NIK ?? '-' }}</td>
                            </tr>
                        @endif
                        @if ($model->fleet)
                            <tr>
                                <td>Kendaraan</td>
                                <td>:</td>
                                <td> {{ $model->fleet?->name ?? '-' }} / {{ $model->fleet?->type ?? '' }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </td>
            <td width="20%"></td>
            <td class="p-0">
                <table>
                    <tbody>
                        <tr>
                            <td width="20%">Tanggal</td>
                            <td width="2%">:</td>
                            <td> {{ localDate($model->date) }}</td>
                        </tr>
                        <tr>
                            <td>Gudang</td>
                            <td>:</td>
                            <td> {{ $model->ware_house?->nama ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div>
        <table class="table table-bordered">
            <tr>
                <th><b>Item</b></th>
                <th><b>Qty</b></th>
                <th><b>Keperluan</b></th>
            </tr>

            @foreach ($model->stock_usage_details as $item)
                <tr>
                    <td>{{ $item->item->kode }} - {{ $item->item->nama }}</td>
                    <td class="text-right">{{ formatNumber($item->quantity) }} {{ $item->unit->name }}</td>
                    <td>{{ $item->necessity }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="row mt-1">
        <p class="small-font my-0">Keterangan:</p>
        <p class="my-0">{{ $model->note }}</p>
    </div>
    <div id="footer" class="mt-1">
        <div class="row">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 25%">
                        <div>
                            <img src="data:image/png;base64, {{ $qr }}" width="70px">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
