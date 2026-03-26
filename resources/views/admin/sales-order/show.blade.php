@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order';
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
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.sales.index') }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')
    @can("view $main")
        <div>
            <div class="box bg-gradient-info-dark text-white">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Sales Order</h4>
                            <h1 class="m-0">{{ $model->nomor_so }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_sale_orders') }}</h5>
                                    <div class="badge badge-lg  badge-{{ status_sale_orders()[$model->status]['color'] }}">
                                        {{ Str::headline(status_sale_orders()[$model->status]['label']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('payment_status') }}</h5>
                                    <div class="badge badge-lg  badge-{{ payment_status()[$model->payment_status]['color'] }}">
                                        {{ Str::headline(payment_status()[$model->payment_status]['label']) }}
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
            <div class="col-md-9">
                <x-card-data-table>
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('branch') }}</label>
                                    <p>{{ $model->branch->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->tanggal) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->nomor_so }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode po external') }}</label>
                                    <p>{{ $model->nomor_po_external }}</p>
                                    <span>
                                        @if ($model->check_available_date)
                                            @if (!in_array($model->status, ['done', 'void']))
                                                <x-button color='info' fontawesome icon="pen-to-square" class="w-auto" size="sm" dataToggle='modal' dataTarget='#update-sale-confirmation' />
                                                <x-modal title="update nomor po external" id="update-sale-confirmation" headerColor="info">
                                                    <x-slot name="modal_body">
                                                        <form action="{{ route("admin.$main.update-nomor-po-external", $model) }}" method="post" enctype="multipart/form-data">
                                                            @method('PUT')
                                                            @csrf
                                                            <div class="form-group">
                                                                <x-input type="text" id="nomor_po_external" name="nomor_po_external" label="nomor_po_external" value="{{ $model->nomor_po_external ?? '' }}" />
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
                                    <label for="">{{ Str::headline('customer') }}</label>
                                    <p>{{ $model->customer->nama }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Sh No.') }}</label>
                                    <p>{{ $model->sh_number->kode }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Supply Point') }}</label>
                                    <p>{{ $model->sh_number->sh_number_details()->where('type', 'Supply Point')->first()?->alamat }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                                    <label for="">{{ Str::headline('Mata Uang') }}</label>
                                    <p>{{ $model->currency?->kode . ' / ' . $model->currency?->nama . ' / ' . $model->currency?->negara }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Nilai Tukar') }}</label>
                                    <p>{{ formatNumber($model->exchange_rate) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Quotation') }}</label>
                                    <p>
                                        @if ($model->quotation)
                                            <x-button color="info" link="{{ url('storage/' . $model->quotation) }}" size="sm" icon="file" fontawesome target="_blank" />
                                        @else
                                            <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg text-wrap text-start badge-{{ status_sale_orders()[$model->status]['color'] }} mb-1">
                                        {{ Str::headline(status_sale_orders()[$model->status]['text']) }} -
                                        {{ Str::headline(status_sale_orders()[$model->status]['label']) }}
                                    </div>
                                    <div class="badge badge-lg text-wrap badge-{{ payment_status()[$model->payment_status]['color'] }} mb-1">
                                        {{ Str::headline(payment_status()[$model->payment_status]['text']) }} -
                                        {{ Str::headline(payment_status()[$model->payment_status]['label']) }}
                                    </div>

                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('pairing_status') }}</label>
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
                            @php
                                $so_trading_detail = $model->so_trading_detail;
                            @endphp
                            <h4 class="fw-bold">Forecast Trading item</h4>
                            <x-table theadColor='dark'>
                                <x-slot name="table_head">
                                    <th class="col-md-3">Detail Item</th>
                                    <th class="col-md"></th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <th>Item</th>
                                        <td>{{ $so_trading_detail->item->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Harga Item</th>
                                        <td>{{ $model->currency->simbol }}
                                            {{ number_format($so_trading_detail->harga, 2, ',', '.') }} / {{ $so_trading_detail->item->unit->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Qty Forecast</th>
                                        <td>{{ formatNumber($so_trading_detail->jumlah) }} {{ $so_trading_detail->item->unit->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Qty Real</th>
                                        <td>{{ formatNumber($model->delivery_orders->where('status', 'done')->where('type', 'delivery-order')->sum('unload_quantity_realization')) }}
                                            {{ $so_trading_detail->item->unit->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('sub_total') }}</th>
                                        <td class="text-end">{{ $model->currency->simbol }}
                                            {{ formatNumber($model->sub_total) }}</td>
                                    </tr>
                                    @if ($model->sale_order_taxes)
                                        @foreach ($model->sale_order_taxes as $item)
                                            <tr>
                                                <th>{{ Str::headline($item->tax?->name ?? 'Undefined') }} -
                                                    {{ $item->value * 100 }}</th>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($item->total) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr class="bg-success">
                                        <th class="text-white text-end">{{ Str::headline('total') }}</th>
                                        <td class="text-white text-end">{{ $model->currency->simbol }}
                                            {{ formatNumber($model->sub_total_after_tax) }}</td>
                                    </tr>
                                </x-slot>
                            </x-table>

                            @php
                                $so_trading_detail = $model->so_trading_detail;
                                $quantity = $model->delivery_orders->where('status', 'done')->where('type', 'delivery-order')->sum('unload_quantity_realization');
                                $sub_total = $so_trading_detail->harga * $model->delivery_orders->where('status', 'done')->where('type', 'delivery-order')->sum('unload_quantity_realization');
                                $total = $sub_total;
                            @endphp
                            <h4 class="fw-bold">Real Trading item</h4>
                            <x-table theadColor='dark'>
                                <x-slot name="table_head">
                                    <th class="col-md-3">Detail Item</th>
                                    <th class="col-md"></th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <th>Qty Forecast</th>
                                        <td>{{ formatNumber($so_trading_detail->jumlah) }} {{ $so_trading_detail->item->unit->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Qty Real</th>
                                        <td>{{ formatNumber($model->delivery_orders->where('status', 'done')->where('type', 'delivery-order')->sum('unload_quantity_realization')) }}
                                            {{ $so_trading_detail->item->unit->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('sub_total') }}</th>
                                        <td class="text-end">{{ $model->currency->simbol }}
                                            {{ formatNumber($sub_total) }}</td>
                                    </tr>
                                    @if ($model->sale_order_taxes)
                                        @foreach ($model->sale_order_taxes as $item)
                                            <tr>
                                                <th>{{ Str::headline($item->tax?->name ?? 'Undefined') }} -
                                                    {{ $item->value * 100 }}</th>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($sub_total * $item->value) }}</td>
                                            </tr>
                                            @php
                                                $total += $sub_total * $item->value;
                                            @endphp
                                        @endforeach
                                    @endif
                                    <tr class="bg-success">
                                        <th class="text-white text-end">{{ Str::headline('total') }}</th>
                                        <td class="text-white text-end">{{ $model->currency->simbol }}
                                            {{ formatNumber($total) }}</td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>

                        @if (count($model->sale_order_additionals))
                            <div class="mt-20">
                                <h4 class="fw-bold">Additional Forecast item</h4>
                                <x-table theadColor='dark'>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('Item') }}</th>
                                        <th>{{ Str::headline('Qty') }}</th>
                                        <th>{{ Str::headline('Harga') }}</th>
                                        <th>{{ Str::headline('Sub total') }}</th>
                                        <th>{{ Str::headline('Tax') }}</th>
                                        <th>{{ Str::headline('value') }}</th>
                                        <th>{{ Str::headline('Total') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->sale_order_additionals as $item)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $item->item?->nama }}</td>
                                                <td>{{ formatNumber($item->quantity) }}</td>
                                                <td>{{ $model->currency->simbol }} {{ formatNumber($item->price) }}</td>
                                                <td> {{ $model->currency->simbol }}{{ formatNumber($item->sub_total) }}</td>
                                                <td>
                                                    <div>
                                                        @forelse ($item->sale_order_additional_taxes as $item_tax)
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
                                                        @forelse ($item->sale_order_additional_taxes as $item_tax)
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
                                            <td class="text-end" colspan="7">Dpp</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->sale_order_additionals->sum('sub_total')) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total pajak</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->additional_tax_total) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total</td>
                                            <td class="bg-success text-white text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->other_cost) }}</td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        @endif

                        @if (count($model->sale_order_additionals))
                            <div class="mt-20">
                                <h4 class="fw-bold">Additional Real item</h4>
                                <x-table theadColor='dark'>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('Item') }}</th>
                                        <th>{{ Str::headline('Qty') }}</th>
                                        <th>{{ Str::headline('Harga') }}</th>
                                        <th>{{ Str::headline('Sub total') }}</th>
                                        <th>{{ Str::headline('Tax') }}</th>
                                        <th>{{ Str::headline('value') }}</th>
                                        <th>{{ Str::headline('Total') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @php
                                            $quantity = $model->delivery_orders->where('status', 'done')->where('type', 'delivery-order')->sum('unload_quantity_realization');
                                            $sub_total = 0;
                                            $tax_total = 0;
                                            $total = 0;
                                        @endphp
                                        @foreach ($model->sale_order_additionals as $item)
                                            @php
                                                $single_total = 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $item->item?->nama }}</td>
                                                <td>{{ formatNumber($item->quantity) }}</td>
                                                <td>{{ $model->currency->simbol }} {{ formatNumber($item->price) }}</td>
                                                <td> {{ $model->currency->simbol }}{{ formatNumber($item->price * $quantity) }}</td>
                                                <td>
                                                    <div>
                                                        @forelse ($item->sale_order_additional_taxes as $item_tax)
                                                            <p class="mb-5">
                                                                {{ $item_tax->tax?->name }}
                                                                {{ $item_tax->value * 100 }}%
                                                            </p>
                                                        @empty
                                                            <x-button size="sm" badge color="danger" label="no Tax" />
                                                        @endforelse
                                                    </div>
                                                    @php
                                                        $sub_total += $item->price * $quantity;
                                                        $total += $sub_total;
                                                        $single_total = $total;
                                                    @endphp
                                                </td>
                                                <td>
                                                    <div>
                                                        @forelse ($item->sale_order_additional_taxes as $item_tax)
                                                            <p class="mb-5"class="mb-4">
                                                                {{ $model->currency->simbol }}
                                                                {{ formatNumber($item->price * $quantity * $item_tax->value) }}
                                                            </p>
                                                            @php
                                                                $tax_total = $item->price * $quantity * $item_tax->value;
                                                                $total += $item->price * $quantity * $item_tax->value;
                                                                $single_total += $total;
                                                            @endphp
                                                        @empty
                                                            <x-button size="sm" badge color="danger" label="no Tax" />
                                                        @endforelse
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($total) }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <td class="text-end" colspan="7">Dpp</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($sub_total) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total pajak</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($tax_total) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total</td>
                                            <td class="bg-success text-white text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($total) }}</td>
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
                                @if (in_array($model->status, ['pending', 'revert']))
                                    @can("edit $main")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan

                                    @can("delete $main")
                                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>
                <x-card-data-table title="{{ 'sales order item ' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        <x-table theadCoor="danger">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Item') }}</th>
                                <th>{{ Str::headline('kuantitas') }}</th>
                                <th>{{ Str::headline('sisa pairing') }}</th>
                                <th>{{ Str::headline('nomor po') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">

                                <tr>
                                    <td>{{ 1 }}</td>
                                    <td>{{ $so_trading_detail->item->nama }}</td>
                                    <td>{{ formatNumber($so_trading_detail->jumlah) }} {{ $so_trading_detail->item->unit->name }}</td>
                                    <td>{{ formatNumber($so_trading_detail->alokasi_tersedia) }} {{ $so_trading_detail->item->unit->name }}</td>
                                    <td>
                                        @foreach ($pairing_pos as $po)
                                            <a href="{{ route('admin.purchase-order.show', $po) }}" target="_blank">{{ $po->nomor_po }}</a>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="badge badge-lg badge-{{ status_sale_orders_details()[$so_trading_detail->status]['color'] }}">
                                            {{ Str::headline(status_sale_orders_details()[$so_trading_detail->status]['text']) }}
                                            -
                                            {{ Str::headline(status_sale_orders_details()[$so_trading_detail->status]['label']) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($so_trading_detail->status == 'pairing')
                                            @if ($so_trading_detail->sudah_dialokasikan < $so_trading_detail->jumlah)
                                                @can("pairing $main")
                                                    <x-button color='primary' fontawesome icon="link" size="sm" link="{{ route('admin.pairing.pairing', $so_trading_detail) }}" />
                                                @endcan
                                            @endif

                                            @if ($model->pairing_status != 'done')
                                                @can("bypass pairing $main")
                                                    <x-button color='warning' fontawesome icon="arrow-right" label="bypass pairing" size="sm" data-toggle="modal" data-target="#bypass-pairing" />
                                                    <x-modal title="bypass pairing" id="bypass-pairing" headerColor="warning">
                                                        <x-slot name="modal_body">
                                                            <form action="{{ route('admin.select.so-trading.bypass-pairing', ['id' => $model->id]) }}" method="post">
                                                                @csrf
                                                                <div class="mt-10">
                                                                    <p>Apakah anda yakin akan bypass pairing SO ini?</p>
                                                                </div>
                                                                <div class="mt-10 border-top pt-10">
                                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="batal" size="sm" icon="times" fontawesome />
                                                                    <x-button type="submit" color="primary" label="ya, lanjutkan" size="sm" icon="" fontawesome />
                                                                </div>
                                                            </form>
                                                        </x-slot>
                                                    </x-modal>
                                                @endcan
                                            @endif
                                        @endif
                                        @if ($model->status == 'ready' && $model->pairing_status == 'done')
                                            <x-button color='warning' fontawesome icon="xmark" label="batalkan bypass pairing" size="sm" data-toggle="modal" data-target="#cancel-bypass-pairing" />
                                            <x-modal title="batalkan bypass pairing" id="cancel-bypass-pairing" headerColor="warning">
                                                <x-slot name="modal_body">
                                                    <form action="{{ route('admin.select.so-trading.cancel-bypass-pairing', ['id' => $model->id]) }}" method="post">
                                                        @csrf
                                                        <div class="mt-10">
                                                            <p>Apakah anda yakin akan membatalkan bypass pairing SO ini?</p>
                                                        </div>
                                                        <div class="mt-10 border-top pt-10">
                                                            <x-button type="button" color="secondary" dataDismiss="modal" label="batal" size="sm" icon="times" fontawesome />
                                                            <x-button type="submit" color="primary" label="ya, lanjutkan" size="sm" icon="" fontawesome />
                                                        </div>
                                                    </form>
                                                </x-slot>
                                            </x-modal>
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
                                <th>No. PO</th>
                                <th>Alokasi</th>
                                <th>Supplier</th>
                                <th>PO Status</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @php
                                    $so_qty_outstanding = $so_trading_detail->jumlah;
                                @endphp
                                @foreach ($so_trading_detail->pairing_so_to_pos as $pairing)
                                    @php
                                        $so_qty_outstanding -= $pairing->alokasi;
                                    @endphp
                                    <tr>
                                        <th>{{ $loop->index + 1 }}</th>
                                        <td><a href="{{ route('admin.purchase-order.show', ['purchase_order' => $pairing->po_trading_detail->po_trading->id]) }}" target="_blank">{{ $pairing->po_trading_detail->po_trading->nomor_po }}</a></td>
                                        <td class="text-end">{{ formatNumber($pairing->alokasi) }}</td>
                                        <td>{{ $pairing->po_trading_detail->po_trading->vendor->nama }}</td>
                                        <td class="text-end">{{ formatNumber($pairing->po_trading_detail->sudah_dialokasikan) }}/{{ formatNumber($pairing->po_trading_detail->jumlah) }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                        <x-button color='primary' fontawesome icon="sliders" size="sm" link="{{ route('admin.sales-order.adjust-pairing', $model) }}" />
                    </x-slot>
                </x-card-data-table>

                @if (count($model->delivery_orders) != 0)
                    <x-card-data-table title="{{ 'delivery order ' }}">
                        <x-slot name="header_content">

                        </x-slot>
                        <x-slot name="table_content">
                            @include('components.validate-error')
                            <x-table id="do-list">
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('Nomor do') }}</th>
                                    <th>{{ Str::headline('Target Delivery') }}</th>
                                    <th>{{ Str::headline('Tanggal muat') }}</th>
                                    <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                    <th>{{ Str::headline('kuantitas_dikirim') }}</th>
                                    <th>{{ Str::headline('kuantitas_diterima') }}</th>
                                    <th>{{ Str::headline('moda transport') }}</th>
                                    <th>{{ Str::headline('status') }}</th>
                                    <th>{{ Str::headline('created at') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </x-slot>
                    </x-card-data-table>
                @endif
            </div>
            <div class="col-md-3">
                {!! $authorization_log_view !!}

                <div id="print-request-container"></div>

                <x-card-data-table title="action">
                    <x-slot name="table_content">
                        @if ($model->check_available_date)
                            @if (in_array($model->status, ['partial_sent']))
                                @can("close $main")
                                    <x-button size="sm" color="success" icon="circle-xmark" fontawesome label="close" dataToggle="modal" dataTarget="#close-modal" />
                                    <x-modal title="close sale-order" id="close-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="done">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="save" size="sm" icon="save" fontawesome />
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
                    <x-slot name="table_content">
                        <a target="_blank" class="mb-1 btn btn-info" href="{{ route('sales-order.export.id', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" @authorize_print('sale_order_trading') data-model="{{ \App\Models\SoTrading::class }}" data-id="{{ $model->id }}" data-print-type="sale_order" data-link="{{ route('admin.sales-order.show', $model->id) }}" data-code="{{ $model->nomor_so }}" @endauthorize_print><i class="fa fa-file"></i> Export</a>
                    </x-slot>
                </x-card-data-table>
                @if ($model->delivery_orders->count() > 0 and !$model->is_delivery_complete)
                    <x-card-data-table>
                        <x-slot name="table_content">
                            <x-button type="button" color="info" label="Generate Invoice" icon="plus" fontawesome id="" link="{{ route('admin.invoice-trading.generate', $model) }}" />
                        </x-slot>
                    </x-card-data-table>
                @endif
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
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table#do-list').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("admin.$main.delivery-order", $model) }}?type=delivery-order',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'target_delivery',
                            name: 'target_delivery'
                        },
                        {
                            data: 'load_date',
                            name: 'load_date'
                        },
                        {
                            data: 'unload_date',
                            name: 'unload_date'
                        },
                        {
                            data: 'load_quantity',
                            name: 'load_quantity'
                        },
                        {
                            data: 'unload_quantity_realization',
                            name: 'unload_quantity_realization'
                        },
                        {
                            data: 'moda_transport',
                            name: 'moda_transport'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        }
                    ]
                });

            });
        </script>
    @endcan
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order');

        $('#history-button').on('click', function() {
            $.ajax({
                url: '{{ route("admin.$main.history", $model->id) }}',
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

        get_request_print_approval(`App\\Models\\SoTrading`, '{{ $model->id }}', 'sale_order');
    </script>
@endsection
