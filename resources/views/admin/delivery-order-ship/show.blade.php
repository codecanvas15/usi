@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order-ship';
    $title = Str::headline('Delivery order kapal');
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.delivery.index') }}">{{ Str::headline('delivery order') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table :title='"Detail $title"'>
                <x-slot name="header_content">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('code') }}</label>
                            <p>{{ $model->code }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('target_delivery') }}</label>
                            <p>{{ \Carbon\Carbon::parse($model->target_delivery)->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('cabang') }}</label>
                            <p>{{ $model->branch->name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('kode so') }}</label>
                            <p>{{ $model->soTrading->nomor_so }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('purchase_transport') }}</label>
                            <p>{{ $model->purchaseTransport->kode }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('gudang') }}</label>
                            <p>{{ $model->wareHouse?->nama }}</p>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('load_date') }}</label>
                            <p>{{ \Carbon\Carbon::parse($model->load_date)->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('kuantitas muat') }}</label>
                            <p>{{ formatNumber($model->load_quantity) }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('realisasi kuantitas muat') }}</label>
                            <p>{{ formatNumber($model->load_quantity_realization) }}</p>
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('unload_date') }}</label>
                            <p>{{ \Carbon\Carbon::parse($model->unload_date)->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('realisasi kuantitas bongkar') }}</label>
                            <p>{{ formatNumber($model->unload_quantity_realization) }}</p>
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('kuantitas sudah di gunakan') }}</label>
                            <p>{{ formatNumber($model->quantity_used) }}</p>
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('hpp') }}</label>
                            <p>{{ $model->hpp }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('segel atas') }}</label>
                            <p>{{ $model->top_seal }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('segel bawah') }}</label>
                            <p>{{ $model->bottom_seal }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('temperatur') }}</label>
                            <p>{{ $model->temperature }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('meter awal') }}</label>
                            <p>{{ $model->initial_meter }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('meter akhir') }}</label>
                            <p>{{ $model->initial_final }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('sg_meter') }}</label>
                            <p>{{ $model->sg_meter }}</p>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('file') }}</label>
                            <p>
                                @if ($model->file)
                                    <x-button type="button" color="info" label="file" size="sm" icon="file" label="view_file" link='{{ url("storage/$model->file") }}' fontawesome />
                                @else
                                    <x-button badge color="danger" icon="eye-slash" size="sm" label="file not available" fontawesome />
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">{{ Str::headline('status') }}</label>
                            <p>
                            <div class="badge badge-lg badge-{{ delivery_order_ship_status()[$model->status]['color'] }}">
                                {{ delivery_order_ship_status()[$model->status]['label'] }} - {{ delivery_order_ship_status()[$model->status]['text'] }}
                            </div>

                            @if ($model->status == 'pending')
                                @can('approve delivery-order')
                                    <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#approve-modal" />
                                    <x-modal title="approve delivery order" id="approve-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="approve">

                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan

                                @can('reject delivery-order')
                                    <x-button color="dark" icon="x" fontawesome label="reject" size="sm" dataToggle="modal" dataTarget="#reject-modal" />
                                    <x-modal title="reject delivery order" id="reject-modal" headerColor="dark">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="reject">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif

                            @if ($model->status == 'approve')
                                @can('void delivery-order')
                                    <x-button color="danger" icon="trash" fontawesome label="void" size="sm" dataToggle="modal" dataTarget="#void-modal" />
                                    <x-modal title="void delivery order" id="void-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="void">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Void" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                                @can('close delivery-order')
                                    <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                    <x-modal title="close delivery order" id="close-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="done">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="close" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif

                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">{{ Str::headline('informasi kapal') }}</label>
                            <p>{{ $model->fleet_information }}</p>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">{{ Str::headline('deskripsi') }}</label>
                            <p>{{ $model->description }}</p>
                        </div>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">

                        @if (in_array($model->status, ['pending', 'revert', 'approve', 'partial-used']))
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if (!in_array($model->status, ['done', 'reject', 'void', 'cancel']))
                                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link="{{ route('admin.delivery-order-ship.edit', ['delivery_order_ship' => $model->id]) }}" />
                            @endif
                        @endif
                    </div>
                </x-slot>
            </x-card-data-table>
        </div>

        <div class="col-md-3">
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
                                <small class="text-secondary">{{ Str::headline($item->user?->name) }} - {{ toDayDateTimeString($item->created_at) }}</small>
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
                                <small class="text-secondary">{{ Str::headline($item->user?->name) }} - {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order');
    </script>
@endsection
