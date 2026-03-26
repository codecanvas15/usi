@extends('layouts.admin.layout.index')

@php
    $main = 'receivables-payment';
    $menu = 'penerimaan customer';
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
                    <li class="breadcrumb-item">
                        Account Receivable
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.incoming-payment.index') }}?tab=receivable-payment-tab">{{ Str::headline($main) }}</a>
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
    <form id="form-data" action="{{ route("admin.$main.store") }}" method="post">
        @csrf
        <x-card-data-table title="{{ 'tambah ' . $menu }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <x-select name="branch_id" id="branch_id" label="branch" required onchange="initSelect2Coa();$('#selected_invoice_table').html('');calculateData();">
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ date('d-m-Y') }}" onchange="checkClosingPeriod($(this))" />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="customer_id" id="customer_id" label="customer" required onchange="$('#selected_invoice_table').html('');calculateData();get_customer_vendor($(this))">

                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
                                    <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="currency_id" id="currency_id" label="cur." required onchange="checkCurrency();initInvoiceCurrency()">
                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                </x-select>
                                <input type="hidden" id="currency_id_symbol" value="{{ get_local_currency()->kode }}">
                                <input type="hidden" id="local_currency_id" value="{{ get_local_currency()->id }}">
                            </div>
                            <div class="col-md-6">
                                <x-select name="invoice_currency_id" id="invoice_currency_id" label="cur. invoice" required onchange="checkCurrency();initCurrency()">
                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end text-end" label="kurs" id="exchange_rate" value="1" required readonly data-is-both-local="true" />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="project_id" id="project_id" label="project">

                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="reference" id="reference" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-md-6">
                                <x-select name="coa_id" id="coa_id" label="kas/bank" required onchange="get_coa_detail($(this))">
                                </x-select>
                            </div>
                            <div class="col-md-6 giro-checkbox d-none align-self-center">
                                <div class="form-group">
                                    <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                                </div>
                            </div>
                            <div class="col-md-6 giro-form d-none">
                                <x-select name="receive_payment_id" id="receive_payment_id" label="giro masuk" onchange="get_receive_payment($(this))">

                                </x-select>
                            </div>
                            <div class="col-md-12 giro-form d-none">
                                <div class="table-responsive">
                                    <table class="table">
                                        <input type="hidden" name="giro_outstanding_amount" id="giro_outstanding_amount">
                                        <tbody>
                                            <tr class="bg-dark">
                                                <td colspan="2">
                                                    <b>INFORMASI GIRO</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>No Cheque</b></td>
                                                <td id="cheque_no"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Jatuh Tempo</b></td>
                                                <td id="due_date"></td>
                                            </tr>
                                            <tr>
                                                <td><b>BG Mundur Bank</b></td>
                                                <td id="from_bank"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Bank Pencairan</b></td>
                                                <td id="realization_bank"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Nominal</b></td>
                                                <td id="giro_amount"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        @include('admin.receivables-payment.list_piutang_create')
        @include('admin.receivables-payment.list_hutang_create')
        @include('admin.receivables-payment.list_lain_lain_create')
    </form>

    <div class="modal fade" id="invoiceEditModal" aria-hidden="true" aria-labelledby="invoiceEditModalLabel" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="invoiceEditModalLabel">Edit Invoice</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="tanggal" id="date_edit" class="datepicker-input" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="no. invoice" id="kode_edit" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="akun piutang" id="coa_edit" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="currency" id="currency_edit" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" id="exchange_rate_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="total" id="total_amount_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="terbayar" id="paid_amount_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="sisa bayar" id="outstanding_amount_edit" class="text-end" required value="0" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receive_amount_edit" class="form-label">Jumlah Bayar</label>
                                <input type="text" label="jumlah bayar" id="receive_amount_edit" required value="" value="" class="form-control commas-form text-end" onkeyup="calculateGapEdit('local')" />
                            </div>
                        </div>
                        <div class="col-md-4" id="multi-currency-form">
                            <div class="form-group">
                                <label for="receive_amount_foreign_edit" class="form-label">Jumlah Bayar </label>
                                <input type="text" label="jumlah bayar" id="receive_amount_foreign_edit" required value="" value="" class="form-control commas-form text-end" onkeyup="calculateGapEdit('foreign')" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receive_amount_gap_foreign_edit" class="form-label">Selisih Bayar </label>
                                <input type="text" label="selisih bayar" id="receive_amount_gap_foreign_edit" required value="" value="0" class="form-control commas-form text-end" readonly />
                            </div>
                        </div>
                        <div class="col-md-2 align-self-center">
                            <x-input-checkbox label="lunas" name="clearing" id="clearing" onclick="clearing()" />
                            @php
                                $clearing_coa = get_default_coa('finance', 'Selisih Bayar');
                            @endphp
                            <input type="hidden" id="default_clearing_coa_id" value="{{ $clearing_coa->coa->id ?? '' }}">
                            <input type="hidden" id="default_clearing_coa_name" value="{{ $clearing_coa->coa->account_code ?? '' }} - {{ $clearing_coa->coa->name ?? '' }}">
                        </div>
                        <div id="clearing_coa_form" class="col-md-12 d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-select name="clearing_coa_id" id="clearing_coa_id" label="coa">
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="keterangan selish bayar" id="clearing_note_edit" required value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="selisih kurs" id="exchange_rate_gap_edit" class="commas-form text-end" required readonly value="0" />
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="exchange_rate_gap_form">
                            <div class="form-group">
                                <x-input type="text" label="keterangan selish kurs" id="exchange_rate_gap_note_edit" required value="" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-input type="text" label="keterangan pembayaran" id="note_edit" required value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="edited_invoice_id">
                    <x-button color="info" label="simpan" type="button" onclick="" id="btn-update-selected-invoice" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="supplierInvoiceEditModal" aria-hidden="true" aria-labelledby="supplierInvoiceEditModalLabel" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="supplierInvoiceEditModalLabel">Edit Purchase Invoice</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="tanggal" id="date_edit_vendor" class="datepicker-input" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="no. purchase invoice" id="code_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="no. faktur pajak" id="reference_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="akun hutang" id="coa_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="currency" id="currency_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" id="exchange_rate_edit_vendor" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="total" id="total_amount_edit_vendor" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="terbayar" id="paid_amount_edit_vendor" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table d-none" id="lpb-table">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>No LPB</th>
                                            <th>Sisa</th>
                                            <th class="label-lpb-amount">Jumlah</th>
                                            <th class="label-lpb-foreign column-multi-currency">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lpb-table-body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="sisa bayar" id="outstanding_amount_edit_vendor" class="text-end" required value="0" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_edit" class="form-label"></label>
                                <input type="text" label="jumlah bayar" id="amount_edit_vendor" required value="" class="form-control commas-form text-end" onkeyup="calculateGapEditVendor('local')" />
                            </div>
                        </div>
                        <div class="col-md-4" id="multi-currency-form-vendor">
                            <div class="form-group">
                                <label for="amount_foreign_edit" class="form-label"></label>
                                <input type="text" label="jumlah bayar" id="amount_foreign_edit_vendor" required value="" value="0" class="form-control commas-form text-end" onkeyup="calculateGapEditVendor('foreign')" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_gap_foreign_edit" class="form-label">Selisih Bayar </label>
                                <input type="text" label="selisih bayar" id="amount_gap_foreign_edit_vendor" required value="" value="0" class="form-control commas-form text-end" readonly />
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-self-end">
                            <div class="form-group">
                                <x-input-checkbox label="lunas" name="clearing" id="clearing_vendor" onclick="clearingVendor()" />
                                @php
                                    $clearing_coa = get_default_coa('finance', 'Selisih Bayar');
                                @endphp
                                <input type="hidden" id="default_clearing_coa_id_vendor" value="{{ $clearing_coa->coa->id ?? '' }}">
                                <input type="hidden" id="default_clearing_coa_name_vendor" value="{{ $clearing_coa->coa->account_code ?? '' }} - {{ $clearing_coa->coa->name ?? '' }}">
                            </div>
                        </div>
                        <div id="clearing_coa_form_vendor" class="col-md-12 d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-select name="clearing_coa_id_vendor" id="clearing_coa_id_vendor" label="coa">
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="keterangan selish bayar" id="clearing_note_edit_vendor" required value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="selisih kurs" id="exchange_rate_gap_edit_vendor" class="commas-form text-end" required readonly value="0" />
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="exchange_rate_gap_form_vendor">
                            <div class="form-group">
                                <x-input type="text" label="keterangan selish kurs" id="exchange_rate_gap_note_edit_vendor" required value="" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-input type="text" label="keterangan pembayaran" id="note_edit_vendor" required value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="edited_supplier_invoice_parent_id_vendor">
                    <x-button color="info" label="simpan" type="button" onclick="" id="btn-update-selected-supplier-invoice" />
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/receivables-payment/transaction.js') }}?v=9.12.2025"></script>
    <script src="{{ asset('js/admin/receivables-payment/adjustment.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/receivables-payment/lpb.js') }}"></script>
    <script src="{{ asset('js/admin/receivables-payment/invoice-return.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#receivables-payment')
        checkClosingPeriod($('#date'));

        initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,negara"
        });

        initSelect2Search(`invoice_currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,negara"
        });

        initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: "Cash & Bank",
            currency_id: function() {
                return $('#currency_id').val();
            }
        });

        initSelect2Search(`receive_payment_id`, `{{ route('admin.select.receive-payment') }}`, {
            id: "id",
            text: "from_bank,cheque_no"
        }, 0, {
            branch_id: function() {
                return $('#branch_id').val();
            },
            customer_id: function() {
                return $('#customer_id').val();
            },
            currency_id: function() {
                return $('#currency_id').val();
            },
            date: function() {
                return $('#date').val();
            },
            status: 'approve'
        });

        initSelect2SearchPagination(`clearing_coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, [],
            '#invoiceEditModal'
        );

        initSelect2SearchPagination(`clearing_coa_id_vendor`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, [],
            '#supplierInvoiceEditModal'
        );

        function checkCurrency() {
            $('#selected_invoice_table').html('');
            calculateData();

            var selected_currency_id = $('#currency_id').val();
            var selected_invoice_currency_id = $('#invoice_currency_id').val();
            var is_selected_currency_id_local = false;
            var is_selected_invoice_currency_id_local = false;

            $.ajax({
                type: "get",
                url: `{{ route('admin.currency.detail') }}/${selected_currency_id}`,
                success: function({
                    data
                }) {
                    if (data.is_local) {
                        is_selected_currency_id_local = true;
                    } else {
                        is_selected_currency_id_local = false;
                    }
                    $('#currency_id_symbol').val(data.kode);
                    $('.currency_id_symbol').text(data.kode);
                },
                complete: function(data) {
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.currency.detail') }}/${selected_invoice_currency_id}`,
                        success: function({
                            data
                        }) {
                            if (data.is_local) {
                                is_selected_invoice_currency_id_local = true;
                            } else {
                                is_selected_invoice_currency_id_local = false;
                            }

                            $('.invoice_currency_id_symbol').text(data.kode);
                        },
                        complete: function(data) {
                            if (is_selected_currency_id_local && is_selected_invoice_currency_id_local) {
                                $('#exchange_rate').val(1);
                                $('#exchange_rate').attr('readonly', 'readonly');
                                $('#exchange_rate').data('is-both-local', 'true');
                            } else {
                                $('#exchange_rate').removeAttr('readonly');
                                $('#exchange_rate').attr('readonly', false);
                                $('#exchange_rate').data('is-both-local', 'false');
                            }
                        }
                    });
                }
            });
        }

        checkCurrency()

        initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
            id: "id",
            text: "code,name"
        }, 2, {
            branch_id: function() {
                return $('#branch_id').val();
            }
        });

        initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        });

        function initInvoiceCurrency() {
            let get_local_currency_id = $('#local_currency_id').val();
            let get_selected_currency_id = $('#currency_id').val();

            let allow_foreign = true;
            $('.column-multi-currency').addClass('d-none');
            if (get_local_currency_id != get_selected_currency_id) {
                allow_foreign = false;
                $('.column-multi-currency').removeClass('d-none');
            }

            initSelect2SearchCurrencyWithCondition(`invoice_currency_id`, `{{ route('admin.select.currency-with-condition') }}`, {
                id: "id",
                text: "kode,negara"
            }, allow_foreign, get_selected_currency_id);
        }

        initCurrency();

        function initCurrency() {
            let get_local_currency_id = $('#local_currency_id').val();
            let get_selected_currency_id = $('#invoice_currency_id').val();

            let allow_foreign = true;
            $('.column-multi-currency').addClass('d-none');
            if (get_local_currency_id != get_selected_currency_id) {
                allow_foreign = false;
                $('.column-multi-currency').removeClass('d-none');
            }

            initSelect2SearchCurrencyWithCondition(`currency_id`, `{{ route('admin.select.currency-with-condition') }}`, {
                id: "id",
                text: "kode,negara"
            }, allow_foreign, get_selected_currency_id);
        }

        $('#sequence_code').on('blur', function() {
            check_bank_code(
                '#coa_id',
                '#sequence_code',
                '#date',
                'in'
            );
        });

        $('#currency_id').change(function() {
            $('#coa_id').val(null).trigger('change');
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
@endsection
