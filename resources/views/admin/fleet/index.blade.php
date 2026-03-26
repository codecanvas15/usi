@extends('layouts.admin.layout.index')

@php
    $main = 'fleet';
@endphp

@section('title', Str::headline('Armada') . ' - ')

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
                        {{ Str::headline('Armada') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="Armada">
            <x-slot name="header_content">
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        </div>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#darat-tab" id="darat-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Darat</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded " data-bs-toggle="tab" href="#laut-tab" id="laut-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Laut</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-30">

                    <div class="tab-pane active" id="darat-tab" role="tabpanel">
                        @if (get_current_branch()->is_primary)
                            <div class="row mb-15">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select id="darat-branch-select" label="branch">

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" id="set-darat-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                        @endif
                        <x-table id="darat-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('Nama') }}</th>
                                <th>{{ Str::headline('Merk') }}</th>
                                <th>{{ Str::headline('kapasitas') }}</th>
                                <th>{{ Str::headline('tahun_pembuatan') }}</th>
                                <th>{{ Str::headline('type') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </div>

                    <div class="tab-pane" id="laut-tab" role="tabpanel">
                        @if (get_current_branch()->is_primary)
                            <div class="row mb-15">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select id="laut-branch-select" label="branch">

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" id="set-laut-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                        @endif
                        <x-table id="laut-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('Nama') }}</th>
                                <th>{{ Str::headline('Merk') }}</th>
                                <th>{{ Str::headline('Kuantitas') }}</th>
                                <th>{{ Str::headline('tahun_pembuatan') }}</th>
                                <th>{{ Str::headline('nomor_lambung') }}</th>
                                <th>{{ Str::headline('panjang') }}</th>
                                <th>{{ Str::headline('lebar') }}</th>
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </div>

                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    @can("view $main")
        <script>
            const setDaratTable = () => {
                initSelect2Search('darat-branch-select', '{{ route('admin.select.branch') }}', {
                    'id': 'id',
                    'text': 'name'
                });
                $('table#darat-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.get-data-by-type") }}/darat',
                        data: {
                            branch_id: $('#darat-branch-select').val()
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'merk',
                            name: 'merk'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'year',
                            name: 'year'
                        },
                        {
                            data: 'vechicle_fleet.type',
                            name: 'vechicle_fleet.type'
                        },
                        {
                            name: "status",
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
            }

            const setLautTable = () => {
                initSelect2Search('laut-branch-select', '{{ route('admin.select.branch') }}', {
                    'id': 'id',
                    'text': 'name'
                });
                $('table#laut-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.get-data-by-type") }}/laut',
                        data: {
                            branch_id: $('#laut-branch-select').val()
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'merk',
                            name: 'merk'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'year',
                            name: 'year'
                        },
                        {
                            data: 'marine_fleet.nomor_lambung',
                            name: 'marine_fleet.nomor_lambung'
                        },
                        {
                            data: 'marine_fleet.panjang',
                            name: 'marine_fleet.panjang'
                        },
                        {
                            data: 'marine_fleet.lebar',
                            name: 'marine_fleet.lebar'
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
            }

            $(document).ready(() => {
                setDaratTable();
                setLautTable();
                $('table').css('width', '100%');
            });

            $('#set-darat-table').click(function(e) {
                e.preventDefault();
                setDaratTable();
            });

            $('#set-laut-table').click(function(e) {
                e.preventDefault();
                setLautTable();
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-garage-sidebar');
        sidebarActive('#fleet')
    </script>
@endsection
