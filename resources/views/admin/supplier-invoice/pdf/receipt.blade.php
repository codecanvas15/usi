<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tanda Terima</title>
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

        .br-dotted-bottom {
            border-bottom: 1px dotted #000;
        }

        .border-all {
            border: 0.5px solid #000;
        }
    </style>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @include('components.print_out_header')
    <div class="mt-1">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <th class="text-left" width="20%">Tanggal</th>
                    <td width="2%">:</td>
                    <td>{{ date('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th class="text-left">Terima Dari</th>
                    <td>:</td>
                    <td>{{ $vendor->nama }}</td>
                </tr>
                <tr>
                    <th class="text-left">Kembali Tanggal</th>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <th class="text-left">Kwitansi/Nota</th>
                    <td>:</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table class="mt-1">
            <tbody>
                @foreach ($supplier_invoices as $key => $supplier_invoice)
                    <tr>
                        <td style="vertical-align: top" width="2%">{{ ++$key }}.</td>
                        <td style="vertical-align: top" width="15%" class="br-dotted-bottom">
                            INV. {{ $supplier_invoice->reference }} <br>
                            <small>{{ $supplier_invoice->code }}</small>
                        </td>
                        <td style="vertical-align: top" width="2%">
                            <input type="checkbox" style="height:12px; margin-top:-4px">
                        </td>
                        <td style="vertical-align: top" width="15%" class="br-dotted-bottom">FP. {{ $supplier_invoice->tax_reference }}</td>
                        <td style="vertical-align: top" width="2%">
                            <input type="checkbox" style="height:12px; margin-top:-4px">
                        </td>
                        <td style="vertical-align: top" width="15%" class="br-dotted-bottom">
                            @php
                                $counter = 0;
                            @endphp
                            @foreach ($supplier_invoice->detail as $key => $detail)
                                @if ($detail->item_receiving_report->do_code_external)
                                    @php
                                        $counter++;
                                    @endphp
                                    <p class="my-0">{!! $key == 0 ? 'SJ.' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' !!} - {{ $detail->item_receiving_report->do_code_external ?? '' }}</p>
                                @endif
                            @endforeach
                            @if ($counter == 0)
                                SJ.
                            @endif
                        </td>
                        <td style="vertical-align: top" width="15%" class="text-right br-dotted-bottom">
                            <table>
                                <tr>
                                    <td class="text-left">{{ $supplier_invoice->currency->simbol }}</td>
                                    <td class="text-right">{{ formatNumber($supplier_invoice->grand_total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7">
                        <br>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th class="border-bottom text-left">TOTAL</th>
                    <th class="text-right border-all">
                        <table>
                            <tr>
                                <td class="text-left">{{ $supplier_invoice->currency->simbol }}</td>
                                <td class="text-right">{{ formatNumber($supplier_invoices->sum('grand_total')) }}</td>
                            </tr>
                        </table>
                    </th>
                </tr>
                <tr>
                    <td colspan="7">
                        <br>
                        <br>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center">
                        <p>PENERIMA</p>
                        <br>
                        <br>
                        <br>
                        (............................)
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
