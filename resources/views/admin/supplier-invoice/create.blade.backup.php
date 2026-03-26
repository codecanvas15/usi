@extends('layouts.admin.layout.index')

@php
$main = 'supplier-invoice';
$title = 'Tagihan Supplier';
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
<form id="form" action="{{ route("admin.$main.store") }}" method="post">
    @csrf
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{ Str::headline('create ' . $title) }}</h3>
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
                    <div class="form-group">
                        <label for="date">Tanggal <span class="text-danger">*</span></label>
                        <input class="datepicker-input" class="form-control mt-2" id="date" name="date" onchange="checkClosingPeriod($(this))" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required autofucus />
                        <span class="text-danger error_date" style="display: none"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date">Tanggal Dokumen Diterima <span class="text-danger">*</span></label>
                        <input class="datepicker-input" class="form-control mt-2" id="accepted-doc-date" name="accepted_doc_date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required autofucus />
                        <span class="text-danger error_accepted_doc_date" style="display: none"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="mb-2" for="select-vendor">Vendor <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="vendor_id" id="select-vendor" value="" required></select>
                    </div>
                    <span class="text-danger error_vendor_id" style="display: none"></span>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="mb-2" for="top">Term Of Payment <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="term_of_payment" id="top" value="" placeholder="Term Of Payment" readonly required />
                        <span class="text-danger error_term_of_payment" style="display: none"></span>
                    </div>
                </div>
                <div class="col-md-1 top-days" style="display: none">
                    <div class="form-group">
                        <label for="topDays">HARI <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mt-2" name="top_days" id="topDays" value="" placeholder="TOP Days" readonly />
                    </div>
                </div>
                <div class="col-md-2 due-date" style="display: none">
                    <div class="form-group">
                        <x-input type="text" name="top_due_date" label="Due Date" id="dueDate" value="" readonly required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="reference">No. Invoice/Nota/Kwitansi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mt-2" name="reference" id="reference" placeholder="No. Invoice/Nota/Kwitansi" required />
                        <span class="text-danger error_reference" style="display: none">No. Invoice/Nota/Kwitansi sudah digunakan!</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="reference">No. Faktur Pajak <span class="text-primary">*</span></label>
                        <input type="text" class="form-control mt-2" name="tax_reference" id="tax-reference" placeholder="No. Faktur Pajak" />
                        <span class="text-danger error_tax_reference" style="display: none">No. faktur pajak sudah digunakan!</span>
                        <span class="text-primary">Jika nilai pajak lebih dari 0 maka wajib di isi.</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <x-select name="currency_id" id="currency_id" label="currency" required>
                        <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                    </x-select>
                </div>
                <div class="col-md-3">
                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="lpb-details" class="col-md-12 table-responsive"></div>
    </div>
    <div class="box">
        <div class="box-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="box-title fw-bold">LPB</h5>
                </div>
                <div class="col-auto">
                    <x-button type="button" color="primary" id="btn-add-lpb" label="+ LPB" />
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-12">
                    <table class="table table-striped mt-10 mb-10">
                        <thead class="bg-dark">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Item Amount</th>
                                <th>Tax Amount</th>
                                <th>Total</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="lpbDetail">
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
    <div class="d-flex justify-content-end gap-3">
        <x-button type="reset" color="secondary" label="Kembali" link="{{ url()->previous() }}" />
        <x-button type="submit" id="btnSave" color="primary" label="Simpan" />
    </div>
</form>
<x-modal title="Pilih LPB" id="add-lpb-modal" headerColor="danger" modalSize="1000">
    <x-slot name="modal_body">
        <div class="row table-responsive mt-3">
            <div class="col-md-12">
                <table class="table table-striped mt-10 mb-10">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>LPB No.</th>
                            <th>PO No.</th>
                            <th>Total Amount</th>
                            <th class="text-center">Pilih</th>
                        </tr>
                    </thead>
                    <tbody id="lpbDetailModal">
                        <tr class='text-center'>
                            <td colspan='6'>Harap pilih currency terlebih dahulu.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>
    <x-slot name="modal_footer">
        <x-button type="button" color="secondary" dataDismiss="modal" label="Batal" />
        <x-button type="button" class="btn-select-lpb-modal" color="primary" label="Pilih" disabled />
    </x-slot>
