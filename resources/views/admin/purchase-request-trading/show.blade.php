@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request-trading';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
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
    @canany(['view purchase-request-service', 'view purchase-request-general', 'view purchase-request-transport'])
        <div>
            <div class="box bg-gradient-warning-dark text-white">
                <div class="box-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Purchase Request</h4>
                            <h1 class="m-0">{{ $model->kode }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_purchase_request') }}</h5>
                                    <div class="badge badge-lg badge-{{ purchase_request_status()[$model->status]['color'] }}">
                                        {{ Str::headline(purchase_request_status()[$model->status]['label']) }}
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
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Nomor') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('customer') }}</label>
                                    <p>{{ $model->customer->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('SH number') }}</label>
                                    <p>{{ $model->sh_number->kode }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('keterangan') }}</label>
                                    <p>{!! $model->note !!}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('dibuat oleh') }}</label>
                                    <p>{{ $model->created_by_user?->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('created_at') }}</label>
                                    <p>{{ toDayDateTimeString($model->created_at) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('last medified') }}</label>
                                    <p>{{ toDayDateTimeString($model->updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">

                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            {!! $auth_revert_void_button !!}

                            @if ($model->status == 'pending' or $model->status == 'revert')
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan

                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="{{ $main . ' item' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>Item</th>
                                <th>Jumlah</th>
                                <th>Jumlah Di Order</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->purchase_request_trading_details as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->item_id ? $item->item->kode . ' ' . $item->item->nama : $item->item->item }}
                                        </td>
                                        <td>{{ formatNUmber($item->qty) }} {{ $item->item->unit?->name ?? '-' }}</td>
                                        <td>{{ formatNUmber($item->ordered_qty) }} {{ $item->item->unit?->name ?? '-' }}</td>
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

                <x-card-data-table>
                    <x-slot name="table_content">
                        <a type="button" target="_blank" class="btn btn-info" href="{{ route($main . '.export', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" @authorize_print('purchase_request_trading') data-model="{{ \App\Models\PurchaseRequestTrading::class }}" data-id="{{ $model->id }}" data-print-type="purchase_request_trading" data-link="{{ route("admin.purchase-request-trading.show", $model->id) }}" data-code="{{ $model->code }}" @endauthorize_print> <i class="fa fa-file"></i> Export</a>
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
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
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
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcanany
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase-request');
        $('body').addClass('sidebar-collapse');
        get_request_print_approval(`App\\Models\\PurchaseRequestTrading`, '{{ $model->id }}', 'purchase_request_trading');
    </script>

    @canany(['approve purchase-request-service', 'approve purchase-request-general', 'approve purchase-request-transport'])
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    @endcan
@endsection
