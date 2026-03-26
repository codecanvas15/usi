@extends('layouts.admin.layout.index')

@php
    $main = 'stock-adjustment';
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
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ $main }}">
            <x-slot name="header_content">
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        </div>
                    </div>
                @endcan
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="from_date" label="from date" value="" id="stock-adjusment-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="stock-adjusment-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-button type="button" color="primary" id="set-stock-adjusment-to-date" icon="search" fontawesome></x-button>
                        </div>
                    </div>
                </div>

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="stock_adjusment">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Kode') }}</th>
                        <th>{{ Str::headline('Status') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Pegawai') }}</th>
                        <th>{{ Str::headline('Keterangan') }}</th>
                        <th>Aksi</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const stockAdjusmentTable = () => {
                const table = $('table#stock_adjusment').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#stock-adjusment-from-date').val(),
                            to_date: $('#stock-adjusment-to-date').val(),
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'employee',
                            name: 'employee'
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            orderable: false,
                            searchable: false
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
            }

            stockAdjusmentTable()

            $('#set-stock-adjusment-to-date').click(function() {
                stockAdjusmentTable()
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-adjustment');
    </script>
@endsection
