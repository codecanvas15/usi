@extends('layouts.admin.layout.index')

@php
    $main = 'cash-bond';
    $title = 'kasbon';
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
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            @can("create $main")
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" :link="route('admin.cash-bond.create')" />
                    </div>
                </div>
            @endcan
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $main")

                @if (get_current_branch()->is_primary)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" label="branch" id="branch-select" required>

                                </x-select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="employee" id="employee-select" required>

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="status" id="form-status" required>
                                <option value="" selected>----</option>
                                @foreach (cash_bond_status() as $key => $item)
                                    <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="_date" id="form-fromdate" value="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="_date" id="form-todate" value="" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="primary" id="datatable-init" icon="search" fontawesome size="sm" />
                        </div>
                    </div>

                    <x-table class="mt-10" id="datatable">
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Pegawai</th>
                            <th>Jumlah</th>
                            <th>Ket</th>
                            <th>Status</th>
                            <th>Dibuat Pada</th>
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
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>

    <script>
        $(document).ready(function() {

            const initTable = () => {
                @if (get_current_branch()->is_primary)
                    initSelect2Search('branch-select', "{{ route('admin.select.branch') }}", {
                        id: "id",
                        text: "name",
                    })
                @endif

                initSelectEmployee('#employee-select');

                let data = {
                    branch_id: $('#branch-select').val() ?? null,
                    employee_id: $('#employee-select').val() ?? null,
                    from_date: $('#form-fromdate').val(),
                    to_date: $('#form-todate').val(),
                    status: $('#form-status').val(),
                };

                const table = $('table#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    order: [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: '{{ route('admin.cash-bond.index') }}',
                        data: data
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'id',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'employee_id',
                            name: 'employee_id'
                        },
                        {
                            data: 'credit',
                            name: 'cash_bond_details.credit'
                        },
                        {
                            data: 'description',
                            name: 'description'
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
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table#datatable').css('width', '100%');
            };

            initTable();

            $('#datatable-init').click(function(e) {
                e.preventDefault();
                console.log('==============================');
                console.log('init datatable');
                console.log('==============================');
                initTable();
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#cash-bond-sidebar');
        sidebarActive('#cash-bond')
    </script>
@endsection
