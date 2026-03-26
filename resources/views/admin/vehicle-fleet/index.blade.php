@extends('layouts.admin.layout.index')

@php
    $main = 'vehicle-fleet';
@endphp

@section('title', Str::headline('Truck') . ' - ')

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
                        {{ Str::headline('Truck') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="truck">
            <x-slot name="header_content">
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        </div>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                @can("view $main")
                    <x-table>
                        <x-slot name="table_head">
                            <th>{{ Str::upper('#') }}</th>
                            <th>{{ Str::upper('Nama') }}</th>
                            <th>{{ Str::upper('Plat Nomor') }}</th>
                            <th>{{ Str::upper('Type') }}</th>
                            <th>{{ Str::upper('Kapasitas') }}</th>
                            <th>{{ Str::upper('Nomor Stnk') }}</th>
                            <th>{{ Str::upper('Last Modified At') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                @endcan
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    @can("view $main")
        <script>
            $(document).ready(() => {
                const table = $('table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("admin.$main.index") }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'plat_nomor',
                            name: 'plat_nomor'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'kapasitas',
                            name: 'kapasitas'
                        },
                        {
                            data: 'nomor_stnk',
                            name: 'nomor_stnk'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-garage-sidebar');
        sidebarActive('#vehicle-fleet')
    </script>
@endsection
