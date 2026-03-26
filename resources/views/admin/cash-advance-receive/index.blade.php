@extends('layouts.admin.layout.index')

@php
    $main = 'cash-advance-receive';
    $menu = 'penerimaan deposit';
@endphp

@section('title', Str::headline($menu) . ' - ')

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
                        <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal awal" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="to_date" name="to" label="tanggal akhir" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                    </div>
                </div>
                <div class="col-md-3 row align-self-end">
                    <div class="form-group">
                        <x-button type="button" color="primary" id="set-service-table" icon="search" fontawesome onclick="table.ajax.reload()" />
                        <x-button color="info" icon="plus" label="Tambah" link='{{ route("admin.$main.create") }}' />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table>
                <x-slot name="table_head">
                    <th></th>
                    <th>#</th>
                    <th>{{ Str::headline('kode') }}</th>
                    <th>{{ Str::headline('tanggal') }}</th>
                    <th>{{ Str::headline('dari') }}</th>
                    <th>{{ Str::headline('project') }}</th>
                    <th>{{ Str::headline('referensi') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th>{{ Str::headline('action') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        var csrf = $('input[name="_token"]').val();

        const setTable = () => {
            initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
                'id': 'id',
                'text': 'name'
            });
            $("table").DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: `${base_url}/cash-advance-receive-datatable`,
                    type: "POST",
                    data: {
                        _token: csrf,
                        coa_id: function() {
                            return $("#coa_id").val();
                        },
                        from_date: function() {
                            return $("#from_date").val();
                        },
                        to_date: function() {
                            return $("#to_date").val();
                        },
                        branch_id: $('#branch-select').val()
                    },
                },
                columns: [{
                        data: "created_at",
                        name: "cash_advance_receives.created_at",
                        visible: false,
                        searchable: false,
                    },
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "code",
                        name: "bank_code_mutations.code",
                    },

                    {
                        data: "date",
                        name: "cash_advance_receives.date",
                    },
                    {
                        data: "customer_name",
                        name: "customers.nama",
                    },
                    {
                        data: "project_name",
                        name: "projects.name",
                    },
                    {
                        data: "reference",
                    },
                    {
                        data: "status",
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                    },
                ],
            });

            $("table").css("width", "100%");
        }

        $(document).ready(() => {
            setTable();
        });

        $('#set-service-table').click(function(e) {
            e.preventDefault();
            setTable();
        });

        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#{{ $main }}');

        // $('.table').dataTable();
    </script>
@endsection
