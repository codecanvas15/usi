@extends('layouts.admin.layout.index')

@php
    $main = 'quotation';
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
                            <x-input class="datepicker-input" name="from_date" label="from date" value="" id="quotation-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="quotation-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-1 row align-self-end">
                        <div class="form-group">
                            <x-button type="submit" color="primary" id="set-quotation-table" size="sm" icon="search" fontawesome />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="quotationTable">
                    <x-slot name="table_head" id="quotationTable">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('tanggal') }}</th>
                        <th>{{ Str::upper('kode') }}</th>
                        <th>{{ Str::upper('total trading') }}</th>
                        <th>{{ Str::upper('total additional') }}</th>
                        <th>{{ Str::upper('Created At') }}</th>
                        <th>{{ Str::upper('Last Modified At') }}</th>
                        <th>{{ Str::upper('') }}</th>
                        <th></th>
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
            const quotationTable = () => {
                const table = $('table#quotationTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#from_date').val(),
                            to_date: $('#to_date').val(),
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
                            name: 'tanggal'
                        },
                        {
                            data: 'code',
                            name: 'kode'
                        },
                        {
                            data: 'total_main',
                            name: 'total_main'
                        },
                        {
                            data: 'total_add',
                            name: 'total_add'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
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
                $('table#quotationTable').css('width', '100%');

            }

            quotationTable();

            $('#set-quotation-table').click(function(e) {
                e.preventDefault();
                quotationTable()
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#quotation')
    </script>
@endsection
