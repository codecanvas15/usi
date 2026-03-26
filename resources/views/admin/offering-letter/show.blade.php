@extends('layouts.admin.layout.index')

@php
    $main = 'offering-letter';
    $title = 'Letter of Intent';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("detail $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table>
            <x-slot name="table_content">
                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#data-tab" id="data-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                            <span class="hidden-xs-down">Data</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded " data-bs-toggle="tab" href="#preview-tab" id="preview-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Preview</span>
                        </a>
                    </li>
                </ul>
            </x-slot>
        </x-card-data-table>

        <div class="tab-content mt-30">
            <div class="tab-pane active" id="data-tab" role="tabpanel">
                <div class="row">
                    <div class="col-md-9">
                        <x-card-data-table :title="'detail ' . $title">
                            <x-slot name="table_content">
                                @include('components.validate-error')
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('kode') }}</label>
                                            <p>{{ $model->reference }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('branch') }}</label>
                                            <p>{{ $model->branch?->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('tanggapan pelamar') }}</label>
                                            <span class="badge bg-{{ offering_letter_status()[$model->applicant_status]['color'] }} text-capitalize">{{ offering_letter_status()[$model->applicant_status]['text'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('keterangan') }}</label>
                                            <p>{{ $model->applicant_status_reason }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('ditanggapi pada') }}</label>
                                            <p>{{ $model->applicant_status_at }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('dibuat oleh') }}</label>
                                            <p>{{ $model->created_by_data->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('dibuat pada') }}</label>
                                            <p>{{ $model->created_at }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('diperbarui pada') }}</label>
                                            <p>{{ $model->updated_at }}</p>
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                            <x-slot name="footer">
                                <div class="d-flex justify-content-end gap-1">
                                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                                    @can("delete $main")
                                        @if ($model->check_available_dates)
                                            <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                            <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                        @endif
                                    @endcan
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
                                            <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                                {{ toDayDateTimeString($item->created_at) }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </x-slot>
                        </x-card-data-table>
                        <x-card-data-table title="{{ 'Export' }}">
                            <x-slot name="header_content">

                            </x-slot>
                            <x-slot name="table_content">
                                <x-button size="md" link="{{ route('admin.offering-letter.export', ['id' => $model->id]) }}" color="info" target="_blank" icon="pdf" label="Export" fontawesome />
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
            </div>

            <div class="tab-pane " id="preview-tab" role="tabpanel">
                <x-card-data-table :title="'preview ' . $title">
                    <x-slot name="table_content">
                        <img src="{{ asset('/images/header-offering-letter.png') }}" style="width: 78rem" alt="header">
                        {!! $model->offering_letter !!}
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#offering-letter')
    </script>
@endsection
