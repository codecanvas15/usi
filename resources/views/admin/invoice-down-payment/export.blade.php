<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice {{ $model->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 8pt;
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
    </style>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @include('components.print_out_header')
    <hr style="border: 0.5px solid grey;">
    <div class="text-center">
        <h2>INVOICE DOWN PAYMENT: {{ $model->code }}</h2>
    </div>

    <div>
        <table class="w-100">
            <tr>
                <td class="valign-top p-0">
                    <table>
                        <tr>
                            <td class="p-0 text-bold" width="25%">Date</td>
                            <td class="p-0" width="2%">:</td>
                            <td class="p-0">{{ localDate($model->date) }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 text-bold" width="25%">Due Date</td>
                            <td class="p-0" width="2%">:</td>
                            <td class="p-0">{{ localDate($model->due_date) }}</td>
                        </tr>
                        @if (count($so_references) > 0)
                            <tr>
                                <td class="p-0 text-bold valign-top" width="25%">Reference No.</td>
                                <td class="p-0 valign-top" width="2%">:</td>
                                <td class="p-0 valign-top">{{ implode(', ', $so_references) }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
                <td width="10%"></td>
                <td class="valign-top p-0">
                    <table>
                        <tr>
                            <td class="valign-top p-0 text-right text-bold" width="25%">Kepada YTH</td>
                            <td class="valign-top p-0" width="2%">:</td>
                            <td class="valign-top p-0">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top p-0 text-right text-bold">Alamat</td>
                            <td class="valign-top p-0">:</td>
                            <td class="valign-top p-0">{{ $model->customer->alamat }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <table class="small-font table table-responsive table-stripe table-bordered border-secondary mt-1">
        <thead class="bg-dark text-white small-font">
            <th>Keterangan</th>
            <th colspan="3">Nominal</th>
            {{-- <th class="col-3"></th> --}}
        </thead>
        <thead>
            <tr>
                <td colspan="3">{{ $model->note }}</td>
                {{-- <td class="text-right">{{ $model->note }}</td> --}}
                {{-- <td class="text-right">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td> --}}
                <td class="text-end">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="text-end">Down Payment</td>
                <td class="text-end">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->down_payment) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-end">Tax</td>
                <td class="text-end">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->tax_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-end text-bold">Total</td>
                <td class="text-end text-bold">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->grand_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="text-uppercase"><b>Terbilang : </b> {{ Terbilang::make($model->grand_total) }} {{ $model->currency->nama }}</p>

    <table class="mt-1">
        <tbody>
            <tr>
                <td class="p-0 text-left valign-top" width="50%">
                    <table class="w-100 p-0">
                        <tbody>
                            <tr>
                                <td class="p-0 text-left valign-top">
                                    <p class="my-0">Account Name : <br><b>{{ $model->bank_internal->on_behalf_of }}</b></p>
                                    <table>
                                        <tr>
                                            <td width="14%" class="p-0 text-left">
                                                @php
                                                    $logoPath = public_path('storage/' . $model->bank_internal->logo);
                                                @endphp

                                                @if (file_exists($logoPath) && $model->bank_internal->logo)
                                                    <img src="{{ public_path('storage/' . $model->bank_internal->logo) }}" alt="{{ $model->bank_internal->nama_bank }}" width="60px">
                                                @else
                                                    <span>{{ $model->bank_internal->nama_bank }}</span>
                                                @endif
                                            </td>
                                            <td class="p-0">
                                                Account No : <b>{{ $model->bank_internal->no_rekening }}</b>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <ol class="pl-1">
                        <li>Pembayaran harap di transfer ke nomer rekening di atas</li>
                        <li>Pembayaran dengan uang kontan hanya sah apabila ada kwitansi resmi dari perusahaan</li>
                        <li>Pembayaran dengan bilyet giro/cheque dianggap sah apabila sudah masuk</li>
                    </ol>

                    <p>Term of payment : <b class="text-uppercase">{{ $model->term_of_payments }} - {{ $model->due }}</b></p>

                    <img src="data:image/png;base64, {{ $qr }}" width="70px" class="mt-1">
                </td>
                <td width="10%"></td>
                <td class="border-0 p-0 valign-top text-center">
                    <p class="my-0">{{ $model->branch->name }}, {{ \Carbon\Carbon::parse($model->date)->translatedFormat('d F Y') }}</p>
                    <p class="my-0 text-bold">Hormat Kami</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p class="my-0">{{ getCompany()->name }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">

                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-center" id="footer">
        <p class="my-0">If you have any questions regarding this invoice, please contact us via:</p>
        <p class="my-0">Mobile/WhatsApp: {{ getCompany()->phone }} ; Email: {{ getCompany()->email }} </p>
    </div>

    {{-- <table class="w-100">
        <td>
            <p class="my-0">Account Name : <br>
                <b>{{ $model->bank_internal->on_behalf_of }}</b>
            </p>
            <table>
                <tbody>
                    <tr>
                        <td width="10%" class="p-0">
                            @if ($model->bank_internal->logo)
                                <img src="{{ public_path('storage/' . $model->bank_internal->logo) }}" alt="" height="18px">
                            @else
                                <span style="color: #ED3338" class="text-bold">{{ $model->bank_internal->nama_bank }}</span>
                            @endif
                        </td>
                        <td class="p-0">
                            Account No : <b>{{ $model->bank_internal->no_rekening }}</b>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="text-end">
            <p class="text-bold my-0" style="color:#ED3338; ">Term of payment</p>
            <p class="text-medium text-uppercase my-0" style="">{{ $model->term_of_payments }} - {{ $model->due }}<br>
        </td>
    </table>

    <div id="footer" class="mt-1">
        <table class="table table-responsive">
            <tbody>
                <tr>
                    <td class="border-0 p-0">
                        <img src="data:image/png;base64, {{ $qr }}" width="70px">
                    </td>
                </tr>
            </tbody>
        </table>
    </div> --}}
</body>

</html>
