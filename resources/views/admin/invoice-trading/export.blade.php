<!DOCTYPE html>
<html>

<head>
    <title>Invoice Trading {{ $model->code }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 12px;
            color: #000;
        }

        @page {
            margin: 1cm 1cm;
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
    @include('components.print_out_header_center')
    <div>
        <center>
            <h2 class="text-uppercase my-1">INVOICE : {{ $model->kode }}</h2>
        </center>
    </div>

    <div>
        <table class="w-100">
            <tr>
                <td class="valign-top">
                    <table>
                        <tr>
                            <td class="valign-top" width="30%">No. Reference</td>
                            <td class="valign-top" width="2%">:</td>
                            <td class="valign-top">{{ $model->so_trading->nomor_po_external }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top">Date</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ localDate($model->date) }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top">Due Date</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ localDate($model->due_date) }}</td>
                        </tr>
                    </table>
                </td>
                <td width="10%"></td>
                <td class="valign-top">
                    <table>
                        <tr>
                            <td class="valign-top" width="25%">Kepada YTH</td>
                            <td class="valign-top" width="2%">:</td>
                            <td class="valign-top">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top">Alamat</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->customer->alamat }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    @php
        if ($model->is_separate_invoice) {
            if ($type == 'transport') {
                $subtotal = $model->other_cost;
            } else {
                $subtotal = $model->subtotal;
            }
        } else {
            $subtotal = $model->subtotal + $model->other_cost;
        }
    @endphp
    <table class="w-100 table table-bordered mt-1">
        <thead class="bg-dark text-white">
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
        </thead>
        <thead>
            @if (($model->is_separate_invoice && $type != 'transport') || !$model->is_separate_invoice)
                <tr>
                    <td class="text-center">{{ $model->item->nama }}</td>
                    {{-- <td class="text-right">{{ formatNumber($model->so_trading?->so_trading_detail?->jumlah) }} Liter</td> --}}
                    <td class="text-center">{{ formatNumber($model->jumlah) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                    <td class="text-center">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ formatNumber($model->harga) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="text-right">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ formatNumber($model->subtotal) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endif
            @if (($model->is_separate_invoice && $type == 'transport') || !$model->is_separate_invoice)
                @foreach ($model->inv_trading_add_on as $item)
                    <tr>
                        <td class="text-center">{{ $item->item->nama }}</td>
                        <td class="text-center">{{ formatNumber($item->quantity) }} {{ $item->item->unit->name ?? '' }}</td>
                        <td class="text-right">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="p-0 border-0 text-right">{{ formatNumber($item->price) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="text-right">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                        <td class="p-0 border-0 text-right">{{ formatNumber($item->sub_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
            @endif

            @php
                $blank_space = 4;
                $blank_space -= count($model->inv_trading_add_on);
            @endphp
            @for ($i = 0; $i < $blank_space; $i++)
                <tr>
                    <td>
                        <br>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="text-right">Subtotal</td>
                <td class="text-right">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ formatNumber($subtotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            @php
                $total = $subtotal;
            @endphp
            @foreach ($taxes_id as $item)
                @php
                    $total += $item['amount'];
                @endphp
                <tr>
                    <td colspan="3" class="text-right">{{ $item['name'] }}</td>
                    <td class="text-right">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ formatNumber($item['amount']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end text-bold"><b>Total</b></td>
                <td class="text-end text-bold">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ formatNumber($total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- <table class="w-100 table table-bordered mt-1">
        <thead class="bg-dark text-white">
            <th>Kuantitas Kirim</th>
            <th>Kuantitas Diterima</th>
            <th>{{ Str::headline('lost_tolerance') }}</th>
        </thead>
        <tbody>
            <tr>
                <td class="text-right">{{ formatNumber($model->total_jumlah_dikirim) }} Liter</td>
                <td class="text-right">{{ formatNumber($model->total_jumlah_diterima) }} Liter</td>
                <td class="text-right">{{ $model->lost_tolerance_type == 'percent' ? formatNumber($model->lost_tolerance * 100) : formatNumber($model->lost_tolerance) }} {{ Str::headline($model->lost_tolerance_type) }}</td>
            </tr>
        </tbody>
    </table> --}}

    <p class="text-uppercase"><b>Terbilang : </b> {{ Terbilang::make($total) }} {{ $model->currency->nama }}</p>

    <table class="mt-1">
        <tbody>
            <tr>
                <td class="p-0 text-left valign-top" width="50%">
                    <table class="w-100 p-0">
                        <tbody>
                            @foreach ($model->bankInternals() as $bank_internal)
                                <tr>
                                    <td class="p-0 text-left valign-top">
                                        <table>
                                            <tr>
                                                <td width="14%" class="p-0 text-left">
                                                    @if ($bank_internal->logo)
                                                        <img src="{{ public_path('storage/' . $bank_internal->logo) }}" alt="" width="60px">
                                                    @else
                                                        <span>{{ $bank_internal->nama_bank }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-0">
                                                    <p class="m-0">Bank : <b>{{ $bank_internal->nama_bank }}</b></p>
                                                    @if ($bank_internal->branch_name)
                                                        <p class="m-0">Cabang: <b>{{ $bank_internal->branch_name }}</b></p>
                                                    @endif
                                                    <p class="m-0">Account Name : <b>{{ $bank_internal->on_behalf_of }}</b></p>
                                                    <p class="m-0">Account No : <b>{{ $bank_internal->no_rekening }}</b></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                    <ol class="pl-1">
                        <li>Pembayaran harap di transfer ke nomer rekening di atas</li>
                        <li>Pembayaran dengan uang kontan hanya sah apabila ada kwitansi resmi dari perusahaan</li>
                        <li>Pembayaran dengan bilyet giro/cheque dianggap sah apabila sudah masuk</li>
                        <li>Overdue penalty rate is 2%/month pro rata</li>
                    </ol>

                    <p>Term of payment : <b class="text-uppercase">{{ $model->customer->term_of_payment }} - {{ $model->due }}</b></p>

                    <img src="data:image/png;base64, {{ $qr }}" width="70px" class="mt-1">
                </td>
                <td width="10%"></td>
                <td class="border-0 p-0 valign-top text-center">
                    <p class="my-0">Surabaya, {{ \Carbon\Carbon::parse($model->date)->translatedFormat('d F Y') }}</p>
                    <p class="my-0 text-bold">{{ getCompany()->name }}</p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p class="my-0"><u>{{ $direktur->name ?? '-' }}</u></p>
                    <p class="my-0">Direktur</p>
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
</body>

</html>
