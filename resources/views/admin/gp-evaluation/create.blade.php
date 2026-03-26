@extends('layouts.admin.layout.index')

@php
    $main = 'gp-evaluation';
    $permission = 'evaluation';
    $title = 'Assessment Karyawan';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline("tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $permission")
        <form id="form" action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table title="tambah {{ $title }}">
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="employee_id" label="Employee" id="employee" hasError errorBorderId="errorSelectEmployee" errorMessageId="errorMsgSelectEmployee" errorMsg="Employee wajib dipilih." required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="Employee ID" id="employeeId" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="Job Title" id="position" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="Tanggal" id="date" onchange="checkClosingPeriod($(this))" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <div class="box">
                <div class="box-header">
                    <div class="row align-items-center">
                        <div class="col mt-2 align-self-center">
                            <h3 class="box-title mb-0">{{ Str::headline('Evaluation Detail') }}</h5>
                        </div>
                        <div class="col-auto mt-2 align-self-center">
                            <x-button type="button" id="btnAddDetails" color="primary" label="Tambah Detail" dataToggle="modal" dataTarget="#addDetailsModal" />
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-12 pb-20 table-responsive">
                        <table id="detailTable" class="table table-striped mt-10 mb-10">
                            <thead class="bg-dark">
                                <tr>
                                    <th colspan="2" class="text-center">Evaluation Factors</th>
                                    <th>Score (1-10)</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="details">
                                <tr class="text-center">
                                    <td colspan="5">Belum ada detail ditambahkan.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total Score</td>
                                    <td class="fw-bold">
                                        <span id="totalScoreSpan">0</span>
                                        <input type="hidden" id="totalScore" name="total_score" value="0">
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Score (%)</td>
                                    <td class="fw-bold">
                                        <span id="totalScorePercentage">0%</span>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="row pt-20 pb-20">
                        <div class="col-4">
                            <div class="form-group">
                                <x-text-area id="notes" name="notes" label="Comments and Recommendations"></x-text-area>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer text-end">
                    <x-button type="button" id="btnBack" color="secondary" label="kembali" icon="x" fontawesome size="sm" />
                    <x-button type="button" id="btnSave" color="primary" label="simpan" icon="save" fontawesome size="sm" />
                </div>
            </div>
        </form>
        <x-modal title="Tambah Detail" id="addDetailsModal" headerColor="danger" modalSize="600">
            <x-slot name="modal_body">
                <input type="hidden" id="evaluationId">
                <input type="hidden" id="evaluationType">
                <input type="hidden" id="evaluationDesc">
                <input type="hidden" id="evaluationScore">
                <input type="hidden" id="evaluationNote">

                <div class="col-md-12">
                    <div class="form-group">
                        <x-select id="evaluationModal" label="Evaluasi" hasError errorBorderId="errorSelectEvaluation" errorMessageId="errorMsgSelectEvaluation" errorMsg="Evaluasi wajib dipilih." required>
                            </x-input>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <x-input type="text" id="typeModal" label="Tipe" useCustomError readonly></x-input>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <x-input type="text" id="scoreModal" label="Score" useCustomError helpers="1-10" required></x-input>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <x-text-area id="noteModal" label="Keterangan"></x-text-area>
                    </div>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="Batal" />
                <x-button type="button" id="btnAddDetailsModal" color="primary" label="Tambah" />
            </x-slot>
        </x-modal>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script>
        $(document).ready(function() {
            checkClosingPeriod($('#date'))
            let previousUrl = "{{ URL::previous() }}";

            let detailId = 0;
            let selectedEvalIds = [];
            let selectedTypes = [];

            const calculateTotalScore = () => {
                let totalScore = 0;

                $("input[name='score[]']").each(function(i, v) {
                    totalScore += parseInt($(this).val());
                })

                $('#totalScoreSpan').text(parseInt(totalScore));
                $('#totalScorePercentage').text(parseInt(totalScore) + '%');
                $('#totalScore').val(parseInt(totalScore));
            }

            initSelectEmployee('#employee', null, false);

            const initSelect2Evaluations = () => {
                let opts = {
                    dropdownParent: $('#addDetailsModal'),
                    placeholder: "Pilih Data",
                    minimumInputLength: 0,
                    allowClear: false,
                    width: "100%",
                    language: {
                        noResults: () => {
                            return "Data can't be found";
                        },
                    },
                    ajax: {
                        url: `${base_url}/select/master-gp-evaluation`,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            result["not_in_ids"] = selectedEvalIds;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: data.description,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };

                $('#evaluationModal').select2(opts);
            }

            $('#employee').change(function() {
                let value = $(this).val();
                if (value) {
                    $(`#errorSelectEmployee`).removeClass('border border-danger');
                    $(`#errorMsgSelectEmployee`).html('');

                    $.ajax({
                        type: "post",
                        url: `${base_url}/employee-find-by-id`,
                        dataType: 'json',
                        data: {
                            _token: token,
                            id: value,
                        },
                        success: function(data) {
                            $('#position').val(data.position.nama);
                            $('#employeeId').val(data.NIK);
                        }
                    });
                } else {
                    $(`#errorSelectEmployee`).addClass('border border-danger');
                    $(`#errorMsgSelectEmployee`).addClass('text-danger');
                }
            });

            $('#addDetailsModal').on('show.bs.modal', function() {
                $('#evaluationId').val(null);
                $('#evaluationType').val(null);
                $('#evaluationScore').val(null);
                $('#evaluationNote').val(null);

                $('#evaluationModal').val(null).trigger('change');
                $('#typeModal').val(null);
                $('#scoreModal').val(null);
                $('#noteModal').val(null);

                $('#errorSelectEvaluation').addClass('border border-danger');
                $('#errorMsgSelectEvaluation').html('Evaluasi wajib dipilih.');
            });

            $('#addDetailsModal').on('shown.bs.modal', function() {
                initSelect2Evaluations();

                $('#scoreModal').addClass('is-invalid');
                $('#error-message-for-scoreModal').text('Score wajib diisi.');

                $('#scoreModal').keyup(debounce(function() {
                    let value = $(this).val();
                    if (value == null || value == '') {
                        $('#scoreModal').addClass('is-invalid');
                        $('#error-message-for-scoreModal').text('Score wajib diisi.');
                    } else if (isNaN(value)) {
                        $('#scoreModal').addClass('is-invalid');
                        $('#error-message-for-scoreModal').text('Score harus berupa angka.');
                    } else if (parseInt(value) < 1 || parseInt(value) > 10) {
                        $('#scoreModal').addClass('is-invalid');
                        $('#error-message-for-scoreModal').text('Score minimal 1 dan maksimal 10.');
                    } else {
                        $('#evaluationScore').val(parseInt(value));
                        $('#scoreModal').removeClass('is-invalid');
                        $('#error-message-for-scoreModal').text(null);
                    }
                }, 500));

                $('#evaluationModal').change(function() {
                    let value = $(this).val();
                    if (value) {
                        $('#evaluationId').val(value);

                        $('#errorSelectEvaluation').removeClass('border border-danger');
                        $('#errorMsgSelectEvaluation').html('');

                        $.ajax({
                            type: "post",
                            url: `${base_url}/master-gp-evaluation-find-by-id`,
                            dataType: 'json',
                            data: {
                                _token: token,
                                id: value,
                            },
                            success: function(data) {
                                $('#evaluationType').val(data.type);
                                $('#evaluationDesc').val(data.description);
                                $('#typeModal').val(ucwords(data.type));
                            }
                        });
                    }
                });
            });

            $('#btnAddDetailsModal').click(function() {
                $('#evaluationNote').val($('#noteModal').val());

                let evaluationId = $('#evaluationId').val();
                let evaluationType = $('#evaluationType').val();
                let evaluationDesc = $('#evaluationDesc').val();
                let evaluationScore = $('#evaluationScore').val();
                let evaluationNote = $('#evaluationNote').val();

                if (evaluationId == null || evaluationId == '' || $('#scoreModal').hasClass('is-invalid')) {
                    showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                } else {
                    if (selectedEvalIds.length == 0) {
                        $('#details').find('tr').remove();
                    }

                    selectedEvalIds.push(evaluationId);

                    let rowIndex = $('#detailTable > tbody > tr').length;

                    $('#details').append(`
                        <tr id="detail${detailId}" data-testId="${detailId}">
                            <td>
                                <span>${ucwords(evaluationType)}</span>
                            </td>
                            <td>
                                <span>${evaluationDesc}</span>
                            </td>
                            <td>
                                <span>${evaluationScore == '' ? '1' : evaluationScore}</span>
                                <input type="hidden" id="score" name="score[]" value="${evaluationScore == '' ? 1 : evaluationScore}">
                            </td>
                            <td>
                                <span>${evaluationNote == '' ? 'Tidak ada keterangan' : evaluationNote}</span>
                                <input type="hidden" id="note" name="detail_notes[]" value="${evaluationNote}">
                            </td>
                            <td>
                                <input type="hidden" id="evaluation${detailId}" name="master_gp_evaluation_id[]" value="${evaluationId}">
                                <x-button type="button" id="btnDeleteDetail${detailId}" color="danger" label="" icon="x" fontawesome size="sm" />
                            </td>
                        </tr>
                    `);

                    $(`#btnDeleteDetail${detailId}`).attr('data-testId', detailId);

                    $(`#btnDeleteDetail${detailId}`).click(function() {
                        let rowIndex = $(this).attr('data-testId');
                        let evaluationId = $(`#evaluation${rowIndex}`).val();

                        let evalIndex = selectedEvalIds.indexOf(evaluationId);
                        if (evalIndex !== -1) {
                            selectedEvalIds.splice(evalIndex, 1);
                        }

                        $(`#detail${rowIndex}`).remove();

                        if ($('#detailTable > tbody > tr').length == 0) {
                            $('#details').append(`
                                <tr class="text-center">
                                    <td colspan="5">Belum ada detail ditambahkan.</td>
                                </tr>
                            `);
                        }

                        calculateTotalScore();
                    });

                    detailId++;

                    $('#addDetailsModal').modal('hide');

                    calculateTotalScore();
                }
            })

            $('#btnSave').click(function() {
                if ($('#employee').val() == null) {
                    showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                } else if (selectedEvalIds.length == 0) {
                    showAlert('', 'Detail tidak boleh kosong!', 'warning');
                } else {
                    $('#form').submit();
                }
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarActive('#gp-evaluation')
    </script>
@endsection
