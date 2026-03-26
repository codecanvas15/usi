@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline('Purchase') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <div>
            <div class="box bg-gradient-success-dark text-white">
                <div class="box-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Purchase Order</h4>
                            <h1 class="m-0">{{ $model->nomor_po }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_purchase_order') }}</h5>
                                    <div class="badge badge-lg badge-{{ PO_STATUS[$model->status]['color'] }}">
                                        {{ Str::headline(PO_STATUS[$model->status]['label']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('pairing_status') }}</h5>
                                    <div class="badge badge-lg badge-{{ pairing_status()[$model->pairing_status]['color'] }}">
                                        {{ Str::headline(pairing_status()[$model->pairing_status]['label']) }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'Detail Purchase Order ' . $model->nomor_po }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            @if ($model->purchase_request_trading)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('Nomor Purchase Order') }}</label>
                                        <p><a href="{{ route('admin.purchase-request-trading.show', $model->purchase_request_trading) }}" target="_blank">{{ $model->purchase_request_trading->code }}</a></p>
                                    </div>
                                </div>
                            @endif
                            @if ($model->sale_order)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('Nomor Sales Order') }}</label>
                                        <p><a href="{{ route('admin.sales-order.show', $model->sale_order) }}" target="_blank">{{ $model->sale_order->nomor_so }}</a></p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Cabang') }}</label>
                                    <p>{{ $model->branch->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->tanggal) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->nomor_po }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('customer') }}</label>
                                    <p>{{ $model->customer->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Sh No.') }}</label>
                                    <p>{{ $model->sh_number->kode }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('keterangan') }}</label>
                                    <p>{{ $model->note ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="col-md-12"></div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Supply Point') }}</label>
                                    <p>{{ $model->sh_number->sh_number_details()->where('type', 'Supply Point')->first()?->alamat }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Drop Point') }}</label>
                                    <p>{{ $model->sh_number->sh_number_details()->where('type', 'Drop Point')->first()?->alamat }}
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('vendor') }}</label>
                                    <p>{{ $model->vendor->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Term of Payment') }}</label>
                                    <p>{{ $model->top }} - {{ $model->top_day }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('SCO Supplier') }}</label>
                                    <p>{{ $model->sale_confirmation }}</p>
                                    <span>
                                        @if ($model->check_available_date)
                                            @if (in_array($model->status, ['pending', 'status']))
                                                <x-button color='info' fontawesome icon="pen-to-square" class="w-auto" size="sm" dataToggle='modal' dataTarget='#update-sale-confirmation' />
                                                <x-modal title="update sale confirmation" id="update-sale-confirmation" headerColor="info">
                                                    <x-slot name="modal_body">
                                                        <form action="{{ route("admin.$main.update_sale_confirmation", $model) }}" method="post" enctype="multipart/form-data">
                                                            @method('PUT')
                                                            @csrf
                                                            <div class="form-group">
                                                                <x-input type="text" id="sale_confirmation" name="sale_confirmation" label=" SCO Supplier" value="{{ $model->sale_confirmation ?? '' }}" />
                                                            </div>
                                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                                            <x-button type="submit" color="primary" label="Save data" />
                                                        </form>
                                                    </x-slot>
                                                </x-modal>
                                            @endif
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('quotation') }}</label>
                                    <p>
                                        @if ($model->quotation)
                                            <x-button color="info" link="{{ url('storage/' . $model->quotation) }}" size="sm" icon="file" fontawesome />
                                        @else
                                            <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('mata uang') }}</label>
                                    <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Nilai Tukar') }}</label>
                                    <p>{{ formatNumber($model->exchange_rate) }}</p>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ status_purchase_orders()[$model->status]['color'] }} mb-1">
                                        {{ Str::headline(status_purchase_orders()[$model->status]['text']) }} -
                                        {{ Str::headline(status_purchase_orders()[$model->status]['label']) }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('pairing status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ pairing_status()[$model->pairing_status]['color'] }}">
                                        {{ Str::headline(pairing_status()[$model->pairing_status]['text']) }} -
                                        {{ Str::headline(pairing_status()[$model->pairing_status]['label']) }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-20">
                            <h4 class="fw-bold">Detail item</h4>
                            <x-table theadColor='dark'>
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('Item') }}</th>
                                    <th class="text-end">{{ Str::headline('Harga') }}</th>
                                    <th class="text-end">{{ Str::headline('Disc') }}</th>
                                    <th class="text-end">{{ Str::headline('Qty') }}</th>
                                    <th class="text-end">{{ Str::headline('Sub Total') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <td>{{ $model->po_trading_detail->item->nama }}</td>
                                        <td class="text-end">{{ $model->currency->simbol }}
                                            {{ formatNumber($model->po_trading_detail->harga) }}
                                        </td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->po_trading_detail->discount_per_liter) }}</td>
                                        <td class="text-end">{{ formatNumber($model->po_trading_detail->jumlah) }} / {{ $model->po_trading_detail->item->unit->name ?? '' }}</td>
                                        @php
                                            $sub_total_main = $model->po_trading_detail->harga - $model->po_trading_detail->discount_per_liter;
                                            $tax_total_main = 0;

                                            foreach ($model->purchase_order_taxes as $tax) {
                                                $tax_total_main += $tax->total;
                                            }
                                        @endphp
                                        <td>
                                            <div class="d-flex text-end">
                                                <p class="mb-0" id="currency-simbol">{{ $model->currency->simbol }} </p>
                                                <h5 class="ms-auto mb-0">{{ formatNumber($model->sub_total) }}</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach ($model->purchase_order_taxes as $tax)
                                        @if ($tax->tax_trading_id)
                                            <tr>
                                                <td colspan="4">
                                                    <p class="text-end mb-0">{{ $tax->tax_trading->name }} -
                                                        {{ $tax->value * 100 }}%</p>
                                                </td>
                                                <td>
                                                    <div class="d-flex text-end">
                                                        <p class="mb-0">{{ $model->currency->simbol }}</p>
                                                        <h5 class="ms-auto mb-0">{{ formatNumber($tax->total) }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="4">
                                                    <p class="text-end mb-0">{{ $tax->tax->name }} - {{ $tax->value * 100 }}%
                                                    </p>
                                                </td>
                                                <td>
                                                    <div class="d-flex text-end">
                                                        <p class="mb-0">{{ $model->currency->simbol }}</p>
                                                        <h5 class="ms-auto mb-0">{{ formatNumber($tax->total) }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <td class="text-end" colspan="4">
                                            <p class=" mb-0">Total</p>
                                        </td>
                                        <td class="bg-success text-white">
                                            <div class="d-flex text-end">
                                                <p class="fw-bold mb-0">{{ $model->currency->simbol }}</p>
                                                <h5 class="fw-bold ms-auto mb-0">
                                                    {{ formatNumber($model->sub_total + $tax_total_main) }}</h5>
                                            </div>
                                        </td>
                                        @php
                                            $total_main = $model->sub_total + $tax_total_main;
                                        @endphp
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>

                        @if (count($model->purchase_order_additionals))
                            <div class="mt-20">
                                <h4 class="fw-bold">Additional item</h4>
                                <x-table theadColor='dark'>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('Item') }}</th>
                                        <th>{{ Str::headline('Harga') }}</th>
                                        <th>{{ Str::headline('Qty') }}</th>
                                        <th>{{ Str::headline('Sub total') }}</th>
                                        <th>{{ Str::headline('Tax') }}</th>
                                        <th>{{ Str::headline('value') }}</th>
                                        <th>{{ Str::headline('Total') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->purchase_order_additionals as $item)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $item->item?->nama }}</td>
                                                <td>{{ $model->currency->simbol }}
                                                    {{ number_format($item->harga, 2, ',', '.') }}</td>
                                                <td>
                                                    {{ number_format($item->jumlah, 2, ',', '.') }}</td>
                                                <td> {{ $model->currency->simbol }}{{ formatNumber($item->sub_total) }}</td>
                                                <td>
                                                    <div>
                                                        @forelse ($item->purchase_order_additional_taxes as $item_tax)
                                                            <p class="mb-5">
                                                                {{ $item_tax->tax?->name }}
                                                                {{ $item_tax->value * 100 }}%
                                                            </p>
                                                        @empty
                                                            <x-button size="sm" badge color="danger" label="no Tax" />
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @forelse ($item->purchase_order_additional_taxes as $item_tax)
                                                            <p class="mb-5"class="mb-4">
                                                                {{ $model->currency->simbol }}
                                                                {{ formatNumber($item_tax->total) }}
                                                            </p>
                                                        @empty
                                                            <x-button size="sm" badge color="danger" label="no Tax" />
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($item->total) }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <td class="text-end" colspan="7">DPP</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->purchase_order_additionals->sum('sub_total')) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total Pajak</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->additional_tax_total) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total</td>
                                            <td class="fw-bold bg-success text-white text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->other_cost) }}</td>
                                            @php
                                                $total_additional = $model->other_cost;
                                            @endphp
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                            <div class="mt-20">
                                <h4 class="fw-bold">Total</h4>
                                <x-table theadColor='dark'>
                                    <x-slot name="table_head">
                                        <th class="col-md-8"></th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <th class="text-end">Trading Total</th>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($total_main) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-end">Additional total</th>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($total_additional) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-end">Grand Total</th>
                                            <td class="fw-bold bg-success text-white text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($total_main + $total_additional) }}</td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        @endif

                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}

                            <x-button type="button" color='primary' fontawesome icon="history" label="riwayat transaksi" class="w-auto" size="sm" id="history-button" />
                            <x-modal title="riwayat transaksi" id="history-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Transaksi</th>
                                                    <th>Nomor</th>
                                                </tr>
                                            </thead>
                                            <tbody id="history-list">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-10 border-top pt-10">
                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                    </div>
                                </x-slot>
                            </x-modal>

                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if ($model->check_available_date)
                                @if ($model->status == 'pending' or $model->status == 'revert')
                                    @can("edit $main")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>
                <x-card-data-table title="{{ $main . ' item' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <x-table theadCoor="danger">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Item') }}</th>
                                <th>{{ Str::headline('kuantitas') }}</th>
                                <th>{{ Str::headline('sisa pairing') }}</th>
                                <th>{{ Str::headline('sudah_dialokasikan') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <td>{{ 1 }}</td>
                                    <td>{{ $model->po_trading_detail->item->nama }}</td>
                                    <td>{{ formatNumber($model->po_trading_detail->jumlah) }} /
                                        {{ $model->po_trading_detail->type }}</td>
                                    <td>{{ formatNumber($model->po_trading_detail->alokasi_tersedia, 2, '.', '.') }} {{ $model->po_trading_detail->item->unit->name ?? '' }}
                                    </td>
                                    <td>{{ formatNumber($model->po_trading_detail->sudah_dialokasikan, 2, '.', '.') }} {{ $model->po_trading_detail->item->unit->name ?? '' }}
                                    </td>
                                    <td>
                                        <div class="badge badge-lg badge-{{ status_purchase_order_details()[$model->po_trading_detail->status]['color'] }}">
                                            {{ Str::headline(status_purchase_order_details()[$model->po_trading_detail->status]['text']) }}
                                            -
                                            {{ Str::headline(status_purchase_order_details()[$model->po_trading_detail->status]['label']) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($model->status != 'pending')
                                            @if ($model->po_trading_detail->alokasi_tersedia != 0)
                                                @if ($model->po_trading_detail->jumlah_lpbs != null or $model->po_trading_detail->alokasi_tersedia > $model->po_trading_detail->sudah_dialokasikan)
                                                    <x-button color='primary' fontawesome icon="link" class="w-auto" size="sm" link="{{ route('admin.pairing.po_pairing', $model->po_trading_detail) }}" />
                                                @endif
                                            @endif
                                        @endif
                                        @if ($model->status == 'pending' and $model->po_trading_detail->sudah_dialokasikan == null)
                                            <x-button color='danger' icon="trash" size="sm" fontawesome dataToggle='modal' dataTarget='#delete-modal-detail-{{ $model->po_trading_detail->id }}' />
                                            <x-modal-delete id="delete-modal-detail-{{ $model->po_trading_detail->id }}" url='{{ 'admin.so-trading-detail.destroy' }}' dataId="{{ $model->po_trading_detail->id }}" />
                                        @endif
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="{{ 'pairing details' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        <x-table theadCoor="danger">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>No. SO</th>
                                <th>Alokasi</th>
                                <th>Customer</th>
                                <th>SO Status</th>
                                <th>PO Status</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @php
                                    $po_qty_outstanding = $model->po_trading_detail->jumlah;
                                @endphp
                                @foreach ($model->po_trading_detail->pairing_po_to_sos as $pairing)
                                    @php
                                        $po_qty_outstanding -= $pairing->alokasi;
                                    @endphp
                                    <tr>
                                        <th>{{ $loop->index + 1 }}</th>
                                        <td><a href="{{ route('admin.sales-order.show', ['sales_order' => $pairing->so_trading_detail->so_trading->id]) }}" target="_blank">{{ $pairing->so_trading_detail->so_trading->nomor_so }}</a></td>
                                        <td class="text-end">{{ formatNumber($pairing->alokasi) }}</td>
                                        <td>{{ $pairing->so_trading_detail->so_trading->customer->nama }}</td>
                                        <td class="text-end">{{ formatNumber($pairing->so_trading_detail->sudah_dialokasikan) }}/{{ formatNumber($pairing->so_trading_detail->jumlah) }}</td>
                                        <td class="text-end">{{ formatNumber($po_qty_outstanding) }} /{{ formatNumber($model->po_trading_detail->jumlah) }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <div id="print-request-container"></div>
                <x-card-data-table title="action">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @if ($model->check_available_date)
                            @if ($model->po_trading_detail->jumlah_lpbs > 0 && $model->po_trading_detail->jumlah > $model->po_trading_detail->jumlah_lpbs && $model->status != 'close')
                                @can('close purchase-order')
                                    <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                    <x-modal title="close purchase order" id="close-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="close">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="simpan" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        @endif
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table>
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <button class="btn btn-info" href="{{ route('purchase-order.export.id', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" @authorize_print('purchase_order_trading') data-model="{{ \App\Models\PoTrading::class }}" data-id="{{ $model->id }}" data-print-type="purchase_order_trading" data-link="{{ route('admin.purchase-order.show', ['purchase_order' => $model->id]) }}" data-code="{{ $model->nomor_po }}" @endauthorize_print>Export PDF</button>
                        @if ($model->status == 'close')
                            <button class="btn btn-info" href="{{ route('purchase-order.export.id', ['id' => encryptId($model->id), 'close' => true]) }}" onclick="show_print_out_modal(event, '&')" @authorize_print('purchase_order_trading') data-model="{{ \App\Models\PoTrading::class }}" data-id="{{ $model->id }}" data-print-type="purchase_order_trading" data-link="{{ route('admin.purchase-order.show', ['purchase_order' => $model->id]) }}" data-code="{{ $model->nomor_po }}" @endauthorize_print>Export PDF (Close)</button>
                        @endif
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.purchase-order.history', $model->id) }}`,
                success: function({
                    data
                }) {
                    $('#history-list').html('');
                    $.each(data, function(key, value) {
                        let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                        $('#history-list').append(`
                                <tr>
                                    <td>${localDate(value.date)}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                    });

                    $('#history-modal').modal('show');
                }
            });
        });

        get_request_print_approval(`App\\Models\\PoTrading`, '{{ $model->id }}', 'purchase_order_trading');
    </script>
@endsection
