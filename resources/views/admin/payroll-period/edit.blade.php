@extends('layouts.admin.layout.index')

@php
    $main = 'payroll-period';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

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
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <x-card-data-table title="{{ 'edit ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if ($model)
                        @method('PUT')
                    @endif
                    <div class="row">
                        @if ($model)
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <x-input type="text" id="name" label="Nama Periode" name="name" value="{{ $model->name ?? '' }}" required />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <x-select name="type" id="type" label="Tipe Periode" required>
                                    <option value="" selected disabled>Pilih Tipe</option>
                                    <option value="mingguan" @if ($model && $model->type == 'mingguan') selected @endif>Mingguan</option>
                                    <option value="bulanan" @if ($model && $model->type == 'bulanan') selected @endif>Bulanan</option>
                                </x-select>
                            </div>
                            <div class="col-sm-3">
                                <x-input class="datepicker-input" label="Dari Tanggal" id="date" name="date" value="{{ \Carbon\Carbon::parse($model->date)->format('d-m-Y') }}" autofucus />
                            </div>
                            <div class="col-sm-3">
                                <x-input class="datepicker-input" label="Sampai Tanggal" id="date-end" name="end_date" value="{{ \Carbon\Carbon::parse($model->end_date)->format('d-m-Y') }}" autofucus />
                            </div>
                        @endif
                        <div class="box-footer">
                            <div class="d-flex justify-content-end gap-3">
                                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                                <x-button type="submit" color="primary" label="Save data" />
                            </div>
                        </div>
                </form>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#payroll-sidebar');
        sidebarActive('#payroll-period')
    </script>
@endsection
