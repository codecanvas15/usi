@extends('layouts.admin.layout.index')

@php
    $main = 'specific-time-work-agreement';
    $title = 'Perjanjian Kerja Waktu Tertentu';
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
                                            <label for="">{{ Str::headline('branch') }}</label>
                                            <p>{{ $model->branch?->name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('kode') }}</label>
                                            <p>{{ $model->code }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('tanggal') }}</label>
                                            <p>{{ localDate($model->date) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('judul') }}</label>
                                            <p>{{ $model->title }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('jenis') }}</label>
                                            <p>{{ $model->work_agreement_type }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('file') }}</label>
                                            <p>
                                                @if ($model->attachment)
                                                    <x-button color="info" icon="file" label="file" fontawesome size="sm" :link="Storage::url($model->attachment)" />
                                                @else
                                                    <x-button color="danger" icon="file-excel" label="no file" fontawesome size="sm" bagde />
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('status') }}</label>
                                            <p>
                                            <div class="badge badge-lg badge-{{ specific_time_work_agreement_status()[$model->status]['color'] }}">
                                                {{ specific_time_work_agreement_status()[$model->status]['label'] }} -
                                                {{ specific_time_work_agreement_status()[$model->status]['text'] }}
                                            </div>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-10">
                                    <h4>Pihak pertama</h4>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">{{ Str::headline('karyawan') }}</label>
                                                <p>{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">{{ Str::headline('divisi') }}</label>
                                                <p>{{ $model->division?->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">{{ Str::headline('jabatan') }}</label>
                                                <p>{{ $model->position?->nama }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-10">
                                    <h4>Pihak kedua</h4>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">{{ Str::headline('karyawan') }}</label>
                                                @if ($model->second_employee_type == 'new')
                                                    <p>{{ $model->reference?->candidate_data?->name }}</p>
                                                @else
                                                    <p>{{ $model->reference?->employee?->name }} -
                                                        {{ $model->reference?->employee?->NIK }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">{{ Str::headline('divisi') }}</label>
                                                <p>{{ $model->second_division?->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">{{ Str::headline('jabatan') }}</label>
                                                <p>{{ $model->second_position?->nama }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </x-slot>

                            <x-slot name="footer">
                                <div class="d-flex justify-content-end gap-1">
                                    {!! $auth_revert_void_button !!}
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
                    </div>
                    <div class="col-md-3">
                        {!! $authorization_log_view !!}
                        <x-card-data-table title="{{ 'Action' }}">
                            <x-slot name="header_content">
                                @if (in_array($model->status, ['approve']))
                                    @can("close $main")
                                        <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                        <x-modal title="close Perjanjian Kerja Waktu Tertentu" id="close-modal" headerColor="success">
                                            <x-slot name="modal_body">
                                                <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                    @csrf
                                                    <input type="hidden" name="status" value="done">
                                                    <div class="mt-10 border-top pt-10">
                                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                        <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                    </div>
                                                </form>
                                            </x-slot>
                                        </x-modal>
                                    @endcan
                                @endif
                            </x-slot>
                            <x-slot name="table_content">

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
            </div>

            <div class="tab-pane " id="preview-tab" role="tabpanel">
                <x-card-data-table :title="'preview ' . $title">
                    <x-slot name="table_content">
                        {{-- <x-markdown> --}}
                        {{-- {{ $model->description }} --}}
                        {!! $model->description !!}
                        {{-- </x-markdown> --}}
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
        sidebarActive('#{{ $main }}');
    </script>
@endsection
