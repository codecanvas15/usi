@extends('layouts.admin.layout.index')

@php
    $title = 'pengembalian uang muka vendor';
    $route = 'cash-advance-return-vendor';
    $main = 'cash-advance-return';
@endphp

@section('title', Str::headline("edit $title") . ' - ')

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
                        {{ Str::headline("edit $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <form action="{{ route("admin.$route.update", $model) }}" method="post" id="form-edit-cash-advance-data">

            <x-card-data-table id="loading-card">

                <x-slot name="table_content">
                    <h4 class="text-center">Loading...</h4>
                </x-slot>
            </x-card-data-table>

            <div id="main-form">
                @method('PUT')
                <div id="first-card"></div>
                <div id="second-card"></div>
                <div id="third-card"></div>
                <div id="fourth-card"></div>
                <x-card-data-table>
                    <x-slot name="table_content">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button color="secondary mr-2" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" class="float-end" label="Save" icon="plus" />
                        </div>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="purchase invoice payment information">
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

            </div>
        </form>

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

        <x-modal title="Edit purchase invoice Invoice" headerColor="primary" id="edit-invoice-modal" modalSize="1000">
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
                                    <x-input type="text" name="amount-to-return" label="amount to return" class="commas-form" id="edit-invoice-amount-to-return" readonly required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="exchange-rate-gap" label="exchange-rate-gap" readonly id="edit-invoice-exchange-rate-gap" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <x-table id="supplier-invoice-table">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>Kode LPB</th>
                                <th>Outstanding Amount</th>
                                <th>Amount</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="description" label="description" id="edit-invoice-description" required />
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
    @endcan
@endsection

@section('js')
    @can("edit $main")
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/admin/select/project.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('#main-form').hide();
                initCommasForm();

                let CASH_BOND_RETURN_DATA = [];

                let VENDOR_ID = null,
                    PROJECT_ID = null,
                    CURRENCY_ID = "{{ get_local_currency()->id }}",
                    BRANCH_ID = "{{ get_current_branch()->id }}";

                let CASH_ADVANCE_PAYMENT_LIST = [],
                    SELECTED_CASH_ADVANCE_PAYMENT_LIST_DATA = [],
                    SUPPLIER_INVOICE_LIST = [],
                    SELECTED_SUPPLIER_INVOICE_LIST_DATA = [],
                    OTHER_TRANSACTION_LIST = [],
                    OTHER_TRANSACTION_INDEX = 1;

                let parent_currency = @json($model->currency),
                    invoice_currency = @json($model->invoice_currency);

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

                const init = () => {
                    getData();
                    handleOnSubmit();
                };

                const getData = () => {
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.cash-advance-return-vendor.get-detail-for-edit') }}",
                        data: {
                            id: "{{ $model->id }}"
                        },
                        success: function({
                            data
                        }) {
                            CASH_BOND_RETURN_DATA = data;
                            displayData();
                        }
                    });
                };

                const displayData = () => {
                    let {
                        date,
                        exchange_rate,
                        branch,
                        project,
                        currency,
                        cash_advanced_return_details,
                        cash_advanced_return_invoices,
                        cash_advanced_return_transactions,
                        reference,
                        invoice_currency,
                    } = CASH_BOND_RETURN_DATA;

                    CASH_ADVANCE_PAYMENT_LIST = cash_advanced_return_details.map((cash_advance_return_detail, cash_advancec_return_detail_index) => {
                        return {
                            data: cash_advance_return_detail,
                            code: cash_advance_return_detail.code,
                            date: cash_advance_return_detail.date,
                            exchange_rate: cash_advance_return_detail.exchange_rate,
                            reference: cash_advance_return_detail.reference,
                            coa: cash_advance_return_detail.coa,
                            currency: cash_advance_return_detail.currency,
                            amount: cash_advance_return_detail.amount,
                            amount_to_return: cash_advance_return_detail.amount_to_return,
                            outstanding_amount: cash_advance_return_detail.outstanding_amount,
                            balance: cash_advance_return_detail.balance,
                            purchase: cash_advance_return_detail.purchase,
                        };
                    });

                    SUPPLIER_INVOICE_LIST = cash_advanced_return_invoices.map((cash_advanced_return_invoice, cash_advanced_return_invoice_index) => {
                        return {
                            data: cash_advanced_return_invoice,
                            date: cash_advanced_return_invoice.date,
                            exchange_rate: cash_advanced_return_invoice.exchange_rate,
                            transaction_code: cash_advanced_return_invoice.transaction_code,
                            outstanding_amount: cash_advanced_return_invoice.outstanding_amount,
                            amount_to_paid_or_return_list: cash_advanced_return_invoice.reference.detail.map((detail, detail_index) => {
                                return detail.amount;
                            }),
                            amount_to_paid_or_return: cash_advanced_return_invoice.amount_to_paid_or_return,
                            exchange_rate_gap: cash_advanced_return_invoice.exchange_rate_gap,
                            reference: cash_advanced_return_invoice.reference,
                            currency: cash_advanced_return_invoice.currency,
                            item_receiving_report: cash_advanced_return_invoice.item_receiving_report,
                        };
                    });

                    OTHER_TRANSACTION_LIST = cash_advanced_return_transactions.map((cash_advanced_return_transaction, cash_advanced_return_transaction_index) => {
                        let amount = 0;

                        if (cash_advanced_return_transaction.credit == 0) {
                            amount = cash_advanced_return_transaction.debit;
                        } else {
                            amount = -cash_advanced_return_transaction.credit;
                        }

                        return {
                            coa_id: cash_advanced_return_transaction.coa_id,
                            amount: amount,
                            description: cash_advanced_return_transaction.description,
                            coa: cash_advanced_return_transaction.coa,
                            coa_name: `${cash_advanced_return_transaction.coa.account_code} - ${cash_advanced_return_transaction.coa.name}`,
                        };
                    });

                    const initializDispayData = () => {
                        $('#loading-card').hide();
                        $('#main-form').show();
                        displayFirstCardData();
                        displaySecondCardData();
                        displayThirdCardData();
                        displayFourthCardData();
                    };

                    const displayFirstCardData = () => {
                        let projectSelectOption = project ? `<option value="${project.id}" selected>${project.name}</option>` : '';
                        let html = `
                            <x-card-data-table title='{{ "Edit $title" }}'>

                            <x-slot name="table_content">
                                @include('components.validate-error')
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="reference_id" label="vendor" id="vendor-select" required autofocus disabled>
                                                <option value="${reference.id}" selected>${reference.nama}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input name="date" label="kode" value="${CASH_BOND_RETURN_DATA.code}" required readonly/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="date" label="tanggal" value="${localDate(date)}" id="" required disabled/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="currency_id" label="mata uang" id="currency-select" required disabled>
                                                <option value="${currency.id}" selected>${currency.nama}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="supplier_invoice_currency_id" label="mata uang purchase invoice" id="supplier-invoice-select-currency" disabled>
                                                 <option value="${invoice_currency.id}" selected>${invoice_currency.nama}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="exchange_rate" label="nilai tukar" value="${formatRupiahWithDecimal(exchange_rate)}" id="exchange-rate-form" class="commas-form" required readonly />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-20">
                                    @if (get_current_branch()->is_primary)
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="branch_id" label="branch" id="select-branch" required disabled>
                                                    <option value="${branch.id}" selected>${branch.name}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="project_id" label="project" id="select-project" disabled>
                                                ${projectSelectOption}
                                            </x-select>
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                        </x-card-data-table>
                        `;

                        $(`#first-card`).append(html);

                        initDatePicker();
                    };

                    const displaySecondCardData = () => {
                        // second-card

                        const initializeDispplaySecondCardData = () => {
                            display();
                        };

                        const display = () => {

                            let html = `
                                        <x-card-data-table title="Uang Muka">
                                            <x-slot name="table_content">
                                                <div class="mt-20">
                                                    <x-table id="cash-advance-payment-resume">
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
                                            </x-slot>
                                        </x-card-data-table>`;

                            $('#second-card').html(html);

                            CASH_ADVANCE_PAYMENT_LIST.map((cash_advance_payment, cash_advance_payment_index) => {
                                let {
                                    data,
                                    reference,
                                    transaction_code,
                                    date,
                                    exchange_rate,
                                    coa,
                                    currency,
                                    amount,
                                    amount_to_return,
                                    outstanding_amount,
                                    balance
                                } = cash_advance_payment;

                                let {
                                    code,
                                    to_name,
                                    // date,
                                    returned_amount,
                                    cash_advance_debit_total,
                                    cash_advance_credit_total,
                                    cash_advance_cash_bank,
                                    cash_advance_cash_advance,
                                    cash_advance_others,
                                    // outstanding_amount,
                                    bank_code_mutation,
                                    cash_advance_payment_details,
                                    // currency
                                } = reference;

                                let cash_advance_return_total = 0;

                                cash_advance_payment_details.map(cash_advance_payment_detail => {
                                    if (cash_advance_payment_detail.type == 'cash_bank') {
                                        cash_advance_return_total += cash_advance_payment_detail.amount;
                                    }
                                });

                                $('#cash-advance-payment-resume tbody').append(`
                                    <tr>
                                        <td>${cash_advance_payment_index + 1}</td>
                                        <td>${localDate(date)}</td>
                                        <td>${bank_code_mutation ?? code}</td>
                                        <td>${currency.nama} / ${formatRupiahWithDecimal(exchange_rate)}</td>
                                        <td>${currency.simbol} ${formatRupiahWithDecimal(amount_to_return)}</td>
                                        <td>${currency.simbol} ${formatRupiahWithDecimal(balance)}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <x-button color="primary" class="float-end" id="edit-cash-advance-data-${cash_advance_payment_index}" label="" icon="pen-to-square" size="sm" fontawesome />

                                                <input type="hidden" name="cash_advance_payments[]" value="${reference.id}" />
                                                <input type="hidden" name="cash_advance_payment_amount_to_returns[]" value="${amount_to_return}" />
                                            </div>
                                        </td>
                                    </tr>
                                `);

                                handleData(cash_advance_payment_index);
                            });
                        };

                        const handleData = (index) => {
                            let cash_advance_payment = CASH_ADVANCE_PAYMENT_LIST[index];

                            let {
                                data,
                                code,
                                date,
                                exchange_rate,
                                reference,
                                coa,
                                currency,
                                amount,
                                amount_to_return,
                                outstanding_amount,
                                balance
                            } = cash_advance_payment;

                            const loadModal = () => {
                                $('#cash-advance-date').val(localDate(reference.date));
                                $('#cash-advance-trasaction-no').val(reference.bank_code_mutation ?? reference.code);
                                $('#cash-advance-account-code').val(`${coa.account_code} - ${coa.name}`);
                                $('#cash-advance-currency').val(`${currency.nama} - ${currency.simbol}`);
                                $('#cash-advance-rate').val(formatRupiahWithDecimal(exchange_rate));
                                $('#cash-advance-total-amount').val(formatRupiahWithDecimal(amount));
                                $('#cash-advance-returned-amount').val(formatRupiahWithDecimal(reference.returned_amount));
                                $('#cash-advance-outstanding-amount').val(formatRupiahWithDecimal(outstanding_amount));
                                $('#cash-advance-amount-to-return').val(formatRupiahWithDecimal(amount_to_return));
                                $('#cash-advance-description').val(reference.keterangan);
                            };

                            const dismissModal = () => {
                                $('#edit-cash-advance').unbind('hide.bs.modal');

                                $('#cash-advance-date').val('');
                                $('#cash-advance-trasaction-no').val('');
                                $('#cash-advance-account-code').val('');
                                $('#cash-advance-currency').val('');
                                $('#cash-advance-rate').val('');
                                $('#cash-advance-total-amount').val('');
                                $('#cash-advance-returned-amount').val('');
                                $('#cash-advance-outstanding-amount').val('');
                                $('#cash-advance-amount-to-return').val('');
                                $('#cash-advance-description').val('');

                                $('#cash-advance-amount-to-return').unbind('keyup');
                                $('#save-edit-cash-advance-data').unbind('click');
                            };

                            const handleForm = () => {
                                $('#cash-advance-amount-to-return').keyup(function(e) {
                                    cash_advance_payment.amount_to_return = thousandToFloat($(this).val());
                                    cash_advance_payment.balance = cash_advance_payment.amount - cash_advance_payment.amount_to_return;

                                });
                            };

                            const handleModal = () => {
                                $('#edit-cash-advance').modal('show');

                                $('#edit-cash-advance').on('hide.bs.modal', function() {
                                    dismissModal();
                                    display();
                                });

                                $('#save-edit-cash-advance-data').click(function(e) {
                                    e.preventDefault();


                                    if (cash_advance_payment.data.reference.tax_id) {
                                        if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) != cash_advance_payment.outstanding_amount) {
                                            alert('jumlah uang muka tidak sama dengan sisa uang muka');
                                            return false;
                                        }
                                    }

                                    if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) > parseFloat(cash_advance_payment.outstanding_amount)) {
                                        alert('jumlah tidak boleh melebihi sisa uang muka');
                                        return false;
                                    }

                                    if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) == 0) {
                                        alert('jumlah tidak boleh 0');
                                        return false;
                                    }

                                    cash_advance_payment.amount_to_return = thousandToFloat($('#cash-advance-amount-to-return').val());
                                    cash_advance_payment.balance = parseFloat(cash_advance_payment.outstanding_amount) - parseFloat(cash_advance_payment.amount_to_return);

                                    calculateTransactionResume();

                                    $('#edit-cash-advance').modal('hide');
                                });
                            };

                            const init = () => {
                                loadModal();
                                handleForm();
                                handleModal();
                            };

                            $(`#edit-cash-advance-data-${index}`).click(function(e) {
                                e.preventDefault();
                                init();
                            });
                        };

                        initializeDispplaySecondCardData();
                    };

                    const displayThirdCardData = () => {
                        // third-card

                        const initializeDisplayThirdCard = () => {
                            display();
                            displayInvoicePaymentInformation();
                        };

                        const displayInvoicePaymentInformation = () => {
                            $('#invoice-payment-information tbody').html('');
                            $.ajax({
                                url: `${base_url}/supplier-invoice-payment-information`,
                                type: 'post',
                                data: {
                                    _token: token,
                                    supplier_invoice_ids: SUPPLIER_INVOICE_LIST.map((data) => {
                                        return data.data.reference_id
                                    }),
                                    date: CASH_BOND_RETURN_DATA.date
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
                                                <td>${value2.currency.simbol} ${formatRupiahWithDecimal(value2.amount_to_pay)}</td>
                                                <td>${value2.currency.simbol} ${formatRupiahWithDecimal(value2.pay_amount)}</td>
                                                <td>${value2.note}</td>
                                            </tr>
                                            `);
                                        });
                                    })
                                }
                            });
                        }


                        const getValueOfGap = (invoice_index) => {

                            let parent_exchange_rate = thousandToFloat($('#exchange-rate-form').val());

                            this_invoice = SUPPLIER_INVOICE_LIST[invoice_index];

                            let this_invoice_currency = SUPPLIER_INVOICE_LIST[invoice_index].currency;
                            let this_data = SUPPLIER_INVOICE_LIST[invoice_index];
                            // BOTH CURRENCY NOT LOCAL OR INVOICE CURRENCY NOT LOCAL
                            if ((!parent_currency.is_local && !this_invoice_currency.is_local) || !this_invoice_currency.is_local) {
                                return (parent_exchange_rate - this_data.exchange_rate) * thousandToFloat($('#edit-invoice-amount-to-return').val());
                            } else {
                                return 0;
                            }
                        };

                        const display = () => {
                            $('#third-card').html(`
                                                    <x-card-data-table title="Purchase Invoice">
                                                        <x-slot name="table_content">
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
                                                        </x-slot>
                                                    </x-card-data-table>

                            `);

                            SUPPLIER_INVOICE_LIST.map((supplier_invoice, supplier_invoice_index) => {
                                let {
                                    date,
                                    exchange_rate,
                                    transaction_code,
                                    outstanding_amount,
                                    amount_to_paid_or_return,
                                    exchange_rate_gap,
                                    reference,
                                    currency
                                } = supplier_invoice;

                                let {
                                    // date,
                                    // due_date,
                                    code,
                                    total,
                                    paid_amount,
                                    // outstanding_amount,
                                    // currency,
                                } = reference;

                                let item_receiving_report_html = '';

                                reference.detail.map((detail, detail_index) => {
                                    item_receiving_report_html += `
                                        <input type="hidden" name="item_receiving_report_ids[${supplier_invoice_index}][${detail_index}]" value="${detail.item_receiving_report_id}" />
                                        <input type="hidden" name="item_receiving_report_amount_to_return[${supplier_invoice_index}][${detail_index}]" value="${detail.amount}" />
                                    `;
                                });

                                $('#invoice-data-resume tbody').append(`
                                    <tr>
                                        <td>${supplier_invoice_index + 1}</td>
                                        <td>${localDate(date)}</td>
                                        <td>${code}</td>
                                        <td>${supplier_invoice.currency.nama} / ${formatRupiahWithDecimal(supplier_invoice.exchange_rate)}</td>
                                        <td>${supplier_invoice.currency.simbol} ${formatRupiahWithDecimal(reference.total)}</td>
                                        <td>${supplier_invoice.currency.simbol} ${formatRupiahWithDecimal(supplier_invoice.outstanding_amount)}</td>
                                        <td>${supplier_invoice.currency.simbol} ${formatRupiahWithDecimal(amount_to_paid_or_return)}</td>
                                        <td>${supplier_invoice.currency.simbol} ${formatRupiahWithDecimal(supplier_invoice.outstanding_amount -amount_to_paid_or_return)}</td>
                                        <td>
                                            <x-button color="primary" class="float-end" id="edit-supplier-invoice-data-${supplier_invoice_index}" label="" icon="pen-to-square" size="sm" fontawesome />
                                            <input type="hidden" name="invoice_ids[]" value="${reference.id}" />
                                            <input type="hidden" name="invoice_amount_to_returns[]" value="${supplier_invoice.amount_to_paid_or_return}" />
                                            <input type="hidden" name="invoice_descriptions[]" value="${supplier_invoice.data?.description}" />
                                            ${item_receiving_report_html}
                                        </td>
                                    </tr>
                                `);

                                handleData(supplier_invoice_index);
                            });
                        };

                        const handleData = (supplier_invoice_index) => {
                            let supplier_invoice = SUPPLIER_INVOICE_LIST[supplier_invoice_index];

                            let {
                                data,
                                date,
                                exchange_rate,
                                transaction_code,
                                outstanding_amount,
                                amount_to_paid_or_return,
                                amount_to_paid_or_return_list,
                                exchange_rate_gap,
                                reference,
                                currency
                            } = supplier_invoice;

                            const loadModal = () => {
                                $('#edit-invoice-date').val(localDate(data.date));
                                $('#edit-invoice-transaction-number').val(date.code);
                                $('#edit-invoice-account-code').val();
                                $('#edit-invoice-currency').val(`${currency.nama} (${currency.simbol})`);
                                $('#edit-invoice-exchange-rate').val(formatRupiahWithDecimal(supplier_invoice.exchange_rate));
                                $('#edit-invoice-total-amount').val(formatRupiahWithDecimal(reference.total));
                                $('#edit-invoice-paid-amount').val(formatRupiahWithDecimal(reference.paid_amount));
                                $('#edit-invoice-outstanding-amount').val(formatRupiahWithDecimal(outstanding_amount));
                                $('#edit-invoice-amount-to-return').val(formatRupiahWithDecimal(supplier_invoice.amount_to_paid_or_return));
                                $('#edit-invoice-exchange-rate-gap').val(formatRupiahWithDecimal(supplier_invoice.exchange_rate));
                                $('#edit-invoice-description').val(data.description);

                                $('#supplier-invoice-table tbody').html('');


                                reference.detail.map((detail, detail_index) => {
                                    var selected_cap = [];
                                    CASH_ADVANCE_PAYMENT_LIST.filter((selected_data) => {
                                        selected_cap.push({
                                            id: selected_data.reference.purchase.model_id,
                                            model: selected_data.reference.purchase.model_reference,
                                        })
                                    });

                                    let find_selected_cash_advance_po = selected_cap.find(element => element.id == detail.item_receiving_report.reference_id && element.model == detail.item_receiving_report.reference_model);

                                    $('#supplier-invoice-table tbody').append(`
                                        <tr>
                                            <td>${detail_index + 1}</td>
                                            <td>${detail.item_receiving_report.kode}</td>
                                            <td>${formatRupiahWithDecimal(detail.item_receiving_report.outstanding)}</td>
                                            <td>
                                                <div class="form-group">
                                                   <x-input type="text" value="${formatRupiahWithDecimal(detail.amount ?? 0)}" id="amount-to-return-item-receiving-report-${detail_index}" label="-" class="commas-form" hideAsterix placeholder="Amount to return" required />
                                                </div>
                                            </td>
                                        </tr>
                                    `);

                                    supplier_invoice.amount_to_paid_or_return_list[detail_index] = detail.amount ?? 0;

                                    $(`#amount-to-return-item-receiving-report-${detail_index}`).keyup(function(e) {

                                        if (thousandToFloat($(this).val()) > detail.item_receiving_report.outstanding) {
                                            alert("Melebihi jumlah pengembalian yang tersedia");
                                            this.value = formatRupiahWithDecimal(detail.outstanding);
                                        } else {
                                            detail.amount = thousandToFloat($(this).val());
                                        }

                                        detail.amount = thousandToFloat($(this).val());
                                        supplier_invoice.amount_to_paid_or_return_list[detail_index] = thousandToFloat($(this).val());
                                        supplier_invoice.amount_to_paid_or_return = supplier_invoice.amount_to_paid_or_return_list.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                                        $('#edit-invoice-amount-to-return').val(formatRupiahWithDecimal(supplier_invoice.amount_to_paid_or_return));
                                        $('#edit-invoice-amount-to-return').trigger('change');
                                    });
                                });

                                initCommasForm();
                            };

                            const dismissModal = () => {
                                $(this).unbind('hide.bs.modal');
                                $('#save-edit-invoice-data').unbind('click');
                                $('#edit-invoice-amount-to-return').unbind('change');

                                reference.detail.map((detail, detail_index) => {
                                    $(`#amount-to-return-item-receiving-report-${detail_index}`).unbind('keyup');
                                });

                                display();
                            };

                            const handleForm = () => {
                                $('#edit-invoice-modal').modal('show');

                                $('#edit-invoice-modal').on('hide.bs.modal', function() {
                                    dismissModal();
                                });

                                $('#save-edit-invoice-data').click(function(e) {
                                    e.preventDefault();
                                    calculateTransactionResume();
                                    $('#edit-invoice-modal').modal('hide');
                                });

                                $('#edit-invoice-amount-to-return').change(function(e) {
                                    e.preventDefault();
                                    getValueOfGap(supplier_invoice_index)
                                    $('#edit-invoice-exchange-rate-gap').val(formatRupiahWithDecimal(supplier_invoice.exchange_rate_gap));
                                });
                            };

                            const init = () => {
                                loadModal();
                                handleForm()
                            };

                            $(`#edit-supplier-invoice-data-${supplier_invoice_index}`).click(function(e) {
                                e.preventDefault();
                                init();
                            });
                        };

                        initializeDisplayThirdCard();
                    };

                    const displayFourthCardData = () => {
                        // fourth-card

                        const initializeFourthCardData = () => {
                            display();
                            handleData();
                        };

                        const display = () => {
                            $('#fourth-card').html(`
                                                    <x-card-data-table title="Other Credit">
                                                        <x-slot name="table_content">
                                                            <div class="d-flex flex-column">
                                                                <div >
                                                                    <x-button color="primary" class="float-end" id="add-other-transaction-modal-btn" label="Tambah transaksi lainnya" icon="plus" />
                                                                </div>
                                                                <div class="mt-10">
                                                                    <x-table id="other-credit-data-resume">
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
                                                                                        <input type="hidden" id="cash_advance_total" value="{{ $model->cash_advance_total }}">
                                                                                    </th>
                                                                                    <th id="cash_advance_total_display" class="text-end">{{ formatNumber($model->cash_advance_total) }}</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>
                                                                                        Total Invoice
                                                                                        <input type="hidden" id="invoice_total" value="{{ $model->invoice_total }}">
                                                                                    </th>
                                                                                    <th id="invoice_total_display" class="text-end">{{ formatNumber($model->invoice_total * -1) }}</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>
                                                                                        Total Lain Lain
                                                                                        <input type="hidden" id="other_total" value="{{ $model->other_total }}">
                                                                                    </th>
                                                                                    <th id="other_total_display" class="text-end">{{ formatNumber($model->other_total * -1) }}</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>
                                                                                        Selisih Uang Muka dan Pembayaran
                                                                                        <input type="hidden" id="gap_total" value="0">
                                                                                    </th>
                                                                                    <th id="gap_total_display" class="text-end"></th>
                                                                                </tr>
                                                                            </tbody>
                                                                        </x-slot>
                                                                    </x-table>
                                                                </div>
                                                            </div>
                                                        </x-slot>
                                                    </x-card-data-table>
                            `);

                            diplayDataOther();
                        };

                        const diplayDataOther = () => {
                            $('#other-credit-data-resume tbody').html('');
                            var iteration = 0;
                            OTHER_TRANSACTION_LIST.map((other_transaction, other_transaction_index) => {

                                let {
                                    coa_id,
                                    coa_name,
                                    amount,
                                    description
                                } = other_transaction;
                                $('#other-credit-data-resume tbody').append(`
                                                        <tr>
                                                            <td>${iteration + 1}</td>
                                                            <td>${other_transaction.coa_name}</td>
                                                            <td>${formatRupiahWithDecimal(other_transaction.amount)}</td>
                                                            <td>${other_transaction.description}</td>
                                                            <td>
                                                                <div class="d-flex justify-content-end">
                                                                    <x-button color="danger" size="sm" icon="trash" fontawesome id="delete-other-transaction-${other_transaction_index}" label="delete" icon="trash" />
                                                                    <input type="hidden" name="cash_advance_return_other_transactions[]" value="" />
                                                                    <input type="hidden" name="cash_advance_return_other_transactions_coa_id[]" value="${coa_id}" />
                                                                    <input type="hidden" name="cash_advance_return_other_transactions_amount[]" value="${amount}" />
                                                                    <input type="hidden" name="cash_advance_return_other_transactions_description[]" value="${description}" />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    `);

                                iteration++;

                                $(`#delete-other-transaction-${other_transaction_index}`).click(function(e) {
                                    e.preventDefault();

                                    OTHER_TRANSACTION_LIST.splice(other_transaction_index, 1);
                                    displayFourthCardData();
                                    calculateTransactionResume();
                                });
                            });

                            initCommasForm();

                        };

                        const handleData = () => {

                            initCoaSelectCashAdvancedReturn('#other-transaction-account', '#add-other-transaction-modal');

                            $('#add-other-transaction-modal-btn').click(function(e) {
                                e.preventDefault();
                                $('#add-other-transaction-modal').modal('show');
                            });

                            $('#add-other-transaction-modal').on("show.bs.modal", function() {
                                $('#other-transaction-account').val('');
                                $('#other-transaction-amount').val(formatRupiahWithDecimal(0));
                                $('#other-transaction-description').val('');
                            });

                            $('#add-other-transaction-modal').on("hide.bs.modal", function() {
                                $('#other-transaction-account').val('');
                                $('#other-transaction-amount').val('');
                                $('#other-transaction-description').val('');
                            });

                            $('#save-other-trasaction-data').click(function(e) {
                                e.preventDefault();

                                if (!$('#other-transaction-description').val()) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Description tidak boleh kosong',
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    });
                                    return;
                                }

                                if (!$('#other-transaction-amount').val()) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Amount tidak boleh kosong',
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    });
                                    return;
                                }

                                if (!$('#other-transaction-account').val()) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Account tidak boleh kosong',
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    });
                                    return;
                                }

                                OTHER_TRANSACTION_LIST.push({
                                    coa_id: $('#other-transaction-account').val(),
                                    coa_name: $('#other-transaction-account option:selected').text(),
                                    amount: thousandToFloat($('#other-transaction-amount').val()),
                                    description: $('#other-transaction-description').val(),
                                });

                                $('#add-other-transaction-modal').modal('hide');

                                diplayDataOther();

                                calculateTransactionResume();
                            });
                        };

                        initializeFourthCardData();
                    };

                    initializDispayData();
                };

                const handleOnSubmit = () => {
                    $('#form-edit-cash-advance-data').submit(function(e) {
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
                            alert("Total cash credit dan debit tidak sama");
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

                        $('#form-edit-cash-advance-data').unbind('submit').submit();
                    });
                };

                const calculateTransactionResume = () => {
                    let exchange_rate = thousandToFloat($('#exchange-rate-form').val());
                    let total_cash_advance = 0,
                        total_invoice = 0,
                        total_other_transaction = 0;

                    CASH_ADVANCE_PAYMENT_LIST.forEach((data) => {
                        total_cash_advance += parseFloat(data.amount_to_return);
                    });

                    SUPPLIER_INVOICE_LIST.forEach((data) => {
                        total_invoice += parseFloat(data.amount_to_paid_or_return);
                    });

                    OTHER_TRANSACTION_LIST.forEach((data) => {
                        total_other_transaction += parseFloat(data.amount);
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
                    $('#gap_total_display').text(formatRupiahWithDecimal(gap.toFixed(2)))
                }

                init();
            });
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#outgoing-payment-sidebar');
        sidebarActive('#cash-advance-return-vendor');
    </script>
@endsection
