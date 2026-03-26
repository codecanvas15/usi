<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Delivery Order {{ $model->code }}</title>
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
    <table>
        <tbody>
            <tr>
                <td width="90%"></td>
                <td class="text-right">
                    <div style="border: 1px solid #000; padding: 2px;" class="text-center">
                        {{ Str::headline($document_stamp) }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    @include('components.print_out_header_center')
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%; vertical-align: top" class="p-0" colspan="2">
                    <h2 class="text-uppercase my-0">Delivery Order : No. {{ $model->code }}</h2>
                </td>
                <td class="text-right"><b>Date : {{ localDate($model->load_date) }}</b></td>
            </tr>
            <tr>
                <td style="width:50%;vertical-align: top;" class="p-0">
                    <table>
                        <tr>
                            <td class="p-0 valign-top" width="25%"><b>No. Referensi</b></td>
                            <td class="p-0 valign-top" width="2%"> : </td>
                            <td class="p-0 valign-top">{{ $model->so_trading->nomor_po_external }}</td>
                        </tr>
                        @if ($model->item_receiving_report)
                            <tr>
                                <td class="p-0 valign-top"><b>No. LPB</b></td>
                                <td class="p-0 valign-top">:</td>
                                <td class="p-0 valign-top">{{ $model->item_receiving_report->kode ?? '' }}</td>
                            </tr>
                        @endif
                        @if ($model->purchase_transport)
                            <tr>
                                <td class="p-0 valign-top"><b>No. PO Transport</b></td>
                                <td class="p-0 valign-top"> : </td>
                                <td class="p-0 valign-top">{{ $model->purchase_transport->kode }}</td>
                            </tr>
                        @endif
                        <tr class="mb-0">
                            <td class="p-0 valign-top text-bold mb-0">SH No.</td>
                            <td class="p-0 valign-top text-medium mb-0">:</td>
                            <td class="p-0 valign-top text-regular mb-0">{{ $model->sh_number->kode }}</td>
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
                            <tr class="valign-top mb-0">
                                <td class="p-0 valign-top text-bold mb-0">{{ $type }}</td>
                                <td class="p-0 valign-top text-medium mb-0">:</td>
                                <td class="p-0 valign-top text-regular mb-0">
                                    {{ $item->alamat }}
                                </td>
                            </tr>
                        @endforeach
                        {{-- <tr class="mb-0">
                            <td class="p-0 valign-top text-bold mb-0">DO External</td>
                            <td class="p-0 valign-top text-medium mb-0">:</td>
                            <td class="p-0 valign-top text-regular mb-0">{{ $model->external_number }}</td>
                        </tr> --}}
                    </table>
                </td>
                <td width="5%"></td>
                <td style="width: 45%;text-align: right; vertical-align: top" class="p-0">
                    <table>

                        <tr>
                            <td class="p-0 valign-top" width="15%"><b>Sold To</b></td>
                            <td class="p-0 valign-top" width="2%">:</td>
                            <td class="p-0 valign-top">{{ $model->so_trading->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 valign-top"><b>Alamat</b></td>
                            <td class="p-0 valign-top">:</td>
                            <td class="p-0 valign-top">{{ $model->so_trading->customer->alamat }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 valign-top"><b>Telp</b></td>
                            <td class="p-0 valign-top">:</td>
                            <td class="p-0 valign-top">{{ $model->so_trading->customer->bussiness_phone }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="max-width: 100%">
        <table class="table-striped table-bordered" style="margin-bottom: 6px">
            <tr>
                <th class="text-center" width="33%">Target Pengiriman</th>
                <th class="text-center" width="33%">Item</th>
                <th class="text-center" width="33%">Qty</th>
            </tr>
            <tr>
                <td class="text-center">{{ localDate($model->target_delivery) }}</td>
                <td class="text-center">{{ $model->so_trading->so_trading_detail->item->nama }}</td>
                <td class="text-center">{{ FormatNumber($model->load_quantity) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
            </tr>
        </table>
        <table class=" table-responsive mb-1 table-striped table-bordered">
            <tr>
                <th colspan="2" class="text-center">Muat</th>
                <th colspan="2" class="text-center">Bongkar</th>
                <th colspan="2" class="text-center">Losses</th>
            </tr>
            <tr>
                <th class="text-center" width="16%">Tanggal</th>
                <th class="text-center" width="16%">Kuantitas</th>
                <th class="text-center" width="16%">Tanggal</th>
                <th class="text-center" width="16%">Kuantitas</th>
                <th class="text-center" width="16%">Jumlah</th>
                <th class="text-center" width="16%">%</th>
            </tr>
            <tr>
                <td class="text-center">{{ localDate($model->load_date) }} </td>
                <td class="text-center">
                    @if ($model->load_quantity_realization > 0)
                        {{ formatNumber($model->load_quantity_realization) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                    @endif
                </td>
                <td class="text-center">{{ localDate($model->unload_date) }}</td>
                <td class="text-center">
                    @if ($model->unload_quantity_realization > 0)
                        {{ formatNumber($model->unload_quantity_realization) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                    @endif
                </td>
                <td class="text-center">
                    {{ formatNumber($model->load_quantity_realization - $model->unload_quantity_realization) }}
                </td>
                <td class="text-center">
                    @if ($model->load_quantity_realization != 0 && $model->unload_quantity_realization != 0)
                        {{ formatNumber((($model->load_quantity_realization - $model->unload_quantity_realization) / $model->load_quantity_realization) * 100) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2" width="33%">{{ Str::headline('segel atas') }} : {{ $model->top_seal }}</td>
                <td colspan="2" width="33%">{{ Str::headline('segel bawah') }} : {{ $model->bottom_seal }}</td>
                <td colspan="2" width="33%">{{ Str::headline('tempertur') }} : {{ $model->temperature }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ Str::headline('meter awal') }} : {{ $model->initial_meter }}</td>
                <td colspan="2">{{ Str::headline('meter akhir') }} : {{ $model->initial_final }}</td>
                <td colspan="2">{{ Str::headline('sg meter') }} : {{ $model->sg_meter }}</td>
            </tr>
            <tr>
                @if (is_null($model->purchase_transport_id))
                    <td colspan="3" style="white-space: normal;">Kendaraan : {{ $model->fleet?->name }} - {{ $model->fleet?->vechicle_fleet?->plat_nomor }}</td>
                @else
                    <td colspan="3" style="white-space: normal;">Kendaraan : {{ $model->vehicle_information }}</td>
                @endif
                <td colspan="3" style="white-space: normal;">Keterangan : {{ $model->description }}</td>
            </tr>
        </table>

        <table class="my-1" style="border-collapse: collapse">
            <tbody>
                <tr>
                    <td class="text-left border-0" width="20%">
                        <img src="data:image/png;base64, {{ $qr }}" width="70px">
                    </td>

                    <td class="valign-bottom text-center font-xsmall-3 bordered">
                        <b>Maker</b><br>
                        <br>
                        <br>
                        <br>
                        @if ($model->created_by_user)
                            @if ($model->created_by_user->employee)
                                <span>{{ Str::headline($model->created_by_user->employee->name) }}</span>
                            @else
                                <span>{{ $model->created_by_user->name }}</span>
                            @endif
                        @endif <br>{{ localDateTime($model->created_at) }}
                    </td>
                    <td class="valign-bottom text-center font-xsmall-3 bordered">
                        <b>Mengetahui</b><br>
                        <br>
                        <br>
                        <br>
                        @if ($approval?->details->last() ?? null)
                            @if ($approval->details->last()->user)
                                <span>{{ Str::headline($approval->details->last()->user->name) }}</span>
                            @else
                                <span>{{ $approval->details->last()->name }}</span>
                            @endif
                            <br>{{ localDateTime($approval->details->last()->updated_at ?? null) }}
                        @else
                            <br>
                            <br>
                        @endif

                    </td>
                    <td class="valign-top text-center font-xsmall-3 bordered">
                        <b>Driver</b>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <span>{{ $model->driver_name ?? ($model->employee->name ?? '') }}</span>
                    </td>
                    <td class="valign-top text-center font-xsmall-3 bordered">
                        <b>Penerima</b>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
