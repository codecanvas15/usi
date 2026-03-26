@extends('layouts.admin.layout.index')

@php
    $main = 'payroll-period';
@endphp

@section('title', Str::headline("Create $main") . ' - ')

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
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
            @csrf

            <x-card-data-table title="{{ 'Tambah Periode Penggajian' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <x-input type="text" id="name" label="Nama Periode" name="name" value="{{ $model->name ?? '' }}" required />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <x-select name="type" id="type" label="Tipe Periode" required>
                                <option value="" selected disabled>Pilih Tipe</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                            </x-select>
                        </div>
                        <div class="col-sm-3">
                            <x-input class="datepicker-input" label="Dari Tanggal" id="date" name="date" onchange="checkClosingPeriod($(this))" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" autofucus />
                        </div>
                        <div class="col-sm-3">
                            <x-input class="datepicker-input" label="Sampai Tanggal" id="date-end" name="date_end" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" />
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <div id="btn-submit">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="submit" color="primary" icon="save" size="sm" label="Save data" />
                </div>
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script>
        checkClosingPeriod($('#date'))
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#payroll-sidebar');
        sidebarActive('#payroll-period')
    </script>
@endsection
