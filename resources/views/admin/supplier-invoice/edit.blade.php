@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice';
    $title = 'Purchase Invoice (LPB)';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

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
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form" action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('Edit ' . $title) }} - {{ $model->code }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                <div class="row mb-5">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <x-input type="text" name="branch" label="branch" id="branch" value="{{ ucwords($model->branch->name) }}" readonly required />
                            <input type="hidden" name="branch_id" id="select-branch" value="{{ $model->branch_id }}">
                            <input type="hidden" name="supplier_invoice_id" id="supplier_invoice_id" value="{{ $model->id }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <input type="hidden" name="vendor_id" id="select-vendor" value="{{ $model->vendor_id }}">
                            <x-input type="text" name="vendor_name" label="Vendor" id="vendor" value="{{ ucwords($model->vendor->nama) }}" readonly required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" class="datepicker-input" name="date" onchange="checkClosingPeriod($(this))" label="Tgl. Dokumen" id="date" value="{{ localDate($model->date) }}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" class="datepicker-input" name="accepted_doc_date" label="Tgl. Dokumen Diterima" id="accepted-doc-date" value="{{ localDate($model->accepted_doc_date) }}" required />
                        </div>
                    </div>
                    <div class="col-md-3 due-date">
                        <div class="form-group">
                            <x-input type="text" name="top_due_date" label="Due Date" id="dueDate" value="{{ localDate($model->top_due_date) }}" readonly required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="term_of_payment" label="Term Of Payment" id="top" value="{{ ucwords($model->term_of_payment) }}" readonly required />
                        </div>
                    </div>
                    <div class="col-md-1 top-days">
                        <div class="form-group">
                            <x-input type="text" name="top_days" label="TOP" id="topDays" value="{{ $model->top_days ?? 0 }}" readonly required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-input type="text" name="currency" label="Currency" id="currency" value="{{ $model->currency->kode }} - {{ $model->currency->nama }}" readonly required />
                    </div>
                    <div class="col-md-3">
                        @if ($model->currency->is_local)
                            <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                        @else
                            <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
                        @endif
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group">
                            @if ($model->po_reference_id)
                                <input type="checkbox" id="filter_purchase_order" name="filter_purchase_order" class="filled-in chk-col-primary" value="1" checked>
                            @else
                                <input type="checkbox" id="filter_purchase_order" name="filter_purchase_order" class="filled-in chk-col-primary" value="1">
                            @endif
                            <label for="filter_purchase_order">Filter Purchase Order</label>
                        </div>
                    </div>
                    <div class="col-md-3 {{ $model->po_reference_id ? '' : 'd-none' }}" id="purchase-form">
                        <div class="form-group mb-0">
                            <label class="mb-2" for="select-po">PO <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="po" id="select-po" value="" required>
                                @if ($model->po_reference_id)
                                    <option value="{{ $model->po_reference_id }},{{ $model->po_reference_model ?? '' }},{{ $model->po_reference_kode ?? '' }}" selected>{{ $model->po_reference_kode }}</option>
                                @endif
                            </select>
                            <input type="hidden" id="po_reference_id" name="po_reference_id" value="{{ $model->po_reference_id ?? '' }}">
                            <input type="hidden" id="po_reference_model" name="po_reference_model" value="{{ $model->po_reference_model ?? '' }}">
                            <input type="hidden" id="po_reference_kode" name="po_reference_kode" value="{{ $model->po_reference_kode ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-12 text-end">
                        <x-button type="button" color="primary" label="tampilkan LPB" id="get-lpb" />
                    </div>
                    <div class="col-md-4">
                        <x-select name="cash_advance_payment_id[]" label="Down Payment" id="cash_advance_payment_id" multiple>
                            @foreach ($model->supplier_invoice_down_payments as $supplier_invoice_down_payment)
                                <option value="{{ $supplier_invoice_down_payment->cash_advance_payment_id }}" selected>{{ $supplier_invoice_down_payment->cash_advance_payment->bank_code_mutation }}</option>
                            @endforeach
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
                                        @foreach ($model->detail as $key => $detail)
                                            <tr id="lpb{{ $detail->item_receiving_report->id }}">
                                                <td>
                                                    <input type="checkbox" id="check_lpb{{ $detail->item_receiving_report->id }}" name="check_lpb[{{ $key }}]" value="{{ $detail->item_receiving_report->id }}" checked class="checkbox-invoice" />
                                                </td>
                                                <td>
                                                    <span id="spanLpbNo{{ $detail->item_receiving_report->id }}">{{ $key + 1 }}</span>
                                                    <input type="hidden" id="lpbId{{ $detail->item_receiving_report->id }}" value="{{ $detail->item_receiving_report->id }}" class="item_receiving_report_id" name="item_receiving_report_id[{{ $key }}]" />
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($detail->item_receiving_report->date_receive)->format('d-m-Y') }}
                                                    <input type="hidden" id="lpbDate{{ $detail->item_receiving_report->id }}" value="{{ $detail->item_receiving_report->date_receive }}" class="item_receiving_report_date" name="item_receiving_report_date[{{ $key }}]" />
                                                </td>
                                                <td>
                                                    {{ $detail->item_receiving_report->kode }}
                                                    <input type="hidden" id="lpbCode{{ $detail->item_receiving_report->id }}" value="{{ $detail->item_receiving_report->kode }}" class="item_receiving_report_kode" name="item_receiving_report_kode[{{ $key }}]" />
                                                    @if ($detail->item_receiving_report->tipe == 'general')
                                                        <input type="hidden" id="lpbPo{{ $detail->item_receiving_report->id }}" value="{{ $detail->reference->kode }}" class="po_kode" name="po_kode[{{ $key }}]" />
                                                    @elseif ($detail->item_receiving_report->tipe == 'jasa')
                                                        <input type="hidden" id="lpbPo{{ $detail->item_receiving_report->id }}" value="{{ $detail->reference->kode }}" class="po_kode" name="po_kode[{{ $key }}]" />
                                                    @elseif ($detail->item_receiving_report->tipe == 'trading')
                                                        <input type="hidden" id="lpbPo{{ $detail->item_receiving_report->id }}" value="{{ $detail->reference->no_po }}" class="po_kode" name="po_kode[{{ $key }}]" />
                                                    @else
                                                        <input type="hidden" id="lpbPo{{ $detail->item_receiving_report->id }}" value="{{ $detail->reference->kode }}" class="po_kode" name="po_kode[{{ $key }}]" />
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $detail->item_receiving_report->do_code_external }}
                                                </td>
                                                <td>
                                                    {{ formatNumber($detail->sub_total) }}
                                                    <input type="hidden" id="lpbItemAmount{{ $detail->item_receiving_report->id }}" value="{{ $detail->sub_total }}" class="item_sub_total" name="item_sub_total[{{ $key }}]" />
                                                </td>
                                                <td>
                                                    {{ formatNumber($detail->tax) }}
                                                    <input type="hidden" id="lpbTaxAmount{{ $detail->item_receiving_report->id }}" value="{{ $detail->tax }}" class="item_tax" name="item_tax[{{ $key }}]" />
                                                </td>
                                                <td>
                                                    {{ formatNumber($detail->total) }}
                                                    <input type="hidden" id="lpbTotal{{ $detail->item_receiving_report->id }}" value="{{ $detail->total }}" class="item_total" name="item_total[{{ $key }}]" />
                                                </td>
                                                <td>
                                                    @if ($detail->item_receiving_report->file)
                                                        <a href="{{ url('storage/' . $detail->item_receiving_report->file) }}" target="_blank">File</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" class="currency-id" id="currency_id" class="currency_id" name="currency_id" value="{{ $model->currency_id }}">
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
                                        <span class="span-dpp">{{ $model->currency->simbol }} {{ formatNumber($model->sub_total) }}</span>
                                        <input type="hidden" class="dpp" name="sub_total" value="{{ $model->sub_total }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-end">Tax Total</th>
                                    <td class="text-end">
                                        <span class="span-tax-total">{{ $model->currency->simbol }} {{ formatNumber($model->tax_total) }}</span>
                                        <input type="hidden" class="tax-total" name="tax_total" value="{{ $model->tax_total }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-end">Grand Total</th>
                                    <td class="text-end">
                                        <span class="span-grand-total">{{ $model->currency->simbol }} {{ formatNumber($model->grand_total) }}</span>
                                        <input type="hidden" class="grand-total" name="grand_total" value="{{ $model->grand_total }}">
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
                <div class="row py-2 mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="reference" label="No. Invoice/Nota/Kwitansi" id="reference" value="{{ $model->reference }}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                        </div>
                    </div>
                    <div class="col-md-2 align-items-center d-flex">
                        @if ($model->file)
                            <x-button color="info" link="{{ url('storage/' . $model->file) }}" size="sm" label="Show File" fontawesome target="_blank" />
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="tax_reference" class="tax-reference-mask" label="No. Faktur Pajak" id="tax-reference" value="{{ $model->tax_reference }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="tax_file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                        </div>
                    </div>
                    <div class="col-md-2 align-items-center d-flex">
                        @if ($model->tax_file)
                            <x-button color="info" class="show-tax-file" link="{{ url('storage/' . $model->tax_file) }}" size="sm" label="Show File" fontawesome target="_blank" />
                        @endif
                    </div>
                </div>
                <div class="row border-top py-2 mt-2">

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

            $('#date').change(function() {
                var topDays = $('#topDays').val();
                if (topDays !== '' || topDays !== null) {
                    $('#dueDate').val(addDays($(this).val(), parseInt(topDays)));
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

            $('#select-po').append(`<option value="">- Pilih PO -</option>`);

            $('#btnSave').click(function(e) {
                e.preventDefault();

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
                checkSelectedLpb();
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
                        supplier_invoice_id: '{{ $model->id }}'
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

                                    let checked = value.checked ?? '';

                                    html += `<tr id="lpb_row${key}">
                                            <td>
                                                <input type="checkbox" id="check_lpb${key}" name="check_lpb[${key}]" value="${value.id}" class="checkbox-invoice" ${checked}  />
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

                            checkSelectedLpb();
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

            checkSelectedLpb();

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
                    'id': [$('#cash_advance_payment_id').val()]
                },
                success: function(res) {
                    cash_advance_data = res.data;
                    displayCashAdvance();
                }
            });
        }

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
@endsection
