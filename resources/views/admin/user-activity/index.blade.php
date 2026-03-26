@extends('layouts.admin.layout.index')

@php
    $main = 'user-activity';
    $title = 'aktivitas pengguna';
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
        <div class="box">
            <div class="box-body border-0">
                <ul class="nav nav-tabs customtab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#activity-tab" id="activity-tab-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Activity</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#status-tab" id="status-tab-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                            <span class="hidden-xs-down">Status</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content mt-30">

            <div class="tab-pane active" id="activity-tab" role="tabpanel">
                <x-card-data-table title='{{ "$title" }}'>
                    <x-slot name="header_content">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="user" id="activity-select" required>

                                    </x-select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="_date" id="activity-fromdate" value="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="_date" id="activity-todate" value="" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="primary" id="activity-init" icon="search" fontawesome />
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="table_content">

                        <x-table id="activity-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('email') }}</th>
                                <th>{{ Str::headline('referensi') }}</th>
                                <th>{{ Str::headline('event') }}</th>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <th>{{ Str::headline('') }}</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>

            <div class="tab-pane" id="status-tab" role="tabpanel">
                <x-card-data-table title='{{ "$main status" }}'>
                    <x-slot name="header_content">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="user" id="status-select" required>

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="_date" id="status-fromdate" value="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="_date" id="status-todate" value="" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="primary" id="status-init" icon="search" fontawesome />
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="table_content">

                        <x-table id="status-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('email user') }}</th>
                                <th>{{ Str::headline('dari status') }}</th>
                                <th>{{ Str::headline('ke status') }}</th>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <th>{{ Str::headline('') }}</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>

        </div>
    @endcan
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/user.js') }}"></script>

        <script>
            $(document).ready(() => {

                const activityTable = () => {

                    initUserSelect('#activity-select', "{{ route('admin.select.user') }}", {
                        id: "id",
                        text: "nama"
                    })

                    let data = {
                        from_date: $('#activity-fromdate').val(),
                        to_date: $('#activity-todate').val(),
                        user_id: $('#activity-select').val(),
                    };

                    const table = $('table#activity-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [4, 'desc']
                        ],
                        ajax: {
                            url: '{{ route('admin.user-activity.activity-log') }}',
                            data: data
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'user.email',
                                name: 'user.email'
                            },
                            {
                                data: 'subject_type',
                                name: 'subject_type'
                            },
                            {
                                data: 'event',
                                name: 'event'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'action',
                                name: 'action'
                            },
                        ]
                    });
                    $('table#activity-table').css('width', '100%');
                };

                activityTable();

                $('#activity-init').click(function(e) {
                    e.preventDefault();
                    activityTable();
                });

                const statusTable = () => {

                    initUserSelect('#status-select', "{{ route('admin.select.user') }}", {
                        id: "id",
                        text: "nama"
                    })

                    let data = {
                        from_date: $('#status-fromdate').val(),
                        to_date: $('#status-todate').val(),
                        user_id: $('#status-select').val(),
                    };

                    const table = $('table#status-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [5, 'desc']
                        ],
                        ajax: {
                            url: '{{ route('admin.user-activity.status-log') }}',
                            data: data
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'code',
                                name: 'code'
                            },
                            {
                                data: 'user.email',
                                name: 'user.email'
                            },
                            {
                                data: 'from_status',
                                name: 'from_status'
                            },
                            {
                                data: 'to_status',
                                name: 'to_status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'action',
                                name: 'action'
                            },
                        ]
                    });
                    $('table#status-table').css('width', '100%');
                };

                statusTable();

                $('#status-init').click(function(e) {
                    e.preventDefault();
                    statusTable();
                });
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#user-activity-sidebar');
    </script>
@endsection
