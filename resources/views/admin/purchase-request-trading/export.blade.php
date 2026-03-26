<!DOCTYPE html>
<html>

<head>
    <title>Purchase Request - {{ $model->kode }}</title>
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
    @include('components.print_out_header_center')
    <div style="max-width: 100%">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top">
                    <h2 class="text-uppercase my-0">Purchase Request : No. {{ $model->code }}</h2>
                </td>
                <td class="text-right">
                    <b>Date: {{ localDate($model->date) }}</b>
                </td>
            </tr>
        </table>
    </div>
    <table>
        <tr>
            <td class="p-0 valign-top" width="45%">
                <table class="small-font">
                    <tbody>
                        <tr>
                            <td class=" valign-top p-0" width="20%">Customer</td>
                            <td class=" valign-top p-0" width="2%">:</td>
                            <td class=" valign-top p-0 text-uppercase">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top p-0" width="20%">SH Number</td>
                            <td class=" valign-top p-0" width="2%">:</td>
                            <td class=" valign-top p-0 text-uppercase">{{ $model->sh_number->kode }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top p-0">Supply Point</td>
                            <td class=" valign-top p-0">:</td>
                            <td class=" valign-top p-0 text-uppercase">{{ $model->sh_number->sh_number_details()->where('type', 'Supply Point')->first()?->alamat }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="5%"></td>
            <td class="p-0 valign-top" width="50%">
                <table class="small-font">
                    <tbody>
                        <tr>
                            <td class=" valign-top p-0" width="15%">Ship to</td>
                            <td class=" valign-top p-0" width="2%">:</td>
                            <td class=" valign-top p-0 text-uppercase">{{ $model->sh_number->sh_number_details()->where('type', 'Drop Point')->first()?->alamat }}</td>
                        </tr>
                        <tr>
                            <td class=" valign-top p-0">Catatan</td>
                            <td class=" valign-top p-0">:</td>
                            <td class=" valign-top p-0 text-uppercase">{{ $model->note }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <div class="container" style="color: black">
        <div class="mt-1" style="max-width:100%;">
            <span>
                Requested Item :
            </span>
        </div>
        <div class="row" style="max-width: 100%">
            <table width="100%" style="margin-top: 5px;" class="table table-bordered">
                <tr style="background-color: black; color:white; height: 50px;">
                    <th style="text-align: center; width: 50%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Item</th>
                    <th style="text-align: center; width: 16.6%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Qty</th>
                    <th style="text-align: center; width: 16.6%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Satuan</th>
                </tr>
                @foreach ($model->purchase_request_trading_details as $data)
                    <tr style="color:white; height: 50px;">
                        <td style="text-align: left; padding: 8px 20px;color:black;">{{ $data->item->nama ?? '' }}</td>
                        <td style="text-align: center; padding: 8px 20px;color:black;">{{ formatNumber($data->qty) }}</td>
                        <td style="text-align: center; padding: 8px 20px;color:black;">{{ $data->item->unit?->name ?? '' }}</td>
                    </tr>
                @endforeach
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
    </div>
</body>

</html>
