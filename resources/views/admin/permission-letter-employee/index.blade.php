@extends('layouts.admin.layout.index')

@php
    $main = 'permission-letter-employee';
    $title = 'surat izin pegawai';
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
    @php
        $tab_active = '';
    @endphp
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            @can("create $main")
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' id="btn-create" />
                    </div>
                </div>
            @endcan
        </x-slot>
        <x-slot name="table_content">

            <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                <li class="nav-item">
                    <a class="nav-link rounded active" data-bs-toggle="tab" href="#permission-late" id="permission-late-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                        <span class="hidden-xs-down">Izin Terlambat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded" data-bs-toggle="tab" href="#permission-home" id="permission-home-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                        <span class="hidden-xs-down">Izin Pulang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded" data-bs-toggle="tab" href="#permission-outside-office" id="permission-outside-office-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                        <span class="hidden-xs-down">Izin Keluar Kantor</span>
                    </a>
                </li>
            </ul>

            @include('components.validate-error')
            <div class="tab-content mt-30">

                <div class="tab-pane" id="late-tab" role="tabpanel">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="dari" id="late-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="sampai" id="late-to-date" required />
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="primary" icon="search" fontawesome size="sm" id="btn-late" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-table id="late-table">
                        <x-slot name="table_head">
                            <th>{{ Str::headline('#') }}</th>
                            <th>{{ Str::headline(Str::headline('letter number')) }}</th>
                            <th>{{ Str::headline('pegawai') }}</th>
                            <th>{{ Str::headline(Str::headline('status')) }}</th>
                            <th>{{ Str::headline('dibuat pada') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </div>
                <div class="tab-pane" id="home-tab" role="tabpanel">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="dari" id="home-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="sampai" id="home-to-date" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="primary" icon="search" fontawesome size="sm" id="btn-home" />
                        </div>
                    </div>
                    <x-table id="home-table">
                        <x-slot name="table_head">
                            <th>{{ Str::headline('#') }}</th>
                            <th>{{ Str::headline(Str::headline('letter number')) }}</th>
                            <th>{{ Str::headline('pegawai') }}</th>
                            <th>{{ Str::headline(Str::headline('status')) }}</th>
                            <th>{{ Str::headline('dibuat pada') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </div>

                <div class="tab-pane" id="outside-office-tab" role="tabpanel">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="dari" id="outside-office-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="sampai" id="outside-office-to-date" required />
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="primary" icon="search" fontawesome size="sm" id="btn-outside-office" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-table id="outside-office-table">
                        <x-slot name="table_head">
                            <th>{{ Str::headline('#') }}</th>
                            <th>{{ Str::headline(Str::headline('letter number')) }}</th>
                            <th>{{ Str::headline('pegawai') }}</th>
                            <th>{{ Str::headline(Str::headline('status')) }}</th>
                            <th>{{ Str::headline('dibuat pada') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </div>
            </div>

        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>

        <script src="{{ asset('js/admin/select/employee.js') }}"></script>
        <script src="{{ asset('js/admin/select/branch.js') }}"></script>
        <script>
            $(document).ready(() => {

                initSelectEmployee('#employee-select')
                initBranchSelect('#branch-select')

                var tableLate = ''
                var tableHome = ''
                var tableNotCome = ''
                var tableOutsideOffice = ''

                const initTableLate = () => {
                    tableLate = $('table#late-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                type: 'came too late',
                                from_date: $('#late-from-date').val(),
                                to_date: $('#late-to-date').val(),
                            },
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'letter_number',
                                name: 'letter_number'
                            },
                            {
                                data: 'employee.name',
                                name: 'employee.name'
                            },
                            {
                                data: 'letter_status',
                                name: 'letter_status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'export',
                                name: 'export',
                                searchable: false,
                                orderable: false,
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                }

                const initTableHome = () => {
                    tableHome = $('table#home-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                type: 'leave early',
                                from_date: $('#home-from-date').val(),
                                to_date: $('#home-to-date').val(),
                            },
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'letter_number',
                                name: 'letter_number'
                            },
                            {
                                data: 'employee.name',
                                name: 'employee.name'
                            },
                            {
                                data: 'letter_status',
                                name: 'letter_status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'export',
                                name: 'export',
                                searchable: false,
                                orderable: false,
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                }

                const initTableOutsideOffice = () => {
                    tableOutsideOffice = $('table#outside-office-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                type: 'leave during working hours',
                                from_date: $('#outside-office-from-date').val(),
                                to_date: $('#outside-office-to-date').val(),
                            },
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'letter_number',
                                name: 'letter_number'
                            },
                            {
                                data: 'employee.name',
                                name: 'employee.name'
                            },
                            {
                                data: 'letter_status',
                                name: 'letter_status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'export',
                                name: 'export',
                                searchable: false,
                                orderable: false,
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                }

                initTableLate()
                activeTab('late-tab')

                $('#permission-late-btn').click(function(e) {
                    e.preventDefault();
                    if (tableLate != '')
                        tableLate.destroy()
                    initTableLate();
                    activeTab('late-tab')
                });

                $('#permission-home-btn').click(function(e) {
                    e.preventDefault();
                    if (tableHome != '')
                        tableHome.destroy()
                    initTableHome();
                    activeTab('home-tab')
                });

                $('#permission-come-btn').click(function(e) {
                    e.preventDefault();
                    if (tableNotCome != '')
                        tableNotCome.destroy()
                    initTableNotCome();
                    activeTab('come-tab')
                });

                $('#permission-outside-office-btn').click(function(e) {
                    e.preventDefault();
                    if (tableOutsideOffice != '')
                        tableOutsideOffice.destroy()
                    initTableOutsideOffice()
                    activeTab('outside-office-tab')
                });

                $('#btn-home').click(function(e) {
                    initTableHome()
                })

                $('#btn-come').click(function(e) {
                    initTableNotCome()
                })

                $('#btn-late').click(function(e) {
                    initTableLate()
                })

                $('#btn-outside-office').click(function(e) {
                    initTableOutsideOffice()
                })

                function activeTab(tab) {
                    $('.tab-pane').removeClass('active')
                    $('#' + tab).addClass('active')
                    $('#btn-create').attr('href', '{{ route("admin.$main.create") }}')
                }

            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarMenuOpen('#permission-letter-employee');
    </script>
@endsection
