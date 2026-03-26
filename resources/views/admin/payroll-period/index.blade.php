@extends('layouts.admin.layout.index')

@php
    $main = 'payroll-period';
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

                <div class="col-md-5">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="" label="dari" id="from-date" required />
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="" label="sampai" id="to-date" required />
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <x-button color="primary" icon="search" fontawesome size="sm" id="btn-payroll-period" />
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
                        <th>{{ Str::headline('Nama Periode') }}</th>
                        <th>{{ Str::headline('Tipe Periode') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th> AKSI </th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>

    @endcan
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const payrollPeriodTable = () => {
                    const table = $('table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                from_date: $('#from-date').val(),
                                to_date: $('#to-date').val()
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                "data": "name",
                            },
                            {
                                "data": "type",
                            },
                            {
                                "data": "date",
                            },
                            {
                                "data": "action"
                            },
                        ]
                    });

                    $('table').css('width', '100%');
                }

                payrollPeriodTable();

                $('#btn-payroll-period').click(function(e) {
                    payrollPeriodTable();
                })

            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#payroll-sidebar');
        sidebarActive('#payroll-period')
    </script>
@endsection
