@extends('layouts.admin.layout.index')

@php
    $main = 'closing-period';
    $title = 'closing';
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
                        {{ Str::headline('keuangan & akuntansi') }}
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
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" link="{{ route('admin.closing-period.create') }}" icon="plus" label="tambah closing" />
                    </div>
                </div>
            @endcan

            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="payroll-to-date" required />
                    </div>
                </div>

                <div class="col-md-1">
                    <x-button type="button" color="info" id="set-table" icon="search" fontawesome />
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $main")
                <x-table id="close-period">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('period') }}</th>
                        <th>{{ Str::headline('status') }}</th>
                        <th></th>
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
                const closePeriodTable = () => {
                    const table = $('table#close-period').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
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
                                data: 'to_date',
                            },

                            {
                                data: 'status',
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

                closePeriodTable()

                $('#set-table').click(function(e) {
                    e.preventDefault();
                    closePeriodTable()
                })
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#closing-period')
    </script>
@endsection
