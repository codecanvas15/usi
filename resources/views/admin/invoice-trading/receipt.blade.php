<!DOCTYPE html>
<html>

<head>
    <title>Kwitansi - {{ $model->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 12px;
            color: #000;
        }

        @page {
            margin: 28px;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        .table tr th,
        .table tr td {
            padding: 2px 4px;
        }

        .border-bottom {
            border-bottom: 0.5px solid #000;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @include('components.print_out_header_center')
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%">
            <div class="text-center">
                <h1 class="font-medium-3">KWITANSI</h1>
            </div>
        </div>
        <div>
            <table>
                <tbody>
                    <tr>
                        <td class="valign-top" width="15%">No.</td>
                        <td width="5%" class="valign-top text-right">:</td>
                        <td class="valign-top border-bottom">{{ $model->receipt_number }}</td>
                    </tr>
                    <tr>
                        <td class="valign-top">Telah Terima Dari</td>
                        <td class="valign-top text-right">:</td>
                        <td class="valign-top border-bottom">{{ $model->customer->nama }}</td>
                    </tr>
                    <tr>
                        <td class="valign-top">Alamat</td>
                        <td class="valign-top text-right">:</td>
                        <td class="valign-top border-bottom">{{ $model->customer->alamat }}</td>
                    </tr>
                    <tr>
                        <td class="valign-top">Uang Sejumlah</td>
                        <td class="valign-top text-right">:</td>
                        <td class="valign-top border-bottom text-uppercase">{{ Terbilang::make($model->total) }} {{ $model->currency->nama }}</td>
                    </tr>
                    <tr>
                        <td rowspan="{{ $model->invoice_trading_details->count() }}" class="valign-top">Untuk Pembayaran</td>
                        <td rowspan="{{ $model->invoice_trading_details->count() }}" class="valign-top text-right">:</td>
                        <td class="valign-top border-bottom">{{ localDate($model->date) }}, {{ $model->item->nama }}, No. PO {{ $model->nomor_po_external }}, {{ $model->kode }}</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                    <tr>
                        <td class="valign-top">
                            <p>Pembayaran harap ditransfer ke:</p>
                            <ul style="padding-left: 10px">
                                @foreach ($model->bankInternals() as $bank)
                                    <li class="mb-1">
                                        @if ($bank->logo)
                                            <img src="{{ public_path() }}/storage/{{ $bank->logo }}" alt="" height="25px">
                                        @else
                                            <p class="my-0">{{ $bank->nama_bank }}</p>
                                        @endif
                                        <p class="my-0">No. Rekening : {{ $bank->no_rekening }}</p>
                                        <p class="my-0">Atas Nama : {{ $bank->on_behalf_of }}</p>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="bordered" style="padding: 5px; width:200px">
                                {{ $model->currency->simbol }} {{ formatNumber($model->total) }}
                            </div>
                        </td>
                        <td class="valign-top text-center">
                            <p class="mb-0">Surabaya, {{ Carbon\Carbon::parse($model->date)->translatedFormat('d F Y') }}</p>
                            <p class="mt-0">{{ getCompany()->name }}</p>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <p class="my-0"><u>{{ $direktur->name ?? 'Mariana' }}</u></p>
                            <p class="my-0">Direktur</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-1">
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
    <div class="text-center" id="footer">
        <p class="my-0">If you have any questions regarding this receipt, please contact us via:</p>
        <p class="my-0">Mobile/WhatsApp: {{ getCompany()->phone }} ; Email: {{ getCompany()->email }} </p>
    </div>
</body>

</html>
