@extends('layouts.admin.layout.index')

@php
    $main = 'position';
    $title = 'jabatan';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

@section('css')
    @can("view $main")
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    @endcan
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ 'detail ' . $title }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <x-table theadColor='danger'>
                    <x-slot name="table_head">
                        <th></th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <th>{{ Str::headline('nama') }}</th>
                            <td>{{ $model->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('created_at') }}</th>
                            <td>{{ toDayDateTimeString($model->created_at) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('last medified') }}</th>
                            <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                        </tr>
                    </x-slot>
                </x-table>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan

                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="data pegawai">
            <x-slot name="table_content">
                <x-table id="employee-table">
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('Pengguna') }}</th>
                        <th>{{ Str::upper('Employee') }}</th>
                        <th>{{ Str::upper('Nama') }}</th>
                        <th>{{ Str::upper('Email') }}</th>
                        <th>{{ Str::upper('Cabang') }}</th>
                        <th>{{ Str::upper('Posisi Pekerjaan') }}</th>
                        <th>{{ Str::upper('Status Karyawan') }}</th>
                        <th>{{ Str::upper('Tanggal Masuk') }}</th>
                        <th>{{ Str::upper('Created At') }}</th>
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

                const initTable = () => {
                    const table = $('table#employee-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.position.employee', $model) }}',
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'user',
                                name: 'user'
                            },
                            {
                                data: 'NIK',
                                name: 'NIK'
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'email',
                                name: 'email'
                            },
                            {
                                data: 'branch.name',
                                name: 'branch.name'
                            },
                            {
                                data: 'position.nama',
                                name: 'position.nama'
                            },
                            {
                                data: 'employment_status.name',
                                name: 'employment_status.name'
                            },
                            {
                                data: 'join_date',
                                name: 'join_date'
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
                    $('table').css('width', '100%');
                };

                $('#btn-datatable').click(function(e) {
                    e.preventDefault();
                    initTable();
                });

                initTable();
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#position-sidebar');
    </script>
@endsection
