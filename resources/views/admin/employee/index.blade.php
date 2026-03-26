@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline($main) . ' - ')

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
                    <li class="breadcrumb-item">
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ $title }}">
            <x-slot name="header_content">
                <div class="row justify-content-between mb-4">
                    <div class="col-md-12">
                        @can("create $main")
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        @endcan

                        @can("export $main")
                            <x-button link='{{ route("admin.$main.export") }}' color="info" icon="upload" label="export" />
                        @endcan

                        @can("import $main")
                            @include('admin.employee.partials.index.import')
                        @endcan
                        @can("create $main")
                            <x-button id="copy-edit-link" color="warning" icon="edit" label="edit-link" />
                        @endcan
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                @if (get_current_branch()->is_primary)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="" label="cabang" id="select-branch"></x-select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <x-select name="" label="pendidikan" id="education-select"></x-select>
                    </div>
                    <div class="col-md-3">
                        <x-select name="" label="jurusan" id="degree-select"></x-select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="" label="jabatan" id="select-posision">
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="" label="status" id="employee_status">
                                <option value="">-- pilih status --</option>
                                @foreach (\App\Enums\EmployeeStatusEnum::cases() as $item)
                                    <option value="{{ $item['name'] }}">{{ $item['value'] }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="" label="status kontrak" id="select-status">
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="" label="division" id="select-division">
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="info" icon="search" size="sm" fontawesome id="btn-datatable" />
                        </div>
                    </div>
                </div>

                <x-table id="employee_table">
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('Pengguna') }}</th>
                        <th>{{ Str::upper('Employee') }}</th>
                        <th>{{ Str::upper('Nama') }}</th>
                        <th>{{ Str::upper('Status') }}</th>
                        <th>{{ Str::upper('Email') }}</th>
                        <th>{{ Str::upper('Cabang') }}</th>
                        <th>{{ Str::upper('Posisi') }}</th>
                        <th>{{ Str::upper('Status Kontrak') }}</th>
                        <th>{{ Str::upper('Tanggal Masuk') }}</th>
                        <th>{{ Str::upper('dibuat pada') }}</th>
                        <th>{{ Str::upper('export') }}</th>
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
                // select-posision
                // select-status

                initSelect2Search('select-posision', "{{ route('admin.select.position') }}", {
                    id: "id",
                    text: "nama"
                })

                initSelect2Search('select-status', "{{ route('admin.select.employment-status') }}", {
                    id: "id",
                    text: "name"
                })

                initSelect2Search('select-branch', "{{ route('admin.select.branch') }}", {
                    id: "id",
                    text: "name"
                })

                initSelect2Search('select-division', "{{ route('admin.select.division') }}", {
                    id: "id",
                    text: "name"
                })

                initSelect2Search('education-select', "{{ route('admin.select.education') }}", {
                    id: "id",
                    text: "name"
                })

                initSelect2Search('degree-select', "{{ route('admin.select.degree') }}", {
                    id: "id",
                    text: "name"
                })


                const initTable = () => {
                    const table = $('table#employee_table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                position: $('#select-posision').val(),
                                employment_status: $('#select-status').val(),
                                branch_id: $('#select-branch').val(),
                                division_id: $('#select-division').val(),
                                education_id: $('#education-select').val(),
                                degree_id: $('#degree-select').val(),
                                employee_status: $('#employee_status').val(),
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'user',
                                name: 'user',
                            },
                            {
                                data: 'NIK',
                                name: 'NIK'
                            },
                            {
                                data: 'name',
                                name: 'name',
                            },
                            {
                                data: 'employee_status',
                                name: 'employee_status',
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
                                data: 'export',
                                name: 'export'
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
        sidebarActive('#employee-sidebar');
    </script>
    <script>
        $(document).ready(function() {
            $('#copy-edit-link').click(function(e) {
                e.preventDefault();

                const codeToCopy = `{{ route('guest.employee.index') }}`;

                navigator.clipboard.writeText(codeToCopy)
                    .then(() => {
                        console.log('Code copied to clipboard');
                    })
                    .catch((error) => {
                        console.error('Failed to copy code to clipboard:', error);
                    });

                alert('Url copied to clipboard.');
            });
        });
    </script>
@endsection
