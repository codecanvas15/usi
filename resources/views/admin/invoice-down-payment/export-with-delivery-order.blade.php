<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice & DO General {{ $model->code }}</title>
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
    @include('components.print_out_header')
    <hr style="border: 0.5px solid grey;">
    <div class="text-center">
        <h2>INVOICE: {{ $model->code }}</h2>
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
            <th>Item</th>
            <th>Jumlah</th>
            <th class="col-3">Harga</th>
            <th class="col-3"></th>
        </thead>
        <thead>
            @foreach ($model->invoice_general_details as $invoice_general_detail)
                <tr>
                    <td>{{ $invoice_general_detail->item?->nama }}</td>
                    <td class="text-right">{{ formatNumber($invoice_general_detail->quantity) }} {{ $invoice_general_detail->unit?->name }}</td>
                    <td class="text-right">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ floatDotFormat($invoice_general_detail->price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="text-end">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ floatDotFormat($invoice_general_detail->sub_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
            @foreach ($model->invoice_general_additionals as $invoice_general_additional)
                <tr>
                    <td>{{ $invoice_general_additional->item?->nama }}</td>
                    <td>{{ formatNumber($invoice_general_additional->quantity) }}</td>
                    <td>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ floatDotFormat($invoice_general_additional->price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ floatDotFormat($invoice_general_additional->sub_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="text-end">Sub Total</td>
                <td class="text-end">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->sub_total_main + $model->sub_total_additional) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            @foreach ($unique_all_taxes as $unique_tax)
                <tr>
                    <td colspan="3" class="text-end">{{ $unique_tax->tax->name }} - {{ $unique_tax->value * 100 }}%</td>
                    <td class="text-end">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                    <td class="p-0 border-0 text-right">{{ floatDotFormat($unique_tax->total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end text-bold">Total</td>
                <td class="text-end text-bold">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-0 border-0 text-left">{{ $model->currency->simbol }}</td>
                                <td class="p-0 border-0 text-right">{{ floatDotFormat($model->total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="w-100">
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
    </div>

    @php
        $data_before = null;
    @endphp
    @foreach ($model->invoice_general_details->unique('delivery_order_id') as $invoice_detail)
        @php
            $delivery_order = $invoice_detail->delivery_order_general_detail->delivery_order_general;
            $delivery_order_qr_url = route('delivery-order-general.export.id', ['id' => encryptId($delivery_order->id)]);
            $delivery_order_qr = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($delivery_order_qr_url));
            $approval = \App\Models\Authorization::where('model', \App\Models\DeliveryOrderGeneral::class)
                ->with([
                    'details' => function ($q) {
                        $q->where('status', 'approve');
                    },
                ])
                ->where('model_id', $delivery_order->id)
                ->first();
        @endphp

        @if ($data_before != $delivery_order->id)
            @php
                $data_before = $delivery_order->id;
            @endphp

            <div style="{{ !$loop->last ? 'page-break-after: always;' : '' }} {{ $loop->index == 0 ? 'page-break-before: always' : '' }}">
                @include('components.print_out_header')
                <hr style="border: 0.5px solid grey;">

                <div style="max-width:100%;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 75%; vertical-align: top">
                                <h2 class="text-uppercase my-0">Delivery Order (DO)</h2>
                                <b class="doc-number-border font-small-3 text-bold">No. {{ $delivery_order->code }}</b>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="max-width:100%;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width:50%;vertical-align: top;">
                                <table>
                                    <tr class="mb-0">
                                        <td class="p-0 text-bold mb-0" style="width: 75px;">Tanggal</td>
                                        <td class="p-0 text-medium mb-0" style="width: 8px;">:</td>
                                        <td class="p-0 text-regular mb-0" style="text-align: left;">{{ localDate($delivery_order->target_delivery) }}</td>
                                    </tr>
                                    <tr class="mb-0">
                                        <td class="p-0 text-bold mb-0" style="width: 75px;">No. SO</td>
                                        <td class="p-0 text-medium mb-0" style="width: 8px;">:</td>
                                        <td class="p-0 text-regular mb-0" style="text-align: left;">{{ $delivery_order->sale_order_general->kode }}</td>
                                    </tr>
                                    <tr class="mb-0">
                                        <td class="p-0 text-bold mb-0" style="width: 75px;">No. PO</td>
                                        <td class="p-0 text-medium mb-0" style="width: 8px;">:</td>
                                        <td class="p-0 text-regular mb-0" style="text-align: left;">{{ $delivery_order->sale_order_general->no_po_external }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%;">
                                <table>
                                    <tr class="m-0">
                                        <td class="p-0 text-bold pb-0" style="width: 75px;line-height: 1;">Sold To :</td>
                                    </tr>
                                    <tr class="m-0">
                                        <td class="p-0 text-regular" style="width: 75px;">{{ $delivery_order->customer->nama }}</td>
                                    </tr>
                                    <tr class="m-0">
                                        <td class="p-0 text-bold pb-0" style="width: 75px;">Alamat :</td>
                                    </tr>
                                    <tr class="m-0">
                                        <td class="p-0 text-regular" style="width: 75px;">{{ $delivery_order->customer->alamat }}</td>
                                    </tr>
                                    <tr class="m-0">
                                        <td class="p-0 text-bold pb-0" style="width: 75px;">Telp :</td>
                                    </tr>
                                    <tr class="m-0">
                                        <td class="p-0 text-regular" style="width: 75px;">{{ $delivery_order->customer->bussiness_phone }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="max-width: 100%">
                    <table class="table mb-1 table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="50%">Item</th>
                                <th width="25%">Jumlah</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($delivery_order->delivery_order_general_details as $item)
                                <tr>
                                    <td>{{ $item->item?->nama }}</td>
                                    <td class="text-right">{{ formatNumber($item->quantity) }}</td>
                                    <td class="text-center">{{ $item->unit?->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="my-1" style="border-collapse: collapse">
                        <tbody>
                            <tr>
                                <td class="text-left border-0" rowspan="2">
                                    <img src="data:image/png;base64, {{ $delivery_order_qr }}" width="70px">
                                </td>
                                <td width="25%" class="valign-bottom text-center font-xsmall-3 bordered">
                                    <b>Maker:</b><br>
                                    @if ($delivery_order->created_by_user)
                                        @if ($delivery_order->created_by_user->employee)
                                            <span>{{ Str::headline($delivery_order->created_by_user->employee->name) }}</span>
                                        @else
                                            <span>{{ $delivery_order->created_by_user->name }}</span>
                                        @endif
                                    @endif <br>{{ localDateTime($delivery_order->created_at) }}
                                </td>
                                <td width="25%" class="valign-top text-center font-xsmall-3 bordered" rowspan="2">
                                    <b>Driver</b>
                                </td>
                                <td width="25%" class="valign-top text-center font-xsmall-3 bordered" rowspan="2">
                                    <b>Penerima</b>
                                </td>
                            </tr>
                            <tr>
                                <td class="valign-bottom text-center font-xsmall-3 bordered">
                                    <b>Mengetahui:</b><br>
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
            </div>
        @endif
    @endforeach
</body>

</html>
