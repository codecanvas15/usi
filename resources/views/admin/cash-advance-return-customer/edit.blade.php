@extends('layouts.admin.layout.index')

@php
    $title = 'pengembalian uang muka customer';
    $route = 'cash-advance-return-customer';
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
            @csrf
            @method('PUT')
            <x-card-data-table id="loading-card">

                <x-slot name="table_content">
                    <h4 class="text-center">Loading...</h4>
                </x-slot>
            </x-card-data-table>

            <div id="main-form">
                <div id="first-card"></div>
                <div id="second-card"></div>
                <div id="third-card"></div>
                <div id="fourth-card"></div>
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
                        <x-select name="account_code" id="edit-other-transaction-account" label="nomor acccount">

                        </x-select>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="amount" id="edit-other-transaction-amount" label="amount" class="commas-form" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-text-area name="description" id="edit-other-transaction-description" labe="description" cols="30" rows="10"></x-text-area>
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
                initCommasForm();

                let CASH_ACVANCE_RETURN_DATA = [];

                let CUSTOMER_ID = null,
                    PROJECT_ID = null,
                    CURRENCY_ID = "$model->currency_id";

                let CASH_ADVANCE_RECEIVE_LIST = [],
                    INVOICE_LIST = [],
                    OTHER_TRANSACTION_LIST = [];

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
                    $('#main-form').hide();
                    getData();
                    handleSubmit();
                };

                const getData = () => {
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.cash-advance-return-customer.get-detail-for-edit') }}",
                        data: {
                            id: "{{ $model->id }}"
                        },
                        success: function({
                            data
                        }) {
                            CASH_ACVANCE_RETURN_DATA = data;
                            displayData();
                        }
                    });
                };

                const displayData = () => {
                    let {
                        date,
                        code,
                        exchange_rate,
                        branch,
                        project,
                        currency,
                        cash_advanced_return_details,
                        cash_advanced_return_invoices,
                        cash_advanced_return_transactions,
                        reference,
                        invoice_currency,
                    } = CASH_ACVANCE_RETURN_DATA;

                    CASH_ADVANCE_RECEIVE_LIST = cash_advanced_return_details.map((cash_advanced_return_detail, cash_advanced_return_detail_index) => {
                        return {
                            data: cash_advanced_return_detail,
                            amount_to_return: cash_advanced_return_detail.amount_to_return,
                            balance: cash_advanced_return_detail.balance
                        };
                    });

                    INVOICE_LIST = cash_advanced_return_invoices.map((cash_advanced_return_invoice, cash_advanced_return_invoice_index) => {
                        return {
                            data: cash_advanced_return_invoice,
                            amount_to_paid_or_return: cash_advanced_return_invoice.amount_to_paid_or_return,
                            exchange_rate_gap: cash_advanced_return_invoice.exchange_rate_gap,
                        };
                    });

                    OTHER_TRANSACTION_LIST = cash_advanced_return_transactions.map((cash_advanced_return_transaction, cash_advanced_return_transaction_index) => {
                        let amount = 0;

                        if (cash_advanced_return_transaction.debit == 0) {
                            amount = -cash_advanced_return_transaction.credit;
                        } else {
                            amount = cash_advanced_return_transaction.debit;
                        }
                        return {
                            account_name: `${cash_advanced_return_transaction.coa.account_code} - ${cash_advanced_return_transaction.coa.name}`,
                            coa_id: cash_advanced_return_transaction.coa_id,
                            amount: amount,
                            description: cash_advanced_return_transaction.description,
                        };
                    });

                    const initDisplay = () => {
                        $('#loading-card').hide();
                        $('#main-form').show(500);

                        displayFirstCard();
                        displaySecondCard();
                        displayThirdCard();
                        displayFourthCard();
                    };

                    const displayFirstCard = () => {
                        // first-card
                        let projectSelectOption = project ? `<option value="${project.id}" selected>${project.name}</option>` : '';

                        $('#first-card').html(`
                            <x-card-data-table title='{{ "Edit $title" }}'>

                                <x-slot name="table_content">
                                    @include('components.validate-error')
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="reference_id" label="customer" id="customer-select" disabled required autofocus>
                                                    <option value="${reference.id}" selected>${reference.nama}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                               <x-input name="date" label="kode" value="${CASH_ACVANCE_RETURN_DATA.code}" required readonly/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-20">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="date" label="tanggal" value="${localDate(date)}" onchange="checkClosingPeriod($(this))" id="date" required/>
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
                                                <x-select name="invoice_currency_id" label="mata uang invoice" id="invoice-select-currency" disabled>
                                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input type="text" name="exchange_rate" label="nilai tukar" value="${formatRupiahWithDecimal(exchange_rate)}" id="exchange-rate-form" class="commas-form" value="1" required readonly />
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
                        `);

                        initDatePicker();
                        checkClosingPeriod($('#date'));
                    };

                    const displaySecondCard = () => {

                        const initializeDisplaySecondCard = () => {
                            display();
                        };

                        const display = () => {
                            $('#second-card').html(`
                                <x-card-data-table title="Edit Uang Muka">
                                    <x-slot name="table_content">

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
                            `);

                            CASH_ADVANCE_RECEIVE_LIST.map((cash_advanced_receive, cash_advanced_receive_index) => {
                                let data = cash_advanced_receive.data;
                                let {
                                    exchange_rate,
                                    amount,
                                    amount_to_return,
                                    outstanding_amount,
                                    reference,
                                    coa
                                    // currency,
                                } = data;

                                $('#cash-advance-receive-resume tbody').append(`
                                    <tr>
                                        <td>${cash_advanced_receive_index + 1}</td>
                                        <td>${localDate(reference.date)}</td>
                                        <td>${data.transaction_code ?? reference.bank_code_mutation ?? reference.code}</td>
                                        <td>${data.currency.nama} / ${formatRupiahWithDecimal(exchange_rate)}</td>
                                        <td>${formatRupiahWithDecimal(cash_advanced_receive.amount_to_return)}</td>
                                        <td>${formatRupiahWithDecimal(cash_advanced_receive.balance)}</td>
                                        <td>
                                            <x-button color="primary" id="edit-cash-advance-receive-data-${cash_advanced_receive_index}" icon="edit" fontawesome size="sm" />
                                            <input type="hidden" name="cash_advance_receives[]" value="${data.reference_id}" />
                                            <input type="hidden" name="cash_advance_receive_amount_to_returns[]" value="${cash_advanced_receive.amount_to_return}" />
                                        </td>
                                    </tr>
                                `);

                                handleData(cash_advanced_receive_index);
                            });
                        };

                        const handleData = (cash_advanced_receive_index) => {

                            let cash_advanced_receive = CASH_ADVANCE_RECEIVE_LIST[cash_advanced_receive_index];

                            let data = cash_advanced_receive.data;

                            let amount = 0,
                                returned_amount = 0;

                            data.reference.cash_advance_receive_details.map((cash_advanced_receive_detail, cash_advanced_receive_detail_index) => {
                                if (cash_advanced_receive_detail.type == 'cash_advance') {
                                    amount += cash_advanced_receive_detail.credit;
                                    returned_amount += cash_advanced_receive_detail.cash_advance_return_total;
                                }
                            });

                            const loadModal = () => {
                                $('#cash-advance-date').val(localDate(data.reference.date));
                                $('#cash-advance-trasaction-no').val(data.reference.bank_code_mutation ?? data.reference.code);
                                $('#cash-advance-account-code').val(`${data.coa.account_code} - ${data.coa.name}`);
                                $('#cash-advance-currency').val(data.currency.nama);
                                $('#cash-advance-rate').val(formatRupiahWithDecimal(data.exchange_rate));
                                $('#cash-advance-total-amount').val(formatRupiahWithDecimal(amount));
                                $('#cash-advance-returned-amount').val(formatRupiahWithDecimal(returned_amount));
                                $('#cash-advance-outstanding-amount').val(formatRupiahWithDecimal(amount - returned_amount));
                                $('#cash-advance-amount-to-return').val(formatRupiahWithDecimal(cash_advanced_receive.amount_to_return));
                                $('#cash-advance-description').val(data.reference.description);

                                handleModal();
                                handleForm();
                            };

                            const handleModal = () => {
                                $('#edit-cash-advance').modal('show');

                                $('#edit-cash-advance').on('hide.bs.modal', function() {
                                    dismissModal();
                                });

                                $('#save-edit-cash-advance-data').click(function(e) {
                                    e.preventDefault();

                                    if (cash_advance_receive.data.tax_id) {
                                        if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) != cash_advance_receive.outstanding_amount) {
                                            alert('jumlah uang muka tidak sama dengan sisa uang muka');
                                            return false;
                                        }
                                    }
                                    if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) > parseFloat(data.outstanding_amount)) {
                                        alert('jumlah tidak boleh melebihi sisa uang muka');
                                        return false;
                                    }

                                    if (thousandToFloat($(`#cash-advance-amount-to-return`).val()) == 0) {
                                        alert('jumlah tidak boleh 0');
                                        return false;
                                    }

                                    cash_advanced_receive.amount_to_return = thousandToFloat($('#cash-advance-amount-to-return').val());
                                    cash_advanced_receive.balance = (amount - returned_amount) - cash_advanced_receive.amount_to_return;

                                    calculateTransactionResume();

                                    $('#edit-cash-advance').modal('hide');
                                });
                            };

                            const dismissModal = () => {

                                $('#cash-advance-amount-to-return').unbind('keyup');
                                $('#save-edit-cash-advance-data').unbind('click');
                                $('#edit-cash-advance').unbind('hide.bs.modal');

                                display();
                            };

                            const handleForm = () => {
                                $('#cash-advance-amount-to-return').keyup(function(e) {});
                            };

                            $(`#edit-cash-advance-receive-data-${cash_advanced_receive_index}`).click(function(e) {
                                e.preventDefault();
                                loadModal();
                            });
                        };

                        initializeDisplaySecondCard();
                    };

                    const displayThirdCard = () => {

                        const initiaizeThirdcard = () => {
                            display();
                            displayInvoicePaymentInformation();
                        };

                        const displayInvoicePaymentInformation = () => {
                            $('#invoice-payment-information tbody').html('');
                            $.ajax({
                                url: `${base_url}/invoice-payment-information`,
                                type: 'post',
                                data: {
                                    _token: token,
                                    invoice_ids: INVOICE_LIST.map((data) => {
                                        return data.data.reference_id
                                    }),
                                    date: CASH_ACVANCE_RETURN_DATA.date
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

                        const getValueOfGap = (invoice_index) => {

                            let parent_exchange_rate = thousandToFloat($('#exchange-rate-form').val());

                            let this_invoice_currency = INVOICE_LIST[invoice_index].data.currency;
                            let this_data = INVOICE_LIST[invoice_index].data;
                            // BOTH CURRENCY NOT LOCAL OR INVOICE CURRENCY NOT LOCAL
                            if ((!parent_currency.is_local && !this_invoice_currency.is_local) || !this_invoice_currency.is_local) {
                                return (parent_exchange_rate - this_data.exchange_rate) * thousandToFloat($('#edit-invoice-amount-to-return').val());
                            } else {
                                return 0;
                            }
                        };

                        const display = () => {
                            $('#third-card').html(`
                                <x-card-data-table title="Edit Invoice">
                                    <x-slot name="table_content">
                                        <div class="d-flex flex-column">
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
                            `);

                            INVOICE_LIST.map((invoice, invoice_index) => {
                                let data = invoice.data;

                                let {
                                    transaction_code,
                                    // exchange_rate,
                                    outstanding_amount,
                                    amount_to_paid_or_return,
                                    // exchange_rate_gap,
                                    description,
                                    reference,
                                    // currency
                                } = data;

                                $('#invoice-data-resume tbody').append(`
                                    <tr>
                                        <td>${invoice_index + 1}</td>
                                        <td>${localDate(reference.date)}</td>
                                        <td>${transaction_code}</td>
                                        <td>${reference.currency.nama} / ${formatRupiahWithDecimal(reference.exchange_rate)}</td>
                                        <td>${formatRupiahWithDecimal(reference.total)}</td>
                                        <td>${formatRupiahWithDecimal(reference.outstanding_amount)}</td>
                                        <td>${formatRupiahWithDecimal(invoice.amount_to_paid_or_return)}</td>
                                        <td>${formatRupiahWithDecimal(reference.outstanding_amount- invoice.amount_to_paid_or_return)}</td>
                                        <td>
                                            <x-button color="primary" id="edit-invoice-data-${invoice_index}" icon="edit" fontawesome size="sm" />
                                            <input type="hidden" name="invoice_ids[]" value="${data.reference_id}" />
                                            <input type="hidden" name="invoice_amount_to_returns[]" value="${invoice.amount_to_paid_or_return}" />
                                            <input type="hidden" name="invoice_descriptions[]" value="${data.description}" />
                                        </td>
                                    </tr>
                                `);

                                handleData(invoice_index)
                            });
                        };

                        const handleData = (invoice_index) => {

                            let invoice = INVOICE_LIST[invoice_index];
                            let data = invoice.data;

                            const loadModal = () => {
                                $('#edit-invoice-date').val(localDate(data.date));
                                $('#edit-invoice-transaction-number').val(data.code);
                                // $('#edit-invoice-account-code').val(`${data.coa.account_code} - ${data.coa.name}`);
                                $('#edit-invoice-currency').val(`${data.currency.nama} / ${formatRupiahWithDecimal(data.exchange_rate)}`);
                                $('#edit-invoice-exchange-rate').val(formatRupiahWithDecimal(data.exchange_rate));
                                $('#edit-invoice-total-amount').val(formatRupiahWithDecimal(data.reference.total));
                                $('#edit-invoice-paid-amount').val(formatRupiahWithDecimal(data.reference.total - data.reference.outstanding_amount));
                                $('#edit-invoice-outstanding-amount').val(formatRupiahWithDecimal(data.reference.outstanding_amount));
                                $('#edit-invoice-amount-to-return').val(formatRupiahWithDecimal(invoice.amount_to_paid_or_return));
                                $('#edit-invoice-exchange-rate-gap').val(invoice.exchange_rate_gap);
                                $('#edit-invoice-description').val(data.description);

                                handleModal();
                                handleForm();
                            };

                            const handleModal = () => {
                                $('#edit-invoice-modal').modal('show');

                                $('#edit-invoice-modal').on('hide.bs.modal', function() {
                                    dismissModal();
                                });

                                $('#save-edit-invoice-data').click(function(e) {
                                    e.preventDefault();
                                    $('#edit-invoice-modal').modal('hide');
                                    displayInvoicePaymentInformation();

                                    calculateTransactionResume();
                                });
                            };

                            const handleForm = () => {
                                $('#edit-invoice-amount-to-return').keyup(function(e) {
                                    if (thousandToFloat($(this).val()) > parseFloat(data.reference.outstanding_amount)) {
                                        alert('jumlah tidak boleh melebihi sisa invoice');
                                        $(this).val(0);
                                        return false;
                                    }

                                    if (thousandToFloat($(this).val()) == 0) {
                                        alert('jumlah tidak boleh 0');
                                        return false;
                                    }

                                    invoice.amount_to_paid_or_return = thousandToFloat($(this).val());
                                    invoice.exchange_rate_gap = getValueOfGap(invoice_index);

                                    $('#edit-invoice-exchange-rate-gap').val(formatRupiahWithDecimal(invoice.exchange_rate_gap));
                                });
                            };

                            const dismissModal = () => {
                                $('#edit-invoice -amount-to-return').unbind('keyup');
                                $('#save-edit-invoice-data').unbind('click');
                                $('#edit-invoice-modal').unbind('hide.bs.modal');

                                display();
                            };

                            $(`#edit-invoice-data-${invoice_index}`).click(function(e) {
                                e.preventDefault();
                                loadModal();
                            });
                        };

                        initiaizeThirdcard();
                    };

                    const displayFourthCard = () => {
                        const initializeFourtCard = () => {
                            display();
                        };

                        const display = () => {
                            $('#fourth-card').html(`
                                <x-card-data-table title="Tambah Transaksi lainnya">
                                    <x-slot name="table_content">

                                        <div class="d-flex flex-column">
                                            <div >
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
                                    <x-slot name="footer">
                                        <x-button type="submit" color="primary" class="float-end" label="Save" icon="plus" />
                                    </x-slot>
                                </x-card-data-table>
                            `);

                            displayOtherTransaction();
                            handleModal();
                        };

                        const displayOtherTransaction = () => {
                            $('#other-transaction-resume tbody').html('');

                            var iteration = 0;
                            OTHER_TRANSACTION_LIST.map((other_transaction, other_transaction_index) => {
                                let {
                                    account_name,
                                    coa_id,
                                    amount,
                                    description
                                } = other_transaction;

                                $('#other-transaction-resume tbody').append(`
                                    <tr>
                                        <td>${iteration + 1}</td>
                                        <td>${account_name}</td>
                                        <td>${formatRupiahWithDecimal(amount)}</td>
                                        <td>${description}</td>
                                        <td>
                                            <x-button color="danger" id="delete-other-transaction-data-${other_transaction_index}" icon="edit" fontawesome size="sm" />
                                            <input type="hidden" name="other_transaction_coa_ids[]" value="${coa_id}" />
                                            <input type="hidden" name="other_transaction_amounts[]" value="${other_transaction.amount}" />
                                            <input type="hidden" name="other_transaction_descriptions[]" value="${other_transaction.description}" />
                                        </td>
                                    </tr>
                                `);

                                iteration++;

                                $(`#delete-other-transaction-data-${other_transaction_index}`).click(function(e) {
                                    e.preventDefault();

                                    OTHER_TRANSACTION_LIST.splice(other_transaction_index, 1);
                                    displayOtherTransaction();
                                    calculateTransactionResume();
                                });
                            });
                        };

                        const handleModal = () => {

                            initCommasForm();
                            initCoaSelectCashAdvancedReturn('#other-transaction-account', '#add-other-transaction-modal')

                            const loadModal = () => {
                                $('#add-other-transaction-modal').modal('show');

                                $('#other-transaction-account').html('');
                                $('#other-transaction-account').val('');
                                $('#other-transaction-amount').val('');
                                $('#other-transaction-description').val('');
                            };

                            const dissmisModal = () => {
                                OTHER_TRANSACTION_LIST.push({
                                    account_name: $('#other-transaction-account option:selected').text(),
                                    coa_id: $('#other-transaction-account').val(),
                                    amount: thousandToFloat($('#other-transaction-amount').val()),
                                    description: $('#other-transaction-description').val()
                                });

                                displayOtherTransaction();
                            };

                            $('#add-other-transaction-modal-btn').click(function(e) {
                                e.preventDefault();
                                loadModal();
                            });

                            $('#add-other-transaction-modal').on('hide.bs.modal', function() {
                                dissmisModal();
                            });

                            $('#save-other-trasaction-data').click(function(e) {
                                e.preventDefault();

                                if ($('#other-transaction-account').val() == '') {
                                    alert('Nomor account harus diisi');
                                    return;
                                }

                                if ($('#other-transaction-amount').val() == '') {
                                    alert('Amount harus diisi');
                                    return;
                                }

                                if ($('#other-transaction-description').val() == '') {
                                    alert('Deskripsi harus diisi');
                                    return;
                                }

                                $('#add-other-transaction-modal').modal('hide');
                                calculateTransactionResume();
                            });
                        };

                        initializeFourtCard();
                    };

                    initDisplay();

                };

                const handleSubmit = () => {
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
                        $('#form-edit-cash-advance-data').unbind('submit').submit();
                    });
                };

                const calculateTransactionResume = () => {
                    let exchange_rate = thousandToFloat($('#exchange-rate-form').val());
                    let total_cash_advance = 0,
                        total_invoice = 0,
                        total_other_transaction = 0;

                    CASH_ADVANCE_RECEIVE_LIST.forEach((data) => {
                        total_cash_advance += parseFloat(data.amount_to_return);
                    });

                    INVOICE_LIST.forEach((data) => {
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

                    var gap = parseFloat(total_cash_advance) - parseFloat(total_invoice) - parseFloat(total_other_transaction);

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
        sidebarMenuOpen('#incoming-payment-sidebar');
        sidebarActive('#cash-advance-return-customer');
    </script>
@endsection
