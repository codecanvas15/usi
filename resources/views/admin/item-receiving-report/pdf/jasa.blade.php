<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ Str::headline('berita acara serah terima') }} - {{ $model->kode }}</title>
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
    @include('components.print_out_header')

    <div style="max-width:100%;">
        <table style="width: 100%;">
            <tr>
                <td colspan="2">
                    <h2 class="text-uppercase my-0" style="text-decoration: underline">{{ Str::headline('berita acara serah terima') }}</h2>
                    <span class="font-small-3 text-bold">NO. {{ $model->kode }}</span>
                </td>
            </tr>
            <tr>
                <td width="60%">
                    <table>
                        <tbody>
                            <tr>
                                <td>Referensi</td>
                                <td>:</td>
                                <td>{{ $model?->reference->spk_number ?? ($model?->reference->kode ?? ($model?->reference->code ?? $model?->reference->nomor_po)) }}</td>
                            </tr>
                            <tr>
                                <td>Vendor</td>
                                <td>:</td>
                                <td>{{ $model->vendor?->nama }}</td>
                            </tr>
                            <tr>
                                <td class="valign-top">Project</td>
                                <td class="valign-top">:</td>
                                <td class="valign-top">{{ implode(', ', $projects->pluck('name')->toArray()) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td class="valign-top">
                    <table>
                        <tr>
                            <td class="text-right" style="width:45%;"> Date </td>
                            <td style="width:2%"> : </td>
                            <td class="text-bold" style="width:50%"> {{ \Carbon\Carbon::parse($model->date_receive)->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="row mt-1" style="max-width: 100%">
            <table width="100%" class="table table-bordered">
                <tr style="background-color: black; color:white; height: 50px;">
                    <th style="text-align: center; width: 5%; padding: 8px;border: 1px solid black;" class="text-semi-bold">No.</th>
                    <th style="text-align: center; width: 60%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Item</th>
                    <th style="text-align: center; width: 15%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Qty</th>
                    <th style="text-align: center; width: 15%; padding: 8px;border: 1px solid black;" class="text-semi-bold">Satuan</th>
                </tr>
                @foreach ($model->item_receiving_report_details as $data)
                    <tr style="color:white; height: 50px;">
                        <td style="text-align: left; padding: 8px 20px;border: 1px solid black;color:black;">{{ $loop->iteration }}.</td>
                        <td style="text-align: left; padding: 8px 20px;border: 1px solid black;color:black;">{{ $data->item->kode }} / {{ $data->item->nama }}</td>
                        <td style="text-align: center; padding: 8px 20px;border: 1px solid black;color:black;">{{ formatNumber($data->jumlah_diterima) }}</td>
                        <td style="text-align: center; padding: 8px 20px;border: 1px solid black;color:black;">{{ $data->item?->unit->name }}</td>
                    </tr>
                @endforeach
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
