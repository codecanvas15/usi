@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request';
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
    @php
        $tab_active = '';

        if (Auth::user()->hasPermissionTo('view purchase-request-general')) {
            $tab_active = 'general';
        }
        if (!Auth::user()->hasPermissionTo('view purchase-request-general') && !Auth::user()->hasPermissionTo('view purchase-request-trading') && !Auth::user()->hasPermissionTo('view purchase-request-service')) {
            $tab_active = 'service';
        }
        if (Auth::user()->hasPermissionTo('view purchase-request-trading') && !Auth::user()->hasPermissionTo('view purchase-request-service') && !Auth::user()->hasPermissionTo('view purchase-request-general')) {
            $tab_active = 'trading';
        }
    @endphp
    @canany(['view purchase-request', 'view purchase-request-service', 'view purchase-request-general', 'view purchase-request-trading'])
        <x-card-data-table title="{{ $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')

                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    @can('view purchase-request-general')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="general-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                <span class="hidden-xs-down">General</span>
                            </a>
                        </li>
                    @endcan
                    @can('view purchase-request-service')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'service' ? 'active' : '' }}" data-bs-toggle="tab" href="#service-tab" id="service-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Service</span>
                            </a>
                        </li>
                    @endcan
                    @can('view purchase-request-trading')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" id="trading-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Trading</span>
                            </a>
                        </li>
                    @endcan
                </ul>

                <div class="tab-content mt-30">
                    @can('view purchase-request-service')
                        <div class="tab-pane {{ $tab_active == 'service' ? 'active' : '' }}" id="service-tab" role="tabpanel">
                            <div class="row justify-content-between mb-4">
                                <div class="col-md-3 col-md-6 col-xl-4">
                                    @canany(['create purchase-request', 'create purchase-request-general'])
                                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}?type=service' />
                                    @endcan
                                </div>
                            </div>

                            <div class="row">
                                @if (get_current_branch()->is_primary)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="customer" id="service-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-15">
                                @can('view purchase-request-service')
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="division_id" label="division" id="service-division">
                                                <option value="">Pilih item</option>
                                            </x-select>
                                        </div>
                                    </div>
                                @endcan
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="status" id="service-status">
                                            <option value="">Pilih item</option>
                                            @foreach (item_report_status() as $key => $item)
                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="project_id" id="service-project">
                                            <option value="">Pilih item</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="service-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" value="" id="service-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-3 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" id="set-service-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="service-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('Project') }}</th>
                                    <th>{{ Str::headline('Created At') }}</th>
                                    <th>{{ Str::headline('created by') }}</th>
                                    <th>{{ Str::headline('') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                    @can('view purchase-request-general')
                        <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">

                            <div class="row justify-content-between mb-4">
                                <div class="col-md-3 col-md-6 col-xl-4">
                                    @canany(['create purchase-request', 'create purchase-request-service'])
                                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}?type=general' />
                                    @endcan
                                </div>
                            </div>

                            <div class="row">
                                @if (get_current_branch()->is_primary)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="customer" id="general-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-15">
                                @can('view-all purchase-request-general')
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="division_id" label="division" id="general-division">
                                                <option value="">Pilih item</option>
                                            </x-select>
                                        </div>
                                    </div>
                                @endcan
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="status" id="general-status">
                                            <option value="">Pilih item</option>
                                            @foreach (purchase_request_status() as $key => $item)
                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="project_id" id="general-project">
                                            <option value="">Pilih item</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="general-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" value="" id="general-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" id="set-general-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="general-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('Created At') }}</th>
                                    <th>{{ Str::headline('Created By') }}</th>
                                    <th>{{ Str::headline('Project') }}</th>
                                    <th>{{ Str::headline('') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                    @can('view purchase-request-trading')
                        <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">
                            <div class="row justify-content-between mb-4">
                                <div class="col-md-3 col-md-6 col-xl-4">
                                    @can('create purchase-request-trading')
                                        <x-button color="info" icon="plus" label="Create" link="{{ route('admin.purchase-request-trading.create') }}" />
                                    @endcan
                                </div>
                            </div>

                            <div class="row mb-15">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="status" id="trading-status">
                                            <option value="">Pilih item</option>
                                            @foreach (purchase_request_status() as $key => $item)
                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="trading-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" value="" id="trading-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" id="set-trading-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="trading-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('customer') }}</th>
                                    <th>{{ Str::headline('status') }}</th>
                                    <th>{{ Str::headline('dibuat oleh') }}</th>
                                    <th>{{ Str::headline('') }}</th>
                                    <th>{{ Str::headline('') }}</th>
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
    @canany(['view purchase-request', 'view purchase-request-service', 'view purchase-request-general', 'view purchase-request-transport'])
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>

        <script>
            $(document).ready(() => {

                @can('view purchase-request-general')
                    const initTableGeneral = () => {
                        const table = $('table#general-table').DataTable({
                            processing: true,
                            serverSide: true,
                            responsive: true,
                            destroy: true,
                            order: [4, 'desc'],
                            ajax: {
                                url: '{{ route("admin.$main.data") }}/general',
                                data: {
                                    from_date: $('#general-from-date').val(),
                                    to_date: $('#general-to-date').val(),
                                    status: $('#general-status').val(),
                                    division_id: $('#general-division').val(),
                                    branch_id: $('#general-branch-select').val(),
                                    project_id: $('#general-project').val(),
                                },
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
                                    data: 'tanggal',
                                    name: 'tanggal'
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
                                    data: 'user',
                                    name: 'created_by_user.name'
                                },
                                {
                                    data: 'project',
                                    name: 'project'
                                },
                                {
                                    data: 'button',
                                    name: 'button'
                                }
                            ]
                        });
                        $('table').css('width', '100%');

                        initSelect2SearchPaginationData(`general-division`, `{{ route('admin.select.division') }}`, {
                            id: 'id',
                            text: 'name'
                        })

                        initSelect2SearchPaginationData(`general-project`, `{{ route('admin.select.project') }}`, {
                            id: 'id',
                            text: 'name'
                        })

                        initSelect2SearchPaginationData(`general-branch-select`, `{{ route('admin.select.branch') }}`, {
                            id: 'id',
                            text: 'name'
                        })
                    }

                    $('#general-btn').click(function(e) {
                        e.preventDefault();
                        initTableGeneral();
                    });

                    $('#set-general-table').click(function(e) {
                        e.preventDefault();
                        initTableGeneral();
                    });

                    {{ $tab_active == 'general' ? 'initTableGeneral()' : '' }}
                @endcan
                @can('view purchase-request-service')
                    const initTableService = () => {
                        const table = $('table#service-table').DataTable({
                            processing: true,
                            serverSide: true,
                            responsive: true,
                            destroy: true,
                            order: [4, 'desc'],
                            ajax: {
                                url: '{{ route("admin.$main.data") }}/service',
                                data: {
                                    from_date: $('#service-from-date').val(),
                                    to_date: $('#service-to-date').val(),
                                    status: $('#service-status').val(),
                                    division_id: $('#service-division').val(),
                                    branch_id: $('#service-branch-select').val(),
                                    project_id: $('#service-project').val(),
                                },
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
                                    data: 'tanggal',
                                    name: 'tanggal'
                                },
                                {
                                    data: 'status',
                                    name: 'status'
                                },
                                {
                                    data: 'project',
                                    name: 'project'
                                },
                                {
                                    data: 'created_at',
                                    name: 'created_at'
                                },
                                {
                                    data: 'user',
                                    name: 'created_by_user.name'
                                },
                                {
                                    data: 'button',
                                    name: 'button'
                                }
                            ]
                        });
                        $('table').css('width', '100%');

                        initSelect2SearchPaginationData(`service-division`, `{{ route('admin.select.division') }}`, {
                            id: 'id',
                            text: 'name'
                        })

                        initSelect2SearchPaginationData(`service-project`, `{{ route('admin.select.project') }}`, {
                            id: 'id',
                            text: 'name'
                        })

                        initSelect2SearchPaginationData(`service-branch-select`, `{{ route('admin.select.branch') }}`, {
                            id: 'id',
                            text: 'name'
                        })
                    }

                    $('#service-btn').click(function(e) {
                        e.preventDefault();
                        initTableService();
                    });

                    $('#set-service-table').click(function(e) {
                        e.preventDefault();
                        initTableService();
                    });

                    {{ $tab_active == 'service' ? 'initTableService()' : '' }}
                @endcan
                @can('view purchase-request-trading')
                    const initTableTrading = () => {
                        const table = $('table#trading-table').DataTable({
                            processing: true,
                            serverSide: true,
                            responsive: true,
                            destroy: true,
                            order: [4, 'desc'],
                            ajax: {
                                url: '{{ route('admin.purchase-request-trading.index') }}',
                                data: {
                                    from_date: $('#trading-from-date').val(),
                                    to_date: $('#trading-to-date').val(),
                                    status: $('#trading-status').val(),
                                },
                            },
                            columns: [{
                                    data: 'DT_RowIndex',
                                    name: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'code',
                                    name: 'purchase_request_tradings.code'
                                },
                                {
                                    data: 'date',
                                    name: 'purchase_request_tradings.date'
                                },
                                {
                                    data: 'customer_name',
                                    name: 'customers.nama'
                                },
                                {
                                    data: 'status',
                                    name: 'purchase_request_tradings.status'
                                },
                                {
                                    data: 'created_by_name',
                                    name: 'users.name'
                                },
                                {
                                    data: 'export',
                                    name: 'export',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'action',
                                    name: 'action',
                                    orderable: false,
                                    searchable: false,
                                }
                            ]
                        });
                        $('table').css('width', '100%');
                    }

                    $('#trading-btn').click(function(e) {
                        e.preventDefault();
                        initTableTrading();
                    });

                    $('#set-trading-table').click(function(e) {
                        e.preventDefault();
                        initTableTrading();
                    });

                    {{ $tab_active == 'trading' ? 'initTableTrading()' : '' }}
                @endcan
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase-request')
    </script>
@endsection
