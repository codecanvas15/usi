@extends('layouts.admin.layout.index')

@php
    $main = 'cash-advance-return';
    $title = 'pengembalian uang muka';
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
                        {{ Str::headline("$title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can(["view $main"])
        <x-card-data-table title='{{ "$title customer" }}'>
            <x-slot name="header_content">
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" link="{{ route('admin.cash-advance-return-customer.create') }}" />
                    </div>
                </div>

                @if (get_current_branch()->is_primary)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" label="branch" id="customer-branch" required>

                                </x-select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="customer" id="customer-select" required>

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-select name="status" id="customer-status" required>
                                <option value="" selected>----</option>
                                @foreach (cash_advance_return() as $key => $item)
                                    <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="_date" id="customer-fromdate" value="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="_date" id="customer-todate" value="" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-button color="primary" id="customer-init" icon="search" fontawesome />
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="table_content">

                <x-table id="customer-table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('tanggal') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('customer') }}</th>
                        <th>{{ Str::headline('total') }}</th>
                        <th>{{ Str::headline('status') }}</th>
                        <th>{{ Str::headline('dibuat pada') }}</th>
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
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script>
            $(document).ready(() => {

                const customerTable = () => {
                    @if (get_current_branch()->is_primary)
                        initSelect2Search('customer-branch', "{{ route('admin.select.branch') }}", {
                            id: "id",
                            text: "name",
                        })
                    @endif

                    initSelect2Search('customer-select', "{{ route('admin.select.customer') }}", {
                        id: "id",
                        text: "nama"
                    })

                    let data = {
                        branch_id: $('#customer-branch').val() ?? null,
                        from_date: $('#customer-fromdate').val(),
                        to_date: $('#customer-todate').val(),
                        status: $('#customer-status').val(),
                        reference_id: $('#customer-select').val(),
                    };

                    const table = $('table#customer-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.cash-advance-return-customer.index') }}',
                            data: data
                        },
                        order: [
                            [6, 'desc']
                        ],
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
                                data: 'reference_id',
                                name: 'reference_id'
                            },
                            {
                                data: 'total',
                                name: 'total'
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
                            }
                        ]
                    });
                    $('table#customer-table').css('width', '100%');
                };

                customerTable();

                $('#customer-init').click(function(e) {
                    e.preventDefault();
                    customerTable();
                });
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#incoming-payment-sidebar');
        sidebarActive('#cash-advance-return-customer');
    </script>
@endsection
