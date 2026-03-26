@extends('layouts.admin.layout.index')

@php
    $main = 'fund-submission';
    $menu = 'pengajuan dana uang muka';
@endphp

@section('title', Str::headline("edit $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($menu) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route("admin.$main.update", ['fund_submission' => $model->id]) }}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_method" value="PUT">
        @csrf
        <x-card-data-table title="{{ 'edit ' . $menu }}">
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                <div id="errorRl" class="alert alert-danger" role="alert" style="display: none">
                    <span id="errorRlMessage"></span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h3 for="">No. <span>{{ $model->code }}</span></h3>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="branch_id" id="branch_id" label="branch" required>
                                        <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="jenis pengajuan" name="item" id="item" required value="DP" readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ Carbon\Carbon::parse($model->date)->format('d-m-Y') }}" />
                                </div>
                            </div>
                            <input type="hidden" name="to_model" value="App\Models\Vendor" id="to_model">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_id" class="form-label">Vendor/Supplier</label>
                                    <select name="to_id" id="to_id" class="form-select" onchange="getInitialCoa()">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_down_payment_id" class="form-label">No PO DP</label>
                                    <select name="purchase_down_payment_id" id="purchase_down_payment_id" class="form-select">
                                        @if ($model->purchase_down_payment)
                                            <option value="{{ $model->purchase_down_payment->id }}">
                                                @if ($model->purchase)
                                                    {{ $model->purchase_down_payment->code }} - {{ $model->purchase?->kode }}
                                                @else
                                                    {{ $model->purchase_down_payment->code }}
                                                @endif
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="total PO" name="purchase_total" id="purchase_total" readonly value="{{ $model->purchase ? formatNumber($model->purchase?->reference?->total ?? 0) : 0 }}" />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="referensi" id="referensi" value="{!! $model->reference !!}" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="project_id" id="project_id" label="project">
                                        @if ($model->project)
                                            <option value="{{ $model->project_id }}">{{ $model->project->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="row">
                            <input type="hidden" name="type[]" value="cash_bank">
                            <input type="hidden" name="position[]" value="credit">
                            <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $model->cash_advance_cash_bank->id }}">
                            <div class="col-md-6">
                                <x-select name="currency_id" id="currency_id" label="currency" required>
                                    <option value="{{ $model->currency->id }}" selected>{{ $model->currency->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                @if ($model->currency->is_local)
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                                @else
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
                                @endif
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="coa_detail_id_0" class="form-label">Akun Kas/Bank</label><br>
                                    <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control cash_bank_coa_id" required autofocus style="width:100%">
                                        <option value="{{ $model->cash_advance_cash_bank->coa_id }}">{{ $model->cash_advance_cash_bank->coa->account_code }} - {{ $model->cash_advance_cash_bank->coa->name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="hidden" id="note_0" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $model->cash_advance_cash_bank->note }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="note_0" class="form-label">Jumlah</label>
                                    <input type="text" id="amount_0" name="amount[]" class="form-control commas-form text-end cash_bank_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($model->cash_advance_cash_bank->credit) }}" readonly />
                                </div>
                            </div>
                            <div class="col-md-12">
                                @if ($model->is_giro)
                                    <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" checked />
                                @else
                                    <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                                @endif
                            </div>
                            <div class="col-md-12 {{ !$model->is_giro ? 'd-none' : '' }}" id="giro-form">
                                <div class="row">
                                    @include('admin.fund-submission.__giro_form', ['data' => $model->send_payment])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr id="cash-advance-detail-1">
                                        <input type="hidden" name="type[]" value="cash_advance">
                                        <input type="hidden" name="position[]" value="debit">
                                        <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $model->cash_advance_cash_advance->id }}">
                                        <td>
                                            <div class="form-group">
                                                <label for="coa_detail_id_1" class="form-label">Akun Uang Muka</label><br>
                                                <select name="coa_detail_id[]" id="coa_detail_id_1" class="form-control cash_advance_coa_id" required autofocus style="width:100%">
                                                    <option value="{{ $model->cash_advance_cash_advance->coa_id }}">{{ $model->cash_advance_cash_advance->coa->account_code }} - {{ $model->cash_advance_cash_advance->coa->name }}</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label for="note_1" class="form-label">Keterangan</label>
                                                <input type="text" id="note_1" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $model->cash_advance_cash_advance->note }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label for="note_1" class="form-label">Jumlah</label>
                                                <input type="text" id="amount_1" name="amount[]" class="form-control commas-form text-end cash_advance_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($model->cash_advance_cash_advance->debit) }}" {{ $model->purchase_down_payment ? 'readonly' : '' }} />
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="tax_id" class="form-label">Pajak <span class="text-primary">*</span></label><br>
                                            <select name="tax_id" id="tax_id" class="form-control" onchange="addOtherCostTax($(this))">
                                                @if ($model->tax)
                                                    <option value="{{ $model->tax->id }}" selected>{{ $model->tax->tax_name_with_percent }}</option>
                                                @endif
                                            </select>

                                            <input type="hidden" id="tax_value" value="{{ $model->tax->value ?? 0 }}">
                                        </td>
                                        <td>
                                            <div class="tax_number_input {{ !$model->tax ? 'd-none' : '' }}">
                                                @if ($model->purchase_down_payment->tax_number ?? null)
                                                    <x-input type="text" name="tax_number" id="tax_number" label="faktur pajak" value="{{ $model->tax_number }}" hideAsterix="true" class="tax-reference-mask" readonly />
                                                @else
                                                    <x-input type="text" name="tax_number" id="tax_number" label="faktur pajak" value="{{ $model->tax_number }}" hideAsterix="true" class="tax-reference-mask" />
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tax_number_input {{ !$model->tax ? 'd-none' : '' }}">
                                                @if (!$model->purchase_down_payment?->tax_attachment ?? null)
                                                    <x-input type="file" name="tax_attachment" id="tax_attachment" label="lampiran" value="" hideAsterix="true" />
                                                @endif
                                                <a href="{{ asset('storage/' . $model->tax_attachment) }}" id="tax_attachment_link" class="{{ $model->tax_attachment ? '' : 'd-none' }}" target="_blank">Lihat file</a>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-info">
                                        <th colspan="4">{{ Str::headline('biaya lain - lain') }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('akun') }}</th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                        <th class="text-end">{{ Str::headline('jumlah') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cash-advance-detail-data">
                                    <input type="hidden" id="count_rows" value="{{ count($model->fund_submission_cash_advances) }}">
                                    @foreach ($model->cash_advance_others as $key => $item)
                                        <tr id="cash-advance-detail-{{ $key + 2 }}" class="{{ $item->type == 'tax' ? 'row-tax' : '' }}">
                                            <input type="hidden" name="type[]" value="{{ $item->type }}">
                                            <input type="hidden" name="position[]" value="debit">
                                            <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $item->id }}">
                                            <td>
                                                <select name="coa_detail_id[]" id="coa_detail_id_{{ $key + 2 }}" class="form-control other_coa_id other_coa_{{ $item->type }}" required autofocus style="width:100%">
                                                    <option value="{{ $item->coa_id }}">{{ $item->coa->account_code }} - {{ $item->coa->name }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="note_{{ $key + 2 }}" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $item->note }}" />
                                            </td>
                                            <td>
                                                <input type="text" id="amount_{{ $key + 2 }}" name="amount[]" class="form-control commas-form text-end other_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($item->debit) }}" {{ $item->type == 'tax' ? 'readonly' : '' }} />
                                            </td>
                                            <td>
                                                @if ($item->type != 'tax')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="$('#cash-advance-detail-{{ $key + 2 }}').remove();countTotal()"><i class="fa fa-times"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="1"></th>
                                        <th class="text-end">Total</th>
                                        <th id="total-cash-advance" class="text-end"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="4">
                                            <x-button color="success" label="Tambah Baris +" type="button" onclick="addOtherCost()" class="btn-sm" />
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="float-end">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection
@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/fund-submission/dp.js') }}?v=27.11.25"></script>
    <script>
        $(document).ready(function() {
            initMaskTaxReference();
            initCoaSelect(`.other_coa_other`);

            countTotal();

            initSelect2SearchPagination(`coa_detail_id_0`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_type: "Cash & Bank",
            });

            initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
                id: "id",
                text: "kode,nama,negara"
            });

            $('#currency_id').change(function(e) {
                e.preventDefault();
                // $('#coa_detail_id_0').val(null).trigger('change');

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

            initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
                id: "id",
                text: "code,name"
            }, 2, {
                branch_id: function() {
                    return $('#branch_id').val();
                }
            });

            initSelect2SearchPaginationData(`purchase_down_payment_id`, `{{ route('admin.select.fund-submission.select-purchase-down-payment') }}`, {
                id: "id",
                text: "code,kode,vendor_name"
            }, 0, {
                vendor_id: function() {
                    return $('#to_id').val();
                },
                currency_id: function() {
                    return $('#currency-select').val();
                },
                branch_id: function() {
                    return $('#branch_id').val();
                },
                selected_id: '{{ $model->purchase_down_payment_id }}'
            });

            $('#purchase_down_payment_id').change(function(e) {
                $('#amount_1').val(null).attr('readonly', false).trigger('keyup');
                $('#tax_id').val(null).trigger('change');
                $('.row-tax').remove();

                $('.tax_number_input').addClass('d-none');
                $('#tax_number').val('').attr('readonly', false);

                $('#tax_attachment').removeClass('d-none');
                $('#tax_attachment_link').addClass('d-none');

                initSelect2Search(`tax_id`, "{{ route('admin.select.tax') }}", {
                    id: "id",
                    text: "name"
                })

                if (this.value) {
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.fund-submission.show-purchase-down-payment') }}/${this.value}`,
                        success: function({
                            data
                        }) {
                            if (data.vendor) {
                                $('#to_id').html(`<option value="${data.vendor.id}" selected>${data.vendor.nama}</option>`);
                                $('#purchase_total').val(formatRupiahWithDecimal(data.purchase_total));
                                let data_purchase_down_payment = data;
                                let data_purchase_tax = data.purchase_down_payment_taxes[0];

                                $('#amount_1').val(formatRupiahWithDecimal(data_purchase_down_payment.subtotal)).attr('readonly', 'readonly')
                                    .trigger('keyup');
                                if (data_purchase_tax) {
                                    $('#tax_id').html(`<option value="${data_purchase_tax.tax_id}" selected>${data_purchase_tax.tax.tax_name_with_percent}</option>`)
                                        .select2('destroy');
                                    addOtherCostTax($('#tax_id'));
                                } else {
                                    initSelect2Search(`tax_id`, "{{ route('admin.select.tax') }}", {
                                        id: "id",
                                        text: "name"
                                    })
                                }
                                if (data_purchase_down_payment.tax_number) {
                                    $('#tax_number').val(data_purchase_down_payment.tax_number).attr('readonly', 'readonly');
                                }
                                if (data_purchase_down_payment.tax_attachment) {
                                    $('#tax_attachment').addClass('d-none');
                                    $('#tax_attachment_link').removeClass('d-none').attr('href', `${base_url}/storage/${data_purchase_down_payment.tax_attachment}`);
                                } else {
                                    $('#tax_attachment').removeClass('d-none');
                                    $('#tax_attachment_link').addClass('d-none');
                                }
                                var vendor_deposite_coa = data.vendor.vendor_coas.filter(function(vendor_coa) {
                                    return vendor_coa.type == 'Vendor Deposite Coa';
                                })[0];

                                if (vendor_deposite_coa) {
                                    $(".cash_advance_coa_id").html(`<option value="${vendor_deposite_coa.coa_id}" selected>${vendor_deposite_coa.coa.account_code} - ${vendor_deposite_coa.coa.name}</option>`);
                                }

                            } else {
                                $('.row-tax').remove();
                                $('.tax_number_input').addClass('d-none');
                            }
                        }
                    });
                }
            })

            $('#to_id').change(function(e) {
                $('#purchase_down_payment_id').val(null).trigger('change');
            })
        });

        $('#to_model').trigger('change');
    </script>
    @if ($model->to_model)
        <script>
            $('#to_id').append('<option value="{{ $model->to_id }}">{{ $model->model_reference->name ?? $model->model_reference->nama }}</option>');
        </script>
    @endif
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });

            $('#branch_id').change(function(e) {
                $('#purchase_down_payment_id').val(null).trigger('change');
            })
        </script>
    @endif
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#fund-submission');
    </script>
@endsection
