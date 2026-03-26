@extends('layouts.admin.layout.index')

@php
    $title = 'pengembalian uang muka customer';
    $route = 'cash-advance-return-customer';
    $main = 'cash-advance-return';
@endphp

@section('title', Str::headline("Tambah $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$route.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route("admin.$route.store") }}" method="post" id="form-create-cash-advance-data">
            <x-card-data-table title='{{ "Tambah $title" }}'>

                <x-slot name="table_content">
                    @include('components.validate-error')
                    @csrf

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="reference_id" label="customer" id="customer-select" required autofocus>

                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" id="date" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="currency_id" label="mata uang" id="currency-select" required>
                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="invoice_currency_id" label="mata uang invoice" id="invoice-select-currency">
                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="exchange_rate" label="nilai tukar" id="exchange-rate-form" class="commas-form" value="1" required readonly />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-20">
                        @if (get_current_branch()->is_primary)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="branch_id" label="branch" id="select-branch" required>

                                    </x-select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="project_id" label="project" id="select-project">

                                </x-select>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Tambah Uang Muka">
                <x-slot name="table_content">

                    <div class="d-flex flex-column">
                        <div>
                            <x-button color="primary" class="float-end" id="add-cash-advance-btn" label="Tambah uang muka" icon="plus" />
                        </div>

                        <div class="mt-20">
                            <x-table id="cash-advance-receive-resume">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Tanggal') }}</th>
                                    <th>{{ Str::headline('Nomor transaksi') }}</th>
                                    <th>{{ Str::headline('mata uang/ nilai tukar') }}</th>
                                    <th>{{ Str::headline('jumlah yang di return') }}</th>
                                    <th>{{ Str::headline('balance') }}</th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body"></x-slot>
                            </x-table>
                        </div>
                    </div>

                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Tambah Invoice">
                <x-slot name="table_content">
                    <div class="d-flex flex-column">
                        <div>
                            <x-button color="primary" class="float-end" id="add-invoice-modal-btn" label="Tambah Invoice" icon="plus" />
                        </div>
                        <div class="mt-10">
                            <x-table id="invoice-data-resume">
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('nomor transaksi') }}</th>
                                    <th>{{ Str::headline('mata uang / nilai tukar') }}</th>
                                    <th>{{ Str::headline('total') }}</th>
                                    <th>{{ Str::headline('sisa invoice') }}</th>
                                    <th>{{ Str::headline('nilai dikembalikan') }}</th>
                                    <th>{{ Str::headline('selisih bayar') }}</th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Tambah Transaksi lainnya">
                <x-slot name="table_content">

                    <div class="d-flex flex-column">
                        <div>
                            <x-button color="primary" class="float-end" id="add-other-transaction-modal-btn" label="Tambah transaksi lainnya" icon="plus" />
                        </div>

                        <div class="mt-20">
                            <x-table id="other-transaction-resume">
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('Nomor accunt') }}</th>
                                    <th>{{ Str::headline('amount') }}</th>
                                    <th>{{ Str::headline('description') }}</th>
                                    <th>{{ Str::headline('') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                        <div class="mt-20">
                            <x-table id="transaction-resume">
                                <x-slot name="table_body">
                                    <tbody>
                                        <tr>
                                            <th>
                                                Total Uang Muka
                                                <input type="hidden" id="cash_advance_total">
                                            </th>
                                            <th id="cash_advance_total_display" class="text-end"></th>
                                        </tr>
                                        <tr>
                                            <th>
                                                Total Invoice
                                                <input type="hidden" id="invoice_total">
                                            </th>
                                            <th id="invoice_total_display" class="text-end"></th>
                                        </tr>
                                        <tr>
                                            <th>
                                                Total Lain Lain
                                                <input type="hidden" id="other_total">
                                            </th>
                                            <th id="other_total_display" class="text-end"></th>
                                        </tr>
                                        <tr>
                                            <th>
                                                Selisih Uang Muka dan Pembayaran
                                                <input type="hidden" id="gap_total">
                                            </th>
                                            <th id="gap_total_display" class="text-end"></th>
                                        </tr>
                                    </tbody>
                                </x-slot>
                            </x-table>
                        </div>
                    </div>

                </x-slot>
                <x-slot name="footer">
                    <x-button type="submit" color="primary" class="float-end" label="Save" icon="plus" />
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="invoice payment information">
                <x-slot name="table_content">
                    <div class="d-flex flex-column">
                        <div class="mt-10">
                            <x-table id="invoice-payment-information">
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('total') }}</th>
                                    <th>{{ Str::headline('bayar') }}</th>
                                    <th>{{ Str::headline('keterangan') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>

        <x-modal title="Tambah transaksi lainnya" headerColor="primary" id="add-other-transaction-modal" modalSize="900">
            <x-slot name="modal_body">
                <div class="row">
                    <div class="col-md-4">
                        <x-select name="" id="other-transaction-account" label="nomor acccount" required>

                        </x-select>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="" id="other-transaction-amount" label="amount" class="commas-form" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-text-area name="" id="other-transaction-description" label="deskripsi" labe="description" cols="30" rows="10" required></x-text-area>
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                <x-button type="button" color="primary" id="save-other-trasaction-data" label="Save" />
            </x-slot>
        </x-modal>

        <x-modal title="Tambah Invoice" headerColor="primary" id="add-invoice-modal" modalSize="900">
            <x-slot name="modal_body">

                <div>
                    <x-table id="invoice-data-list">
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>{{ Str::headline('tanggal') }}</th>
                            <th>{{ Str::headline('nomor transaksi') }}</th>
                            <th>{{ Str::headline('mata uang / nilai tukar') }}</th>
                            <th>{{ Str::headline('total') }}</th>
                            <th>{{ Str::headline('outstanding amount') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </div>

            </x-slot>

            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                <x-button type="button" color="primary" id="save-invoice-data" label="Save" />
            </x-slot>
        </x-modal>

        <x-modal title="Tambah Uang muka" headerColor="primary" id="add-cash-advance" modalSize="900">
            <x-slot name="modal_body">
                <x-table theadColor="danger" id="cash-advance-receive">
                    <x-slot name="table_head">
                        <th>#</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Nomor transaksi') }}</th>
                        <th>{{ Str::headline('nomor akun') }}</th>
                        <th>{{ Str::headline('mata uang / nilai tukar') }}</th>
                        <th>{{ Str::headline('amount') }}</th>
                        <th>{{ Str::headline('outstanding amount') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body"></x-slot>
                </x-table>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                <x-button type="button" color="primary" id="save-cash-advance-data" label="Save" />
            </x-slot>
        </x-modal>

        <x-modal title="Edit Uang muka" headerColor="primary" id="edit-cash-advance" modalSize="1000">
            <x-slot name="modal_body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="date" id="cash-advance-date" label="tanggal" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="trasaction_no" id="cash-advance-trasaction-no" label="nomor_transaksi" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="account_code" id="cash-advance-account-code" label="nomor_account" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="currency" id="cash-advance-currency" label="mata_uang" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="rate" id="cash-advance-rate" label="kurs" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="total_amount" id="cash-advance-total-amount" label="total_amount" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="returned_amount" id="cash-advance-returned-amount" label="returned_amount" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="outstanding_amount" id="cash-advance-outstanding-amount" label="outstanding_amount" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="amount_to_return" id="cash-advance-amount-to-return" label="amount_to_return" class="commas-form" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="description" id="cash-advance-description" label="description" disabled />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="primary" id="save-edit-cash-advance-data" label="Save" />
            </x-slot>
        </x-modal>

        <x-modal title="Edit Invoice" headerColor="primary" id="edit-invoice-modal" modalSize="1000">
            <x-slot name="modal_body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="date" label="tanggal" readonly id="edit-invoice-date" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="transaction-number" label="nomor-transaksi" readonly id="edit-invoice-transaction-number" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="account-code" label="nomor akun" readonly id="edit-invoice-account-code" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="currency" label="mata-uang" readonly id="edit-invoice-currency" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="exchange-rate" label="" readonly id="edit-invoice-exchange-rate" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="total-amount" label="total-amount" readonly id="edit-invoice-total-amount" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="paid-amount" label="paid-amount" readonly id="edit-invoice-paid-amount" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="outstanding-amount" label="outstanding-amount" readonly id="edit-invoice-outstanding-amount" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="amount-to-return" label="amount to return" class="commas-form" id="edit-invoice-amount-to-return" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="exchange-rate-gap" label="exchange-rate-gap" readonly id="edit-invoice-exchange-rate-gap" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="description" label="description" id="edit-invoice-description" required />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </x-slot>

            <x-slot name="modal_footer">
                <x-button type="button" color="primary" id="save-edit-invoice-data" label="Save" />
            </x-slot>
        </x-modal>

        <x-modal title="Edit transaksi lainnya" headerColor="primary" id="edit-other-transaction-modal" modalSize="900">
            <x-slot name="modal_body">
                <div class="row">
                    <div class="col-md-4">
                        <x-select name="account_code" id="edit-other-transaction-account" label="nomor acccount" required>

                        </x-select>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="amount" id="edit-other-transaction-amount" label="amount" class="commas-form" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-text-area name="description" id="edit-other-transaction-description" labe="description" cols="30" rows="10" required></x-text-area>
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                <x-button type="button" color="primary" id="save-other-trasaction-edit" label="Save" />
            </x-slot>
        </x-modal>
    @endcan
@endsection

@section('js')
    @can("create $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/admin/select/project.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>

        <script>
            $(document).ready(function() {
                initCommasForm();
                checkClosingPeriod($('#date'));

                $('.modal').css('overflow-y', 'auto');

                let CUSTOMER_ID = null,
                    PROJECT_ID = null,
                    CURRENCY_ID = "{{ get_local_currency()->id }}";


                let CASH_ADVANCE_RECEIVE_LIST = [],
                    SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA = [],
                    INVOICE_LIST = [],
                    SELECTED_INVOICE_LIST_DATA = [],
                    OTHER_TRANSACTION_LIST = [],
                    OTHER_TRANSACTION_INDEX = 1;

                let parent_currency = @json(get_local_currency()),
                    invoice_currency = @json(get_local_currency());

                const initCoaSelectCashAdvancedReturn = (element, modal_id) => {
                    var select2Option = {
                        dropdownParent: $(modal_id),
                        placeholder: "Pilih Data",
                        minimumInputLength: 3,
                        allowClear: true,
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
                            url: `${base_url}/select/coa`,
                            dataType: "json",
                            delay: 250,
                            type: "get",
                            data: (params) => {
                                let result = {};
                                result["search"] = params.term;
                                result['page_limit'] = 10;
                                result['page'] = params.page;
                                return result;
                            },
                            processResults: (data, params) => {
                                params.page = params.page || 1;
                                let final_data = data.data.data.map((data, key) => {
                                    return {
                                        id: data.id,
                                        text: `${data.account_code} - ${data.name}`,
                                    };
                                });
                                return {
                                    results: final_data,
                                    pagination: {
                                        more: (params.page * 10) < data.data.total
                                    }
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

                const firstCardFuntion = () => {

                    const currencyTrigger = () => {

                        const resetCurrencyValue = () => {
                            parent_currency = @json(get_local_currency());
                            invoice_currency = @json(get_local_currency());

                            $('#exchange-rate-form').val(1);

                            $('#currency-select').val(parent_currency.id)
                            $('#invoice-select-currency').val(parent_currency.id)

                            $('#currency-select').html(`<option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>`);
                            $('#invoice-select-currency').html(`<option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>`);

                            initSelect2Search('currency-select', "{{ route('admin.select.currency') }}", {
                                id: "id",
                                text: "kode,nama,negara"
                            });

                            initSelect2Search('invoice-select-currency', "{{ route('admin.select.currency') }}", {
                                id: "id",
                                text: "kode,nama,negara"
                            });

                            alert("pastikan salah satu currency local, atau gunakan currency yang sama")
                        };

                        const handleCurrency = () => {

                            if ((!parent_currency.is_local && !invoice_currency.is_local) && (parent_currency.id != invoice_currency.id)) {
                                resetCurrencyValue();
                                return;
                            }

                            if (parent_currency.is_local && invoice_currency.is_local) {
                                $('#exchange-rate-form').val(1).attr('readonly', true);
                            } else {
                                $('#exchange-rate-form').val('1').attr('readonly', false);
                            }
                        };

                        initSelect2Search('currency-select', "{{ route('admin.select.currency') }}", {
                            id: "id",
                            text: "nama"
                        });

                        $('#currency-select').change(function(e) {
                            e.preventDefault();

                            if (this.value) {
                                CURRENCY_ID = this.value;
                                secondCardFunction();
                                thirdCardFunction();
                                fourthCardFunction();

                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        parent_currency = data;
                                        handleCurrency();
                                    }
                                });
                            }
                        });

                        initSelect2Search('invoice-select-currency', "{{ route('admin.select.currency') }}", {
                            id: "id",
                            text: "nama"
                        });

                        $('#invoice-select-currency').change(function(e) {
                            e.preventDefault();

                            if (this.value) {
                                fourthCardFunction();
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        invoice_currency = data;
                                        handleCurrency();
                                    }
                                });
                            }
                        });
                    };

                    const initCard = () => {
                        CASH_ADVANCE_RECEIVE_LIST = [];
                        SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA = [];

                        initSelect2Search('customer-select', "{{ route('admin.select.customer') }}", {
                            id: "id",
                            text: "nama"
                        });

                        initSelect2Search('select-branch', "{{ route('admin.select.branch') }}", {
                            id: "id",
                            text: "name"
                        });

                        initProjectSelect('#select-project');

                        $('#customer-select').change(function(e) {
                            e.preventDefault();

                            if (this.value) {
                                CUSTOMER_ID = this.value;
                                secondCardFunction();
                                thirdCardFunction();
                                fourthCardFunction();
                            }
                        });

                        $('#select-project').change(function(e) {
                            e.preventDefault();

                            if (this.value) {
                                PROJECT_ID = this.value;
                                secondCardFunction();
                                thirdCardFunction();
                                fourthCardFunction();
                            }
                        });

                        $('#select-branch').change(function(e) {
                            e.preventDefault();

                            if (this.value) {
                                BRANCH_ID = this.value;
                                secondCardFunction();
                                thirdCardFunction();
                                fourthCardFunction();
                            }
                        });
                    };

                    const initializeFirstCard = () => {
                        currencyTrigger();
                        initCard();
                    }

                    initializeFirstCard();
                };

                const secondCardFunction = () => {

                    const initializeSecondCard = () => {
                        getData();
                        triggerModalAddCashAdvanceReceive();
                    };

                    const getData = () => {
                        $.ajax({
                            type: "post",
                            url: `{{ route('admin.cash-advance-return-customer.cash-advance-receives') }}/${CUSTOMER_ID}/${CURRENCY_ID ?? ''}/${PROJECT_ID ?? ''}`,
                            data: {
                                _token: token
                            },
                            success: function({
                                data
                            }) {
                                CASH_ADVANCE_RECEIVE_LIST = data;
                                SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA = [];
                                displayCashAdvancedReturnData();
                            }
                        });
                    };

                    const displayCashAdvancedReturnData = () => {

                        $('#cash-advance-receive tbody').html('');

                        CASH_ADVANCE_RECEIVE_LIST.map((cash_advance_receive, cash_advance_receive_index) => {
                            let {
                                cash_advance_receive_details
                            } = cash_advance_receive;

                            cash_advance_receive_details.map((cash_advance_receive_detail, cash_advance_receive_detail_index) => {

                                if (cash_advance_receive_detail.type == 'cash_advance') {
                                    $('#cash-advance-receive tbody').append(`
                                    <tr>
                                        <td>${cash_advance_receive_index + 1}</td>
                                        <td>${localDate(cash_advance_receive.date)}</td>
                                        <td>${cash_advance_receive.bank_code_mutation ?? cash_advance_receive.code}</td>
                                        <td>${cash_advance_receive_detail.coa.account_code} - ${cash_advance_receive_detail.coa.name}</td>
                                        <td>${cash_advance_receive.currency.nama} - ${formatRupiahWithDecimal(cash_advance_receive.exchange_rate)}</td>
                                        <td>${cash_advance_receive.currency.simbol} ${formatRupiahWithDecimal(cash_advance_receive_detail.credit)}</td>
                                        <td>
                                            <span class="d-flex">
                                                <span class="me-5">${cash_advance_receive.currency.simbol}</span>
                                                <span id="cash-advance-receive-outstanding-amount-${cash_advance_receive_index}">${formatRupiahWithDecimal(cash_advance_receive_detail.credit - cash_advance_receive_detail.cash_advance_return_total)}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <x-button color="primary" class="float-end" id="edit-cash-advance-data-${cash_advance_receive_index}" label="" icon="pen-to-square" size="sm" fontawesome />
                                                <x-input-checkbox label="-" name="check" id="checkbox-cash-advance-${cash_advance_receive_index}" hideAsterix />
                                            </div>
                                        </td>
                                    </tr>
                                    `);

                                    SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA[cash_advance_receive_index] = {
                                        selected: false,
                                        amount: 0,
                                        balance: 0,
                                        outstanding_amount: cash_advance_receive_detail.credit - cash_advance_receive_detail.cash_advance_return_total,
                                        data: cash_advance_receive
                                    };

                                    $(`#edit-cash-advance-data-${cash_advance_receive_index}`).click(function(e) {
                                        e.preventDefault();
                                        editCashAdvanceReceive(cash_advance_receive_index);
                                    });

                                    $(`#checkbox-cash-advance-${cash_advance_receive_index}`).click(function(e) {
                                        SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA[cash_advance_receive_index].selected = this.checked ? true : false;
                                    });

                                }
                            });
                        });
                    };

                    const triggerModalAddCashAdvanceReceive = () => {
                        $('#add-cash-advance-btn').click(function(e) {
                            e.preventDefault();

                            $('#add-cash-advance').modal('show');
                        });

                        $('#save-cash-advance-data').click(function(e) {
                            e.preventDefault();

                            $('#add-cash-advance').modal('hide');
                            displayCashAdvancedResume();
                            calculateTransactionResume();
                        });
                    };

                    const editCashAdvanceReceive = (cash_advance_index) => {
                        $('#edit-cash-advance').modal('show');

                        let single_data = SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA[cash_advance_index];
                        let latest_amount = single_data.amount;

                        single_data.data.cash_advance_receive_details.map((detail, single_index) => {
                            if (detail.type == 'cash_advance') {
                                $(`#cash-advance-date`).val(localDate(single_data.data.date));
                                $(`#cash-advance-trasaction-no`).val(single_data.data.bank_code_mutation ?? single_data.data.code);
                                $(`#cash-advance-account-code`).val(`${detail.coa.account_code} - ${detail.coa.name}`);
                                $(`#cash-advance-currency`).val(single_data.data.currency.nama);
                                $(`#cash-advance-rate`).val(formatRupiahWithDecimal(single_data.data.exchange_rate));
                                $(`#cash-advance-total-amount`).val(formatRupiahWithDecimal(detail.credit));
                                $(`#cash-advance-returned-amount`).val(formatRupiahWithDecimal(detail.cash_advance_return_total));
                                $(`#cash-advance-outstanding-amount`).val(formatRupiahWithDecimal(detail.credit - detail.cash_advance_return_total));
                                if (single_data.amount != 0) {
                                    $(`#cash-advance-amount-to-return`).val(formatRupiahWithDecimal(single_data.amount));
                                } else {
                                    $('#cash-advance-amount-to-return').val('');
                                }
                                $(`#cash-advance-description`).val(detail.note);
                            }
                        })

                        $('#save-edit-cash-advance-data').click(function(e) {
                            e.preventDefault();

                            if (single_data.data.tax_id) {
                                if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) != single_data.outstanding_amount) {
                                    alert('jumlah uang muka tidak sama dengan sisa uang muka');
                                    return false;
                                }
                            }

                            if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) > single_data.outstanding_amount) {
                                alert('jumlah tidak boleh melebihi sisa uang muka');
                                return false;
                            }

                            if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) == 0) {
                                alert('jumlah tidak boleh 0');
                                return false;
                            }

                            single_data.amount = thousandToFloat($(`#cash-advance-amount-to-return`).val());
                            single_data.balance = single_data.outstanding_amount - single_data.amount;

                            $(`#cash-advance-receive-amount-to-return-html-${cash_advance_index}`).html(formatRupiahWithDecimal(single_data.amount));
                            $(`#cash-advance-receive-outstanding-amount-${cash_advance_index}`).html(formatRupiahWithDecimal(single_data.outstanding_amount));
                            $(`#cash-advance-receive-balance-${cash_advance_index}`).html(formatRupiahWithDecimal(single_data.balance));
                            $('#edit-cash-advance').modal('hide');

                            calculateTransactionResume();
                        });

                        $('#edit-cash-advance').on('hide.bs.modal', function() {
                            $('#save-edit-cash-advance-data').unbind('click');
                        });
                    };

                    const displayCashAdvancedResume = () => {
                        $('#cash-advance-receive-resume tbody').html('');

                        SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA.map((cash_advance_receive, cash_advance_index) => {
                            if (cash_advance_receive.selected) {
                                let {
                                    data
                                } = cash_advance_receive;

                                data.cash_advance_receive_details.map((cash_advance_receive_detail, cash_advance_receive_detail_index) => {
                                    if (cash_advance_receive_detail.type == 'cash_advance') {
                                        $('#cash-advance-receive-resume tbody').append(`
                                    <tr>
                                        <td>${cash_advance_index + 1}</td>
                                        <td>${localDate(data.date)}</td>
                                        <td>${data.bank_code_mutation ?? data.code}</td>
                                        <td>${data.currency.nama} / ${data.exchange_rate}</td>
                                        <td>
                                            <span class="d-flex">
                                                <span class="me-5">${data.currency.simbol}</span>
                                                <span id="cash-advance-receive-amount-to-return-html-${cash_advance_index}">${formatRupiahWithDecimal(cash_advance_receive.amount)}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-flex">
                                                <span class="me-5">${data.currency.simbol}</span>
                                                <span id="cash-advance-receive-balance-${cash_advance_index}">${formatRupiahWithDecimal(cash_advance_receive.balance)}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <input type="hidden" name="cash_advance_receives[]" value="${data.id}" />
                                            <input type="hidden" name="cash_advance_receive_amount_to_returns[]" value="${cash_advance_receive.amount}" />
                                        </td>
                                    </tr>
                                    `);
                                    }
                                });
                            }
                        })
                    };

                    initializeSecondCard();
                };

                const thirdCardFunction = () => {

                    let THIRD_CARD_CURRENCY = null;

                    INVOICE_LIST = [];
                    SELECTED_INVOICE_LIST_DATA = [];

                    const getValueOfGap = (invoice_index) => {

                        let parent_exchange_rate = thousandToFloat($('#exchange-rate-form').val());

                        let this_invoice_currency = SELECTED_INVOICE_LIST_DATA[invoice_index].data.currency;
                        let this_data = SELECTED_INVOICE_LIST_DATA[invoice_index].data;

                        // BOTH CURRENCY NOT LOCAL OR INVOICE CURRENCY NOT LOCAL
                        if ((!parent_currency.is_local && !this_invoice_currency.is_local) || !this_invoice_currency.is_local) {
                            return (parent_exchange_rate - this_data.exchange_rate) * thousandToFloat($('#edit-invoice-amount-to-return').val());
                        } else {
                            return 0;
                        }
                    };

                    const displayDataModal = () => {
                        $('#invoice-data-list tbody').html('');

                        INVOICE_LIST.map((invoice, invoice_index) => {
                            $('#invoice-data-list tbody').append(`
                            <tr>
                                <td>${invoice_index + 1}</td>
                                <td>${localDate(invoice.date)}</td>
                                <td>${invoice.code}</td>
                                <td>${invoice.currency.nama} / ${formatRupiahWithDecimal(invoice.exchange_rate)}</td>
                                <td>${invoice.currency.simbol} ${formatRupiahWithDecimal(invoice.total)}</td>
                                <td>
                                    <span class="d-flex">
                                        <span class="me-5">${invoice.currency.simbol}</span>
                                        <span>
                                            ${formatRupiahWithDecimal(invoice.outstanding_amount)}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <x-button color="primary" class="float-end" id="edit-cash-advance-return-invoice-modal-btn-${invoice_index}" label="" icon="pen-to-square" size="sm" fontawesome />
                                        <x-input-checkbox label="-" name="check" id="check-cash-advance-return-invoice-${invoice_index}" hideAsterix />
                                    </div>
                                </td>
                            </tr>
                            `);

                            SELECTED_INVOICE_LIST_DATA[invoice_index] = {
                                selected: false,
                                data: invoice,
                                amount_to_return: 0,
                                description: null,
                            };

                            $(`#edit-cash-advance-return-invoice-modal-btn-${invoice_index}`).click(function(e) {
                                e.preventDefault();
                                editInvoice(invoice_index)
                            });

                            $(`#check-cash-advance-return-invoice-${invoice_index}`).click(function(e) {
                                SELECTED_INVOICE_LIST_DATA[invoice_index].selected = this.checked ? true : false;
                            });
                        });
                    };

                    const displayResumeData = () => {
                        $('#invoice-data-resume tbody').html('');

                        SELECTED_INVOICE_LIST_DATA.map((selected_data, selected_data_index) => {
                            if (selected_data.selected) {
                                $('#invoice-data-resume tbody').append(`
                                <tr>
                                    <td>${selected_data_index + 1}</td>
                                    <td>${localDate(selected_data.data.date)}</td>
                                    <td>${selected_data.data.code}</td>
                                    <td>${selected_data.data.currency.nama} / ${formatRupiahWithDecimal(selected_data.data.exchange_rate)}</td>
                                    <td>${selected_data.data.currency.simbol} ${formatRupiahWithDecimal(selected_data.data.total)}</td>
                                    <td>${selected_data.data.currency.simbol} ${formatRupiahWithDecimal(selected_data.data.outstanding_amount)}</td>
                                    <td>${selected_data.data.currency.simbol} ${formatRupiahWithDecimal(selected_data.amount_to_return)}</td>
                                    <td>${selected_data.data.currency.simbol} ${formatRupiahWithDecimal(selected_data.data.outstanding_amount - selected_data.amount_to_return)}</td>
                                    <td>
                                        <input type="hidden" name="invoice_ids[]" value="${selected_data.data.id}" />
                                        <input type="hidden" name="invoice_amount_to_returns[]" value="${selected_data.amount_to_return}" />
                                        <input type="hidden" name="invoice_descriptions[]" value="${selected_data.description}" />
                                    </td>
                                </tr>
                                `);
                            }
                        });
                    };

                    const handleAddModal = () => {

                        $.ajax({
                            type: "post",
                            url: `{{ route('admin.cash-advance-return-customer.unpaid-full-invoices') }}/${CUSTOMER_ID ?? ''}/${$('#invoice-select-currency').val()}`,
                            data: {
                                _token: token,
                            },
                            success: function({
                                data
                            }) {
                                INVOICE_LIST = data;
                                SELECTED_INVOICE_LIST_DATA = [];
                                displayDataModal();
                            }
                        });

                        $('#add-invoice-modal-btn').click(function(e) {
                            e.preventDefault();
                            $('#add-invoice-modal').modal('show');
                        });

                        $('#save-invoice-data').unbind('click');

                        $('#save-invoice-data').click(function(e) {
                            e.preventDefault();

                            displayResumeData();
                            calculateTransactionResume();
                            displayInvoicePaymentInformation();
                            $('#add-invoice-modal').modal('hide');
                        });
                    };

                    const displayInvoicePaymentInformation = () => {
                        $('#invoice-payment-information tbody').html('');
                        $.ajax({
                            url: `${base_url}/invoice-payment-information`,
                            type: 'post',
                            data: {
                                _token: token,
                                invoice_ids: SELECTED_INVOICE_LIST_DATA.map((data) => {
                                    if (data.selected) {
                                        return data.data.id
                                    }
                                }),
                            },
                            success: function(response) {
                                $.each(response, function(key, value) {
                                    $('#invoice-payment-information tbody').append(`
                                    <tr>
                                        <td colspan="5" class="bg-info">${value.code}</td>
                                    </tr>
                                    `);

                                    $.each(value.payment_informations, function(key2, value2) {
                                        $('#invoice-payment-information tbody').append(`
                                            <tr>
                                                <td>${key2 + 1}</td>
                                                <td>${localDate(value2.date)}</td>
                                                <td>${value2.currency.simbol} ${formatRupiahWithDecimal(value2.amount_to_receive)}</td>
                                                <td>${value2.currency.simbol} ${formatRupiahWithDecimal(value2.receive_amount)}</td>
                                                <td>${value2.note}</td>
                                            </tr>
                                            `);
                                    });
                                })
                            }
                        });
                    }

                    const editInvoice = (invoice_index) => {
                        let {
                            data,
                            amount_to_return,
                            description,
                        } = SELECTED_INVOICE_LIST_DATA[invoice_index];

                        let {
                            date,
                            code,
                            currency,
                            exchange_rate,
                            total,
                            paid_amount,
                            outstanding_amount,
                            customer,
                        } = data;

                        let latest_amout_to_return = amount_to_return;
                        let this_gap = getValueOfGap(invoice_index);

                        $('#edit-invoice-date').val(localDate(date));
                        $('#edit-invoice-transaction-number').val(code);
                        $('#edit-invoice-account-code').val();
                        $('#edit-invoice-currency').val(`${currency.nama} / ${currency.simbol}`);
                        $('#edit-invoice-exchange-rate').val(formatRupiahWithDecimal(exchange_rate));
                        $('#edit-invoice-total-amount').val(formatRupiahWithDecimal(total));
                        $('#edit-invoice-paid-amount').val(formatRupiahWithDecimal(paid_amount));
                        $('#edit-invoice-outstanding-amount').val(formatRupiahWithDecimal(total - paid_amount));
                        if (amount_to_return != 0) {
                            $('#edit-invoice-amount-to-return').val(formatRupiahWithDecimal(amount_to_return));
                        } else {
                            $('#edit-invoice-amount-to-return').val('');
                        }
                        $('#edit-invoice-exchange-rate-gap').val(formatRupiahWithDecimal(this_gap));
                        $('#edit-invoice-description').val(description);
                        initCommasForm();

                        $('#edit-invoice-modal').modal('show');

                        $('#edit-invoice-amount-to-return').keyup(function(e) {
                            e.preventDefault();

                            this_gap = getValueOfGap(invoice_index);

                            $('#edit-invoice-exchange-rate-gap').val(formatRupiahWithDecimal(this_gap));
                        });

                        $('#save-edit-invoice-data').click(function(e) {
                            e.preventDefault();

                            date = $('#edit-invoice-date').val();
                            transaction_number = $('#edit-invoice-transaction-number').val();
                            account_code = $('#edit-invoice-account-code').val();
                            currency = $('#edit-invoice-currency').val();
                            exchange_rate = $('#edit-invoice-exchange-rate').val();
                            total_amount = $('#edit-invoice-total-amount').val();
                            paid_amount = $('#edit-invoice-paid-amount').val();
                            amount_to_return = thousandToFloat($('#edit-invoice-amount-to-return').val());
                            exchange_rate_gap = $('#edit-invoice-exchange-rate-gap').val();
                            description = $('#edit-invoice-description').val();

                            if (description == '') {
                                alert('Description must be filled');
                                return;
                            }

                            if (amount_to_return == 0) {
                                alert('Jumlah tidak boleh 0');
                                return;
                            }

                            if (amount_to_return > outstanding_amount) {
                                alert('Jumlah tidak boleh melebihi sisa tagihan');
                                return;
                            }

                            SELECTED_INVOICE_LIST_DATA[invoice_index].description = description;
                            SELECTED_INVOICE_LIST_DATA[invoice_index].amount_to_return = amount_to_return;

                            $('#edit-invoice-modal').modal('hide');
                            calculateTransactionResume();
                        });

                        $('#edit-invoice-modal').on('hide.bs.modal', function() {
                            $('#save-edit-invoice-data').unbind('click');
                        });
                    };

                    const initializeThirdCard = () => {
                        handleAddModal();
                    };

                    initializeThirdCard();
                };

                const fourthCardFunction = () => {
                    OTHER_TRANSACTION_LIST = [];
                    OTHER_TRANSACTION_INDEX = 1;

                    const deleteOtherTransaction = (transaction_index) => {
                        $(`#other-trasaction-row-${transaction_index}`).remove();
                        OTHER_TRANSACTION_LIST.splice(transaction_index, 1);
                    };

                    const displayTableThirdcard = () => {

                        $('#other-transaction-resume tbody').html('');

                        var iteration = 0;
                        OTHER_TRANSACTION_LIST.map((data, index) => {
                            let {
                                account,
                                account_description,
                                amount,
                                description
                            } = data;

                            $('#other-transaction-resume tbody').append(`
                                    <tr id="other-trasaction-row-${index}">
                                        <td>${iteration+1}</td>
                                        <td>${account_description}</td>
                                        <td>${formatRupiahWithDecimal(amount)}</td>
                                        <td>${description}</td>
                                        <td>
                                            <x-button color="primary" id="edit-other-transaction-btn-${index}" icon="pen-to-square" fontawesome size="sm" />
                                            <x-button color="danger" icon="trash" fontawesome size="sm" id="delete-other-trasaction-${index}" />

                                            <input type="hidden" name="cash_advance_return_other_transactions[]" value="" />
                                            <input type="hidden" name="cash_advance_return_other_transactions_coa_id[]" value="${account}" />
                                            <input type="hidden" name="cash_advance_return_other_transactions_amount[]" value="${amount}" />
                                            <input type="hidden" name="cash_advance_return_other_transactions_description[]" value="${description}" />
                                        </td>
                                    </tr>
                                    `);

                            iteration++;

                            $(`#delete-other-trasaction-${index}`).click(function(e) {
                                e.preventDefault();
                                deleteOtherTransaction(index)
                            });

                            $(`#edit-other-transaction-btn-${index}`).click(function(e) {
                                e.preventDefault();
                                editOtherTransaction(index);
                            });
                        })
                    };

                    const addOtherTrasaction = (transaction_index) => {
                        $('#other-transaction-account').val(null);
                        $('#other-transaction-amount').val(null);
                        $('#other-transaction-description').val(null);
                        $('#other-transaction-account').html('');

                        $('#add-other-transaction-modal').modal('show');

                        initCommasForm();
                        initCoaSelectCashAdvancedReturn('#other-transaction-account', '#add-other-transaction-modal');

                        $('#save-other-trasaction-data').click(function(e) {
                            e.preventDefault();

                            if (
                                !$('#other-transaction-account').val() ||
                                !$('#other-transaction-description').val()
                            ) {
                                alert("Harap isi semua data");
                                return;
                            }

                            if (!$('#other-transaction-amount').val()) {
                                alert("Harap isi amount");
                                return;
                            }

                            OTHER_TRANSACTION_LIST[transaction_index] = {
                                account: $('#other-transaction-account').val(),
                                account_description: $('#other-transaction-account option:selected').text(),
                                amount: thousandToFloat($('#other-transaction-amount').val()),
                                description: $('#other-transaction-description').val()
                            };
                            displayTableThirdcard();

                            OTHER_TRANSACTION_INDEX++;
                            $('#add-other-transaction-modal').modal('hide');
                            $('#save-other-trasaction-data').unbind('click');
                            calculateTransactionResume();
                        });

                        $('#add-other-transaction-modal').on('hide.bs.modal', function() {
                            $('#save-other-trasaction-data').unbind('click');
                        });
                    };

                    const editOtherTransaction = (transaction_index) => {

                        let {
                            account,
                            account_description,
                            amount,
                            description
                        } = OTHER_TRANSACTION_LIST[transaction_index];

                        $('#edit-other-transaction-account').html(`
                        <option value="${account}" selected>${account_description}</option>
                        `);

                        initCoaSelectCashAdvancedReturn('#edit-other-transaction-account', '#edit-other-transaction-modal')

                        $('#edit-other-transaction-amount').val(amount);
                        $('#edit-other-transaction-description').val(description);

                        $('#edit-other-transaction-modal').modal('show');

                        $('#save-other-trasaction-edit').click(function(e) {
                            e.preventDefault();

                            if (
                                !$('#edit-other-transaction-account').val() ||
                                !$('#edit-other-transaction-description').val()
                            ) {
                                alert("Harap isi semua data");
                                return;
                            }

                            if (!$('#edit-other-transaction-amount').val()) {
                                alert("Harap isi amount");
                                return;
                            }

                            OTHER_TRANSACTION_LIST[transaction_index] = {
                                account: $('#edit-other-transaction-account').val(),
                                account_description: $('#edit-other-transaction-account option:selected').text(),
                                amount: thousandToFloat($('#edit-other-transaction-amount').val()),
                                description: $('#edit-other-transaction-description').val()
                            }

                            displayTableThirdcard();

                            $('#edit-other-transaction-modal').modal('hide');
                            $('#save-other-trasaction-edit').unbind('click');

                            calculateTransactionResume();
                        });

                        $('#edit-other-transaction-modal').on('hide.bs.modal', function() {
                            $('#save-other-trasaction-edit').unbind('click');
                        });
                    };

                    const initializeThirdCardFunction = () => {
                        $('#other-transaction-resume tbody').html('');

                        $('#add-other-transaction-modal-btn').click(function(e) {
                            e.preventDefault();
                            addOtherTrasaction(OTHER_TRANSACTION_INDEX);
                        });

                    };

                    initializeThirdCardFunction();
                };

                const handleOnSubmit = () => {
                    $('#form-create-cash-advance-data').submit(function(e) {
                        e.preventDefault();

                        $(this).find('input[type=submit]').prop('disabled', true);
                        $(this).find('button[type=submit]').prop('disabled', true);

                        let cash_advance_total = 0,
                            invoice_total = 0,
                            other_total = 0,
                            gap_total = 0;

                        cash_advance_total = parseFloat($('#cash_advance_total').val());
                        invoice_total = parseFloat($('#invoice_total').val());
                        other_credit_total = parseFloat($('#other_total').val());
                        gap_total = parseFloat($('#gap_total').val());

                        // CAR AND INVOICE NOT SAME
                        if (gap_total != 0) {
                            alert("Total uang muka dan pembayaran tidak sama");
                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);
                            return;
                        }

                        if (cash_advance_total == 0 || invoice_total == 0) {
                            alert("jumlah pengembalian uang muka/ purchase invoice tidak boleh 0");
                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);
                            return;
                        }

                        // SUBMIT
                        $('#form-create-cash-advance-data').unbind('submit').submit();
                    });
                };

                const calculateTransactionResume = () => {
                    let exchange_rate = thousandToFloat($('#exchange-rate-form').val());
                    let total_cash_advance = 0,
                        total_invoice = 0,
                        total_other_transaction = 0;

                    SELECTED_CASH_ADVANCE_RECEIVE_LIST_DATA.forEach((data) => {
                        if (data.selected) {
                            total_cash_advance += parseFloat(data.amount);
                        }
                    });

                    SELECTED_INVOICE_LIST_DATA.forEach((data) => {
                        if (data.selected) {
                            total_invoice += parseFloat(data.amount_to_return);
                        }
                    });

                    OTHER_TRANSACTION_LIST.forEach((data) => {
                        total_other_transaction += data.amount;
                    });

                    if (parent_currency.id != invoice_currency.id) {
                        if (parent_currency.is_local && !invoice_currency.is_local) {
                            total_invoice = total_invoice * exchange_rate;
                        }

                        if (!parent_currency.is_local && invoice_currency.is_local) {
                            total_invoice = total_invoice / exchange_rate;
                        }
                    }

                    let gap = parseFloat(total_cash_advance) - parseFloat(total_invoice) - parseFloat(total_other_transaction);

                    $('#cash_advance_total').val(total_cash_advance);
                    $('#invoice_total').val(total_invoice)
                    $('#other_total').val(total_other_transaction)
                    $('#gap_total').val(gap.toFixed(2))

                    $('#cash_advance_total_display').text(formatRupiahWithDecimal(total_cash_advance));
                    $('#invoice_total_display').text(formatRupiahWithDecimal(total_invoice * -1))
                    $('#other_total_display').text(formatRupiahWithDecimal(total_other_transaction * -1))
                    $('#gap_total_display').text(formatRupiahWithDecimal(gap))
                }

                const init = () => {
                    firstCardFuntion();
                    fourthCardFunction();
                    handleOnSubmit();
                    calculateTransactionResume();
                };

                init();
            });
        </script>
        <script>
            sidebarMenuOpen('#finance-main-sidebar');
            sidebarMenuOpen('#incoming-payment-sidebar');
            sidebarActive('#cash-advance-return-customer');
        </script>
    @endcan
@endsection
