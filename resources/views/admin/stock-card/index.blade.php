@extends('layouts.admin.layout.index')

@php
    $main = 'stock-card';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.css" />
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
    @php
        $tab_active = '';

        if (Auth::user()->hasPermissionTo('view stock-card-general')) {
            $tab_active = 'general';
        }
        if (!Auth::user()->hasPermissionTo('view stock-card-general') && Auth::user()->hasPermissionTo('view stock-card-trading')) {
            $tab_active = 'trading';
        }
    @endphp
    @can("view $main")
        <x-card-data-table title="{{ $main }}">
            <x-slot name="header_content">
                <div class="col-md-3">

                </div>
                <br>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    @can('view stock-card-general')
                        <li class="nav-item {{ $tab_active == 'general' ? 'active' : '' }}">
                            <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="general-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">General</span>
                            </a>
                        </li>
                    @endcan
                    @can('view stock-card-trading')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" id="trading-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Trading</span>
                            </a>
                        </li>
                    @endcan
                </ul>

                <div class="tab-content mt-30">
                    @can('view stock-card-general')
                        <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">
                            <div class="row mb-3 align-items-end">
                                <div class="col-lg-3">
                                    <x-select name="ware_house_id" id="general_ware_house_id" onchange="reinitTable()" label="Gudang" required>

                                    </x-select>
                                </div>
                                <div class="col-lg-3">
                                    <x-select name="item_id" id="general_item_id" onchange="reinitTable()" label="item" required>

                                    </x-select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" onchange="reinitTable()" value="" id="general-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" onchange="reinitTable()" value="" id="general-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-button type="submit" name="set_card" icon="search" color="primary" fontawesome id="set-stock-card" required />
                                    </div>
                                </div>
                            </div>

                            <x-table id="general-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Kode Barang') }}</th>
                                    <th>{{ Str::headline('Nama') }}</th>
                                    <th>{{ Str::headline('Unit') }}</th>
                                    <th>{{ Str::headline('Stock Minimum') }}</th>
                                    <th>{{ Str::headline('Stock') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                    @can('view stock-card-trading')
                        <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <x-select name="ware_house_id" id="trading_ware_house_id" onchange="reinitTable()" label="Gudang" required>

                                    </x-select>
                                </div>
                                <div class="col-lg-3">
                                    <x-select name="item_id" id="trading_item_id" onchange="reinitTable()" label="item" required>

                                    </x-select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" onchange="reinitTable()" label="from date" value="" id="trading-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" onchange="reinitTable()" label="to date" value="" id="trading-to-date" required />
                                    </div>
                                </div>
                            </div>

                            <x-table id="trading-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Kode Barang') }}</th>
                                    <th>{{ Str::headline('Nama') }}</th>
                                    <th>{{ Str::headline('Unit') }}</th>
                                    <th>{{ Str::headline('Stock Minimum') }}</th>
                                    <th>{{ Str::headline('Stock') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                </div>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        let [
            general,
            trading,
        ] = [
            false,
            false,
        ];

        const initGeneralTable = () => {
            initSelect2Search('general_ware_house_id', `{{ route('admin.select.ware-house') }}?type=general`, {
                id: "id",
                text: "nama"
            });
            inititemSelect('general_item_id', 'general', 'purchase item');
            const table = $('table#general-table').DataTable({
                bDestroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.$main.index") }}' + '?warehouse_id=' + $('#general_ware_house_id').val() + ($('#general_item_id').val() ? '&item_id=' + $('#general_item_id').val() : '') + '&type=general',
                    data: {
                        from_date: $('#general-from-date').val(),
                        to_date: $('#general-to-date').val(),
                        wherehouse_id: $('#general_ware_house_id').val(),
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'unit',
                        name: 'units.name'
                    },
                    {
                        data: 'minimum_stock',
                        name: 'item_minimums.qty',
                        className: 'text-end',
                    },
                    {
                        data: 'stock',
                        name: 'nama',
                        className: 'text-end'
                    }
                ]
            });
            if (!general) {
                $('table').css('width', '100%');
                general = true;
            }
            return;
        }
        $('#general-btn').click(function(e) {
            e.preventDefault();
            initGeneralTable();
        });

        $('#set-stock-card').click(function(e) {
            e.preventDefault()
            initGeneralTable()
        })

        {{ $tab_active == 'general' ? 'initGeneralTable()' : '' }}


        const initTradingTable = () => {
            initSelect2Search('trading_ware_house_id', `{{ route('admin.select.ware-house') }}?type=trading`, {
                id: "id",
                text: "nama"
            });
            inititemSelect('trading_item_id', 'trading');
            const table = $('table#trading-table').DataTable({
                bDestroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.$main.index") }}' + '?warehouse_id=' + $('#trading_ware_house_id').val() + '&item_id=' + $('#trading_item_id').val() + '&type=trading',
                    data: {
                        from_date: $('#trading-from-date').val(),
                        to: $('#trading-to-date').val(),
                        wherehouse_id: $('#trading_ware_house_id').val(),
                    }

                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'unit',
                        name: 'units.name'
                    },
                    {
                        data: 'minimum_stock',
                        name: 'item_minimums.qty',
                        className: 'text-end',
                    },
                    {
                        data: 'stock',
                        name: 'nama',
                        className: 'text-end'
                    }
                ]
            });
            if (!trading) {
                $('table').css('width', '100%');
                trading = true;
            }
            return;
        }
        $('#trading-btn').click(function(e) {
            e.preventDefault();
            initTradingTable();
        });
        {{ $tab_active == 'trading' ? 'initTradingTable()' : '' }}
    </script>
    <script>
        function reinitTable() {
            if (general) {
                initGeneralTable();
            }
            if (trading) {
                initTradingTable();
            }
        }
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#stock-card');
    </script>
@endsection
