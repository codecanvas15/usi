<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Transfer Stock {{ $model->code }}</title>
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
                    <u>
                        <h2 class="mt-0 text-uppercase">Stock Transfer</h2>
                    </u>
                </td>
        </table>
        <table>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td width="10%">No.</td>
                            <td width="2%">:</td>
                            <td>{{ $model->code }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>{{ localDate($model->date) }}</td>
                        </tr>
                        <tr>
                            <td>Dari</td>
                            <td>:</td>
                            <td>{{ $model->fromWarehouse->nama }}</td>
                        </tr>
                        <tr>
                            <td>Tujuan</td>
                            <td>:</td>
                            <td>{{ $model->toWarehouse->nama }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="row" style="max-width: 100%">
            <table width="100%" style="margin-top: 5px;border: 1px solid black;">
                <tr style="background-color: black; color:white; height: 50px;">
                    <th style="text-align: center; width: 60%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Item</th>
                    <th style="text-align: center; width: 15%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Qty</th>
                    <th style="text-align: center; width: 15%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Satuan</th>
                </tr>
                @foreach ($model->details as $data)
                    <tr style="color:white; height: 50px;">
                        <td style="text-align: left; padding: 8px 20px;border: 1px solid black;color:black;">{{ $data->item->kode }} / {{ $data->item->nama }}</td>
                        <td style="text-align: center; padding: 8px 20px;border: 1px solid black;color:black;">{{ formatNumber($data->qty) }}</td>
                        <td style="text-align: center; padding: 8px 20px;border: 1px solid black;color:black;">{{ $data->item?->unit->name }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <p class="mt-1 mb-0">Keterangan:</p>
    <p class="mt-0">{{ $model->note ?? '-' }}</p>
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
