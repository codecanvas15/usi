@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice-general';
    $title = 'Purchase Invoice (Non LPB)';
@endphp

@section('title', Str::headline("Create $title") . ' - ')

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
                        {{ Str::headline('Create ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form" action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('create ' . $title) }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Tanggal <span class="text-danger">*</span></label>
                            <input class="datepicker-input form-control mt-2" id="date" value="{{ date('d-m-Y') }}" onchange="checkClosingPeriod($(this))" name="date" required autofucus />
                            <span class="text-danger error_date" style="display: none"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="mb-2" for="select-vendor">Vendor <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="vendor_id" id="select-vendor" value="" required></select>
                        </div>
                        <span class="text-danger error_vendor_id" style="display: none"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="top">Term Of Payment <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="term_of_payment" id="top" value="" placeholder="Term Of Payment" readonly required />
                            <span class="text-danger error_term_of_payment" style="display: none"></span>
                        </div>
                    </div>
                    <div class="col-md-3 top-days" style="display: none">
                        <div class="form-group">
                            <label for="topDays">TOP Days <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-2" name="top_days" id="topDays" value="" placeholder="TOP Days" readonly />
                        </div>
                    </div>
                    <div class="col-md-3 due-date" style="display: none">
                        <div class="form-group">
                            <x-input type="text" name="top_due_date" label="Due Date" id="dueDate" value="" readonly required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="mb-2" for="select-currency">Currency <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="select-currency" name="currency_id" required></select>
                    </div>
                    <div class="col-md-3">
                        <x-input type="text" name="exchange_rate" class="commas-form" label="kurs" id="exchangeRate" value="" required />
                        <span class="text-muted">default IDR = 1</span>
                    </div>
                </div>
                <div class="row mt-15">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="mb-2" for="select-branch">Branch <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="branch_id" id="select-branch" required>
                                <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                            </select>
                        </div>
                        <span class="text-danger error_branch_id" style="display: none">Branch tidak boleh kosong!</span>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="project_id" id="select-project" label="project">

                            </x-select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="reference">No. Faktur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-2" name="reference" id="reference" placeholder="No. Faktur" />
                            <span class="text-danger error_reference" style="display: none">No. faktur sudah digunakan!</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="attachment" label="lampiran" id="attachment" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                        </div>
                    </div>
                </div>
                <hr class="mt-30 mb-30">
                <div class="row">
                    <div class="col-md-3">
                        <x-select name="general_detail_coa_id[]" id="vendor_coa_id" label="akun hutang" required>
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vendorCoaNote">Keterangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-2" id="vendorCoaNote" name="general_detail_notes[]" value="" placeholder="Keterangan" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vendorCoaAmount">Amount<span class="text-primary">*</span></label>
                            <input type="text" class="form-control commas-form mt-2 text-end" id="vendorCoaAmount" name="general_detail_amount[]" value="0" placeholder="Amount" readonly />
                        </div>
                    </div>
                </div>
                <h5 class="box-title fw-bold mt-20">Detail</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="select-general-coa">Account <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="select-general-coa"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="generalNote">Keterangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-2" id="generalNote" value="" placeholder="Keterangan" req />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="generalAmount">Amount<span class="text-danger">*</span></label>
                            <input type="text" class="form-control commas-form mt-2 text-end" id="generalAmount" value="" placeholder="Amount" />
                        </div>
                    </div>
                    <div class="col-auto align-self-end mb-3">
                        <button type="button" class="w-auto btn btn-md btn-primary btn-add-detail"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
                <div id="detail" class="row"></div>
                <hr class="mt-30 mb-30">
                <h5 class="box-title fw-bold">Adjustment Journal</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="select-adjustment-coa">Account <span class="text-primary">*</span></label>
                            <select class="form-control select2" id="select-adjustment-coa"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="adjustmentNote">Keterangan <span class="text-primary">*</span></label>
                            <input type="text" class="form-control mt-2" id="adjustmentNote" value="" placeholder="Keterangan" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="debit">Jumlah <span class="text-primary">*</span></label>
                            <input type="text" class="form-control commas-form mt-2 text-end" id="debit" value="" placeholder="Debit" />
                        </div>
                    </div>
                    <div class="col-auto align-self-end mb-3">
                        <button type="button" class="w-auto btn btn-md btn-primary btn-add-adjustment"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
                <div id="adjustment" class="row"></div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="Kembali" link="{{ url()->previous() }}" />
            <x-button type="submit" id="btnSave" color="primary" label="Simpan" />
        </div>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/project.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script>
        $(document).ready(function() {

            var details = [];
            var detailId = 0;
            var adjustmentId = 0;

            initSelect2SearchPagination(`vendor_coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_category: "pasiva"
            });

            checkClosingPeriod($('#date'));
            $('#date').change(function() {
                var topDays = $('#topDays').val();
                if (topDays !== '' || topDays !== null) {
                    $('#dueDate').val(addDays($(this).val(), parseInt(topDays)));
                }
            });

            $('#select-vendor').change(function() {
                if ($(this).val() !== '') {
                    $('.error_vendor_id').text('');
                    $('.error_vendor_id').hide();

                    $.ajax({
                        url: "{{ route('admin.supplier-invoice.vendor-top') }}",
                        dataType: "json",
                        delay: 250,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": token,
                        },
                        data: {
                            id: $(this).val()
                        },
                        success: function(res) {
                            if (res.data.top == 'by days') {
                                $('.error_term_of_payment').text('');
                                $('.error_term_of_payment').hide();

                                $('#top').val(res.data.top);
                                $('.top-days').show();
                                $('#topDays').val(res.data.top_days);
                                $('.due-date').show();
                                $('#dueDate').val(addDays($('#date').val(), parseInt(res.data.top_days)));
                            } else if (res.data.top == 'cash') {
                                $('.error_term_of_payment').text('');
                                $('.error_term_of_payment').hide();

                                $('#top').val(res.data.top);
                                $('.top-days').hide();
                                $('#topDays').val(null);
                                $('.due-date').hide();
                                $('#dueDate').val(null);
                            } else {
                                $('.error_term_of_payment').text('Vendor belum memiliki Term Of Payment!');
                                $('.error_term_of_payment').show();

                                $('#top').val(null);
                                $('.top-days').hide();
                                $('#topDays').val(null);
                                $('.due-date').hide();
                                $('#dueDate').val(null);
                            }
                        }
                    });

                    $.ajax({
                        url: "{{ route('admin.supplier-invoice-general.vendor-coa-id') }}",
                        dataType: "json",
                        delay: 250,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": token,
                        },
                        data: {
                            id: $(this).val()
                        },
                        success: function(res) {
                            $('#vendor_coa_id').html(`<option value="${res.data.id}">${res.data.account_code} - ${res.data.name}</option>`);
                        }
                    });
                } else {
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                    $('.error_vendor_id').text('Vendor tidak boleh kosong!');
                    $('.error_vendor_id').show();

                    $('#top').val(null);
                    $('.top-days').hide();
                    $('#topDays').val(null);
                    $('.due-date').hide();
                    $('#dueDate').val(null);
                }
            });

            $('#select-currency').change(function() {
                if ($(this).val() !== '105') {
                    $('#exchangeRate').val('');
                    $('#exchangeRate').prop('readonly', false);
                } else {
                    $('#exchangeRate').val('1');
                    $('#exchangeRate').prop('readonly', true);
                }
            });

            $('.btn-add-detail').click(function() {
                let coaId = $('#select-general-coa').val();
                let note = $('#generalNote').val();
                let amount = $('#generalAmount').val();

                if (coaId == '' || coaId == null) {
                    Swal.fire('Account belum dipilih!', '', 'warning');
                } else if (note == '') {
                    Swal.fire('Keterangan belum diisi!', '', 'warning');
                } else if (amount == 0 || amount == '') {
                    Swal.fire('Amount belum diisi!', '', 'warning');
                } else {
                    detailId++;
                    details.push(detailId);

                    let html = `<div id="detail${detailId}" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="generalDetailCoaId${detailId}" value="" readonly />
                                                <input type="hidden" class="general-detail-coa-id-${detailId}" name="general_detail_coa_id[]" value="" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="generalDetailNotes${detailId}" value="" readonly />
                                                <input type="hidden" class="general-detail-notes-${detailId}" name="general_detail_notes[]" value="" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control text-end" id="generalDetailAmount${detailId}" value="" readonly />
                                                <input type="hidden" class="general-detail-amount-${detailId}" name="general_detail_amount[]" value="" />
                                            </div>
                                        </div>
                                        <div class="col-auto align-self-end mb-3">
                                            <button type="button" class="w-auto btn btn-md btn-danger btn-delete-detail" data-id="${detailId}"><i class="fa-solid fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>`;
                    $('#detail').append(html);

                    $('.btn-delete-detail').click(function() {
                        let id = $(this).data('id');
                        let index = details.indexOf(id);
                        details.splice(index, 1);
                        $('#detail' + id).remove();

                        countDebitCredit();
                    });

                    if (coaId !== null) {
                        $.ajax({
                            url: "{{ route('admin.supplier-invoice-general.coa.code-name') }}",
                            dataType: "json",
                            delay: 250,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": token,
                            },
                            data: {
                                id: coaId
                            },
                            success: function(res) {
                                $('#generalDetailCoaId' + detailId).val(res.data.account_code + ' - ' + res.data.name);
                                $('.general-detail-coa-id-' + detailId).val(res.data.id);
                            }
                        });
                    }

                    $('#generalDetailNotes' + detailId).val(note);
                    $('.general-detail-notes-' + detailId).val(note);

                    $('#generalDetailAmount' + detailId).val(numberWithDot(replaceComma2($('#generalAmount').val())));
                    $('.general-detail-amount-' + detailId).val(replaceComma2($('#generalAmount').val()));

                    $('.btn-delete-detail').click(function() {
                        let id = $(this).data('id');

                        let index = details.indexOf(id);
                        if (index !== -1) {
                            details.splice(index, 1);
                        }

                        $(`#detail${id}`).remove();
                        countDebitCredit();
                    });

                    $('#select-general-coa').val(null).trigger('change');
                    $('#generalNote').val(null);
                    $('#generalAmount').val('');
                }

                countDebitCredit();
            });

            $('.btn-add-adjustment').click(function() {
                var coaId = $('#select-adjustment-coa').val();
                let note = $('#adjustmentNote').val();
                let debit = $('#debit').val();
                let credit = $('#credit').val();

                if (coaId == '' || coaId == null) {
                    Swal.fire('Account belum dipilih!', '', 'warning');
                } else if (note == '') {
                    Swal.fire('Keterangan belum diisi!', '', 'warning');
                } else if (debit == 0 || debit == '') {
                    if (credit == 0 || credit == '') {
                        Swal.fire('Debit/Credit belum diisi!', '', 'warning');
                    } else {
                        adjustmentId++;

                        let html = `<div id="adjustment${adjustmentId}" class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="adjustmentCoaId${adjustmentId}" value="" readonly />
                                                    <input type="hidden" class="adjustment-coa-id-${adjustmentId}" name="adjustment_coa_id[]" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="adjustmentNotes${adjustmentId}" value="" readonly />
                                                    <input type="hidden" class="adjustment-notes-${adjustmentId}" name="adjustment_notes[]" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="text" class="form-control text-end" id="adjustmentDebit${adjustmentId}" value="" readonly />
                                                    <input type="hidden" class="adjustment-debit-${adjustmentId}" name="adjustment_debit[]" value="" />
                                                </div>
                                            </div>
                                            <div class="col-auto align-self-end mb-3">
                                                <button type="button" class="w-auto btn btn-md btn-danger btn-delete-adjustment" data-id="${adjustmentId}"><i class="fa-solid fa-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>`;
                        $('#adjustment').append(html);

                        if (coaId !== null) {
                            $.ajax({
                                url: "{{ route('admin.supplier-invoice-general.coa.code-name') }}",
                                dataType: "json",
                                delay: 250,
                                type: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": token,
                                },
                                data: {
                                    id: coaId
                                },
                                success: function(res) {
                                    $('#adjustmentCoaId' + adjustmentId).val(res.data.account_code + ' - ' + res.data.name);
                                    $('.adjustment-coa-id-' + adjustmentId).val(res.data.id);
                                }
                            });
                        }

                        $('#adjustmentNotes' + adjustmentId).val(note);
                        $('.adjustment-notes-' + adjustmentId).val(note);

                        $('#adjustmentDebit' + adjustmentId).val($('#debit').val() == '' || $('#debit').val() == '0' ? '0' : numberWithDot(replaceComma2($('#debit').val())));
                        $('.adjustment-debit-' + adjustmentId).val($('#debit').val() == '' || $('#debit').val() == '0' ? '0' : replaceComma2($('#debit').val()));

                        // $('#adjustmentCredit' + adjustmentId).val($('#credit').val() == '' || $('#credit').val() == '0' ? '0' : numberWithDot(replaceComma2($('#credit').val())));
                        // $('.adjustment-credit-' + adjustmentId).val($('#credit').val() == '' || $('#credit').val() == '0' ? '0' : replaceComma2($('#credit').val()));

                        $('.btn-delete-adjustment').click(function() {
                            let id = $(this).data('id');
                            $(`#adjustment${id}`).remove();
                            countDebitCredit();
                        });

                        $('#select-adjustment-coa').val(null).trigger('change');
                        $('#adjustmentNote').val(null);
                        $('#debit').val('');
                        $('#credit').val('');
                    }
                } else {
                    adjustmentId++;

                    let html = `<div id="adjustment${adjustmentId}" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="adjustmentCoaId${adjustmentId}" value="" readonly />
                                                <input type="hidden" class="adjustment-coa-id-${adjustmentId}" name="adjustment_coa_id[]" value="" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="adjustmentNotes${adjustmentId}" value="" readonly />
                                                <input type="hidden" class="adjustment-notes-${adjustmentId}" name="adjustment_notes[]" value="" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="adjustmentDebit${adjustmentId}" value="" readonly />
                                                <input type="hidden" class="adjustment-debit-${adjustmentId}" name="adjustment_debit[]" value="" />
                                            </div>
                                        </div>
                                        <div class="col-auto align-self-end mb-3">
                                            <button type="button" class="w-auto btn btn-md btn-danger btn-delete-adjustment" data-id="${adjustmentId}"><i class="fa-solid fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>`;
                    $('#adjustment').append(html);

                    if (coaId !== null) {
                        $.ajax({
                            url: "{{ route('admin.supplier-invoice-general.coa.code-name') }}",
                            dataType: "json",
                            delay: 250,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": token,
                            },
                            data: {
                                id: coaId
                            },
                            success: function(res) {
                                $('#adjustmentCoaId' + adjustmentId).val(res.data.account_code + ' - ' + res.data.name);
                                $('.adjustment-coa-id-' + adjustmentId).val(res.data.id);
                            }
                        });
                    }

                    $('#adjustmentNotes' + adjustmentId).val(note);
                    $('.adjustment-notes-' + adjustmentId).val(note);

                    $('#adjustmentDebit' + adjustmentId).val($('#debit').val() == '' || $('#debit').val() == '0' ? '0' : numberWithDot(replaceComma2($('#debit').val())));
                    $('.adjustment-debit-' + adjustmentId).val($('#debit').val() == '' || $('#debit').val() == '0' ? '0' : replaceComma2($('#debit').val()));

                    // $('#adjustmentCredit' + adjustmentId).val($('#credit').val() == '' || $('#credit').val() == '0' ? '0' : numberWithDot(replaceComma2($('#credit').val())));
                    // $('.adjustment-credit-' + adjustmentId).val($('#credit').val() == '' || $('#credit').val() == '0' ? '0' : replaceComma2($('#credit').val()));

                    $('.btn-delete-adjustment').click(function() {
                        let id = $(this).data('id');
                        $(`#adjustment${id}`).remove();

                        countDebitCredit();
                    });

                    $('#select-adjustment-coa').val(null).trigger('change');
                    $('#adjustmentNote').val(null);
                    $('#debit').val('');
                    $('#credit').val('');
                }

                countDebitCredit();
            });

            $('#btnSave').click(function(e) {
                e.preventDefault();

                if ($('#date').val() == '') {
                    $('.error_date').text('Tanggal tidak boleh kosong!');
                    $('.error_date').show();
                } else {
                    $('.error_date').text('');
                    $('.error_date').hide();
                }

                if ($('#select-vendor').val() == '') {
                    $('.error_vendor_id').text('Vendor tidak boleh kosong!');
                    $('.error_vendor_id').show();
                } else {
                    $('.error_vendor_id').text('');
                    $('.error_vendor_id').hide();
                }

                if ($('#top').val() == '') {
                    $('.error_term_of_payment').text('Vendor belum memiliki Term Of Payment!');
                    $('.error_term_of_payment').show();
                } else {
                    $('.error_term_of_payment').text('');
                    $('.error_term_of_payment').hide();
                }

                if ($('#select-branch').val() == null) {
                    $('.error_branch_id').text('Branch tidak boleh kosong!');
                    $('.error_branch_id').show();
                } else {
                    $('.error_branch_id').text('');
                    $('.error_branch_id').hide();
                }

                if ($('#date').val() !== '' && $('#select-vendor').val() !== '' && $('#top').val() !== '' && $('#select-branch').val() !== null) {
                    if (details.length == 0) {
                        Swal.fire('Setidaknya tambahkan 1 Purchase Invoice Detail!', '', 'warning');
                    } else {
                        $('#form').submit();
                    }
                } else {
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                }
            });

            initSelect2Search('select-currency', "{{ route('admin.select.currency') }}", {
                id: "id",
                text: "kode,nama,negara"
            });

            initSelect2Search('select-vendor', "{{ route('admin.select.vendor') }}", {
                id: "id",
                text: "nama"
            });

            function addDays(inputDate, days) {
                var date = new Date(parseDate(inputDate));


                let unix_timestamp = date.setDate(date.getDate() + days);

                var date = new Date(unix_timestamp);

                var day = date.getDate();
                var month = date.getMonth() + 1;
                var year = date.getFullYear();

                if (day < 10) {
                    day = '0' + day;
                }

                if (month < 10) {
                    month = '0' + month;
                }

                return day + '-' + month + '-' + date.getFullYear();
            }

            initProjectSelect('#select-project')
            initCoaSelect('#select-general-coa');
            initCoaSelect('#select-adjustment-coa');
        });

        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#supplier-invoice-sidebar');
        sidebarActive('#supplier-invoice-general');

        function countDebitCredit() {
            var debit = 0;
            var credit = 0;

            $('input[name="general_detail_amount[]"]').each(function(index) {
                if (index != 0) {
                    debit += thousandToFloat($(this).val());
                }
            });

            $('input[name="adjustment_debit[]"]').each(function(index) {
                debit += thousandToFloat($(this).val());
            });

            // $('input[name="adjustment_credit[]"]').each(function(index) {
            //     credit += thousandToFloat($(this).val());
            // });

            $("#vendorCoaAmount").val((debit - credit)).trigger('blur');
        }
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`select-branch`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
