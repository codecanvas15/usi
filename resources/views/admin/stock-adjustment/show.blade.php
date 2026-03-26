@extends('layouts.admin.layout.index')

@php
    $main = 'stock-adjustment';
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
    @can(["view $main"])

        <div class="row">
            <div class="col-md-9">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <x-table theadColor='danger'>
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th>{{ Str::headline('kantor') }}</th>
                                    <td>{{ $model->warehouse->nama }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('kode') }}</th>
                                    <td>{{ $model->code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <td>{{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('keterangan') }}</th>
                                    <td>{{ $model->note }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('gudang') }}</th>
                                    <td>{{ $model->warehouse->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('coa expense') }}</th>
                                    <td>{{ $model->coa->name ?? '' }} - {{ $model->coa->account_code ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ Str::headline('status') }}
                                    </th>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <div class="badge badge-lg badge-{{ stock_usage_status()[$model->status]['color'] }}">
                                                {{ stock_usage_status()[$model->status]['label'] }} -
                                                {{ stock_usage_status()[$model->status]['text'] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('created_by') }}</th>
                                    <td>{{ $model->creator->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('created_at') }}</th>
                                    <td>{{ toDayDateTimeString($model->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('last medified') }}</th>
                                    <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
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
                                <th>Stok</th>
                                <th>Stok Fisik</th>
                                <th class="text-end">HPP</th>
                                <th class="text-end">Nilai Selisih</th>
                                <th>Keterangan</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->details as $detail)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $detail->item->nama }}</td>
                                        <td>{{ formatNumber($detail->stock) }} {{ $detail->item->unit->name }}</td>
                                        <td>{{ $detail->real_stock }} {{ $detail->item->unit->name }}</td>
                                        <td class="text-end">{{ formatNumber($detail->price_unit) }}</td>
                                        <td class="text-end">{{ formatNumber($detail->value) }}</td>
                                        <td>{{ $detail->note }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <td colspan="5" class="text-end">Total</td>
                                    <td class="text-end">{{ formatNumber($model->details->sum('value')) }}</td>
                                    <td></td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>

                @can('view journal')
                    @include('components.journal-table')
                @endcan
            </div>
            <div class="col-md-3">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Status Logs' }}">
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @forelse ($status_logs as $item)
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
                            @empty
                                <li class="list-group-item">
                                    <h5 class="fw-bold">Empty</h5>
                                </li>
                            @endforelse
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
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-adjustment');
    </script>

    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\StockOpname`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
