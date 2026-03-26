@extends('layouts.admin.layout.index')

@php
    $main = 'cash-advance-receive';
    $menu = 'penerimaan deposit';
@endphp

@section('title', Str::headline("tambah $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.incoming-payment.index') }}?tab=cash-advance-receive-tab">{{ Str::headline($menu) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route('admin.' . $main . '.store') }}" method="post">
        @csrf
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('tambah ' . $menu) }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h3 for="">No. <span>{{ $code }}</span></h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="branch_id" id="branch_id" label="branch" required>
                                <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" label="tanggal" name="date" id="date" onchange="checkClosingPeriod($(this))" class="datepicker-input" required value="{{ date('d-m-Y') }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_id" class="form-label">Terima Dari <span class="text-danger">*</span></label>
                            <select name="customer_id" id="customer_id" required class="form-select" onchange="getInitialCoa()"></select>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" label="note" name="reference" id="reference" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="project_id" id="project_id" label="project">

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr id="cash-advance-detail-0">
                                        <input type="hidden" name="type[]" value="cash_bank">
                                        <input type="hidden" name="position[]" value="debit">
                                        <td>
                                            <div class="form-group">
                                                <label for="coa_detail_id_0" class="form-label">Akun Kas/Bank <span class="text-danger">*</span></label><br>
                                                <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control cash_bank_coa_id" required autofocus style="width:100%" onchange="get_coa_detail($(this))">
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label for="note_0" class="form-label">Jumlah</label>
                                                <input type="text" id="amount_0" name="amount[]" class="form-control commas-form text-end cash_bank_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="0" readonly />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="hidden" id="note_0" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" />
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">
                                            <div class="form-group">
                                                <label for="currency_id" class="form-label">Currency <span class="text-danger">*</span></label>
                                                <br>
                                                <select class="form-control" name="currency_id" id="currency_id" required>
                                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="align-top">
                                            <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
                                        </td>
                                        <td class="align-top">
                                            <div class="form-group">
                                                <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
                                                <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr id="cash-advance-detail-1">
                                        <input type="hidden" name="type[]" value="cash_advance">
                                        <input type="hidden" name="position[]" value="credit">
                                        <td>
                                            <div class="form-group">
                                                <label for="coa_detail_id_1" class="form-label">Akun Uang Muka <span class="text-danger">*</span></label><br>
                                                <select name="coa_detail_id[]" id="coa_detail_id_1" class="form-control cash_advance_coa_id" required autofocus style="width:100%">
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label for="note_1" class="form-label">Keterangan</label>
                                                <input type="text" id="note_1" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label for="note_1" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                                <input type="text" id="amount_1" name="amount[]" class="form-control commas-form text-end cash_advance_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="" />
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="tax_id" class="form-label">Pajak <span class="text-primary">*</span></label><br>
                                            <select name="tax_id" id="tax_id" class="form-control" onchange="addOtherCostTax($(this))">
                                            </select>

                                            <input type="hidden" id="tax_value" value="0">
                                        </td>
                                        <td>
                                            <div class="d-none tax_number_input">
                                                <x-input type="text" name="tax_number" id="tax_number" label="faktur pajak" value="" hideAsterix="true" class="tax-reference-mask" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-none tax_number_input">
                                                <x-input type="file" name="tax_attachment" id="tax_attachment" label="lampiran" value="" hideAsterix="true" />
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-info">
                                        <th colspan="4">{{ Str::headline('biaya lain - lain') }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('akun') }} <span class="text-danger">*</span></th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                        <th class="text-end">{{ Str::headline('jumlah') }} <span class="text-danger">*</span></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cash-advance-detail-data">
                                    <input type="hidden" id="count_rows" value="3">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th></th>
                                        <th></th>
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
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.incoming-payment.index') }}?tab=cash-advance-receive-tab" class="btn btn-secondary">Cancel</a>
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/cash-advance-receive/index.js') }}"></script>
    <script>
        $(document).ready(function() {
            initMaskTaxReference();
            checkClosingPeriod($('#date'))
            $(document).ready(function() {
                initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                });

                initCoaSelect(`select[name="coa_detail_id[]"]`);

                initSelect2SearchPagination(`coa_detail_id_0`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                }, 0, {
                    account_type: "Cash & Bank",
                    currency_id: function() {
                        return $('#currency_id').val();
                    }
                });

                initSelect2SearchPagination(`coa_detail_id_1`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                });

                initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
                    id: "id",
                    text: "kode,nama,negara"
                });

                $('#currency_id').change(function(e) {
                    e.preventDefault();
                    $('#coa_detail_id_0').val(null).trigger('change');

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

                initSelect2Search(`tax_id`, "{{ route('admin.select.tax') }}", {
                    id: "id",
                    text: "name"
                })
            })
        });

        $('#sequence_code').on('blur', function() {
            check_bank_code(
                '#coa_detail_id_0',
                '#sequence_code',
                '#date',
                'in'
            );
        });
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#incoming-payment')
    </script>
@endsection
