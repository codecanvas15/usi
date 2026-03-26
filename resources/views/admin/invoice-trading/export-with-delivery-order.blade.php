<!DOCTYPE html>
<html>

<head>
    <title>Invoice & DO Trading {{ $model->code }}</title>
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
        $subtotal = $model->subtotal + $model->other_cost;
    @endphp
    <table class="w-100 table table-bordered mt-1">
        <thead class="bg-dark text-white">
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
        </thead>
        <thead>
            <tr>
                <td class="text-center">{{ $model->item->nama }}</td>
                {{-- <td class="text-right">{{ formatNumber($model->so_trading?->so_trading_detail?->jumlah) }} Liter</td> --}}
                <td class="text-center">{{ formatNumber($model->jumlah) }} {{ $model->item->unit->name ?? '' }}</td>
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
            @foreach ($taxes_id as $item)
                <tr>
                    <td colspan="3" class="text-right">{{ $item['name'] }} - {{ $item['value'] * 100 }} %</td>
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
                                <td class="p-0 border-0 text-right">{{ formatNumber($model->total) }}</td>
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

    <p class="text-uppercase"><b>Terbilang : </b> {{ Terbilang::make($model->total) }} {{ $model->currency->nama }}</p>

    <table class="mt-1">
        <tbody>
            <tr>
                <td class="p-0 text-left valign-top" width="50%">
                    <table class="w-100 p-0">
                        <tbody>
                            @foreach ($model->bankInternals() as $bank_internal)
                                <tr>
                                    <td class="p-0 text-left valign-top">
                                        <p class="my-0">Account Name : <br><b>{{ $bank_internal->on_behalf_of }}</b></p>
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
                        <li>Terms of Payment: 20 days from Invoiced date, with 10 days max Grace Period Overdue penalty rate is 2%/month pro rata</li>
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

    @foreach ($model->invoice_trading_details as $invoice_detail)
        @php
            $delivery_order = $invoice_detail->delivery_order;
            $delivery_order_qr_url = route('delivery-order.export.id', ['id' => encryptId($delivery_order->id)]);
            $delivery_order_qr = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($delivery_order_qr_url));
            $approval = \App\Models\Authorization::where('model', \App\Models\DeliveryOrder::class)
                ->with([
                    'details' => function ($q) {
                        $q->where('status', 'approve');
                    },
                ])
                ->where('model_id', $delivery_order->id)
                ->first();
        @endphp

        <div style="{{ !$loop->last ? 'page-break-after: always;' : '' }} {{ $loop->index == 0 ? 'page-break-before: always' : '' }}">

            @include('components.print_out_header_center')
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top" class="p-0" colspan="2">
                            <h2 class="text-uppercase my-0">Delivery Order : No. {{ $delivery_order->code }}</h2>
                        </td>
                        <td class="text-right"><b>Date : {{ localDate($delivery_order->load_date) }}</b></td>
                    </tr>
                    <tr>
                        <td style="width:50%;vertical-align: top;" class="p-0">
                            <table>
                                <tr>
                                    <td class="p-0 valign-top" width="25%"><b>No. Referensi</b></td>
                                    <td class="p-0 valign-top" width="2%"> : </td>
                                    <td class="p-0 valign-top">{{ $delivery_order->so_trading->nomor_po_external }}</td>
                                </tr>
                                @if ($delivery_order->item_receiving_report)
                                    <tr>
                                        <td class="p-0 valign-top"><b>No. LPB</b></td>
                                        <td class="p-0 valign-top">:</td>
                                        <td class="p-0 valign-top">{{ $delivery_order->item_receiving_report->kode ?? '' }}</td>
                                    </tr>
                                @endif
                                @if ($delivery_order->purchase_transport)
                                    <tr>
                                        <td class="p-0 valign-top"><b>No. PO Transport</b></td>
                                        <td class="p-0 valign-top"> : </td>
                                        <td class="p-0 valign-top">{{ $delivery_order->purchase_transport->kode }}</td>
                                    </tr>
                                @endif
                                <tr class="mb-0">
                                    <td class="p-0 valign-top text-bold mb-0">SH No.</td>
                                    <td class="p-0 valign-top text-medium mb-0">:</td>
                                    <td class="p-0 valign-top text-regular mb-0">{{ $delivery_order->sh_number->kode }}</td>
                                </tr>
                                @foreach ($delivery_order->sh_number->sh_number_details as $item)
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
                                <tr class="mb-0">
                                    <td class="p-0 valign-top text-bold mb-0">DO External</td>
                                    <td class="p-0 valign-top text-medium mb-0">:</td>
                                    <td class="p-0 valign-top text-regular mb-0">{{ $delivery_order->external_number }}</td>
                                </tr>
                            </table>
                        </td>
                        <td width="5%"></td>
                        <td style="width: 45%;text-align: right; vertical-align: top" class="p-0">
                            <table>

                                <tr>
                                    <td class="p-0 valign-top" width="15%"><b>Sold To</b></td>
                                    <td class="p-0 valign-top" width="2%">:</td>
                                    <td class="p-0 valign-top">{{ $delivery_order->so_trading->customer->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="p-0 valign-top"><b>Alamat</b></td>
                                    <td class="p-0 valign-top">:</td>
                                    <td class="p-0 valign-top">{{ $delivery_order->so_trading->customer->alamat }}</td>
                                </tr>
                                <tr>
                                    <td class="p-0 valign-top"><b>Telp</b></td>
                                    <td class="p-0 valign-top">:</td>
                                    <td class="p-0 valign-top">{{ $delivery_order->so_trading->customer->bussiness_phone }}</td>
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
                        <td class="text-center">{{ localDate($delivery_order->target_delivery) }}</td>
                        <td class="text-center">{{ $delivery_order->so_trading->so_trading_detail->item->nama }}</td>
                        <td class="text-center">{{ FormatNumber($delivery_order->load_quantity) }} {{ $delivery_order->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
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
                        <td class="text-center">{{ localDate($delivery_order->load_date) }} </td>
                        <td class="text-center">
                            @if ($delivery_order->load_quantity_realization > 0)
                                {{ formatNumber($delivery_order->load_quantity_realization) }} {{ $delivery_order->so_trading->so_trading_detail->item->unit->name ?? '' }}
                            @endif
                        </td>
                        <td class="text-center">{{ localDate($delivery_order->unload_date) }}</td>
                        <td class="text-center">
                            @if ($delivery_order->unload_quantity_realization > 0)
                                {{ formatNumber($delivery_order->unload_quantity_realization) }} {{ $delivery_order->so_trading->so_trading_detail->item->unit->name ?? '' }}
                            @endif
                        </td>
                        <td class="text-center">
                            {{ formatNumber($delivery_order->load_quantity_realization - $delivery_order->unload_quantity_realization) }}
                        </td>
                        <td class="text-center">
                            @if ($delivery_order->load_quantity_realization != 0 && $delivery_order->unload_quantity_realization != 0)
                                {{ formatNumber((($delivery_order->load_quantity_realization - $delivery_order->unload_quantity_realization) / $delivery_order->load_quantity_realization) * 100) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" width="33%">{{ Str::headline('segel atas') }} : {{ $delivery_order->top_seal }}</td>
                        <td colspan="2" width="33%">{{ Str::headline('segel bawah') }} : {{ $delivery_order->bottom_seal }}</td>
                        <td colspan="2" width="33%">{{ Str::headline('tempertur') }} : {{ $delivery_order->temperature }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">{{ Str::headline('meter awal') }} : {{ $delivery_order->initial_meter }}</td>
                        <td colspan="2">{{ Str::headline('meter akhir') }} : {{ $delivery_order->initial_final }}</td>
                        <td colspan="2">{{ Str::headline('sg meter') }} : {{ $delivery_order->sg_meter }}</td>
                    </tr>
                    <tr>
                        @if (is_null($delivery_order->purchase_transport_id))
                            <td colspan="3" style="white-space: normal;">Kendaraan : {{ $delivery_order->fleet?->name }} - {{ $delivery_order->fleet?->vechicle_fleet?->plat_nomor }}</td>
                        @else
                            <td colspan="3" style="white-space: normal;">Kendaraan : {{ $delivery_order->vehicle_information }}</td>
                        @endif
                        <td colspan="3" style="white-space: normal;">Keterangan : {{ $delivery_order->description }}</td>
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
                                @if ($delivery_order->created_by_user)
                                    @if ($delivery_order->created_by_user->employee)
                                        <span>{{ Str::headline($delivery_order->created_by_user->employee->name) }}</span>
                                    @else
                                        <span>{{ $delivery_order->created_by_user->name }}</span>
                                    @endif
                                @endif <br>{{ localDateTime($delivery_order->created_at) }}
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
                                <span>{{ $delivery_order->driver_name ?? ($delivery_order->employee->name ?? '') }}</span>
                            </td>
                            <td class="valign-top text-center font-xsmall-3 bordered">
                                <b>Penerima</b>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</body>

</html>