</x-modal>
<x-modal title="Edit LPB" id="edit-lpb-modal" headerColor="danger">
    <x-slot name="modal_body">
        <div class="col-md-12">
            <div class="form-group">
                <x-input type="text" id="editLpbModalDate" label="Tanggal" required readonly />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <x-input type="text" id="editLpbModalCode" label="LPB No." required readonly />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <x-input type="text" id="editLpbModalPo" label="PO No." required readonly />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <x-input type="text" id="editLpbModalItemAmount" label="Item Amount" required readonly />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <x-input type="text" id="editLpbModalTotal" label="Total Amount" required readonly />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <x-input type="text" id="editLpbModalTaxAmount" label="Tax Amount" required readonly />
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <x-text-area type="text" id="editLpbModalNote" label="Keterangan" rows="3" />
            </div>
        </div>
    </x-slot>
    <x-slot name="modal_footer">
        <x-button type="button" color="secondary" dataDismiss="modal" label="Batal" />
        <x-button type="button" class="btn-update-lpb-modal" color="primary" label="Simpan" />
    </x-slot>
</x-modal>
@endsection

@section('js')
<script src="{{ asset('js/helpers/helpers.js') }}"></script>
<script src="{{ asset('js/form/select2search.js') }}"></script>
<script>
    $(document).ready(function() {
        checkClosingPeriod($('#date'))
        var selectedVendorId = 0;
        var lpbs = [];
        var lpbDetailIdx = 0;
        var lpbDetailModalIdx = 0;
        var dpp = 0;
        var taxTotal = 0;
        var grandTotal = 0;

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

        $('#add-lpb-modal').on('shown.bs.modal', function(e) {
            // initCurrency();
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
            lpbs = [];
            lpbDetailIdx = 0;

            $('#lpbDetail').find('tr').remove();
            $('#lpbDetail').append('<tr class="text-center"><td colspan="9">Belum ada LPB yang dipilih</td></tr>');

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
                        } else {
                            $('.error_term_of_payment').text('');
                            $('.error_term_of_payment').hide();

                            $('#top').val(res.data.top);
                            $('.top-days').hide();
                            $('#topDays').val(null);
                            $('.due-date').hide();
                            $('#dueDate').val(null);
                        }
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

        $('#btn-add-lpb').click(function(e) {
            let vendorId = $('#select-vendor').val();
            let currency = $('#select-currency').val();

            if (vendorId == '') {
                Swal.fire('Vendor belum dipilih!', '', 'warning');
            } else if (currency == '') {
                Swal.fire('Currency belum dipilih!', '', 'warning');
            } else {
                $('#add-lpb-modal').modal('show');
                getLpb();
            }
        });

        $('.btn-select-lpb-modal').click(function() {
            let lpbDetail = $('#lpbDetailModal').find('input[type=checkbox]:checked');

            if (lpbDetail.length > 0 && selectedVendorId !== $('#select-vendor').val()) {
                selectedVendorId = $('#select-vendor').val();
            }

            lpbDetail.each(function() {
                var lpbKode = $(this).attr('data-lpb');

                let indexOfLpb = lpbs.indexOf(lpbKode);
                if (indexOfLpb == -1) {
                    lpbDetailIdx++;

                    if (lpbDetailIdx == 1) {
                        $('#lpbDetail').find('tr').remove();
                    }

                    let lpbId = $('#addLpbModalId' + lpbKode).val();
                    let lpbDate = $('#addLpbModalDate' + lpbKode).val();
                    let lpbCode = $('#addLpbModalCode' + lpbKode).val();
                    let lpbPo = $('#addLpbModalPo' + lpbKode).val();
                    let lpbItemAmount = $('#addLpbModalItemAmount' + lpbKode).val();
                    let lpbTaxAmount = $('#addLpbModalTaxAmount' + lpbKode).val();
                    let lpbTotal = $('#addLpbModalTotal' + lpbKode).val();

                    let html = `<tr id="lpb${lpbKode}">
                                        <td>
                                            <span id="spanLpbNo${lpbKode}">${lpbDetailIdx}</span>
                                            <input type="hidden" id="lpbId${lpbKode}" value="${lpbId}" name="item_receiving_report_id[]" />
                                        </td>
                                        <td>
                                            ${lpbDate}
                                            <input type="hidden" id="lpbDate${lpbKode}" value="${lpbDate}" name="item_receiving_report_date[]" />
                                        </td>
                                        <td>
                                            ${lpbCode}
                                            <input type="hidden" id="lpbCode${lpbKode}" value="${lpbCode}" name="item_receiving_report_kode[]" />
                                            <input type="hidden" id="lpbPo${lpbKode}" value="${lpbPo}" name="po_kode[]" />
                                        </td>
                                        <td>
                                            ${formatRupiahWithDecimal(lpbItemAmount)}
                                            <input type="hidden" id="lpbItemAmount${lpbKode}" value="${lpbItemAmount}" name="item_sub_total[]" />
                                        </td>
                                        <td>
                                            ${formatRupiahWithDecimal(lpbTaxAmount)}
                                            <input type="hidden" id="lpbTaxAmount${lpbKode}" value="${lpbTaxAmount}" name="item_tax[]" />
                                        </td>
                                        <td>
                                            ${formatRupiahWithDecimal(lpbTotal)}
                                            <input type="hidden" id="lpbTotal${lpbKode}" value="${lpbTotal}" name="item_total[]" />
                                        </td>
                                        <td>
                                            <span id="spanLpbNote${lpbKode}">-</span>
                                            <input type="hidden" class="lpb-note" id="lpbNote${lpbKode}" value="" name="notes[]" />
                                        </td>
                                        <td>
                                            <x-button type="button" id="btnEditLpb${lpbKode}" color='secondary' fontawesome icon="edit" class="w-auto" size="sm" />
                                            <x-button type="button" id="btnDeleteLpb${lpbKode}" color='danger' fontawesome icon="trash" class="w-auto" size="sm" data-lpb="${lpbKode}" />
                                        </td>
                                    </tr>`;
                    $('#lpbDetail').append(html);

                    $(`#btnEditLpb${lpbKode}`).click(function() {

                        let lpbDate = $(`#lpbDate${lpbKode}`).val();

                        $('#editLpbModalDate').val(lpbDate);
                        $('#editLpbModalCode').val($(`#lpbCode${lpbKode}`).val());
                        $('#editLpbModalPo').val($(`#lpbPo${lpbKode}`).val());
                        $('#editLpbModalItemAmount').val(formatRupiahWithDecimal($(`#lpbItemAmount${lpbKode}`).val()));
                        $('#editLpbModalTaxAmount').val(formatRupiahWithDecimal($(`#lpbTaxAmount${lpbKode}`).val()));
                        $('#editLpbModalTotal').val(formatRupiahWithDecimal($(`#lpbTotal${lpbKode}`).val()));
                        $('#editLpbModalNote').val($(`#lpbNote${lpbKode}`).val());

                        $('#edit-lpb-modal').modal('show');
                    });

                    $(`#btnDeleteLpb${lpbKode}`).click(function() {
                        Swal.fire({
                            title: `Anda yakin ingin menghapus ${lpbCode}?`,
                            showCancelButton: true,
                            confirmButtonText: 'Iya, hapus!',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {

                                dpp -= parseFloat($(`#lpbItemAmount${lpbKode}`).val());
                                taxTotal -= parseFloat($(`#lpbTaxAmount${lpbKode}`).val());
                                grandTotal -= parseFloat($(`#lpbItemAmount${lpbKode}`).val()) + parseFloat($(`#lpbTaxAmount${lpbKode}`).val());

                                $('.span-dpp').text(formatRupiahWithDecimal(dpp));
                                $('.span-tax-total').text(formatRupiahWithDecimal(taxTotal));
                                $('.span-grand-total').text(formatRupiahWithDecimal(grandTotal));
                                $('.dpp').val(dpp);
                                $('.tax-total').val(taxTotal);
                                $('.grand-total').val(grandTotal);

                                $(`#lpb${lpbKode}`).remove();

                                let indexOfLpb = lpbs.indexOf(lpbKode);
                                if (indexOfLpb !== -1) {
                                    lpbs.splice(indexOfLpb, 1);
                                }

                                deleteLPB();
                                Swal.fire('LPB berhasil dihapus!', '', 'success');
                            }
                        })
                    });

                    dpp += parseFloat(lpbItemAmount);
                    taxTotal += parseFloat(lpbTaxAmount);
                    grandTotal += parseFloat(lpbItemAmount) + parseFloat(lpbTaxAmount);

                    $('.span-dpp').text(formatRupiahWithDecimal(parseFloat(dpp)));
                    $('.span-tax-total').text(formatRupiahWithDecimal(parseFloat(taxTotal)));
                    $('.span-grand-total').text(formatRupiahWithDecimal(parseFloat(grandTotal)));
                    $('.dpp').val(parseFloat(dpp));
                    $('.tax-total').val(parseFloat(taxTotal));
                    $('.grand-total').val(parseFloat(grandTotal));

                    lpbs.push(lpbKode);
                }
            });

            $('#add-lpb-modal').modal('hide');
        });

        $('.btn-update-lpb-modal').click(function() {
            let lpbCode = $('#editLpbModalCode').val();

            if (lpbCode.includes("/")) {
                lpbCode = lpbCode.replace(/[/\\*]/g, "-");
            }

            let note = $('#editLpbModalNote').val() == '' ? '-' : $('#editLpbModalNote').val();

            $(`#lpbNote${lpbCode}`).val(note);

            $(`#spanLpbNote${lpbCode}`).text(note);

            $('#editLpbModalNote').val(null);

            $('#edit-lpb-modal').modal('hide');
        });

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

        function getLpb() {
            $('#lpbDetailModal').find('tr').remove();
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
                },
                success: function(res) {
                    lpbDetailModalIdx = 0;

                    if (res.data.length > 0) {
                        let isEmpty = 0;

                        $.each(res.data, function(key, value) {
                            lpbDetailModalIdx++;

                            if (value.currency_id == $('#currency_id').val()) {
                                isEmpty++;

                                if (value.tipe == 'general') {
                                    var itemAmount = [];
                                    var taxAmount = [];

                                    $.each(value.item_receiving_report_details, function(lpb_detail_key, lpb_detail) {
                                        var itemPrice = lpb_detail.reference.price;
                                        var subTotal = parseFloat(lpb_detail.jumlah_diterima) * parseFloat(itemPrice);
                                        itemAmount.push(subTotal);

                                        $.each(lpb_detail.reference.purchase_order_general_detail_item_taxes, function(key, item_tax) {
                                            var total = item_tax.value * subTotal;
                                            taxAmount.push(total);
                                        });
                                    });

                                    if (itemAmount.length > 0) {
                                        itemAmount = itemAmount.reduce(function(a, b) {
                                            return parseFloat(a) + parseFloat(b);
                                        });
                                    } else {
                                        itemAmount = 0;
                                    }

                                    if (taxAmount.length > 0) {
                                        taxAmount = taxAmount.reduce(function(a, b) {
                                            return parseFloat(a) + parseFloat(b);
                                        });
                                    } else {
                                        taxAmount = 0;
                                    }

                                    let htmlModal = `<tr id="lpbRowModal${key}">
                                                            <td>
                                                                ${lpbDetailModalIdx}
                                                                <input type="hidden" id="addLpbModalId${key}" value="${value.id}" />
                                                            </td>
                                                            <td>
                                                                ${value.date_receive}
                                                                <input type="hidden" id="addLpbModalDate${key}" value="${value.date_receive}" />
                                                            </td>
                                                            <td>
                                                                ${value.kode}
                                                                <input type="hidden" id="addLpbModalCode${key}" value="${value.kode}" />
                                                            </td>
                                                            <td>
                                                                ${value.reference.code}
                                                                <input type="hidden" id="addLpbModalPo${key}" value="${value.reference.code}" />
                                                            </td>
                                                            <td>
                                                                ${formatRupiahWithDecimal(itemAmount + taxAmount)}
                                                                <input type="hidden" id="addLpbModalItemAmount${key}" value="${itemAmount}" />
                                                            </td>
                                                        </tr>`;
                                    $('#lpbDetailModal').append(htmlModal);

                                    let htmlRowModal = `<td align="center">
                                                                <input type="checkbox" data-lpb="${key}" style="position: unset; opacity: unset;" />
                                                                <input type="hidden" id="addLpbModalTaxAmount${key}" value="${taxAmount}" />
                                                                <input type="hidden" id="addLpbModalTotal${key}" value="${parseFloat(itemAmount + taxAmount)}" />
                                                            </td>`;

                                    let htmlRowModal2 = `<td align="center">
                                                                Sudah dipilih
                                                            </td>`;

                                    let indexOfLpb = lpbs.indexOf(value.kode);
                                    if (indexOfLpb == -1) {
                                        $('#lpbRowModal' + key).append(htmlRowModal);
                                    } else {
                                        $('#lpbRowModal' + key).append(htmlRowModal2);
                                    }
                                } else if (value.tipe == 'jasa') {
                                    var itemAmount = [];
                                    var taxAmount = [];

                                    $.each(value.item_receiving_report_details, function(lpb_detail_key, lpb_detail) {
                                        var itemPrice = lpb_detail.reference.price;
                                        var subTotal = parseFloat(lpb_detail.jumlah_diterima) * parseFloat(itemPrice);
                                        itemAmount.push(subTotal);

                                        $.each(lpb_detail.reference.purchase_order_service_detail_item_taxes, function(key, item_tax) {
                                            var total = item_tax.value * subTotal;
                                            taxAmount.push(total);
                                        });
                                    });

                                    if (itemAmount.length > 0) {
                                        itemAmount = itemAmount.reduce(function(a, b) {
                                            return parseFloat(a) + parseFloat(b);
                                        });
                                    } else {
                                        itemAmount = 0;
                                    }

                                    if (taxAmount.length > 0) {
                                        taxAmount = taxAmount.reduce(function(a, b) {
                                            return parseFloat(a) + parseFloat(b);
                                        });
                                    } else {
                                        taxAmount = 0;
                                    }

                                    let htmlModal = `<tr id="lpbRowModal${key}">
                                                            <td>
                                                                ${lpbDetailModalIdx}
                                                                <input type="hidden" id="addLpbModalId${key}" value="${value.id}" />
                                                            </td>
                                                            <td>
                                                                ${value.date_receive}
                                                                <input type="hidden" id="addLpbModalDate${key}" value="${value.date_receive}" />
                                                            </td>
                                                            <td>
                                                                ${value.kode}
                                                                <input type="hidden" id="addLpbModalCode${key}" value="${key}" />
                                                            </td>
                                                            <td>
                                                                ${value.reference.code}
                                                                <input type="hidden" id="addLpbModalPo${key}" value="${value.reference.code}" />
                                                            </td>
                                                            <td>
                                                                ${formatRupiahWithDecimal(itemAmount + taxAmount)}
                                                                <input type="hidden" id="addLpbModalItemAmount${key}" value="${itemAmount}" />
                                                            </td>
                                                        </tr>`;
                                    $('#lpbDetailModal').append(htmlModal);

                                    let htmlRowModal = `<td align="center">
                                                                <input type="checkbox" data-lpb="${key}" style="position: unset; opacity: unset;" />
                                                                <input type="hidden" id="addLpbModalTaxAmount${key}" value="${taxAmount}" />
                                                                <input type="hidden" id="addLpbModalTotal${key}" value="${parseFloat(itemAmount) + parseFloat(taxAmount)}" />
                                                            </td>`;

                                    let htmlRowModal2 = `<td align="center">
                                                                Sudah dipilih
                                                            </td>`;

                                    let indexOfLpb = lpbs.indexOf(value.kode);
                                    if (indexOfLpb == -1) {
                                        $('#lpbRowModal' + key).append(htmlRowModal);
                                    } else {
                                        $('#lpbRowModal' + key).append(htmlRowModal2);
                                    }
                                } else if (value.tipe == 'trading') {
                                    let kode = value.kode.replace(/[/\\*]/g, "-");

                                    let price = value.reference.po_trading_detail.harga;
                                    let discount_price = value.reference.po_trading_detail.discount_per_liter;

                                    let itemAmount = parseFloat(value.item_receiving_report_po_trading.liter_15) * (parseFloat(price) - parseFloat(discount_price));
                                    let taxAmount = [];

                                    $.each(value.reference.purchase_order_taxes, function(index, purchase_order_tax) {
                                        taxAmount.push(parseFloat(purchase_order_tax.total));
                                    });

                                    if (taxAmount.length > 0) {
                                        taxAmount = taxAmount.reduce(function(a, b) {
                                            return parseFloat(a) + parseFloat(b);
                                        });
                                    } else {
                                        taxAmount = 0;
                                    }

                                    let htmlModal = `<tr id="lpbRowModal${key}">
                                                            <td>
                                                                ${lpbDetailModalIdx}
                                                                <input type="hidden" id="addLpbModalId${key}" value="${value.id}" />
                                                            </td>
                                                            <td>
                                                                ${value.date_receive}
                                                                <input type="hidden" id="addLpbModalDate${key}" value="${value.date_receive}" />
                                                            </td>
                                                            <td>
                                                                ${value.kode}
                                                                <input type="hidden" id="addLpbModalCode${key}" value="${value.kode}" />
                                                            </td>
                                                            <td>
                                                                ${value.reference.nomor_po}
                                                                <input type="hidden" id="addLpbModalPo${key}" value="${value.reference.nomor_po}" />
                                                            </td>
                                                            <td>
                                                                ${formatRupiahWithDecimal(itemAmount + taxAmount)}
                                                                <input type="hidden" id="addLpbModalItemAmount${key}" value="${itemAmount}" />
                                                            </td>
                                                        </tr>`;
                                    $('#lpbDetailModal').append(htmlModal);

                                    let htmlRowModal = `<td align="center">
                                                                <input type="checkbox" data-lpb="${key}" style="position: unset; opacity: unset;" />
                                                                <input type="hidden" id="addLpbModalTaxAmount${key}" value="${taxAmount}" />
                                                                <input type="hidden" id="addLpbModalTotal${key}" value="${parseFloat(itemAmount) + parseFloat(taxAmount)}" />
                                                            </td>`;

                                    let htmlRowModal2 = `<td align="center">
                                                                Sudah dipilih
                                                            </td>`;

                                    let indexOfLpb = lpbs.indexOf(kode);
                                    if (indexOfLpb == -1) {
                                        $('#lpbRowModal' + key).append(htmlRowModal);
                                    } else {
                                        $('#lpbRowModal' + key).append(htmlRowModal2);
                                    }
                                } else if (value.tipe == 'transport') {
                                    let itemAmount = [];
                                    let taxAmount = [];

                                    $.each(value.reference.purchase_transport_details, function(index, purchase_transport_detail) {
                                        itemAmount.push(parseFloat(value.reference.harga) * (purchase_transport_detail.jumlah * purchase_transport_detail.jumlah_do));
                                    });

                                    itemAmount = itemAmount.reduce(function(a, b) {
                                        return parseFloat(a) + parseFloat(b);
                                    });

                                    $.each(value.reference.purchase_transport_taxes, function(index, purchase_transport_tax) {
                                        taxAmount.push(parseFloat(purchase_transport_tax.value) * itemAmount);
                                    });
                                    taxAmount = taxAmount.reduce(function(a, b) {
                                        return parseFloat(a) + parseFloat(b);
                                    });

                                    let htmlModal = `<tr id="lpbRowModal${key}">
                                                            <td>
                                                                ${lpbDetailModalIdx}
                                                                <input type="hidden" id="addLpbModalId${key}" value="${value.id}" />
                                                            </td>
                                                            <td>
                                                                ${value.date_receive}
                                                                <input type="hidden" id="addLpbModalDate${key}" value="${value.date_receive}" />
                                                            </td>
                                                            <td>
                                                                ${value.kode}
                                                                <input type="hidden" id="addLpbModalCode${key}" value="${value.kode}" />
                                                            </td>
                                                            <td>
                                                                ${value.reference.kode}
                                                                <input type="hidden" id="addLpbModalPo${key}" value="${value.reference.kode}" />
                                                            </td>
                                                            <td>
                                                                ${formatRupiahWithDecimal(itemAmount + taxAmount)}
                                                                <input type="hidden" id="addLpbModalItemAmount${key}" value="${itemAmount}" />
                                                            </td>
                                                        </tr>`;
                                    $('#lpbDetailModal').append(htmlModal);

                                    let htmlRowModal = `<td align="center">
                                                                <input type="checkbox" data-lpb="${key}" style="position: unset; opacity: unset;" />
                                                                <input type="hidden" id="addLpbModalTaxAmount${key}" value="${taxAmount}" />
                                                                <input type="hidden" id="addLpbModalTotal${key}" value="${parseFloat(itemAmount) + parseFloat(taxAmount)}" />
                                                            </td>`;

                                    let htmlRowModal2 = `<td align="center">
                                                                Sudah dipilih
                                                            </td>`;

                                    let indexOfLpb = lpbs.indexOf(value.kode);
                                    if (indexOfLpb == -1) {
                                        $('#lpbRowModal' + key).append(htmlRowModal);
                                    } else {
                                        $('#lpbRowModal' + key).append(htmlRowModal2);
                                    }
                                } else {
                                    //
                                }

                                $('.btn-select-lpb-modal').attr('disabled', false);
                            }
                        });

                        if (isEmpty == 0) {
                            let html = "<tr class='text-center'><td colspan='6'>Tidak ada LPB sesuai currency yang anda pilih.</td></tr>";
                            $('#lpbDetailModal').append(html);
                            $('.btn-select-lpb-modal').attr('disabled', true);
                        }
                    } else {
                        let html = "<tr class='text-center'><td colspan='6'>Vendor yang anda pilih tidak memiliki LPB.</td></tr>";
                        $('#lpbDetailModal').append(html);
                        $('.btn-select-lpb-modal').attr('disabled', true);
                    }
                }
            });
        }

        function deleteLPB() {
            lpbDetailIdx = 0;

            $.each(lpbs, function(index, lpb) {
                lpbDetailIdx++;
                $(`#spanLpbNo${lpb}`).text(lpbDetailIdx);
            });

            if (lpbs.length == 0) {
                $('#lpbDetail').append('<tr class="text-center"><td colspan="9">Belum ada LPB yang dipilih</td></tr>');
            }
        }

        function addDays(date, days) {
            var date = new Date(date);

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
        if ($('input[name="tax_total"]').val() > 0) {
            if ($('input[name="tax_reference"]').val() == "") {
                e.preventDefault();
                setTimeout(() => {
                    $('#form').find('input[type=submit]').prop('disabled', false);
                    $('#form').find('button[type=submit]').prop('disabled', false);
                }, 1000);

                Swal.fire({
                    icon: 'error',
                    title: '',
                    text: 'Maaf faktur pajak harus diisi',
                });

            }
        }
    });
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