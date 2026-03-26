@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-down-payment';
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
                        <a href="{{ route('admin.invoice-trading.index') }}">{{ Str::headline($main) }}</a>
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
                            <x-select name="customer_id" label="customer" id="customer_id" required autofocus>
                                <option value="{{ $model->customer_id }}">{{ $model->customer->nama }}</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="bank_internal_id" label="bank" id="bank_internal_id" required helpers="jika bank kosong silahkan isi dari master customer">
                                <option value="{{ $model->bank_internal_id }}">{{ $model->bank_internal->nama_bank }} - {{ $model->bank_internal->no_rekening }}</option>
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
                            <label class="mb-2" for="sale_order_model">Jenis Sales Order <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="sale_order_model" id="sale_order_model" value="" required>
                                <option value="">Jenis Sales Order</option>
                                <option value="{{ \App\Models\SoTrading::class }}" {{ $model->sale_order_model == \App\Models\SoTrading::class ? 'selected' : '' }}>Trading</option>
                                <option value="{{ \App\Models\SaleOrderGeneral::class }}" {{ $model->sale_order_model == \App\Models\SaleOrderGeneral::class ? 'selected' : '' }}>General</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="sale_order_model_id">Sales Order <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="sale_order_model_id" id="sale_order_model_id" value="" required>
                                @if ($model->sale_order_reference)
                                    <option value="{{ $model->sale_order_model_id }}">{{ $model->sale_order_reference->nomor_so ?? ($model->sale_order_reference->kode ?? '') }}</option>
                                @endif
                            </select>
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
                                <x-input type="text" name="total_amount" label="total_amount" id="total_amount" required class="commas-form text-end" value="{{ formatNumber($model->total_amount) }}" />
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
                                <x-select name="tax_id[]" label="tax_id" id="tax_id" label="pajak" useBr="true">
                                    @foreach ($model->invoice_down_payment_taxes as $invoice_down_payment_tax)
                                        <option value="{{ $invoice_down_payment_tax->tax_id }}" selected>{{ $invoice_down_payment_tax->tax->tax_name_with_percent }}</option>
                                    @endforeach
                                </x-select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"></td>
                            <td>
                                <table width="100%">
                                    <tbody id="table-tax">
                                        @foreach ($model->invoice_down_payment_taxes as $invoice_down_payment_tax)
                                            <tr>
                                                <td>{{ $invoice_down_payment_tax->tax->tax_name_with_percent }}</td>
                                                <td class="text-end">{{ formatNumber($invoice_down_payment_tax->amount) }}</td>
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

            initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
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

            $('#customer_id').change(function() {
                let value = $(this).val();
                changeCustomerAndCurrency(value);
                return;
            });

            const changeCustomerAndCurrency = (value) => {
                if (value) {
                    $.ajax({
                        url: `{{ route('admin.select.customer-detail') }}/${value}`,
                        type: "GET",
                        success: function(res) {
                            console.log('res', res);

                            $("#bank_internal_id").attr('disabled', false);
                            $("#bank_internal_id").empty();
                            res.data.customer_banks.map((bank, index) => {
                                let opts = new Option(`${bank.bank_internal.nama_bank} - ${bank.bank_internal.no_rekening}`, bank.bank_internal.id, index == 0 ? true : false, index == 0 ? true : false);
                                $("#bank_internal_id").append(opts).trigger('change');
                            });
                        }
                    });
                }
            };

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

            $('#sale_order_model').change(function() {
                let url = '{{ route('admin.sales-order-general.get-by-customer') }}';
                if ($(this).val() == '{{ \App\Models\SoTrading::class }}') {
                    url = '{{ route('admin.select.sale-order') }}'
                }
                $('#sale_order_model_id').select2({
                    allowClear: false,
                    placeholder: 'Pilih Sales Order',
                    language: {
                        noResults: () => {
                            return "Data tidak ditemukan";
                        },
                    },
                    ajax: {
                        url: url,
                        dataType: "json",
                        delay: 250,
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": token,
                        },
                        data: (params) => {
                            let result = {};
                            result["search"] = params.term;
                            result["page_limit"] = 10;
                            result["page"] = params.page;
                            result["customer_id"] = function() {
                                return $('#customer_id').val();
                            };
                            result['branch_id'] = function() {
                                return $('#branch_id').val();
                            };
                            result['currency_id'] = function() {
                                return $('#currency_id').val();
                            };

                            return result;
                        },
                        processResults: (data, params) => {
                            params.page = params.page || 1;
                            let final_data = data.data.data.map((data, key) => {
                                return {
                                    id: data['id'],
                                    text: data['kode'],
                                };
                            });
                            return {
                                results: final_data,
                                pagination: {
                                    more: params.page * 10 < data.data.total,
                                },
                            };
                        },
                        cache: true,
                    },
                });

                $('#sale_order_model_id').attr('disabled', false);
            })

            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this,
                        args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
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
                        displayTax();
                    }
                });
            }

            $('#tax_id').change(debounce(fetchTaxDetails, 300));

            const displayTax = () => {
                let html = ``;
                let down_payment = thousandToFloat($('#down_payment').val());

                $.each(tax_data, function(key, value) {
                    let tax_amount = value.value * down_payment;
                    html += `<tr>
                                <td>${value.tax_name_with_percent}</td>
                                <td class="text-end">${formatRupiahWithDecimal(tax_amount)}</td>
                            </tr>`;

                    tax_data[key]['tax_amount'] = tax_amount;
                });

                $('#table-tax').html(html);

                calculateGrandTotal();
            }

            $('#down_payment').on('keyup', displayTax);

            const calculateGrandTotal = () => {
                const down_payment = thousandToFloat($('#down_payment').val());
                const tax_total = tax_data.reduce((acc, curr) => acc + curr.tax_amount, 0);

                $('#grand_total').val(formatRupiahWithDecimal(down_payment + tax_total));

                console.log(tax_total)
            }

            $('#tax_id').trigger('change');
        });
    </script>

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading')
    </script>
@endsection
