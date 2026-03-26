@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice';
    $title = 'Purchase Invoice (LPB)';
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
                <h3 class="box-title">{{ Str::headline($title) }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                <div class="row mb-5">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="mb-2" for="select-branch">Branch <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="branch_id" id="select-branch" value="" required>
                                <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                            </select>
                        </div>
                        <span class="text-danger error_branch_id" style="display: none">Branch tidak boleh kosong!</span>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="mb-2" for="select-vendor">Vendor <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="vendor_id" id="select-vendor" value="" required></select>
                        </div>
                        <span class="text-danger error_vendor_id" style="display: none"></span>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Tanggal Dokumen <span class="text-danger">*</span></label>
                            <input class="datepicker-input form-control mt-2" id="date" name="date" onchange="checkClosingPeriod($(this))" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required autofucus />
                            <span class="text-danger error_date" style="display: none"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Tanggal Dokumen Diterima <span class="text-danger">*</span></label>
                            <input class="datepicker-input form-control mt-2" id="accepted-doc-date" name="accepted_doc_date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required autofucus />
                            <span class="text-danger error_accepted_doc_date" style="display: none"></span>
                        </div>
                    </div>
                    <div class="col-md-3 due-date">
                        <div class="form-group">
                            <x-input type="text" name="top_due_date" label="Due Date" id="dueDate" value="" readonly required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="top">Term Of Payment <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="term_of_payment" id="top" value="" placeholder="Term Of Payment" readonly required />
                            <span class="text-danger error_term_of_payment" style="display: none"></span>
                        </div>
                    </div>
                    <div class="col-md-3 top-days">
                        <div class="form-group">
                            <label for="topDays">HARI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-2" name="top_days" id="topDays" value="" placeholder="TOP Days" readonly />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-select name="currency_id" id="currency_id" label="currency" required>
                            <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group">
                            <input type="checkbox" id="filter_purchase_order" name="filter_purchase_order" class="filled-in chk-col-primary" value="1">
                            <label for="filter_purchase_order">Filter Purchase Order</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-none" id="purchase-form">
                        <div class="form-group mb-0">
                            <label class="mb-2" for="select-po">PO <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="po" id="select-po" value="" required></select>
                            <input type="hidden" id="po_reference_id" name="po_reference_id" value="">
                            <input type="hidden" id="po_reference_model" name="po_reference_model" value="">
                            <input type="hidden" id="po_reference_kode" name="po_reference_kode" value="">
                        </div>
                    </div>
                    <div class="col-md-12 text-end">
                        <x-button type="button" color="primary" label="tampilkan LPB" id="get-lpb" />
                    </div>
                    <div class="col-md-4">
                        <x-select name="cash_advance_payment_id[]" label="Down Payment" id="cash_advance_payment_id" multiple>

                        </x-select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped mt-10 mb-10">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Tgl LPB</th>
                                            <th>Kode LPB</th>
                                            <th>No. Surat Jalan</th>
                                            <th>Item Amount</th>
                                            <th>Tax Amount</th>
                                            <th>Total Amount</th>
                                            <th>File</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lpb-list">
                                        <tr class="text-center">
                                            <td colspan="9">Belum ada LPB yang dipilih</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box mt-20">
                    <div class="box-header">
                        <h4 class="fw-bold">Total</h4>
                    </div>
                    <div class="box-body">
                        <x-table theadColor='dark'>
                            <x-slot name="table_head">
                                <th class="col-md-10"></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th class="text-end">DPP</th>
                                    <td class="text-end">
                                        <span class="span-dpp">0</span>
                                        <input type="hidden" class="dpp" name="sub_total">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-end">Tax Total</th>
                                    <td class="text-end">
                                        <span class="span-tax-total">0</span>
                                        <input type="hidden" class="tax-total" name="tax_total">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-end">Grand Total</th>
                                    <td class="text-end">
                                        <span class="span-grand-total">0</span>
                                        <input type="hidden" class="grand-total" name="grand_total">
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                </div>
                <div class="box mt-20 d-none" id="down-payment-container">
                    <div class="box-header">
                        <h4 class="fw-bold">Uang Muka</h4>
                    </div>
                    <div class="box-body">
                        <x-table theadColor='dark' id="down-payment-table">
                            <x-slot name="table_head">
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </x-slot>
                            <x-slot name="table_body">
                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('detail ' . $title) }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                <div class="row py-2 mt-2">
                    <div class="col-md-12"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reference">No. Invoice/Nota/Kwitansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-2" name="reference" id="reference" placeholder="No. Invoice/Nota/Kwitansi" required />
                            <span class="text-danger error_reference" style="display: none">No. Invoice/Nota/Kwitansi sudah digunakan!</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" required />
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reference">No. Faktur Pajak <span class="text-primary">*</span></label>
                            <input type="text" class="form-control mt-2 tax-reference-mask" name="tax_reference" id="tax-reference" placeholder="No. Faktur Pajak" />
                            <span class="text-danger error_tax_reference" style="display: none">No. faktur pajak sudah digunakan!</span>
                            <span class="text-primary">Jika nilai pajak lebih dari 0 maka wajib di isi.</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="tax_file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="Kembali" link="{{ url()->previous() }}" />
            <x-button type="submit" id="btnSave" color="primary" label="Simpan" />
        </div>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js?v=1.2') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        var cash_advance_data = [];
        $(document).ready(function() {
            checkClosingPeriod($('#date'))
            initMaskTaxReference();

            var selectedVendorId = 0;
            var lpbs = [];
            var lpbDetailIdx = 0;
            var lpbDetailModalIdx = 0;
            var dpp = 0;
            var taxTotal = 0;
            var grandTotal = 0;

            $('#select-branch').change(function(e) {
                getVendorPo();
            });

            initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
                id: "id",
                text: "kode,negara"
            });


            $('#currency_id').change(function(e) {
                e.preventDefault();

                lpbs = [];
                dpp = 0;
                taxTotal = 0;
                grandTotal = 0;

                $('#lpbDetail').find('tr').remove();
                $('#lpbDetail').append('<tr class="text-center"><td colspan="9">Belum ada LPB yang dipilih</td></tr>');

                $('.span-dpp').text(0);
                $('.span-tax-total').text(0);
                $('.span-grand-total').text(0);
                $('.dpp').val(0);
                $('.tax-total').val(0);
                $('.grand-total').val(0);

                console.log('currency id: ', this.value);

                $.ajax({
                    type: "get",
                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                    success: function({
                        data
                    }) {
                        if (data.is_local) {
                            $('#exchange_rate').val(1);
                            $('#exchange_rate').attr('readonly', 'readonly');
                        } else {
                            $('#exchange_rate').removeAttr('readonly');
                            $('#exchange_rate').attr('readonly', false);
                        }
                    }
                });
            });

            $('#date').change(function() {
                var topDays = $('#topDays').val();
                if (topDays !== '' || topDays !== null) {
                    $('#dueDate').val(addDays($(this).val(), parseInt(topDays)));
                }
            });

            $('#reference').keyup(function() {
                $.ajax({
                    url: "{{ route('admin.supplier-invoice.is-reference-exists') }}",
                    dataType: "json",
                    delay: 250,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: {
                        reference: $(this).val()
                    },
                    success: function(res) {
                        if (res.message == 'not exists') {
                            $('.error_reference').hide();
                            $('#btnSave').attr('disabled', false);
                        } else {
                            $('.error_reference').text('No. faktur sudah digunakan!');
                            $('.error_reference').show();
                            $('#btnSave').attr('disabled', true);
                        }
                    }
                });
            });

            $('#tax-reference').keyup(function() {
                $.ajax({
                    url: "{{ route('admin.supplier-invoice.is-tax-reference-exists') }}",
                    dataType: "json",
                    delay: 250,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: {
                        tax_reference: $(this).val()
                    },
                    success: function(res) {
                        if (res.message == 'not exists') {
                            $('.error_tax_reference').hide();
                            $('#btnSave').attr('disabled', false);
                        } else {
                            $('.error_tax_reference').text('No. faktur pajak sudah digunakan!');
                            $('.error_tax_reference').show();
                            $('#btnSave').attr('disabled', true);
                        }
                    }
                });
            });

            $('#select-vendor').on('change', function() {
                let vendor_id = $(this).val();

                lpbs = [];
                lpbDetailIdx = 0;

                dpp = 0;
                taxTotal = 0;
                grandTotal = 0;

                $('.dpp').val(parseFloat(dpp));
                $('.tax-total').val(parseFloat(taxTotal));
                $('.grand-total').val(parseFloat(grandTotal));
                $('.span-dpp').text(formatRupiahWithDecimal(parseFloat(dpp)));
                $('.span-tax-total').text(formatRupiahWithDecimal(parseFloat(taxTotal)));
                $('.span-grand-total').text(formatRupiahWithDecimal(parseFloat(grandTotal)));

                deleteLPB();

                if (vendor_id !== '') {
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
                            id: vendor_id
                        },
                        success: function(res) {
                            if (res.data.top == 'by days') {
                                $('.error_term_of_payment').text('');
                                $('.error_term_of_payment').hide();

                                $('#top').val(res.data.top);
                                // $('.top-days').show();
                                $('#topDays').val(res.data.top_days);
                                // $('.due-date').show();
                                $('#dueDate').val(addDays($('#date').val(), parseInt(res.data.top_days)));
                            } else {
                                $('.error_term_of_payment').text('');
                                $('.error_term_of_payment').hide();

                                $('#top').val(res.data.top);
                                // $('.top-days').hide();
                                $('#topDays').val(0);
                                // $('.due-date').hide();
                                $('#dueDate').val($('#date').val());
                            }
                        }
                    });

                    getVendorPo();
                } else {
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                    $('.error_vendor_id').text('Vendor tidak boleh kosong!');
                    $('.error_vendor_id').show();

                    $('#top').val(null);
                    // $('.top-days').hide();
                    $('#topDays').val(0);
                    // $('.due-date').hide();
                    $('#dueDate').val($('#date').val());
                }
            });

            $('#select-po').on('change', function() {
                let value = $(this).val();
                let split = value.split(',');

                $('#po_reference_id').val(split[0]);
                $('#po_reference_model').val(split[1]);
                $('#po_reference_kode').val(split[2]);

                deleteLPB();
            });

            $('#select-po').append(`<option value="" selected>- Pilih PO -</option>`);

            $('#btnSave').click(function(e) {
                e.preventDefault();

                if ($('#date').val() == '') {
                    $('.error_date').text('Tanggal tidak boleh kosong!');
                    $('.error_date').show();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                } else {
                    $('.error_date').text('');
                    $('.error_date').hide();
                }

                if ($('#select-vendor').val() == '') {
                    $('.error_vendor_id').text('Vendor tidak boleh kosong!');
                    $('.error_vendor_id').show();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                } else {
                    $('.error_vendor_id').text('');
                    $('.error_vendor_id').hide();
                }

                if ($('#select-branch').val() == null) {
                    $('.error_branch_id').text('Branch tidak boleh kosong!');
                    $('.error_branch_id').show();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                } else {
                    $('.error_branch_id').text('');
                    $('.error_branch_id').hide();
                }

                if ($('#reference').val() == '') {
                    $('.error_reference').text('No. Invoice/Nota/Kwitansi tidak boleh kosong!');
                    $('.error_reference').show();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                } else {
                    $('.error_reference').text('');
                    $('.error_reference').hide();
                }

                if (lpbs.length == 0) {
                    Swal.fire('Setidaknya pilih 1 LPB!', '', 'warning');
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                }

                if ($('#date').val() != '' && $('#select-vendor').val() != '' && $('#select-branch').val() != null && $('#reference').val() != '' && lpbs.length != 0) {
                    $('#form').submit();
                }
            });

            function initVendor() {
                $.ajax({
                    url: "{{ route('admin.supplier-invoice.get-vendor') }}",
                    type: "GET",
                    dataType: "JSON",
                    success: function(res) {
                        $('#select-vendor').append(`<option value="" selected>- Pilih vendor -</option>`);
                        $.each(res.data, function(key, value) {
                            $('#select-vendor').append(`<option value="${value.id}">${value.nama}</option>`);
                        });
                    }
                });
            }
            initVendor();

            function initCurrency() {
                $.ajax({
                    url: "{{ route('admin.supplier-invoice.get-currency') }}",
                    type: "GET",
                    dataType: "JSON",
                    success: function(res) {
                        $.each(res.data, function(key, value) {
                            $('#select-currency').append(`<option value="${value.id}">${value.kode} - ${value.nama} -  ${value.negara}</option>`);
                        });
                    }
                });
            }
            initCurrency();

            function getVendorPo() {
                let vendor_id = $('#select-vendor').val();
                let branch_id = $('#select-branch').val();

                $('#select-po').html('<option selected>- Pilih PO -</option>')
                $.ajax({
                    url: "/supplier-invoice/" + vendor_id + "/get-po",
                    type: "GET",
                    dataType: "JSON",
                    data: {
                        branch_id: branch_id,
                    },
                    success: function(res) {
                        $.each(res, function(key, value) {
                            if (value.is_has_lpb) {
                                let id = value.model_id;
                                let code = null;
                                let model = value.model_reference;
                                if (value.reference.code && value.reference.code !== '') {
                                    code = value.reference.code;
                                    $('#select-po').append(`<option value="${id + ',' + model + "," + code}">${code}</option>`);
                                } else if (value.reference.nomor_po && value.reference.nomor_po !== '') {
                                    code = value.reference.nomor_po;
                                    $('#select-po').append(`<option value="${id + ',' + model + "," + code}">${code}</option>`);
                                } else if (value.reference.kode && value.reference.kode !== '') {
                                    code = value.reference.kode;
                                    $('#select-po').append(`<option value="${id + ',' + model + "," + code}">${code}</option>`);
                                }
                            }
                        });
                    },
                    error: function(err) {
                        //
                    }
                });
            }

            function getLpb() {
                $.ajax({
                    url: "{{ route('admin.supplier-invoice.get-lpb') }}",
                    dataType: "json",
                    delay: 250,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: {
                        vendor_id: $('#select-vendor').val(),
                        branch_id: $('#select-branch').val(),
                        po_id: $('#po_reference_id').val(),
                        po_model: $('#po_reference_model').val(),
                        po_code: $('#po_reference_kode').val(),
                    },
                    success: function(res) {
                        lpbDetailModalIdx = 0;

                        if (res.data.length > 0) {
                            let isEmpty = 0;

                            let html = '';
                            $.each(res.data, function(key, value) {
                                if (value.currency_id == $('#currency_id').val()) {
                                    isEmpty++;

                                    var itemAmount = parseFloat(value.sub_total);
                                    var taxAmount = parseFloat(value.tax_total);

                                    var cash_advance_badge = '';
                                    if (value.is_has_cash_advance) {
                                        cash_advance_badge = `<br>
                                        <span class="badge bg-warning"><i class="fa fa-exclamation-circle"></i> PO Memiliki Uang Muka</span>`;
                                    }

                                    let html_file = '';
                                    if (value.file) {
                                        html_file = `<a href="{{ url('storage/') }}/${value.file}" target="_blank">File</a>`
                                    }

                                    html += `<tr id="lpb_row${key}">
                                            <td>
                                                <input type="checkbox" id="check_lpb${key}" name="check_lpb[${key}]" value="${value.id}" class="checkbox-invoice"  />
                                            </td>
                                            <td>
                                                ${lpbDetailModalIdx++ + 1}
                                                <input type="hidden" id="item_receiving_report_id${key}" value="${value.id}" class="item_receiving_report_id" name="item_receiving_report_id[${key}]" />
                                            </td>
                                            <td>
                                                ${localDate(value.date_receive)}
                                                <input type="hidden" id="date_receive${key}" value="${localDate(value.date_receive)}" class="date_receive" name="date_receive[${key}]" />
                                            </td>
                                            <td>
                                                ${value.kode}
                                                 ${cash_advance_badge}
                                                <input type="hidden" id="addLpbModalCode${key}" value="${value.kode}" />
                                            </td>
                                            <td>
                                                ${value.do_code_external ?? '-'}
                                            </td>
                                            <td class="text-end">
                                                ${formatRupiahWithDecimal(itemAmount)}
                                                <input type="hidden" id="item_amount${key}" value="${itemAmount}" class="item_sub_total" name="item_sub_total[${key}]" />
                                            </td>
                                            <td class="text-end">
                                                ${formatRupiahWithDecimal(taxAmount)}
                                                <input type="hidden" id="tax_amount${key}" value="${taxAmount}" class="item_tax" name="item_tax[${key}]" />
                                            </td>
                                            <td class="text-end">
                                                ${formatRupiahWithDecimal(itemAmount + taxAmount)}
                                                <input type="hidden" id="total_amount${key}" value="${itemAmount + taxAmount}" class="item_total" name="item_total[${key}]" />
                                            </td>
                                            <td>
                                                ${html_file}
                                            </td>
                                        </tr>`;
                                }
                            });

                            $('#lpb-list').html(html);

                            $('.checkbox-invoice').each(function() {
                                $(this).css('position', 'unset').css('left', '0').css('opacity', 1);
                                $(this).on('click', function() {
                                    checkSelectedLpb();
                                });
                            })

                        } else {
                            $('#lpb-list').html('<tr><td colspan="9" class="text-center">Belum ada LPB.</td></tr>');
                            checkSelectedLpb();
                        }
                    }
                });
            }

            function deleteLPB() {
                lpbDetailIdx = 0;
                checkSelectedLpb();
                $('#lpb-list').html('<tr class="text-center"><td colspan="9">Belum ada LPB yang dipilih</td></tr>');
            }

            $('#filter_purchase_order').click(function() {
                if ($(this).is(':checked')) {
                    $('#purchase-form').removeClass('d-none');
                    deleteLPB();
                    getVendorPo();

                } else {
                    $('#purchase-form').addClass('d-none');

                    $('#po_reference_id').val('');
                    $('#po_reference_model').val('');
                    $('#po_reference_kode').val('');

                    $('#select-po').html(`<option value="" selected>- Pilih PO -</option>`);
                    deleteLPB();
                }
            })

            $('#get-lpb').click(function() {
                getLpb();
            });

            function checkSelectedLpb() {
                dpp = 0;
                taxTotal = 0;
                grandTotal = 0;
                lpbs = [];
                $('.checkbox-invoice').each(function() {
                    if ($(this).is(':checked')) {
                        dpp += parseFloat($(this).closest('tr').find('input[class="item_sub_total"]').val());
                        taxTotal += parseFloat($(this).closest('tr').find('input[class="item_tax"]').val());
                        grandTotal += parseFloat($(this).closest('tr').find('input[class="item_total"]').val());

                        lpbs.push($(this).val());
                    }
                });

                $('.span-dpp').text(formatRupiahWithDecimal(dpp));
                $('.span-tax-total').text(formatRupiahWithDecimal(taxTotal));
                $('.span-grand-total').text(formatRupiahWithDecimal(grandTotal));
                $('.dpp').val(dpp);
                $('.tax-total').val(taxTotal);
                $('.grand-total').val(grandTotal);
            }

            function addDays(date, days) {
                var date = new Date(convertLocalDate(date));

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

            sidebarMenuOpen('#finance-main-sidebar');
            sidebarActive('#supplier-invoice-sidebar');
            sidebarActive('#supplier-invoice');
        });

        $('#form').submit(function(e) {
            e.preventDefault();

            let error = 0;
            if ($('input[name="tax_total"]').val() > 0) {
                let errMsg = "";

                // if ($('input[name="tax_reference"]').val() == "") {
                //     error++;
                //     errMsg = "Maaf faktur pajak harus diisi";
                // }

                // if ($('input[name="tax_file"]').val() == "" || $('input[name="tax_file"]').val() == null) {
                //     error++;
                //     errMsg = "Maaf file faktur pajak harus diisi";
                // }

                if (error > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '',
                        text: errMsg,
                    });

                    setTimeout(() => {
                        $('#btnSave').attr('disabled', false);
                    }, 1000);
                }
            }

            if (error == 0) {
                $(this).unbind('submit').submit();
            }
        });

        initSelect2SearchPaginationData(`cash_advance_payment_id`, `{{ route('admin.select.supplier-invoice.cash-advance') }}`, {
            id: 'id',
            text: 'code'
        }, 0, {
            vendor_id: function() {
                return $('#select-vendor').val();
            },
            accepted_doc_date: function() {
                return $('#accepted-doc-date').val();
            },
            currency_id: function() {
                return $('#currency_id').val();
            },
            date: function() {
                return $('#date').val();
            }
        }, 0, false);

        function fetchCashAdvanceDetail() {
            $.ajax({
                url: `${base_url}/cash-advance-payment/detail`,
                method: "POST",
                data: {
                    '_token': token,
                    'id': $('#cash_advance_payment_id').val()
                },
                success: function(res) {
                    cash_advance_data = res.data;
                    displayCashAdvance();
                }
            });
        }

        $('#accepted-doc-date').on('change', function() {
            $('#cash_advance_payment_id').val('').trigger('change');
        });

        function displayCashAdvance() {
            $('#down-payment-table tbody').html('');
            if (cash_advance_data.length > 0) {
                $('#down-payment-container').removeClass('d-none');

                $.each(cash_advance_data, function(key, value) {
                    $('#down-payment-table tbody').append(`
                        <tr>
                            <td>${localDate(value.date)}</td>
                            <td>${formatRupiahWithDecimal(value.cash_advance_cash_advance.debit)}</td>
                            <td>${value.reference}</td>
                        </tr>
                    `);
                })

            } else {
                $('#down-payment-container').addClass('d-none');
            }
        }

        $('#cash_advance_payment_id').change(debounceGlobal(fetchCashAdvanceDetail, 300));
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
