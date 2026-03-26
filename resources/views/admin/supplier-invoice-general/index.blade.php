@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice-general';
    $title = 'Purchase Invoice (Non LPB)';
@endphp

@section('title', Str::headline($title) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        .form-group {
            margin-bottom: 0px !important;
        }
    </style>
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
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            <div class="row mb-4">
                @if (get_current_branch()->is_primary)
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-select id="branch-select" label="branch">

                            </x-select>
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="from-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="to-date" required />
                    </div>
                </div>

                <div class="col-md-1 align-self-end">
                    <x-button type="button" color="info" id="set-table" icon="search" fontawesome />
                </div>
                <div class="col-md-3 col-md-6 col-xl-4 align-self-end">
                    <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table id="supp-inv-gen">
                <x-slot name="table_head">
                    <th></th>
                    <th>#</th>
                    <th>{{ Str::headline('Kode') }}</th>
                    <th>{{ Str::headline('Vendor') }}</th>
                    <th>{{ Str::headline('Tanggal') }}</th>
                    <th>{{ Str::headline('No. Faktur') }}</th>
                    {{-- <th>{{ Str::headline('Debit') }}</th> --}}
                    {{-- <th>{{ Str::headline('Credit') }}</th> --}}
                    <th>{{ Str::headline('Status') }}</th>
                    <th>{{ Str::headline('Aksi') }}</th>
                </x-slot>
                <x-slot name="table_body"></x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        const setTable = () => {
            initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
                'id': 'id',
                'text': 'name'
            });
            $('table#supp-inv-gen').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: '{{ route("admin.$main.index") }}',
                    data: {
                        branch_id: $('#branch-select').val(),
                        from_date: $('#from-date').val(),
                        to_date: $('#to-date').val()
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "code",
                    },
                    {
                        "data": "vendor",
                        "name": "vendors.nama"
                    },
                    {
                        "data": "date",
                    },
                    {
                        "data": "reference",
                    },
                    // {
                    //     "data": "debit",
                    // },
                    // {
                    //     "data": "credit",
                    // },
                    {
                        "data": "status",
                    },
                    {
                        "data": "action"
                    },
                ]
            });
            $('table').css('width', '100%');
        }

        $(document).ready(function() {
            setTable();
        });

        $('#set-table').click(function(e) {
            e.preventDefault();
            setTable();
        });

        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#supplier-invoice-sidebar');
        sidebarActive('#supplier-invoice-general');
    </script>
@endsection
