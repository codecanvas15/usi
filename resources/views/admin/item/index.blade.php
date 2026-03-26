@extends('layouts.admin.layout.index')

@php
    $main = 'item';
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
            <x-slot name="table_content">

                @can("create $main")
                    <div>
                        <x-button color="info" icon="download" label="import saldo awal" class="mb-10" link='{{ route("admin.$main.beginning-balance.index") }}' />
                        <x-button color="info" icon="download" label="import item" class="mb-10" link='{{ route("admin.$main.view-import") }}' />
                        <x-button color="info" icon="download" label="export item" class="mb-10 export-item-excel" link='#' />
                    </div>
                @endcan

                <ul class="nav nav-tabs customtab2 my-10" role="tablist">
                    @foreach (['general', 'trading', 'service', 'transport'] as $item)
                        <li class="nav-item ">
                            <a class="nav-link rounded {{ $loop->index == 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#{{ $item }}-tab" id="{{ $item }}-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                <span class="hidden-xs-down">{{ Str::headline($item) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content mt-30">
                    @foreach (['general', 'trading', 'service', 'transport'] as $item)
                        <div class="tab-pane {{ $loop->index == 0 ? 'active' : '' }}" id="{{ $item }}-tab" role="tabpanel">
                            @can("create $main")
                                <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}?type={{ $item }}' />
                            @endcan
                            <x-table id="table-{{ $item }}">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('kode') }}</th>
                                    <th>{{ Str::headline('Nama') }}</th>
                                    <th>{{ Str::headline('Item Category') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('Last Modified At') }}</th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endforeach
                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {

                let [general, trading, servive, transport] = [false, false, false, false];

                const initDataTable = (type) => {
                    const url = '{{ route("admin.$main.export-item-excel", ['type' => 'typeValue']) }}'.replace("typeValue", type)
                    $('.export-item-excel').attr('href', url)

                    if (type == 'general') {
                        if (general) {
                            return;
                        }
                        general = true;
                    } else if (type == 'trading') {
                        if (trading) {
                            return;
                        }
                        trading = true;
                    } else if (type == 'service') {
                        if (servive) {
                            return;
                        }
                        servive = true;
                    } else if (type == 'transport') {
                        if (transport) {
                            return;
                        }
                        transport = true;
                    }

                    const table = $(`table#table-${type}`).DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: `{{ route("admin.$main.type") }}/${type}`,
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
                                data: 'item_category.nama',
                                name: 'item_category.nama'
                            },
                            {
                                data: 'status',
                                name: 'status'
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
                }

                $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                    const type = $(e.target).attr('id').split('-')[0];
                    initDataTable(type);
                });

                initDataTable('general');
                $('#general-btn').click(function(e) {
                    e.preventDefault();
                    initDataTable('general');
                });
                $('#trading-btn').click(function(e) {
                    e.preventDefault();
                    initDataTable('trading');
                });
                $('#service-btn').click(function(e) {
                    e.preventDefault();
                    initDataTable('service');
                });
                $('#transport-btn').click(function(e) {
                    e.preventDefault();
                    initDataTable('transport');
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item')
    </script>
@endsection
