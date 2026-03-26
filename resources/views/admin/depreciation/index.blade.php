@extends('layouts.admin.layout.index')

@php
    $main = 'depreciation';
    $menu = 'depresiasi aset';
@endphp

@section('title', Str::headline($main) . ' - ')

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
                        {{ Str::headline('keuangan & akuntansi') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('asset') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $menu }}">
        <x-slot name="header_content">
            <div class="row justify-content-between mb-4">
                <div class="row mb-4">
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-select id="branch_id" label="branch">
                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal awal" value="" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="to_date" name="to" label="tanggal akhir" value="" required />
                        </div>
                    </div>
                    <div class="col-md-3 row align-self-end">
                        <div class="form-group">
                            <x-button type="button" color="info" id="" icon="search" fontawesome onclick="table.ajax.reload()" />
                            @can('create ' . $main)
                                <x-button color="info" dataToggle="modal" dataTarget="#depreciation-modal" label="Depresiasi" />
                                <div class="modal fade" id="depreciation-modal" aria-hidden="true" aria-labelledby="depreciation-modalLabel" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('admin.' . $main . '.store') }}" method="post">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="depreciation-modalLabel">{{ Str::headline('depresiasi asset') }}</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-input type="text" label="tanggal depresiasi" name="date" id="date" class="month-year-picker-input" required value="{{ date('m-Y') }}" onchange="checkClosingPeriod($(this))" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="col-md-12 text-center">
                                                        <x-button color="info" label="Proses Depresiasi" class="btn-sm" type="submit" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="col-md-3 row align-self-end">
                        <div class="form-group">
                            @can('create ' . $main)
                                <x-button color="info" dataToggle="modal" dataTarget="#cancel-depreciation-modal" label="Cancel Depresiasi" />
                                <div class="modal fade" id="cancel-depreciation-modal" aria-hidden="true" aria-labelledby="cancel-depreciation-modalLabel" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('admin.depreciation.destroyRange') }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="cancel-depreciation-modalLabel">{{ Str::headline('Cancel depresiasi asset') }}</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <x-input type="text" label="bulan" name="month" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="col-md-12 text-center">
                                                        <x-button color="info" label="Proses Cancel Depresiasi" class="btn-sm" type="submit" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table>
                <x-slot name="table_head">
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('asset') }}</th>
                    <th>{{ Str::headline('tanggal') }}</th>
                    <th>{{ Str::headline('dari tanggal') }}</th>
                    <th>{{ Str::headline('sampai tanggal') }}</th>
                    <th>{{ Str::headline('jumlah') }}</th>
                    <th>{{ Str::headline('keterangan') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/admin/depreciation/datatable.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#asset-finance-sidebar');
        sidebarActive('#depreciation');
    </script>

    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
