<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order Trading {{ $model->nomor_po }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 8pt;
            color: #000;
        }

        @page {
            margin: 19px;
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
    <div style="max-width: 100%">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top">
                    <h2 class="text-uppercase my-0">Purchase Order : No. {{ $model->nomor_po }}</h2>
                </td>
                <td class="text-right">
                    <b>Date : {{ \Carbon\Carbon::parse($model->tanggal)->format('d-m-Y') }}</b>
                </td>
            </tr>
        </table>
    </div>
    <table>
        <tbody>
            <tr>
                <td style="vertical-align: top" class="p-0" width="50%">
                    <table class="small-font">
                        @if ($model->sale_order)
                            <tr>
                                <td class="valign-top p-0"><b>No SO.</b></td>
                                <td class="valign-top">:</td>
                                <td class="valign-top">{{ $model->sale_order->nomor_so }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="valign-top p-0" width="20%"><b>Reference No.</b></td>
                            <td class="valign-top" width="2%">:</td>
                            @if ($model->sale_order)
                                <td class="valign-top">{{ $model->sale_order->nomor_po_external }}</td>
                            @else
                                <td class="valign-top">{{ $model->purchase_request_trading->code ?? '' }}</td>
                            @endif
                        </tr>

                        <tr>
                            <td class="valign-top p-0"><b>Customer</b></td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top p-0"><b>SH No.</b></td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->sh_number->kode }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top p-0"><b>Supply Point</b></td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->sh_number->sh_number_details->where('type', 'Supply Point')->first()?->alamat }}</td>
                        </tr>
                        <tr>
                            <td class="valign-top p-0"><b>Ship To</b></td>
                            <td class="valign-top">:</td>
                            <td class="valign-top">{{ $model->sh_number->sh_number_details->where('type', 'Drop Point')->first()?->alamat }}</td>
                        </tr>
                    </table>
                </td>
                <td width="10%"></td>
                <td style="vertical-align: top" class="p-0" width="40%">
                    <table class="small-font">
                        <tr>
                            <td>
                                <span class="text-bold">Kepada YTH :</span>
                                <br>
                                {{ $model->vendor->nama }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-bold">Alamat :</span>
                                <br>
                                {{ $model->vendor->alamat }}
                            </td>
                        </tr>
                        <tr>
                            <td class="valign-top p-0"><b>Term of Payment :</b>
                                <br>
                                {{ $model->top }} / {{ $model->top_day }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="table-responsive">
        <table class="table table-striped table-bordered small-font">
            <thead class="bg-dark text-white">
                <th>Item</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Price</th>
                <th>Discount</th>
                <th></th>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $model->po_trading_detail->item->nama }}</td>
                    <td class="text-right">{{ formatNumber($model->jumlah) }}</td>
                    <td class="text-center">{{ $model->po_trading_detail->type }} </td>
                    <td class="text-right p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0">{{ $model->currency->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->po_trading_detail->harga) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="text-right p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0">{{ $model->currency->simbol }}</td>
                                    <td class="border-0 text-right">{{ formatNumber($model->po_trading_detail->discount_per_liter) }}</td>
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
                @php
                    $sub_total_main = $model->po_trading_detail->harga - $model->po_trading_detail->discount_per_liter;
                    $tax_total_main = 0;

                    foreach ($model->purchase_order_taxes as $tax) {
                        $tax_total_main += $tax->total;
                    }
                @endphp
                @foreach ($model->purchase_order_taxes as $tax)
                    @if ($tax->tax_trading_id)
                        <tr>
                            <td colspan="5">
                                <p class="text-end my-0">{{ $tax->tax_trading->tax_name_without_percent }} - {{ $tax->value * 100 }}%</p>
                            </td>
                            <td class="text-right p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($tax->total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5">
                                <p class="text-end my-0">{{ $tax->tax->tax_name_without_percent }} - {{ $tax->value * 100 }}%</p>
                            </td>
                            <td class="text-right p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($tax->total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-end fw-bolder" colspan="5">
                        <p class="my-0"><b>Total</b></p>
                    </td>
                    <td class="text-right p-0 ">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="border-0"><b>{{ $model->currency->simbol }}</b></td>
                                    <td class="border-0 text-right"><b>{{ formatNumber($model->sub_total + $tax_total_main) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if (count($model->purchase_order_additionals))
        <div class="mt-1">
            <h4><b>Additional Item</b></h4>
            <table class="table table-striped table-bordered">
                <thead class="bg-dark text-white">
                    <th>#</th>
                    <th>{{ Str::headline('Item') }}</th>
                    <th>{{ Str::headline('Harga') }}</th>
                    <th>{{ Str::headline('Sub total') }}</th>
                    <th>{{ Str::headline('Tax') }}</th>
                    <th>{{ Str::headline('value') }}</th>
                    <th>{{ Str::headline('Total') }}</th>
                </thead>
                <tbody>
                    @foreach ($model->purchase_order_additionals as $item)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $item->item?->nama }}</td>
                            <td class="text-right p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($item->harga) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="text-right p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($item->sub_total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <div>
                                    @forelse ($item->purchase_order_additional_taxes as $item_tax)
                                        <p class="my-0">
                                            {{ $item_tax->tax?->name }}
                                            {{ $item_tax->value * 100 }}%
                                        </p>
                                    @empty
                                        -
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-right p-0">
                                <table>
                                    <tbody>
                                        @forelse ($item->purchase_order_additional_taxes as $item_tax)
                                            <tr>
                                                <td class="border-0">{{ $model->currency->simbol }}</td>
                                                <td class="border-0 text-right">{{ formatNumber($item_tax->total) }}</td>
                                            </tr>
                                        @empty
                                            -
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                            <td class="text-right p-0">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="border-0">{{ $model->currency->simbol }}</td>
                                            <td class="border-0 text-right">{{ formatNumber($item->total) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-end" colspan="6">DPP</td>
                        <td class="text-right p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->purchase_order_additionals->sum('sub_total')) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end" colspan="6">Total Pajak</td>
                        <td class="text-right p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->additional_tax_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end" colspan="6"><b>Total</b></td>
                        <td class="text-right p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0"><b>{{ $model->currency->simbol }}</b></td>
                                        <td class="border-0 text-right"><b>{{ formatNumber($model->other_cost) }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        @php
                            $total_additional = $model->other_cost;
                        @endphp
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-1">
            <h4><b>Total</b></h4>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td class="text-end"><b>Trading Total</b></td>
                        <td class="text-right p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->total - $model->other_cost) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end"><b>Additional total</b></td>
                        <td class="text-right p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0">{{ $model->currency->simbol }}</td>
                                        <td class="border-0 text-right">{{ formatNumber($model->other_cost) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end"><b>Grand Total</b></td>
                        <td class="text-right p-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="border-0"><b>{{ $model->currency->simbol }}</b></td>
                                        <td class="border-0 text-right"><b>{{ formatNumber($model->total) }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <p class="text-uppercase mt-0" style="margin-bottom: 2px"><b>Terbilang : </b> {{ Terbilang::make($model->total) }} {{ $model->currency->nama }}</p>

    @if ($model->po_trading_detail->pairing_po_to_sos->count() > 0)
        <h3 class="my-0">Pairing Details</h3>
        <table class="table table-bordered small-font">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. SO</th>
                    <th>Alokasi</th>
                    <th>Customer</th>
                    <th>SO Status</th>
                    <th>PO Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $po_qty_outstanding = $model->po_trading_detail->jumlah;
                @endphp
                @foreach ($model->po_trading_detail->pairing_po_to_sos as $pairing)
                    @php
                        $po_qty_outstanding -= $pairing->alokasi;
                    @endphp
                    <tr>
                        <th>{{ $loop->index + 1 }}</th>
                        <td class="text-center">{{ $pairing->so_trading_detail->so_trading->nomor_so }}</td>
                        <td class="text-end">{{ formatNumber($pairing->alokasi) }}</td>
                        <td class="text-center">{{ $pairing->so_trading_detail->so_trading->customer->nama }}</td>
                        <td class="text-end">{{ formatNumber($pairing->so_trading_detail->sudah_dialokasikan) }}/{{ formatNumber($pairing->so_trading_detail->jumlah) }}</td>
                        <td class="text-end">{{ formatNumber($po_qty_outstanding) }} / {{ formatNumber($model->po_trading_detail->jumlah) }}</td>
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
