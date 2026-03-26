@extends('layouts.admin.layout.index')

@php
    $main = 'contract-extension';
    $title = 'pengajuan kontrak';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . Str::headline($title)) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title='{{ "detail $title" }}'>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('branch') }}</label>
                                    <p>{{ $model->branch->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('employee') }}</label>
                                    <p>{{ $model->employee->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('divisi') }}</label>
                                    <p>{{ $model->division->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('periode kontrak') }}</label>
                                    <p>{{ localDate($model->from_date) }} - {{ localDate($model->to_date) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status pengajuan') }}</label>
                                    <p>{{ Str::headline($model->submission_status) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('dibuat oleh') }}</label>
                                    <p>{{ $model->user?->name }} - {{ $model->user?->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>{{ $model->status }}</p>
                                    <td>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="badge badge-lg badge-{{ contract_extension_status()[$model->status]['color'] }}">
                                                {{ contract_extension_status()[$model->status]['label'] }} -
                                                {{ labor_demand_status()[$model->status]['text'] }}
                                            </div>
                                        </div>
                                    </td>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if ($model->status == 'pending')
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
                <x-card-data-table title='{{ "Assesment $title" }}'>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="col-md-12 row mt-2">
                            <x-table>
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('Aspek Penilaian') }}</th>
                                    <th>{{ Str::headline('nilai') }}</th>
                                    <th>{{ Str::headline('catatan') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->assesment as $item)
                                        <tr>
                                            <td class="align-top"><b>{{ Str::headline($item->type) }}</b></td>
                                            <td class="align-top">
                                                {{ Str::headline($item->value) }}
                                            </td>
                                            <td class="align-top">
                                                {{ $item->note }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>
                    </x-slot>

                    <x-slot name="footer">

                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
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
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#contract-extension');
    </script>
@endsection
