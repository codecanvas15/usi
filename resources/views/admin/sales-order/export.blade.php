<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sale Order {{ $model->nomor_so }}</title>
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
    @include('components.print_out_header_center')
    <div style="max-width:100%;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top">
                    <h2 class="text-uppercase my-0">Sales Order : No. {{ $model->nomor_so }}</h2>
                </td>
                <td style="width: 25%;text-align: right; vertical-align: top">
                    <table>
                        <tr>
                            <td style="width:45%;" class="small-font text-right"> Date </td>
                            <td style="width:5%"> : </td>
                            <td class="text-bold text-right small-font" style="width:50%"> {{ localDate($model->tanggal) }} </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="row small-font">
        <table style="width: 100%">
            <tr>
                <td style="width:55%;vertical-align: top;" class="px-0">
                    <table>
                        <tr class="mb-0">
                            <td class="valign-top px-0 text-bold" width="20%">Reference No.</td>
                            <td class="valign-top px-0 text-right" width="5%">:</td>
                            <td class="valign-top px-0" width="75%" style="text-align: left;">{{ $model->nomor_po_external }}</td>
                        </tr>
                        <tr class="mb-0">
                            <td class="valign-top px-0 text-bold">SH No.</td>
                            <td class="valign-top px-0 text-right">:</td>
                            <td class="valign-top px-0" style="text-align: left;">{{ $model->sh_number->kode }}</td>
                        </tr>
                        @foreach ($model->sh_number->sh_number_details as $item)
                            @php
                                $type = $item->type;
                            @endphp

                            @if ($item->type == 'Drop Point')
                                @php
                                    $type = 'Ship To';
                                @endphp
                            @endif
                            <tr class="mb-0">
                                <td class="valign-top text-bold mb-0">{{ $type }}</td>
                                <td class="valign-top mb-0 text-right">:</td>
                                <td style="">
                                    <div style="word-break: break-all; white-space: normal;">
                                        {{ $item->alamat }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td width="5%"></td>
                <td style="width: 40%;" class="valign-top">
                    <table>
                        <tr class="m-0">
                            <td class="text-bold valign-top" width="20%">Sold To</td>
                            <td class="valign-top" width="2%">:</td>
                            <td class="valign-top">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-bold valign-top">Alamat</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">
                                <div style="word-break: break-all; white-space: normal;">
                                    {{ $model->customer->alamat }}
                                </div>
                            </td>
                        </tr>
                        <tr class="m-0">
                            <td class="text-bold valign-top">Term of Payment</td>
                            <td class="valign-top">:</td>
                            <td class="valign-top"><span class="text-capitalize">{{ $model->customer->term_of_payment }}</span> / {{ $model->customer->top_days }} Days</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row showTable small-font" style="margin-top: 8px;max-width: 100%">
        <table class="table table-striped table-bordered">

            <thead class="bg-dark text-white">
                <th>Item</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Price</th>
                <th>Amount</th>
            </thead>

            <tbody>
                @php
                    $subtotal = $model->sub_total + $model->sale_order_additionals->sum('sub_total');
                @endphp
                <tr>
                    <td>{{ $model->so_trading_detail->item->nama }}</td>
                    <td class="text-right">{{ formatNumber($model->so_trading_detail->jumlah) }}</td>
                    <td class="text-center">{{ $model->so_trading_detail->item->unit->name }}</td>
                    <td class="text-right p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0">{{ $model->currency->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->so_trading_detail->harga) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="text-right p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0">{{ $model->currency->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->sub_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                @foreach ($model->sale_order_additionals as $item)
                    <tr>
                        <td>{{ $item->item?->nama }}</td>
                        <td class="text-right">{{ formatNumber($item->quantity) }}</td>
                        <td class="text-center">{{ $item->item?->unit->name }}</td>
                        <td class="text-end p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($item->price) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="text-end p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($item->sub_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bolder">
                        <table>
                            <tbody>
                                <tr>
                                    @if (!$taxes_id)
                                        <th class="text-left p-0 border-0">LOSS TOLERANCE {{ $model->customer->lost_tolerance_name }} </th>
                                    @endif
                                    <td class="text-right p-0 border-0 fw-bolder">{{ Str::headline('subtotal') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="text-end p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0 fw-bolder"><b>{{ $model->currency->simbol }}</b></td>
                                    <td class="border-0 fw-bolder text-right"><b>{{ formatNumber($subtotal) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                @if ($taxes_id)
                    @foreach ($taxes_id as $key => $item)
                        <tr>
                            <td colspan="4" class="text-end fw-bolder">
                                <table>
                                    <tbody>
                                        <tr>
                                            @if ($key + 1 == count($taxes_id))
                                                <th class="text-left p-0 border-0">LOSS TOLERANCE {{ $model->customer->lost_tolerance_name }} </th>
                                            @endif
                                            <td class="text-right p-0 border-0">{{ Str::upper($item['name'] ?? 'Undefined') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="text-right p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0 fw-bolder"><b>{{ $model->currency->simbol }}</b></td>
                                            <td class="border-0 fw-bolder text-right"><b>{{ formatNumber($item['amount']) }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <th colspan="4" class="text-end fw-bolder">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="text-left p-0 border-0"><b>NOTE :</b></td>
                                    <td class="text-right p-0 border-0">{{ Str::headline('total') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </th>
                    <td class="text-end p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0 fw-bolder"><b>{{ $model->currency->simbol }}</b></td>
                                    <td class="border-0 fw-bolder text-right"><b>{{ formatNumber($model->total) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <p class="text-uppercase small-font"><b>Terbilang : </b> {{ Terbilang::make($model->sub_total_after_tax + $model->other_cost) }} {{ $model->currency->nama }}</p>

    @if ($model->so_trading_detail->pairing_so_to_pos->count() > 0)
        <h3>Pairing Details</h3>
        <table class="table table-bordered small-font">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. PO</th>
                    <th>Alokasi</th>
                    <th>Supplier</th>
                    <th>PO Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $so_qty_outstanding = $model->so_trading_detail->jumlah;
                @endphp
                @foreach ($model->so_trading_detail->pairing_so_to_pos as $pairing)
                    @php
                        $so_qty_outstanding -= $pairing->alokasi;
                    @endphp
                    <tr>
                        <th>{{ $loop->index + 1 }}</th>
                        <td class="text-center">{{ $pairing->po_trading_detail->po_trading->nomor_po }}</td>
                        <td class="text-end">{{ formatNumber($pairing->alokasi) }}</td>
                        <td class="text-center">{{ $pairing->po_trading_detail->po_trading->vendor->nama }}</td>
                        <td class="text-end">{{ formatNumber($pairing->po_trading_detail->sudah_dialokasikan) }}/{{ formatNumber($pairing->po_trading_detail->jumlah) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="my-1" style="border-collapse: collapse">
        <tbody>
            <tr>
                <td class="text-left border-0" width="20%">
                    <img src="data:image/png;base64, {{ $qr }}" width="70px">
                </td>
                <td class="valign-bottom text-center font-xsmall-3 bordered">
                    <b>Maker:</b>
                    @if ($model->create_by)
                        @if ($model->create_by->employee)
                            <span>{{ Str::headline($model->create_by->employee->name) }}</span>
                        @else
                            <span>{{ $model->create_by->name }}</span>
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
