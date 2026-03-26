@extends('layouts.admin.layout.index')

@php
    $main = 'mass-leave';
    $title = 'cuti bersama';
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
                        <a href="{{ route('admin.' . $main . '.index') }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("edit $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')

    @can("edit $main")
        <x-card-data-table title='{{ $title }}'>
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="employee_ids" id="employee_ids">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" label="branch" id="branch-select">
                                    <option value="{{ $model->branch->id }}" selected>{{ $model->branch->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" value="{{ localDate($model?->date) }}" label="tanggal" id="date" required onblur="checkDate()" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="from_date" value="{{ localDate($model?->from_date) }}" label="dari tanggal" id="from_date" required onblur="checkDate()" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="to_date" value="{{ localDate($model?->to_date) }}" label="sampai tanggal" id="to_date" required onblur="checkDate()" />
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-text-area name="cause" label="alasan/keperluan cuti" id="cause" cols="30" rows="10" required>
                                    {!! $model->necessary !!}
                                </x-text-area>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-text-area name="note" label="keterangan" id="" cols="30" rows="10" required>
                                    {!! $model->note !!}
                                </x-text-area>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <x-table id="employee-table">
                                <x-slot name="table_head">
                                    <th>
                                        <input type="checkbox" id="check-all" style="position: unset; left: 0; opacity: 1">
                                    </th>
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('kode') }}</th>
                                    <th>{{ Str::headline('pegawai') }}</th>
                                    <th>{{ Str::headline('divisi') }}</th>
                                    <th>{{ Str::headline('jabatan') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="file" name="attachment" label="upload file" id="attachment" onchange="handleChangeAttachment(event)" />
                                <div id="preview-attachment">
                                    @if ($model && $model->attachment)
                                        <embed width="150" src="{{ asset('storage/' . $model->attachment) }}"></embed>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#mass-leave')

        var employee_ids = JSON.parse('{{ $employee_ids }}');
        var selected_employee_ids = JSON.parse('{{ $model->mass_leave_details()->pluck('employee_id') }}');

        const initTable = () => {
            const table = $('table#employee-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: '{{ route('admin.mass-leave.employee-data') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        branch_id: function() {
                            return $('#branch-select').val()
                        }
                    },
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        render: function(row, data, DataIndex) {
                            var is_checked = selected_employee_ids.includes(DataIndex.id) ? 'checked' : '';
                            return `<input type="checkbox" value="${DataIndex.id}" style="position: unset; left: 0; opacity: 1" onclick="check_employee($(this))" data-id="${DataIndex.id}" ${is_checked}>`
                        },
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'NIK',
                        name: 'employees.NIK',
                    },
                    {
                        data: 'name',
                        name: 'employees.name',
                    },
                    {
                        data: 'division_name',
                        name: 'divisions.name',
                    },
                    {
                        data: 'position_nama',
                        name: 'positions.nama',
                    },
                ]
            });

            $('table').css('width', '100%');
        };

        function check_employee(e) {
            if ($(e).is(':checked')) {
                selected_employee_ids.push($(e).data('id'));
            } else {
                selected_employee_ids.splice(selected_employee_ids.indexOf($(e).data('id')), 1);
            }
        }

        $('#check-all').change(function() {
            if ($(this).is(':checked')) {
                $('table#employee-table input[type=checkbox]').prop('checked', true);
                selected_employee_ids = employee_ids;
            } else {
                $('table#employee-table input[type=checkbox]').prop('checked', false);
                selected_employee_ids = [];
            }
        });

        $('#branch-select').change(function() {
            $('table#employee-table').DataTable().ajax.reload();
        });

        $('form').submit(function(e) {
            $('#employee_ids').val(selected_employee_ids);
        });

        initTable();
    </script>
    @if (get_current_branch()->is_primary)
        <script>
            initSelect2Search('branch-select', "{{ route('admin.select.branch') }}", {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
