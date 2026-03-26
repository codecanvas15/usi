@extends('layouts.admin.layout.index')

@php
    $main = 'amortization';
    $menu = 'amortisasi';
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
                            <x-button type="button" color="info" id="set-service-table" icon="search" fontawesome onclick="table.ajax.reload()" />
                            @can('create ' . $main)
                                <x-button color="info" dataToggle="modal" dataTarget="#amortization-modal" label="amortisasi" />
                                <div class="modal fade" id="amortization-modal" aria-hidden="true" aria-labelledby="amortization-modalLabel" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('admin.' . $main . '.store') }}" method="post">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="amortization-modalLabel">{{ Str::headline('amortisasi') }}</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-input type="text" label="tanggal amortisasi" name="date" id="date" class="month-year-picker-input" required value="{{ date('m-Y') }}" onchange="checkClosingPeriod($(this))" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="col-md-12 text-center">
                                                        <x-button color="info" label="Proses Amortisasi" class="btn-sm" type="submit" />
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
                                <x-button color="info" dataToggle="modal" dataTarget="#cancel-amortization-modal" label="Cancel Amortisasi" />
                                <div class="modal fade" id="cancel-amortization-modal" aria-hidden="true" aria-labelledby="cancel-amortization-modalLabel" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('admin.amortization.destroyRange') }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="cancel-amortization-modalLabel">{{ Str::headline('Cancel amortisasi asset') }}</h1>
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
                                                        <x-button color="info" label="Proses Cancel Amortisasi" class="btn-sm" type="submit" />
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
    <script src="{{ asset('js/admin/amortization/datatable.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#asset-finance-sidebar')
        sidebarActive('#amortization');
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
