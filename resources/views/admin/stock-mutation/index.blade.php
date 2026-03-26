@extends('layouts.admin.layout.index')

@php
    $main = 'stock-mutation';
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
                <div class="row">
                    <div class="col-md-3">
                        <x-select name="ware_house_id" id="ware_house_id" onchange="initTable()" label="Gudang" required>

                        </x-select>
                    </div>
                    <div class="col-lg-3">
                        <x-select name="item_id" id="general_item_id" onchange="initTable()" label="item" required>

                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" class="datepicker-input" name="from_date" label="from date" value="" id="general-formDate-inputForm" required value="" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" class="datepicker-input" name="to" label="to date" value="" id="general-toDate-inputForm" required />
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group">
                            <x-button color="primary" size="sm" onclick="initTable()" icon="search" size="sm" fontawesome />
                        </div>
                    </div>
                    @can('refresh stock-mutation')
                        <div class="col-md-12">
                            <x-button color="info" icon="plus" fontawesome dataToggle="modal" label="Refresh Stock" dataTarget="#refresh-stock-modal" />
                            @role('super_admin')
                                <x-button color="info" icon="plus" fontawesome dataToggle="modal" label="Weekly Refresh Stock" dataTarget="#weekly-refresh-stock-modal" />
                            @endrole
                            @if ($refresh_log)
                                <br>
                                <small class="text-danger" data-bs-toggle="modal" data-bs-target="#refresh-history-modal">Terakhir di refresh : {{ Carbon\Carbon::parse($refresh_log->created_at)->diffForHumans() }} {{ $refresh_log->user ? 'oleh ' . $refresh_log->user->name : 'otomatis by sistem' }}</small>

                                <x-modal title="refresh history" id="refresh-history-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <x-table id="refresh-log-table" class="w-100">
                                            <x-slot name="table_head">

                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </x-slot>
                                </x-modal>
                            @endif
                            <x-modal title="refresh stock" id="refresh-stock-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    <form action="{{ route('admin.stock-mutation.refresh') }}" method="post">
                                        @csrf
                                        <div class="mt-10">
                                            <div class="form-group">
                                                <x-input type="text" id="period" label="period" name="period" class="month-year-picker-input" />
                                                <span class="text-danger">Jika periode tidak dipilih, maka semua periode yang belum closing akan di refresh</span>
                                            </div>
                                        </div>
                                        <div class="mt-10 border-top pt-10">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                            <x-button type="submit" color="primary" label="refresh" size="sm" icon="refresh" fontawesome />
                                        </div>
                                    </form>
                                </x-slot>
                            </x-modal>
                            <x-modal title="weekly refresh stock" id="weekly-refresh-stock-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    <form action="{{ route('admin.stock-mutation.weekly-refresh') }}" method="post">
                                        @csrf
                                        <div class="mt-10 border-top pt-10">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                            <x-button type="submit" color="primary" label="refresh" size="sm" icon="refresh" fontawesome />
                                        </div>
                                    </form>
                                </x-slot>
                            </x-modal>
                        </div>
                    @endcan
                </div>
                <br>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Tanggal Dok.') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Kode Barang') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
                        <th>{{ Str::headline('Satuan') }}</th>
                        <th>{{ Str::headline('Stock Awal') }}</th>
                        <th>{{ Str::headline('Masuk') }}</th>
                        <th>{{ Str::headline('Keluar') }}</th>
                        <th>{{ Str::headline('Stock Akhir') }}</th>
                        {{-- <th>{{ Str::headline('Supp./Cust.') }}</th> --}}
                        <th>{{ Str::headline('kode dokumen') }}</th>
                        <th>{{ Str::headline('Keterangan') }}</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script>
        initSelect2Search('ware_house_id', `{{ route('admin.select.ware-house') }}`, {
            id: "id",
            text: "nama"
        });

        inititemSelect('general_item_id', 'all', 'purchase item,');
    </script>
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        const initTable = () => {
            const table = $('#table').DataTable({
                bDestroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.$main.index") }}',
                    data: {
                        warehouse_id: $('#ware_house_id').val(),
                        item_id: $('#general_item_id').val(),
                        from_date: $('#general-formDate-inputForm').val(),
                        to_date: $('#general-toDate-inputForm').val(),
                    },
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'kode',
                        name: 'items.kode',
                        orderable: false,
                    },
                    {
                        data: 'nama',
                        name: 'items.nama',
                        orderable: false,
                    },
                    {
                        data: 'unit_name',
                        name: 'units.name',
                        orderable: false,
                    },
                    {
                        data: 'stock_before',
                        className: 'text-end',
                        searchable: 'stock_mutations.id',
                        orderable: false,
                    },
                    {
                        data: 'in',
                        className: 'text-end',
                        searchable: 'stock_mutations.id',
                        orderable: false,
                    },
                    {
                        data: 'out',
                        className: 'text-end',
                        searchable: 'stock_mutations.id',
                        orderable: false,
                    },
                    {
                        data: 'left',
                        className: 'text-end',
                        searchable: 'stock_mutations.id',
                        orderable: false,
                    },
                    {
                        data: 'document_code',
                        name: 'document_code',
                        orderable: false,
                    },
                    {
                        data: 'note',
                        name: 'note',
                        orderable: false,
                    },
                ]
            });

            const refresh_log_table = $('#refresh-log-table').DataTable({
                bDestroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '{{ route("admin.$main.refresh-stock-log") }}',
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'period',
                        name: 'period'
                    },
                    {
                        data: 'user_name',
                        name: 'users.name'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    },
                ]
            });

            $('#refresh-log-table').style('width', '100%')
        }
        initTable();
    </script>
    <script>
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#stock-mutation');
    </script>
@endsection
