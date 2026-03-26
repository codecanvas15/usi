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
    @include('components.print_out_header')
    <hr style="border: 0.5px solid grey;">
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
                        <td rowspan="{{ $model->invoice_general_details->count() }}" class="valign-top">Untuk Pembayaran</td>
                        <td rowspan="{{ $model->invoice_general_details->count() }}" class="valign-top text-right">:</td>
                        <td class="valign-top border-bottom">No. PO {{ $model->sale_order_general->no_po_external }}, {{ $model->code }}</td>
                        {{-- @foreach ($model->invoice_general_details->take(1)->values()->all() as $invoice_general_detail)
                            @php
                                $delivery_order = $invoice_general_detail->delivery_order_general_detail->delivery_order_general;
                                $sale_order_general = $delivery_order->sale_order_general;
                            @endphp
                            <td class="valign-top border-bottom">No. PO {{ $sale_order_general->no_po_external }}, {{ $model->code }}</td>
                        @endforeach --}}
                    </tr>
                    {{-- @foreach ($model->invoice_general_details->slice(1, count($model->invoice_general_details)) as $invoice_general_detail)
                        @php
                            $delivery_order = $invoice_general_detail->delivery_order_general_detail->delivery_order_general;
                            $sale_order_general = $delivery_order->sale_order_general;
                        @endphp
                        <tr>
                            <td class="valign-top border-bottom">No. PO {{ $sale_order_general->no_po_external }}, {{ $model->code }}</td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
            <table>
                <tbody>
                    <tr>
                        <td class="valign-top">
                            <p>Pembayaran harap ditransfer ke:</p>
                            <ul style="padding-left: 10px">
                                @foreach ($model->bank_internals ?? [] as $bank_internal)
                                    <li>
                                        @if ($bank_internal->logo)
                                            <img src="{{ public_path() }}/storage/{{ $bank_internal->logo }}" alt="" height="25px">
                                        @else
                                            <p class="my-0">{{ $bank_internal->nama_bank }}</p>
                                        @endif
                                        <p class="my-0">No. Rekening : {{ $bank_internal->no_rekening }}</p>
                                        <p class="my-0">Atas Nama : {{ $bank_internal->on_behalf_of }}</p>
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
