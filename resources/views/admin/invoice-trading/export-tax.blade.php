<!DOCTYPE html>
<html>

<head>
    <title>Export Invoice Trading {{ $model->kode }} - {{ getCompany()->name }}</title>
    <style>
        @font-face {
            font-family: 'montserrat';
            src: url('fonts/montserrat.ttf') format("truetype");
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        @font-face {
            font-family: 'montserrat-bold';
            src: url('fonts/montserrat-bold.ttf') format("truetype");
            font-weight: 500; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        body {
            font-family: "montserrat";
            font-size: 12px
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table>thead>tr>th {
            padding: 5px;
            border: 1px solid #000;
            font-size: 12px;
            font-weight: 700;
        }

        .table>tbody>tr>td {
            padding: 5px;
            border: 1px solid #000;
            font-size: 12px;
            font-weight: 400;
        }

        .border-none {
            border: none;
        }

        .table-border-none {
            border: none;
        }

        .table-border-none td {
            border: none;
        }

        .table-border-none th {
            border: none;
        }

        .table-in {
            width: 100%;
            border-collapse: collapse;
        }

        .table-in>thead>tr>th {
            padding: 5px;
            border: 1px solid #000;
            font-size: 12px;
            font-weight: 700;
            border-top: none;
        }

        .table-in>tbody>tr>td {
            padding: 0px;
            font-size: 12px;
            font-weight: 400;
            border-bottom: none;
            text-align: center
        }
    </style>

    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">

<body style="font-size: 12px">
    <div>
        <center><span class="bold text-center" style="justify-content:center;font-size:25px;margin-bottom:10px">{{ $model->kode }}</span></center><br>
    </div>

    <div class="">
        <table class="table">
            @php
                $reference = $model->reference;
                if ($model->is_separate_invoice) {
                    if ($type == 'transport') {
                        $reference = explode('/', $model->reference)[1] ?? $model->reference;
                    } else {
                        $reference = explode('/', $model->reference)[0] ?? $model->reference;
                    }
                }
            @endphp
            <thead>
                <tr>
                    <th class="text-left">{{ Str::headline('Kode dan nomor seri faktur pajak') }} : {{ $reference }}</th>
                </tr>
                <tr>
                    <th class="text-left">{{ Str::headline('Pengusaha kena pajak') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 0px">
                        <table style="border-collapse: collapse; border: none" cellspacing="0" cellpadding="0">
                            @foreach ([
        'Nama' => getCompany()->name,
        'Alamat' => getCompany()->address,
        'NPWP' => getCompany()->npwp,
    ] as $key => $item)
                                <tr>
                                    <td style="border: none; vertical-align: top" width="35%">{{ $key }}</td>
                                    <td style="border: none; vertical-align: top" width="5%">:</td>
                                    <td style="border: none; vertical-align: top">{{ $item }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td><b>{{ Str::headline('Pembeli barang kena pajak / penerima jasa kena pajak') }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 0px; border-bottom: none">
                        <table style="border-collapse: collapse; border: none" cellspacing="0" cellpadding="0">
                            @foreach ([
        'Nama' => $model->customer?->nama,
        'Alamat' => $model->customer?->alamat,
        'NPWP' => $model->customer?->npwp,
    ] as $key => $item)
                                <tr>
                                    <td style="border: none; vertical-align: top" width="35%">{{ $key }}</td>
                                    <td style="border: none; vertical-align: top" width="5%">:</td>
                                    <td style="border: none; vertical-align: top">{{ $item }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px">
                        <table class="table-in" style="border: none">
                            <thead>
                                <tr>
                                    <th style="vertical-align: middle; border-left:none" width="5%">No Urt</th>
                                    <th style="vertical-align: middle" width="65%">{{ Str::headline('Nama barang kena pajak / jasa kena pajak') }}</th>
                                    <th style="vertical-align: middle; border-right:none" width="30%">{{ Str::headline('harga jual / penggantian / uang muka / termin') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $subtotal = 0;
                                    $iteration = 0;
                                    $total_tax = 0;
                                @endphp
                                @if (($model->is_separate_invoice && $type != 'transport') || !$model->is_separate_invoice)
                                    @php
                                        $subtotal += $model->subtotal;
                                        $total_tax += $model->invoice_trading_taxes->sum('amount');
                                        $iteration++;
                                    @endphp
                                    <tr>
                                        <td style="border-right: 1px solid black">{{ $iteration }}</td>
                                        <td style="padding: 0%;border-right: 1px solid black">
                                            <table style="border: none">
                                                <tr>
                                                    <td style="border: none">
                                                        {{ $model->item?->nama }}
                                                    </td>
                                                    <td align="right" style="border: none">
                                                        {{ number_format($model->harga, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td align="right" style="padding-left: 0%; padding-right: 0%">
                                            <table style="border: none">
                                                <tr>
                                                    <td style="border: none">
                                                        {{ number_format($model->jumlah, 0, ',', '.') }}
                                                    </td>
                                                    <td align="right" style="border: none; padding: 0px 2px 0px 0px">
                                                        {{ floatDotFormat($model->subtotal) }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                                @if (($model->is_separate_invoice && $type == 'transport') || !$model->is_separate_invoice)
                                    @php
                                        $subtotal += $model->other_cost;
                                        $total_tax += $model->inv_trading_add_on->map(fn($i) => $i->inv_trading_add_on_tax->sum('total'))->sum();
                                        $iteration++;
                                    @endphp
                                    @foreach ($model->inv_trading_add_on ?? [] as $item)
                                        <tr>
                                            <td style="border-right: 1px solid black">{{ $iteration }}</td>
                                            <td style="padding: 0%;border-right: 1px solid black">
                                                <table style="border: none">
                                                    <tr>
                                                        <td style="border: none">
                                                            {{ $item->item?->nama }}
                                                        </td>
                                                        <td align="right" style="border: none">
                                                            {{ number_format($item->price, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td align="right" style="padding-left: 0%; padding-right: 0%">
                                                <table style="border: none">
                                                    <tr>
                                                        <td style="border: none">
                                                            {{ number_format($item->quantity, 0, ',', '.') }}
                                                        </td>
                                                        <td align="right" style="border: none; padding: 0px 2px 0px 0px">
                                                            {{ floatDotFormat($item->sub_total) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px">
                        <table style="border-collapse: collapse; border: none; padding: 0px">
                            <tr>
                                <td width="70%" style="border-right: 1px solid black;">{{ Str::headline('harga jual / penggantian / uang muka / termin') }}</td>
                                <td style="text-align: right; padding-right: 5px">{{ floatDotFormat($subtotal) }}</td>
                            </tr>
                            <tr>
                                <td width="70%" style="border-right: 1px solid black;">{{ Str::headline('dikurangi potongan harga') }}</td>
                                <td style="text-align: right; padding-right: 5px">{{ floatDotFormat(0) }}</td>
                            </tr>
                            <tr>
                                <td width="70%" style="border-right: 1px solid black;">{{ Str::headline('dikurangi uang muka yang telah diiterima') }}</td>
                                <td style="text-align: right; padding-right: 5px">{{ floatDotFormat(0) }}</td>
                            </tr>
                            <tr>
                                <td width="70%" style="border-right: 1px solid black;">{{ Str::headline('dasar pengenaan pajak') }}</td>
                                <td style="text-align: right; padding-right: 5px">{{ floatDotFormat($subtotal) }}</td>
                            </tr>

                            @foreach ($taxes_id as $item)
                                <tr>
                                    <td width="70%" style="border-right: 1px solid black; border-bottom: none;">PPN = {{ $item['value'] * 100 }}% X Dasar Pengenaan Pajak</td>
                                    <td style="border-bottom: none; text-align: right; padding-right: 5px">{{ floatDotFormat($subtotal * $item['value']) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="footer">
        <div class="row">
            <table style="width: 100%;margin-top:20px;">
                <tr>
                    <td style="width: 65%; vertical-align: top">
                        <div>
                            <img src="data:image/png;base64, {{ $qr }}" width="70px">
                        </div>
                    </td>
                </tr>
            </table>
            <table class=" my-2">
                <tbody>
                    <tr>
                        <td class="p-0">
                            @if ($model->created_by_user)
                                @if ($model->created_by_user?->employee)
                                    <span>Dibuat Oleh</span> : <span> {{ $model->created_by_user?->employee?->name }} / {{ $model->created_by_user?->employee?->NIK }}</span>
                                @else
                                    <span>Dibuat Oleh</span> : <span> {{ $model->created_by_user?->name }} - {{ $model->created_by_user?->email }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="text-end p-0">
                            @if ($model->approved_by_user)
                                @if ($model->approved_by_user?->employee)
                                    <span>Disetujui Oleh</span> : <span> {{ $model->approved_by_user?->employee?->name }} / {{ $model->approved_by_user?->employee?->NIK }}</span>
                                @else
                                    <span>Disetujui Oleh</span> : <span> {{ $model->approved_by_user?->name }} - {{ $model->approved_by_user?->email }}</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
