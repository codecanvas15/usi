@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-down-payment';
@endphp

@section('title', Str::headline("edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $main) }}
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

        <x-card-data-table title="{{ 'Create ' . $main }}">
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="vendor_id" label="vendor" id="vendor_id" required autofocus>
                                <option value="{{ $model->vendor_id }}">{{ $model->vendor->nama }}</option>
                            </x-select>
                        </div>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="date" label="tanggal" value="{{ localDate($model->date) }}" id="date" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="due_date" label="due_date" value="{{ localDate($model->due_date) }}" id="" required id="due_date" />
                        </div>
                    </div>
                </div>
                <div class="row mt-20">
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select label="branch" name="branch_id" id="branch_id" required disabled>
                                    <option value="{{ $model->branch->id }}">{{ $model->branch->name }}</option>
                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="currency_id" label="mata uang" id="currency_id" required disabled>
                                <option value="{{ $model->currency_id }}">{{ $model->currency->kode }} - {{ $model->currency->nama }} - {{ $model->currency->negara }}</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="exchange_rate" label="nilai_tukar" id="exchange_rate" class="commas-form" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                        </div>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="purchase_id">Sales Order <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="purchase_id" id="purchase_id" value="" required>
                                @if ($model->purchase)
                                    <option value="{{ $model->purchase_id }}">{{ $model->purchase->nomor_so ?? ($model->purchase->kode ?? '') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="total_po" id="total_po" class="commas-form text-end total_po" readonly value="{{ formatNumber($model->purchase->reference->total ?? 0) }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="outstanding_amount" id="outstanding_amount" class="commas-form text-end outstanding_amount" readonly value="{{ formatNumber($model->po_outstanding ?? 0) }}" />
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="reference">No. Faktur Pajak</label>
                            <input type="text" class="form-control mt-2 tax-reference-mask" name="tax_number" id="tax-reference" placeholder="No. Faktur Pajak" value="{{ $model->tax_number }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="tax_attachment" label="file" id="tax_attachment" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                            @if ($model->tax_attachment)
                                <a href="{{ asset('storage/' . $model->tax_attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> File</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if ($model->is_include_tax)
                            <x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" checked />
                        @else
                            <x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" />
                        @endif
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>

        <div class="box">
            <div class="box-body">
                <table class="table ">
                    <tbody>
                        <tr>
                            <td>
                                <x-input type="text" name="note" label="note" id="note" required value="{{ $model->note }}" />
                            </td>
                            <td>
                                <x-input type="text" name="total_amount" label="total_amount" id="total_amount" required class="commas-form text-end total_po" value="{{ formatNumber($model->total_amount) }}" />
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"></td>
                            <td>
                                <x-input type="text" name="down_payment" label="down_payment" id="down_payment" required class="commas-form text-end" value="{{ formatNumber($model->down_payment) }}" />
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"></td>
                            <td>
                                <x-input type="text" name="subtotal" label="subtotal" id="subtotal" required class="commas-form text-end" readonly value="{{ formatNumber($model->subtotal) }}" />
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"></td>
                            <td>
                                <x-select name="tax_id[]" label="tax_id" id="tax_id" label="pajak" useBr="true">
                                    @foreach ($model->purchase_down_payment_taxes as $purchase_down_payment_tax)
                                        <option value="{{ $purchase_down_payment_tax->tax_id }}" selected>{{ $purchase_down_payment_tax->tax->tax_name_with_percent }}</option>
                                    @endforeach
                                </x-select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"></td>
                            <td>
                                <table width="100%">
                                    <tbody id="table-tax">
                                        @foreach ($model->purchase_down_payment_taxes as $purchase_down_payment_tax)
                                            <tr>
                                                <td>{{ $purchase_down_payment_tax->tax->tax_name_with_percent }}</td>
                                                <td class="text-end">{{ formatNumber($purchase_down_payment_tax->amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"></td>
                            <td>
                                <x-input type="text" name="grand_total" label="grand_total" id="grand_total" label="grand_total" required readonly class="commas-form text-end" value="{{ formatNumber($model->grand_total) }}" />
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

        <div class="box">
            <div class="box-footer">
                <div class="d-flex justify-content-end">
                    <x-button type="submit" color="primary" label="Simpan" />
                </div>
            </div>
        </div>

    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        let tax_data = [];
        $(document).ready(function() {

            checkClosingPeriod($('#date'));

            initSelect2Search(`vendor_id`, `{{ route('admin.select.vendor') }}`, {
                id: "id",
                text: "nama"
            });

            initSelect2SearchPaginationData(`currency_id`, `{{ route('admin.select.currency') }}`, {
                id: 'id',
                text: 'kode,nama,negara'
            })

            initSelect2SearchPaginationData(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: 'id',
                text: 'name'
            })

            initSelect2SearchPaginationData(`tax_id`, `{{ route('admin.select.tax') }}`, {
                id: 'id',
                text: 'name'
            })

            initSelect2SearchPaginationData(`purchase_id`, `{{ route('admin.select.purchase-down-payment.select-purchase') }}`, {
                id: "id",
                text: "kode,vendor_name"
            }, 0, {
                vendor_id: function() {
                    return $('#vendor_id').val();
                },
                branch_id: function() {
                    return $('#branch_id').val();
                }
            });

            $('#vendor_id').change(function() {
                let value = $(this).val();
                changeVendorAndCurrency(value);
                return;
            });

            $('#currency_id').change(function(e) {
                e.preventDefault();

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

            const changeVendorAndCurrency = (value) => {
                if (value) {
                    $.ajax({
                        url: `{{ route('admin.select.vendor-detail') }}/${value}`,
                        type: "GET",
                        success: function(res) {
                            console.log('res', res);

                            $("#bank_internal_id").attr('disabled', false);
                            $("#bank_internal_id").empty();
                            res.data.vendor_banks.map((bank, index) => {
                                let opts = new Option(`${bank.bank_internal.nama_bank} - ${bank.bank_internal.no_rekening}`, bank.bank_internal.id, index == 0 ? true : false, index == 0 ? true : false);
                                $("#bank_internal_id").append(opts).trigger('change');
                            });
                        }
                    });
                }
            };

            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this,
                        args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            const calculateGrandTotal = () => {
                const down_payment = thousandToFloat($('#down_payment').val());
                var subtotal = 0;
                var tax_total = 0;
                if ($('#is_include_tax').is(':checked')) {
                    subtotal = down_payment
                    let tax_percentage = 0;
                    tax_data.map((tax_value, tax_value_index) => {
                        tax_percentage += tax_value.value * 100
                    });

                    subtotal -= (down_payment - (100 / (100 + tax_percentage) * down_payment));
                } else {
                    subtotal = down_payment;
                }

                $('#subtotal').val(formatRupiahWithDecimal(subtotal));

                displayTax();

                tax_data.map((tax_value, tax_value_index) => {
                    let tax_amount = tax_value.value * subtotal;
                    tax_total += tax_amount;
                })

                $('#grand_total').val(formatRupiahWithDecimal(subtotal + tax_total));

            }

            function fetchTaxDetails() {
                $.ajax({
                    url: `${base_url}/tax/detail`,
                    method: "POST",
                    data: {
                        '_token': token,
                        'id': [$('#tax_id').val()]
                    },
                    success: function(res) {
                        tax_data = res.data;
                        calculateGrandTotal();
                    }
                });
            }

            $('#tax_id').change(debounce(fetchTaxDetails, 300));

            const displayTax = () => {
                let html = ``;
                let subtotal = thousandToFloat($('#subtotal').val());

                $.each(tax_data, function(key, value) {
                    let tax_amount = value.value * subtotal;
                    html += `<tr>
                                <td>${value.tax_name_with_percent}</td>
                                <td class="text-end">${formatRupiahWithDecimal(tax_amount)}</td>
                            </tr>`;

                    tax_data[key]['tax_amount'] = tax_amount;
                });

                $('#table-tax').html(html);

            }

            $('#down_payment').on('keyup', calculateGrandTotal);

            initMaskTaxReference();

            $('#purchase_id').on('change', function() {
                let value = $(this).val();
                if (value) {
                    $.ajax({
                        url: `{{ route('admin.select.purchase-down-payment.purchase-detail') }}/${value}`,
                        type: "GET",
                        success: function(res) {
                            $('.total_po').each(function() {
                                $(this).val(formatRupiahWithDecimal(res.total));
                            });
                            $('#outstanding_amount').val(formatRupiahWithDecimal(res.outstanding_amount));
                        }
                    });
                } else {
                    $('.total_po').each(function() {
                        $(this).val(0);
                    });
                }

            });

            $('#total_amount').on('keyup', function() {
                let value = thousandToFloat($(this).val());
                let total_po = thousandToFloat($('#total_po').val());
                if (value > total_po) {
                    alert('Jumlah pembayaran tidak boleh melebihi total PO');
                    $(this).val(total_po);

                    calculateGrandTotal();
                }
            })

            $('#down_payment').on('keyup', function() {
                let value = thousandToFloat($(this).val());
                let outstanding_amount = thousandToFloat($('#outstanding_amount').val());
                if (value > outstanding_amount) {
                    alert('Jumlah DP tidak boleh melebihi sisa PO');
                    $(this).val(outstanding_amount);

                    calculateGrandTotal();
                }
            })

            $('#is_include_tax').on('change', function() {
                calculateGrandTotal();
            });

            fetchTaxDetails();
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');
    </script>
@endsection
