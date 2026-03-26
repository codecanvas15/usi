@extends('layouts.admin.layout.index')

@php
    $main = 'specific-time-work-agreement';
    $title = 'Perjanjian Kerja Waktu Tertentu';
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
    <x-card-data-table :title="$title">
        <x-slot name="header_content">

            <div class="row">
                <div class="col-4">
                    @can("create $main")
                        <x-button color="info" icon="plus" label="Create" :link='route("admin.$main.create")' />
                    @endcan
                </div>
            </div>

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <div class="row my-10">

                @if (get_current_branch()->is_primary)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch" id="branch-selectForm" required>
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="second_employee_type" label="tipe" id="second-employee-type" required>
                                <option value="new" selected>Karyawan Baru</option>
                                <option value="existing">Perpanjang Kontrak</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select label="pihak pertama" id="firstEmployee-selectForm" required>

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="">Pihak Kedua <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="reference_id" id="referenceSelect" required></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select label="status" id="status-selectForm" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                                <option value="pending">Pending</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="dari" id="fromDate-input" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="sampai" id="toDate-input" required />
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-self-end">
                        <div class="form-group">
                            <x-button type="button" color="info" size="sm" icon="search" fontawesome id="btn-init-table" />
                        </div>
                    </div>
                </div>
            </div>

            @can("view $main")
                <x-table id="specific_time_work_aggrement">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('date') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('pihak pertama') }}</th>
                        <th>{{ Str::headline('pihak kedua') }}</th>
                        <th>{{ Str::headline('judul') }}</th>
                        <th>{{ Str::headline('status') }}</th>
                        <th>Aksi</th>
                        <th>Export</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/admin/select/branch.js') }}"></script>
        <script src="{{ asset('js/admin/select/employee.js') }}"></script>

        <script>
            $(document).ready(() => {

                const initSelectSecondEmployee = (element) => {
                    let secondEmployeeType = $('#second-employee-type').val();

                    const formatText = (data) => {
                        if (secondEmployeeType == 'new') {
                            return `${data.candidate_data.code} - ${data.candidate_data.name}`;
                        } else {
                            return `${data.code} - ${data.employee.name}`;
                        }
                    }

                    var select2Option = {
                        placeholder: "Pilih Data",
                        minimumInputLength: 0,
                        allowClear: false,
                        width: "100%",
                        language: {
                            inputTooShort: () => {
                                return "Ketik minimal 3 karakter";
                            },
                            noResults: () => {
                                return "Data tidak ditemukan";
                            },
                        },
                        ajax: {
                            url: `${base_url}/select/specific-time-work-agreement/select-second-employee`,
                            dataType: "json",
                            delay: 250,
                            type: "get",
                            data: ({
                                term
                            }) => {
                                let result = {};
                                result["search"] = term;
                                result["second_employee_type"] = secondEmployeeType;
                                return result;
                            },
                            processResults: ({
                                data
                            }) => {
                                let final_data = data.map((data, key) => {
                                    return {
                                        id: data.id,
                                        text: formatText(data),
                                    };
                                });
                                return {
                                    results: final_data,
                                };
                            },
                            cache: true,
                        },
                    };

                    let elements = $(element);
                    if (elements.length > 1) {
                        $.each(elements, function(e) {
                            $(this).select2(select2Option);
                        });
                    } else {
                        $(element).select2(select2Option);
                    }
                }
                initSelectSecondEmployee('#referenceSelect');

                initBranchSelect('#branch-selectForm');
                initSelectEmployee("#firstEmployee-selectForm");

                const initializeDataTable = () => {
                    let data = {
                        branch: $('#branch-selectForm').val(),
                        employee_id: $('#firstEmployee-selectForm').val(),
                        status: $('#status-selectForm').val(),
                        from_date: $('#fromDate-input').val(),
                        to_date: $('#toDate-input').val(),
                    };


                    const table = $('table#specific_time_work_aggrement').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            data: data,
                            url: '{{ route("admin.$main.index") }}'
                        },
                        order: [
                            [2, "desc"]
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
                                data: 'code',
                                name: 'code'
                            },
                            {
                                data: 'employee_id',
                                name: 'employee_id'
                            },
                            {
                                data: 'reference_id',
                                name: 'reference_id'
                            },
                            {
                                data: 'title',
                                name: 'title'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'action',
                            },
                            {
                                data: 'export',
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                };
                initializeDataTable();

                $('#btn-init-table').click(function() {
                    initializeDataTable();
                });
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
