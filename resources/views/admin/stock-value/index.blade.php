@extends('layouts.admin.layout.index')

@php
    $main = 'stock-value';
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
    @can('view stock-card-value')
        <x-card-data-table title="{{ $main }}">
            <x-slot name="header_content">
                <div class="row">
                    <div class="col-md-3">
                        <x-select name="ware_house_id" id="ware_house_id" onchange="initTable()" label="Gudang" required>

                        </x-select>
                    </div>
                    <div class="col-lg-3">
                        <x-select name="item_id" id="item_id" onchange="initTable()" label="item" required>

                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" class="datepicker-input" name="from_date" label="from date" value="" id="from_date" required value="" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input type="text" class="datepicker-input" name="to" label="to date" value="" id="to_date" required />
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group">
                            <x-button color="primary" size="sm" onclick="initTable()" icon="search" size="sm" fontawesome />
                        </div>
                    </div>
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
                        <th>{{ Str::headline('Kode Item') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
                        <th>{{ Str::headline('Keterangan') }}</th>
                        <th>{{ Str::headline('kode dokumen') }}</th>
                        <th>{{ Str::headline('Satuan') }}</th>
                        <th>{{ Str::headline('Stock Awal') }}</th>
                        <th>{{ Str::headline('Masuk') }}</th>
                        <th>{{ Str::headline('Keluar') }}</th>
                        <th>{{ Str::headline('Nilai') }}</th>
                        <th>{{ Str::headline('SubTotal') }}</th>
                        <th>{{ Str::headline('Total') }}</th>
                        <th>{{ Str::headline('Stock Akhir') }}</th>
                        <th>{{ Str::headline('Nilai Akhir') }}</th>
                        {{-- <th>{{ Str::headline('Supp./Cust.') }}</th> --}}
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    @can('view stock-card-value')
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script>
            initSelect2Search('ware_house_id', `{{ route('admin.select.ware-house') }}`, {
                id: "id",
                text: "nama"
            });

            inititemSelect('item_id', 'all', 'purchase item,');
        </script>
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            // $(document).ready(() => {
            const initTable = () => {
                const table = $('#table').DataTable({
                    bDestroy: true,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("admin.$main.index") }}' + '?warehouse_id=' + $('#ware_house_id').val() + '&item_id=' + $('#item_id').val() + '&from_date=' + $('#from_date').val() + '&to_date=' + $('#to_date').val(),
                    order: [
                        [1, 'asc']
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
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'item_code',
                            name: 'item_code'
                        },
                        {
                            data: 'nama',
                            name: 'items.nama'
                        },
                        {
                            data: 'note',
                            name: 'note'
                        },
                        {
                            data: 'document_code',
                            name: 'document_code'
                        },
                        {
                            data: 'unit_name',
                            name: 'units.name'
                        },
                        {
                            data: 'stock_before',
                            name: 'in',
                            className: 'text-end',
                        },
                        {
                            data: 'in',
                            name: 'in',
                            className: 'text-end',
                        },
                        {
                            data: 'out',
                            name: 'out',
                            className: 'text-end',
                        },
                        {
                            "data": "price_unit",
                            name: 'price_unit'
                        },
                        {
                            "data": "subtotal",
                            name: 'subtotal'
                        },
                        {
                            "data": "total",
                            name: 'total'
                        },
                        {
                            data: 'left',
                            name: 'id',
                            className: 'text-end',
                        },
                        {
                            "data": "value",
                            name: 'value'
                        },
                    ],
                    "drawCallback": function(settings) {
                        var api = this.api();
                        var rows = api.rows({
                            page: 'current'
                        }).nodes();
                        var last = null;
                        var subTotal = new Array();
                        var groupID = -1;
                        var aData = new Array();
                        var index = 0;

                        api.column(3, {
                            page: 'current'
                        }).data().each(function(group, i) {
                            var vals = api.row(api.row($(rows).eq(i)).index()).data();
                            var total = vals.total ? numberWithCommas(vals.total) : 0;

                            if (typeof aData[group] == 'undefined') {
                                aData[group] = new Array();
                                aData[group].rows = [];
                                aData[group].total = [];
                            }

                            aData[group].rows.push(i);
                            aData[group].total.push(total);
                        });
                        var idx = 0;

                        for (var item in aData) {
                            idx = Math.max.apply(Math, aData[item].rows);

                            var sum = 0;
                            $.each(aData[item].total, function(k, v) {
                                sum = v;
                            });

                            $(rows).eq(idx).after(
                                '<tr class="group"><td colspan="14">Jumlah</td>' +
                                '<td>' + sum + '</td></tr>'
                            );
                        }
                    }
                });
            }
            initTable();
        </script>
    @endcan
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#stock-value');
    </script>
@endsection
