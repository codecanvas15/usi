@extends('layouts.admin.layout.index')

@php
    $main = 'user-assessment';
    $title = 'Interview User';
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
    @can("create $main")
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
                                <x-select name="candidate" label="Kandidat" id="candidate" hasError errorBorderId="errorSelectCandidate" errorMessageId="errorMsgSelectCandidate" errorMsg="Kandidat wajib dipilih." required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="Tanggal" id="date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingDate($(this))" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="Job Title" id="position" readonly required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="Hiring Manager" id="hiringManager" name="hiring_manager" useCustomError required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="Department Name" id="division" readonly required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="interviewer" label="Interviewer" id="interviewer" hasError errorBorderId="errorSelectInterviewer" errorMessageId="errorMsgSelectInterviewer" errorMsg="Interviewer wajib dipilih." required />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <div class="box">
                <div class="box-header">
                    <div class="row align-items-center">
                        <div class="col mt-2 align-self-center">
                            <h3 class="box-title mb-0">{{ Str::headline('Assessment Detail') }}</h5>
                        </div>
                        <div class="col-auto mt-2 align-self-center">
                            <x-button type="button" id="btnAddCompetencies" color="primary" label="Add Competencies" dataToggle="modal" dataTarget="#addCompetenciesModal" />
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row border-bottom border-primary pb-20">
                        <div class="col-12">
                            <h4><b>{{ Str::headline('Key Behavioral Competencies') }}</b></h4>
                        </div>
                        <div class="col-12 table-responsive">
                            <table id="kbcTable" class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Wts</th>
                                        <th>Rating 1-5 (5 Highest)</th>
                                        <th>Weighted Score</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="kbcTBody">
                                    <tr class="text-center">
                                        <td colspan="6">Belum ada key behavioral competencies ditambahkan.</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-dark">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Overall Behavioral Competency Rating</td>
                                        <td class="fw-bold">
                                            <span id="obcrPercentage">0</span><span>%</span>
                                        </td>
                                        <td></td>
                                        <td class="fw-bold">
                                            <div>
                                                <span id="obcRatingTable" class="fw-bold">0,00</span>
                                                <input type="hidden" id="obcRating" name="behavioral_rating" value="0" />
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row py-20">
                        <div class="col-12">
                            <h4><b>{{ Str::headline('Key Skill Competencies') }}</b></h4>
                        </div>
                        <div class="col-12 table-responsive">
                            <table id="kscTable" class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Wts</th>
                                        <th>Rating 1-5 (5 Highest)</th>
                                        <th>Weighted Score</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="kscTBody">
                                    <tr class="text-center">
                                        <td colspan="6">Belum ada key skill competencies ditambahkan.</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-dark">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Overall Skill Competency Rating</td>
                                        <td class="fw-bold">
                                            <div>
                                                <span id="oscrPercentage">0</span><span>%</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div>
                                                <span id="oscRatingTable" class="fw-bold">0,00</span>
                                                <input type="hidden" id="oscRating" name="skill_rating" value="0" />
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row border-top border-primary pt-20">
                        <h4 class="mb-0 mt-10 mb-30"><b>{{ Str::headline('Hiring Recommendation') }}</b></h4>
                        <div class="col-4">
                            <div class="form-group">
                                <x-text-area id="" name="first_note" label="What impressed you the most"></x-text-area>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <x-text-area id="" name="second_note" label="What impressed you the least"></x-text-area>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <x-text-area id="" name="third_note" label="What questions or reservations do you have?"></x-text-area>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 mt-30">
                        <div class="col-4 mx-5 my-5">
                            <div class="row">
                                <div class="col-auto">
                                    <h4 class="fw-bold mb-0">Overall Rating</h4>
                                    <p class="mt-3 mb-0">Ratings:</p>
                                    <ul>
                                        <li>5. Excellent</li>
                                        <li>4. Good</li>
                                        <li>3. Fair</li>
                                        <li>2. Poor</li>
                                        <li>1. Unacceptable</li>
                                    </ul>
                                </div>
                                <div class="col-auto">
                                    <h4 id="overallRatingText" class="fw-bold text-danger mb-0">0,00</h4>
                                    <input type="hidden" id="overallRating" name="total_rating" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="">Hiring Recommendation <span class="text-danger">*</span></label>
                            <div id="errorSelectStatus" class="border border-danger mt-2">
                                <select class="form-control select2" id="recommendStatus" name="recommend_status" required>
                                    <option value="" selected disabled>-- pilih status rekomendasi --</option>
                                    <option value="y">1st Choice</option>
                                    <option value="r">2nd Choice</option>
                                    <option value="x">Not a Fit</option>
                                </select>
                            </div>
                            <span id="errorMsgSelectStatus" class="text-danger mt-1">Status rekomendasi wajib dipilih.</span>
                        </div>

                    </div>
                </div>
                <div class="box-footer text-end">
                    <x-button id="btnBack" color="secondary" label="kembali" icon="x" fontawesome size="sm" />
                    <x-button type="button" id="btnSave" color="primary" label="simpan" icon="save" fontawesome size="sm" />
                </div>
            </div>
        </form>
        <x-modal title="Add Competencies" id="addCompetenciesModal" headerColor="danger" modalSize="600">
            <x-slot name="modal_body">
                <input type="hidden" id="keyCompetencyId" />
                <input type="hidden" id="keyCompetencyName" />
                <input type="hidden" id="keyCompetencyType" />
                <input type="hidden" id="keyCompetencyWeight" />
                <input type="hidden" id="keyCompetencyWScore" />

                <div class="col-md-12">
                    <div class="form-group">
                        <x-select label="Key Competencies" id="keyCompetenciesModal" hasError errorBorderId="errorKeyCompetenciesModal" errorMessageId="errorMsgKeyCompetenciesModal" errorMsg="Key competencies wajib dipilih." required />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <x-input type="text" label="Tipe" id="keyCompetencyTypeModal" readonly />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <x-input type="text" label="Wts" id="keyCompetencyWtsModal" readonly />
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="">Rating <span class="text-danger">*</span></label>
                    <div id="errorSelectRatingModal" class="border border-danger mt-2">
                        <select class="form-control select2" id="ratingModal" required>
                            <option value="" disabled selected>-- pilih rating --</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Fair</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Unacceptable</option>
                        </select>
                    </div>
                    <span id="errorMsgSelectRatingModal" class="text-danger mt-1">Rating wajib dipilih.</span>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <x-input type="text" label="Weight Score" id="keyCompetencyWsModal" readonly />
                    </div>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="Batal" />
                <x-button type="button" class="btn-add-competencies-modal" color="primary" label="Add" />
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
            let selectedKcIds = [];
            let selectedKbcTotal = 0;
            let selectedKscTotal = 0;

            const initSelectCandidate = (element) => {
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
                        url: `${base_url}/select/user-assessment/select-candidate`,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: `${ucwords(data.name)} - ${data.code}`,
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
            initSelectCandidate('#candidate');

            initSelectEmployee('#interviewer', null, false);

            const initSelect2KeyCompetencies = () => {
                let opts = {
                    dropdownParent: $('#addCompetenciesModal'),
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
                        url: `${base_url}/select/master-user-assessment`,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            result["not_in_ids"] = selectedKcIds;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: data.name,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };

                $('#keyCompetenciesModal').select2(opts);
            }

            const reorderIndex = (type) => {
                $(`#${type}Table > tbody > tr`).each(function(i, v) {
                    let rowIndex = $(this).attr('data-testId');
                    $(`#${type}RowId${rowIndex}`).text(i + 1);
                });
            }

            const calculateObcr = () => {
                let percentage = 0;
                let kbcWs = 0;

                $('.kbc-weight-percentage').each(function() {
                    percentage += parseFloat($(this).val()) * 100;
                });
                $('#obcrPercentage').html(percentage);

                $('.kbc-weight').each(function() {
                    kbcWs += parseFloat($(this).val());
                });
                $('#obcRating').val(kbcWs.toFixed(2));
                $('#obcRatingTable').html(kbcWs.toFixed(2));
            }

            const calculateOscr = () => {
                let percentage = 0;
                let kscWs = 0;

                $('.ksc-weight-percentage').each(function() {
                    percentage += parseFloat($(this).val()) * 100;
                });
                $('#oscrPercentage').html(percentage);

                $('.ksc-weight').each(function() {
                    kscWs += parseFloat($(this).val());
                });
                $('#oscRating').val(kscWs.toFixed(2));
                $('#oscRatingTable').html(kscWs.toFixed(2));
            }

            const calculateOverallRating = () => {
                let kbcWs = 0;
                let kscWs = 0;

                $('.kbc-weight').each(function(k, v) {
                    kbcWs += parseFloat($(this).val());
                });

                $('.ksc-weight').each(function(k, v) {
                    kscWs += parseFloat($(this).val());
                });

                let overallRating = parseFloat(kbcWs) + parseFloat(kscWs);

                $('#overallRatingText').html((0.5 * overallRating).toFixed(2));
                $('#overallRating').val((0.5 * overallRating).toFixed(2));
            }

            $('#hiringManager').addClass('is-invalid');
            $('#error-message-for-hiringManager').text('Hiring manager wajib diisi.');

            $('#hiringManager').keyup(function() {
                if ($(this).val() !== '') {
                    if ($(this).hasClass('is-invalid')) {
                        $('#hiringManager').removeClass('is-invalid');
                        $('#error-message-for-hiringManager').text('');
                    }
                } else {
                    $('#hiringManager').addClass('is-invalid');
                    $('#error-message-for-hiringManager').text('Hiring manager wajib diisi.');
                }
            });

            $('#candidate').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectCandidate').removeClass('border border-danger');
                    $('#errorMsgSelectCandidate').html('');

                    $.ajax({
                        type: "post",
                        url: `${base_url}/labor-application-find-by-id`,
                        dataType: 'json',
                        data: {
                            _token: token,
                            id: value,
                        },
                        success: function(data) {
                            $('#position').val(data.labor_demand_detail.position.nama);
                            $('#division').val(data.labor_demand_detail.labor_demand.division.name);
                        }
                    });
                }
            });

            $('#interviewer').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectInterviewer').removeClass('border border-danger');
                    $('#errorMsgSelectInterviewer').html('');
                }
            });

            $('#recommendStatus').change(function() {
                let value = $(this).val();
                if (value) {
                    $(`#errorSelectStatus`).removeClass('border border-danger');
                    $(`#errorMsgSelectStatus`).html('');
                } else {
                    $(`#errorSelectStatus`).addClass('border border-danger');
                    $(`#errorMsgSelectStatus`).addClass('text-danger');
                }
            });

            $('#addCompetenciesModal').on('show.bs.modal', function() {
                $('#keyCompetenciesModal').val(null).trigger('change');
                $('#keyCompetenciesModal').select2("destroy");
                $('#errorKeyCompetenciesModal').addClass('border border-danger');
                $('#errorMsgKeyCompetenciesModal').html('Key competencies wajib dipilih.');

                $('#keyCompetencyTypeModal').val(null);
                $('#keyCompetencyWtsModal').val(null);

                $('#ratingModal').val(null).trigger('change');
                $('#errorSelectRatingModal').addClass('border border-danger');
                $('#errorMsgSelectRatingModal').html('Rating wajib dipilih.');

                $('#keyCompetencyWsModal').val(null);
            });

            $('#addCompetenciesModal').on('shown.bs.modal', function() {
                initSelect2KeyCompetencies();

                $('#keyCompetenciesModal').change(function() {
                    let value = $(this).val();
                    if (value) {
                        $('#errorKeyCompetenciesModal').removeClass('border border-danger');
                        $('#errorMsgKeyCompetenciesModal').html('');

                        $.ajax({
                            type: "post",
                            url: `${base_url}/master-user-assessment-find-by-id`,
                            dataType: 'json',
                            data: {
                                _token: token,
                                id: value,
                            },
                            success: function(data) {
                                let rating = $('#ratingModal').val();
                                let weight = $('#keyCompetencyWeight').val();
                                let ws = parseFloat(weight || 0) * parseInt(value || 0);

                                $('#keyCompetencyId').val(data.id);
                                $('#keyCompetencyName').val(data.name);
                                $('#keyCompetencyType').val(data.type);
                                $('#keyCompetencyWeight').val(data.weight);
                                $('#keyCompetencyWScore').val(ws);

                                $('#keyCompetencyWsModal').val(ws.toFixed(2));
                                $('#keyCompetencyTypeModal').val(data.type);
                                $('#keyCompetencyWtsModal').val(`${data.weight * 100 }%`);
                            }
                        });
                    }
                });

                $('#ratingModal').select2("destroy");
                $('#ratingModal').select2({
                    dropdownParent: $('#addCompetenciesModal'),
                });

                $('#ratingModal').change(function() {
                    let weight = $('#keyCompetencyWeight').val();
                    let value = $(this).val();

                    if (value) {
                        let ws = parseFloat(weight) * parseInt(value);

                        $('#errorSelectRatingModal').removeClass('border border-danger');
                        $('#errorMsgSelectRatingModal').html('');

                        $('#keyCompetencyWsModal').val(ws.toFixed(2));
                        $('#keyCompetencyWScore').val(ws.toFixed(2));
                    }
                });
            });

            $('.btn-add-competencies-modal').click(function() {
                let keyCompetency = $('#keyCompetenciesModal').val();
                let rating = $('#ratingModal').val();

                let selectedCompetencyId = $('#keyCompetencyId').val();
                let selectedCompetencyName = $('#keyCompetencyName').val();
                let selectedCompetencyType = $('#keyCompetencyType').val();
                let selectedCompetencyWeight = $('#keyCompetencyWeight').val() || 0;
                let selectedCompetencyWScore = $('#keyCompetencyWScore').val() || 0;

                let kcWeight = parseFloat(selectedCompetencyWeight) * 100;

                if (!keyCompetency || !rating) {
                    showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                } else {
                    detailId++;

                    if (selectedCompetencyType == 'key behavioral competencies') {
                        if (kcWeight + parseInt(selectedKbcTotal) <= 100) {
                            if (selectedKbcTotal == 0) {
                                $('#kbcTBody').html('');
                            }

                            let rowCount = $('#kbcTable > tbody > tr').length + 1;

                            selectedKcIds.push(selectedCompetencyId);

                            $('#kbcTBody').append(`
                                <tr class="kbc-detail" id="kbcDetail${detailId}" data-testId="${detailId}">
                                    <td>
                                        <span id="kbcRowId${detailId}">${rowCount}</span>
                                        <input type="hidden" name="type[]" value="kbc" />
                                        <input type="hidden" class="kbc-mua-id" id="kbcMuaId${detailId}" name="master_user_assessment_id[]" value="${selectedCompetencyId}" />
                                    </td>
                                    <td>
                                        <span>${selectedCompetencyName}</span>
                                        <input type="hidden" class="kbc-competency-name" id="${detailId}" value="${selectedCompetencyName}" />
                                    </td>
                                    <td>
                                        <span>${kcWeight}%</span>
                                        <input type="hidden" class="kbc-weight-percentage" id="${detailId}" value="${selectedCompetencyWeight}" />
                                    </td>
                                    <td>
                                        <span>${rating}</span>
                                        <input type="hidden" class="kbc-rating" id="${detailId}" name="rating[]" value="${rating}" />
                                    </td>
                                    <td>
                                        <span>${selectedCompetencyWScore}</span>
                                        <input type="hidden" class="kbc-weight" id="kbc${detailId}" name="weight[]" value="${selectedCompetencyWScore}" />
                                    </td>
                                    <td>
                                        <x-button type="button" id="btnDeleteKbcDetail${detailId}" color="danger" label="" icon="x" fontawesome size="sm" />
                                    </td>
                                </tr>
                            `);

                            $(`#btnDeleteKbcDetail${detailId}`).attr('data-testId', detailId);

                            $(`#btnDeleteKbcDetail${detailId}`).click(function() {
                                let rowIndex = $(this).attr('data-testId');

                                $(`#kbcDetail${rowIndex}`).remove();

                                if ($('#kbcTable > tbody > tr').length == 0) {
                                    $('#kbcTBody').append(`
                                        <tr class="text-center">
                                            <td colspan="6">Belum ada key behavioral competencies ditambahkan.</td>
                                        </tr>
                                    `);
                                } else {
                                    reorderIndex('kbc');
                                }

                                let kcIndex = selectedKcIds.indexOf(selectedCompetencyId);
                                if (kcIndex !== -1) {
                                    selectedKcIds.splice(kcIndex, 1);
                                }

                                selectedKbcTotal -= kcWeight;

                                calculateObcr();
                                calculateOverallRating();
                            });

                            selectedKbcTotal += kcWeight;

                            calculateObcr();
                            calculateOverallRating();
                        } else {
                            showAlert('', 'Wts Key Behavioral Competency tidak boleh lebih dari 100%!', 'warning');
                        }
                    } else {
                        if (kcWeight + parseInt(selectedKscTotal) <= 100) {
                            if (selectedKscTotal == 0) {
                                $('#kscTBody').html('');
                            }

                            let rowCount = $('#kscTable > tbody > tr').length + 1;

                            selectedKcIds.push(selectedCompetencyId);

                            $('#kscTBody').append(`
                                <tr class="ksc-detail" id="kscDetail${detailId}" data-testId="${detailId}">
                                    <td>
                                        <span id="kscRowId${detailId}">${rowCount}</span>
                                        <input type="hidden" name="type[]" value="ksc" />
                                        <input type="hidden" class="ksc-mua-id" id="kscMuaId${detailId}" name="master_user_assessment_id[]" value="${selectedCompetencyId}" />
                                    </td>
                                    <td>
                                        <span>${selectedCompetencyName}</span>
                                        <input type="hidden" class="ksc-competency-name" id="${detailId}" value="${selectedCompetencyName}" />
                                    </td>
                                    <td>
                                        <span>${kcWeight}%</span>
                                        <input type="hidden" class="ksc-weight-percentage" id="${detailId}" value="${selectedCompetencyWeight}" />
                                    </td>
                                    <td>
                                        <span>${rating}</span>
                                        <input type="hidden" class="ksc-rating" id="${detailId}" name="rating[]" value="${rating}" />
                                    </td>
                                    <td>
                                        <span>${selectedCompetencyWScore}</span>
                                        <input type="hidden" class="ksc-weight" id="ksc${detailId}" name="weight[]" value="${selectedCompetencyWScore}" />
                                    </td>
                                    <td>
                                        <x-button type="button" id="btnDeleteKscDetail${detailId}" color="danger" label="" icon="x" fontawesome size="sm" />
                                    </td>
                                </tr>
                            `);

                            $(`#btnDeleteKscDetail${detailId}`).attr('data-testId', detailId);

                            $(`#btnDeleteKscDetail${detailId}`).click(function() {
                                let rowIndex = $(this).attr('data-testId');

                                $(`#kscDetail${rowIndex}`).remove();

                                if ($('#kscTable > tbody > tr').length == 0) {
                                    $('#kscTBody').append(`
                                        <tr class="text-center">
                                            <td colspan="6">Belum ada key skill competencies ditambahkan.</td>
                                        </tr>
                                    `);
                                } else {
                                    reorderIndex('ksc');
                                }

                                let kcIndex = selectedKcIds.indexOf(selectedCompetencyId);
                                if (kcIndex !== -1) {
                                    selectedKcIds.splice(kcIndex, 1);
                                }

                                selectedKscTotal -= kcWeight;

                                calculateOscr();
                                calculateOverallRating();
                            });

                            selectedKscTotal += kcWeight;

                            calculateOscr();
                            calculateOverallRating();
                        } else {
                            showAlert('', 'Wts Key Skill Competency tidak boleh lebih dari 100%!', 'warning');
                        }
                    }

                    $('#addCompetenciesModal').modal('hide');
                }
            });

            $('#btnSave').click(function() {
                let candidate = $('#candidate').val();
                let hiringManager = $('#hiringManager').val();
                let interviewer = $('#interviewer').val();
                let recommendStatus = $('#recommendStatus').val();

                if (candidate != '' && hiringManager != '' && interviewer != '' && recommendStatus != null) {
                    if (selectedKbcTotal > 0 && selectedKscTotal > 0) {
                        $('#form').submit();
                    } else {
                        showAlert('', 'Minimal tambahkan 1 Key Behavioral Competency & Key Skill Competency!', 'warning');
                    }
                } else {
                    showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                }
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#user-assessment')
    </script>
@endsection
