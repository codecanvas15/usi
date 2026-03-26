@extends('layouts.admin.layout.index')

@php
    $main = 'specific-time-work-agreement';
    $title = 'Perjanjian Kerja Waktu Tertentu';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

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
                        <a href="{{ route('admin.index') }}">{{ Str::headline($title) }}</a>
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
    @can("view $main")
        <form action="{{ route("admin.$main.update", $model) }}" method="post">
            @csrf
            @method('put')
            <x-card-data-table :title="'Edit ' . $title">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="code-input" label="kode" value="{{ $model->code }}" readonly required />
                            </div>
                        </div>
                    </div>
                    @if (get_current_branch()->is_primary)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="branch_id" label="branch" id="select-branch" required>
                                        @if ($model->branch)
                                            <option value="{{ $model->branch->id }}" selected>{{ $model->branch->name }}</option>
                                        @else
                                            <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-10">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="tanggal" value="{{ localDate($model->date) }}" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="title" label="judul" value="{{ $model->title }}" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="attachment" label="file" value="{{ $model->attachment }}" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h4>Pihak Pertama</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="employee_id" label="karyawan" id="firstEmployee-select" required>
                                        @if ($model->employee_id)
                                            <option value="{{ $model->employee_id }}" selected>{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="division_id" label="divisi" id="FirstDivision-select" required disabled>
                                        @if ($model->employee->division_id)
                                            <option value="{{ $model->employee->division_id }}" selected>{{ $model->employee->division?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="position_id" label="jabatan" id="FirstPosition-select" required disabled>
                                        @if ($model->employee->position_id)
                                            <option value="{{ $model->employee->position_id }}" selected>{{ $model->employee->position?->nama }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h4>Pihak Kedua</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="second_employee_type" label="tipe" id="second-employee-type" required>
                                        <option value="new" @if ($model->second_employee_type == 'new') selected @endif>Karyawan Baru</option>
                                        <option value="existing" @if ($model->second_employee_type == 'existing') selected @endif>Perpanjang Kontrak</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="mb-2" for="">Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="reference_id" id="referenceSelect" required></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" label="divisi" id="second-division" value="{{ $model->second_division->nama }}" readonly required></x-input>
                                    <input type="hidden" id="second-division-id" name="second_division_id" value="{{ $model->second_division->id }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" label="jabatan" id="second-position" value="{{ $model->second_position->nama }}" readonly required></x-input>
                                    <input type="hidden" id="second-position-id" name="second_position_id" value="{{ $model->second_position->id }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="master_letter_id" label="template dokumen" id="master_letter_id" required></x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-button color="info" label="generate" soft icon="rotate" fontawesome size="sm" id="generate-button" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-20">
                        <textarea name="description" id="content-generated" cols="30" rows="10">{!! $model->description !!}</textarea>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-button color="primary" label="Simpan" icon="save" fontawesome id="button-save" />
                </x-slot>
            </x-card-data-table>

        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/admin/select/division.js') }}"></script>
    <script src="{{ asset('js/admin/select/position.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
    <script>
        $(document).ready(function() {
            let reference_id = @json($model->reference_id);

            let secondEmployeeNIK = '-',
                secondEmployeeName = '',
                secondEmployeeAlamat = '';

            const initSelectSecondEmployee = (element, selected_id = null) => {
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

                $(element).select2(select2Option);

                if (reference_id) {
                    let option, code, name;

                    if (secondEmployeeType == 'new') {
                        $.ajax({
                            type: "post",
                            url: `${base_url}/user-assessment-find-by-id`,
                            dataType: 'json',
                            data: {
                                _token: token,
                                id: reference_id,
                            },
                            success: function(data) {
                                var newOption = new Option(`${data.candidate_data.code} - ${data.candidate_data.name}`, data.id, true, true);
                                $(element).append(newOption).trigger('change');
                            }
                        });
                    } else {
                        $.ajax({
                            type: "post",
                            url: `${base_url}/contract-extension-find-by-id`,
                            dataType: 'json',
                            data: {
                                _token: token,
                                id: reference_id,
                            },
                            success: function(data) {
                                var newOption = new Option(`${data.code} - ${data.employee.name}`, data.id, true, true);
                                $(element).append(newOption).trigger('change');
                            }
                        });
                    }
                }
            }
            initSelectSecondEmployee('#referenceSelect', reference_id);

            const initializeSelect2SearchCreateForm = () => {
                initBranchSelect('#select-branch');

                initSelectEmployee("#firstEmployee-select");
                initDivisionSelect('#FirstDivision-select');
                initPositionSelect("#FirstPosition-select");
                initSelect2SearchPaginationData('master_letter_id', `${base_url}/select/master-letter`, {
                    id: "id",
                    text: "document_name"
                }, 0, {});

                handleSelect2Change();
            };

            const handleSelect2Change = () => {
                $('#firstEmployee-select').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.employee.detail') }}/${this.value}`,
                            success: function({
                                data
                            }) {
                                let {
                                    division,
                                    position
                                } = data;

                                // $('#FirstDivision-select').attr("disabled", false);
                                // $('#FirstPosition-select').attr("disabled", false);

                                initDivisionSelect('#FirstDivision-select');
                                initPositionSelect("#FirstPosition-select");

                                $('#FirstDivision-select').html(`<option value="${division.id}" selected>${division.name}</option>`);
                                $('#FirstPosition-select').html(`<option value="${position.id}" selected>${position.nama}</option>`);

                                $('#FirstDivision-select').val(division.id).trigger('change');
                                $('#FirstPosition-select').val(position.id).trigger('change');

                            }
                        });
                    } else {
                        $('#FirstDivision-select').attr("disabled", true);
                        $('#FirstPosition-select').attr("disabled", true);

                        $('#FirstDivision-select').val(null);
                        $('#FirstPosition-select').val(null);
                    }

                });

                $('#second-employee-type').change(function() {
                    $('#referenceSelect').val(null).trigger('change');
                    $('#second-division').val(null);
                    $('#second-division-id').val(null);
                    $('#second-position').val(null);
                    $('#second-position-id').val(null);

                    initSelectSecondEmployee('#referenceSelect');
                })

                $('#referenceSelect').change(function() {
                    let secondEmployeeType = $('#second-employee-type').val();

                    if ($(this).val() !== null || $(this).val() !== '') {
                        if (secondEmployeeType == 'new') {
                            $.ajax({
                                type: "post",
                                url: `${base_url}/user-assessment-find-by-id`,
                                dataType: 'json',
                                data: {
                                    _token: token,
                                    id: $(this).val(),
                                },
                                success: function(data) {
                                    secondEmployeeName = data.candidate_data.name;
                                    secondEmployeeAlamat = data.candidate_data.address == "" ? data.candidate_data.address_domicil : data.candidate_data.address;

                                    $('#second-division').val(data.candidate_data.labor_demand_detail.labor_demand.division.name);
                                    $('#second-division-id').val(data.candidate_data.labor_demand_detail.labor_demand.division.id);
                                    $('#second-position').val(data.candidate_data.labor_demand_detail.position.nama);
                                    $('#second-position-id').val(data.candidate_data.labor_demand_detail.position.id);
                                }
                            });
                        } else {
                            $.ajax({
                                type: "post",
                                url: `${base_url}/contract-extension-find-by-id`,
                                dataType: 'json',
                                data: {
                                    _token: token,
                                    id: $(this).val(),
                                },
                                success: function(data) {
                                    secondEmployeeNIK = data.employee.NIK;
                                    secondEmployeeName = data.employee.name;
                                    secondEmployeeAlamat = data.employee.alamat !== "" ? data.employee.alamat : data.employee.alamat_domisili;

                                    $('#second-division').val(data.employee.division.name);
                                    $('#second-division-id').val(data.employee.division.id);
                                    $('#second-position').val(data.employee.position.nama);
                                    $('#second-position-id').val(data.employee.position.id);
                                }
                            });
                        }
                    }
                });

                ClassicEditor
                    .create(document.querySelector('#content-generated'))
                    .catch(error => {
                        console.error(error);
                    });
            };

            $('button#generate-button').click(function(e) {
                e.preventDefault();

                $('#content-generated').fadeIn(500);
                $('#button-save').fadeIn(500);

                let editor = null;
                $('.ck.ck-reset.ck-editor.ck-rounded-corners').remove();

                $.ajax({
                    url: `{{ route('admin.specific-time-work-agreement.generate') }}`,
                    type: 'POST',
                    data: {
                        _token: token,
                        code: $('#code-input').val(),
                        branch_id: $('#select-branch').val(),
                        date: $('#date-input').val(),
                        first_employee_id: $('#firstEmployee-select').val(),
                        reference_id: $('#referenceSelect').val(),
                        master_letter_id: $('#master_letter_id').val(),
                    },
                    success: function(res) {
                        ClassicEditor
                            .create(document.querySelector('#content-generated'))
                            .then(newEditor => {
                                editor = newEditor;
                            })
                            .catch(error => {
                                console.error(error);
                            });

                        setTimeout(() => {
                            editor.setData(res);
                        }, 500);
                    }
                });
            });

            initializeSelect2SearchCreateForm();
        });
    </script>
@endsection
