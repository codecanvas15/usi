@extends('layouts.admin.layout.index')

@php
    $main = 'hrd-assessment';
    $title = 'Interview HRD';
    $id_hrd = 3; // HRD ID
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="Tanggal" id="date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="interviewer" label="Interviewer" id="interviewer" hasError errorBorderId="errorSelectInterviewer" errorMessageId="errorMsgSelectInterviewer" errorMsg="Interviewer wajib dipilih." required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="candidate" label="Kandidat" id="candidate" hasError errorBorderId="errorSelectCandidate" errorMessageId="errorMsgSelectCandidate" errorMsg="Kandidat wajib dipilih." required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="position" label="Posisi" id="position" hasError errorBorderId="errorSelectPosition" errorMessageId="errorMsgSelectPosition" errorMsg="Posisi wajib dipilih." required />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="box-title">{{ Str::headline('Assessment Detail') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div id="assessmentDetail"></div>
                    <div class="row justify-content-center">
                        <div class="col-auto align-self-center">
                            <x-button type="button" class="mb-30" id="btnAddDetail" color="primary" label="Tambah Detail Baru" />
                        </div>
                    </div>
                    <div class="row border-top border-primary pt-20">
                        <h4><b>{{ Str::headline('Ringkasan') }}</b></h4>
                    </div>
                    <div class="row py-10">
                        <div class="col-md-4">
                            <p class="fw-bold mb-0">Kesan dan Rekomendasi Secara Keseluruhan</p>
                            <p class="mb-4">Ringkasan persepsi Anda tentang kekuatan/kelemahan kandidat.</p>
                        </div>
                        <div class="col-md-3">
                            <div id="errorSelectStatus" class="border border-danger">
                                <select class="form-control select2" id="assessmentStatus" name="assessment_status" required>
                                    <option value="" selected disabled>-- pilih hasil ringkasan --</option>
                                    <option value="y">Lanjut Tahap 2</option>
                                    <option value="r">Lanjut Dengan Reservasi</option>
                                    <option value="n">Tidak Lanjut</option>
                                </select>
                            </div>
                            <span id="errorMsgSelectStatus" class="text-danger mt-1">Hasil ringkasan wajib dipilih.</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-text-area name="notes" label="Komentar" id="notes" value="" />
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="division-selectForm" value="{{ $id_hrd }}">

                <div class="box-footer text-end">
                    <x-button id="btnBack" color="secondary" label="kembali" icon="x" fontawesome size="sm" />
                    <x-button id="btnSave" color="primary" label="simpan" icon="save" fontawesome size="sm" />
                </div>
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script>
        $(document).ready(function() {
            checkClosingPeriod($('#date'))
            let previousUrl = "{{ URL::previous() }}"
            let detailId = 0;

            let titles = 0;
            let ratings = 0;

            const isError = () => {
                if (!$('#interviewer').val() || !$('#candidate').val() || !$('#position').val() || !$('#assessmentStatus').val()) {
                    showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                } else {
                    $('.select-title-detail').each(function() {
                        if (!$(this).val()) {
                            titles++;
                        }
                    });

                    $('.rating-detail').each(function() {
                        if (!$(this).val()) {
                            ratings++;
                        }
                    });

                    if (titles > 0 || ratings > 0) {
                        showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                    } else {
                        $('#btnSave').attr('disabled', false);
                        $('#form').submit();
                    }
                }
            }

            initSelectEmployee('#interviewer', null, false);

            $('#interviewer').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectInterviewer').removeClass('border border-danger');
                    $('#errorMsgSelectInterviewer').html('');
                }
            });

            const initSelectCandidate = (element, modal_id = null) => {
                var select2Option = {
                    dropdownParent: modal_id ? $(modal_id) : null,
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
                        url: `${base_url}/select/hrd-assessment/select-candidate`,
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

            $('#candidate').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectCandidate').removeClass('border border-danger');
                    $('#errorMsgSelectCandidate').html('');
                }
            });

            initSelect2Search('position', "{{ route('admin.select.position') }}", {
                id: "id",
                text: "nama"
            });

            $('#position').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectPosition').removeClass('border border-danger');
                    $('#errorMsgSelectPosition').html('');
                }
            });

            const initSelect2Custom = (id) => {
                let selectedTitleIds = [];

                $('.select-title-detail').each(function() {
                    if ($(this).val()) {
                        selectedTitleIds.push($(this).val());
                    }
                });

                let selectTaxOpts = {
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
                        url: `${base_url}/select/master-hrd-assessment`,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            result["not_in_ids"] = selectedTitleIds;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: data.title,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };

                $(`#select-title-detail${id}`).select2(selectTaxOpts);
            }

            const initDetail = () => {
                $('#assessmentDetail').append(`
                    <div class="row pb-20" id="detail0">
                        <div class="col-md-6">
                            <div class="col-12">
                                <x-select type="text" class="select-title-detail" id="select-title-detail0" hasError errorBorderId="errorSelectDetail0" errorMessageId="errorMsgSelectDetail0" errorMsg="" name="master_hrd_assessment_id[]" label="Pilih Judul" required />
                            </div>
                            <div class="col-12">
                                <label for="">Deskripsi</label>
                                <p id="description-detail0" class="mt-1">Harap pilih judul terlebih dahulu!</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="mb-2">Rating <span class="text-danger">*</span></label>
                                <div id="errorRatingDetail0" class="border border-danger">
                                    <select class="form-control select2 rating-detail" id="rating0" name="rating[]">
                                        <option value="" selected>-- pilih rating --</option>
                                        <option value="1">1 - Unsatisfactory</option>
                                        <option value="2">2 - Satisfactory</option>
                                        <option value="3">3 - Average</option>
                                        <option value="4">4 - Above Average</option>
                                        <option value="5">5 - Exceptional</option>
                                    </select>
                                </div>
                                <div id="errorMsgRating0" class="text-danger mt-1">Rating wajib diisi.</div>
                            </div>
                            <div class="form-group">
                                <x-text-area name="detail_notes[]" label="Komentar" id="notes" value="" />
                            </div>
                        </div>
                    </div>
                `);

                initSelect2Custom(0);

                $('#select-title-detail0').change(function() {
                    let value = $(this).val();
                    if (value) {
                        $('#errorSelectDetail0').removeClass('border border-danger');
                        $('#errorMsgSelectDetail0').html('');

                        $.ajax({
                            type: "post",
                            url: `${base_url}/master-hrd-assessment/get-data-by-id`,
                            dataType: 'json',
                            data: {
                                _token: token,
                                id: value,
                            },
                            success: function(data) {
                                $('#description-detail0').html(data.description);
                            }
                        });
                    } else {
                        $('#errorSelectDetail0').addClass('border border-danger');
                        $('#errorMsgSelectDetail0').addClass('text-danger');
                    }
                });

                $('#rating0').select2();

                $('#rating0').change(function() {
                    let value = $(this).val();
                    if (value) {
                        $('#errorRatingDetail0').removeClass('border border-danger');
                        $('#errorMsgRating0').html('');
                    } else {
                        $('#errorRatingDetail0').addClass('border border-danger');
                        $('#errorMsgRating0').addClass('text-danger');
                    }
                });
            }
            initDetail();

            const addDetail = (id) => {
                $('#assessmentDetail').append(`
                    <div id="detail${id}" class="row py-20 border-primary border-top">
                        <div class="col-md-6">
                            <div class="col-12">
                                <x-select type="text" class="select-title-detail" id="select-title-detail${id}" label="Pilih Judul" name="master_hrd_assessment_id[]" hasError errorBorderId="errorSelectDetail${id}" errorMessageId="errorMsgSelectDetail${id}" errorMsg="" required />
                            </div>
                            <div class="col-12">
                                <label for="">Deskripsi</label>
                                <p id="description-detail${id}" class="mt-1">Harap pilih judul terlebih dahulu!</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="mb-2">Rating <span class="text-danger">*</span></label>
                                <div id="errorRatingDetail${id}" class="border border-danger">
                                    <select class="form-control select2 rating-detail" id="rating${id}" name="rating[]">
                                        <option value="" disabled selected>-- pilih rating --</option>
                                        <option value="1">1 - Unsatisfactory</option>
                                        <option value="2">2 - Satisfactory</option>
                                        <option value="3">3 - Average</option>
                                        <option value="4">4 - Above Average</option>
                                        <option value="5">5 - Exceptional</option>
                                    </select>
                                </div>
                                <div id="errorMsgRating${id}" class="text-danger mt-1">Rating wajib diisi.</div>
                            </div>
                            <div class="form-group">
                                <x-text-area name="detail_notes[]" label="Komentar" id="notes" value="" />
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <x-button type="button" id="delete-detail${id}" class="mt-10" color="danger" label="Hapus" icon="x" fontawesome size="sm" />
                            </div>
                        </div>
                    </div>
                `);

                $([document.documentElement, document.body]).animate({
                    scrollTop: $(`#detail${id}`).offset().top
                }, "fast");

                initSelect2Custom(id);

                $(`#errorMsgSelectDetail${id}`).html('Judul wajib diisi.');

                $(`#select-title-detail${id}`).change(function() {
                    let value = $(this).val();
                    if (value) {
                        $(`#errorSelectDetail${id}`).removeClass('border border-danger');
                        $(`#errorMsgSelectDetail${id}`).html('');

                        $.ajax({
                            type: "post",
                            url: `${base_url}/master-hrd-assessment/get-data-by-id`,
                            dataType: 'json',
                            data: {
                                _token: token,
                                id: value,
                            },
                            success: function(data) {
                                $(`#description-detail${id}`).html(data.description);
                            }
                        });
                    } else {
                        $(`#errorSelectDetail${id}`).addClass('border border-danger');
                        $(`#errorMsgSelectDetail${id}`).addClass('text-danger');
                    }
                });

                $(`#rating${id}`).select2();

                $(`#rating${id}`).change(function() {
                    let value = $(this).val();
                    if (value) {
                        $(`#errorRatingDetail${id}`).removeClass('border border-danger');
                        $(`#errorMsgRating${id}`).html('');
                    } else {
                        $(`#errorRatingDetail${id}`).addClass('border border-danger');
                        $(`#errorMsgRating${id}`).addClass('text-danger');
                    }
                });

                // $(`#add-another-detail${id}`).click(function(){
                //     $(this).hide();
                //     detailId++;
                //     addDetail(detailId);
                // });

                $(`#delete-detail${id}`).click(function() {
                    $(`#detail${id}`).remove();
                    titles--;
                    ratings--;
                });
            }

            $('#assessmentStatus').change(function() {
                let value = $(this).val();
                if (value) {
                    $(`#errorSelectStatus`).removeClass('border border-danger');
                    $(`#errorMsgSelectStatus`).html('');
                } else {
                    $(`#errorSelectStatus`).addClass('border border-danger');
                    $(`#errorMsgSelectStatus`).addClass('text-danger');
                }
            });

            $('#btnAddDetail').click(function() {
                detailId++;
                addDetail(detailId);
            });

            $('#btnBack').click(function() {
                window.location.href = previousUrl;
            });

            $('#btnSave').click(function() {
                isError();
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#hrd-assessment')
    </script>
@endsection
