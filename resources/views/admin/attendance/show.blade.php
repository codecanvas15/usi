@extends('layouts.admin.layout.index')

@php
    $main = 'attendance';
    $title = 'presensi';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Detail $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('view presensi')
        <x-card-data-table :title="'Detail ' . $title">
            <x-slot name="header_content">

            </x-slot>
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
                            <label for="">{{ Str::headline('karyawan') }}</label>
                            <p>{{ $model->employee->name }} - {{ $model->employee->NIK }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('tanggal') }}</label>
                            <p>{{ $model->date ? localDate($model->date) : '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('masuk') }}</label>
                            <p>{{ $model->in_time ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('keluar') }}</label>
                            <p>{{ $model->out_time ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('pulang lebih cepat') }}</label>
                            <p>{{ $model->go_home_early ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('telambat') }}</label>
                            <p>{{ $model->late ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('lembur') }}</label>
                            <p>{{ $model->overtime ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('jam kerja') }}</label>
                            <p>{{ $model->work_hours ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('jam hadir') }}</label>
                            <p>{{ $model->attendance_hours ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('deskripsi') }}</label>
                            <p>{{ $model->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                    @if ($model->check_available_date)
                        @can('edit presensi')
                            <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                        @endcan

                        @can('delete presensi')
                            <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                            <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                        @endcan
                    @endif
                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
