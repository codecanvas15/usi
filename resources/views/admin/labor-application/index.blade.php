@extends('layouts.admin.layout.index')

@php
    $main = 'labor-application';
    $title = 'lamaran pekerjaan';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table :title="$title">
        <x-slot name="header_content">
            @can("create $main")
                <div class="row mb-4">
                    <div class="col-md-2 col-xl-2">
                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                    </div>
                    <div class="col-md-3 col-xl-4">
                        <x-button link='{{ route("admin.$main.download") }}' color="info" icon="download" label="form lamaran karyawan" />
                    </div>
                </div>
            @endcan
            <div class="row align-items-center" id="stock_transfer_receiving">
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="labor-application-from-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="labor-application-to-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-button type="submit" color="primary" class="h-full" id="labor-application-table" icon="search" fontawesome />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $main")
                <x-table id="labor-application">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('tanggal') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('diajukan oleh') }}</th>
                        <th>{{ Str::headline('status') }}</th>
                        <th>{{ Str::headline('dibuat pada') }}</th>
                        <th>{{ Str::headline('export') }}</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const laborApp = () => {
                    const table = $('table#labor-application').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                from_date: $('#labor-application-from-date').val(),
                                to_date: $('#labor-application-to-date').val(),
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'date',
                                name: 'date'
                            },
                            {
                                data: 'code',
                                name: 'code'
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'export',
                                name: 'export'
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                }

                laborApp()

                $('#labor-application-table').click(function(e) {
                    e.preventDefault();
                    laborApp();
                })
            });
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#labor-application');
    </script>
@endsection
