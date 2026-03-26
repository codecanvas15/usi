@extends('layouts.admin.layout.index')

@php
    $main = 'cash-bond-return';
    $title = 'pengembalian kasbon';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Edit $main") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data" id="cash-bond-return-form">
            @csrf
            @method('PUT')

            <x-card-data-table id="loading-card">
                <x-slot name="table_content">
                    <h4 class="text-center">Loading...</h4>
                </x-slot>
            </x-card-data-table>

            <div id="form-main">
                <div id="first-card"></div>
                <div id="second-card"></div>
                <div id="third-card"></div>
            </div>
        </form>

        <x-modal title="Edit cash bond" headerColor="primary" id="edit-cash-bond" modalSize="1000">
            <x-slot name="modal_body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="date" id="cash-bond-date" label="tanggal" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="trasaction_no" id="cash-bond-trasaction-no" label="nomor_transaksi" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="account_code" id="cash-bond-account-code" label="nomor_account" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="currency" id="cash-bond-currency" label="mata_uang" disabled />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" name="rate" id="cash-bond-rate" label="kurs" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="total_amount" id="cash-bond-total-amount" label="total_amount" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="returned_amount" id="cash-bond-returned-amount" label="returned_amount" disabled />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="amount_to_return" id="cash-bond-amount-to-return" label="amount_to_return" class="commas-form" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" name="description" id="cash-bond-description" label="description" />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="primary" id="save-edit-cash-bond-data" label="Save" />
            </x-slot>
        </x-modal>

        <x-modal title="Edit transaksi lainnya" headerColor="primary" id="edit-other-transaction-modal" modalSize="900">
            <x-slot name="modal_body">
                <div class="row">
                    <div class="col-md-6">
                        <x-select name="account_code" id="edit-other-transaction-account" label="nomor acccount">

                        </x-select>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-6">
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
    @endcan
@endsection

@section('js')
    @can("edit $main")
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/helpers/ValidateCurrencies.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/employee.js') }}"></script>
        <script src="{{ asset('js/admin/select/coa.js') }}"></script>
        <script src="{{ asset('js/admin/select/project.js') }}"></script>

        <script>
            $(document).ready(function() {
                let EMPLOYEE_ID = null,
                    PROJECT_ID = null,
                    CURRENCY_ID = "{{ get_local_currency()->id }}";

                let CASH_BOND_LIST = [],
                    SELECTED_CASH_BOND_LIST = [],
                    OTHER_LIST = [],
                    OTHER_INDEX = 0;

                let CASH_BOND_RETURN_DATA = [];

                $('#form-main').hide();

                const initCoaSelectCashBondReturn = (element, modal_id) => {
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
                    handleSubmit();
                };

                const getData = () => {
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.cash-bond-return.edit', $model) }}",
                        success: function({
                            data
                        }) {

                            SELECTED_CASH_BOND_LIST = data.cash_bond_return_details.map((data, index) => {
                                return {
                                    data: data,
                                    outstanding: data.amount - data.cash_bond.total_returned_amount,
                                    amount_to_return: data.amount_to_return,
                                    balance: data.balance,
                                };
                            });

                            CASH_BOND_RETURN_DATA = data;
                            $('#loading-card').hide(500);
                            $('#form-main').show(500);
                            displayData();
                        }
                    });
                };

                const displayData = () => {
                    let {
                        branch,
                        cash_bond_return_details,
                        cash_bond_return_others,
                        coa,
                        currency_id,
                        currency,
                        date,
                        description,
                        employee_id,
                        employee,
                        exchange_rate,
                        project_id,
                        project
                    } = CASH_BOND_RETURN_DATA;

                    EMPLOYEE_ID = employee.id;
                    PROJECT_ID = project_id;
                    CURRENCY_ID = currency.id;

                    const initDisplay = () => {
                        initializeDisplayFirstCard();
                        initializeDisplaySecondCard();
                        initializeDisplayThirdCard();
                    };

                    const initializeDisplayFirstCard = () => {
                        let project_html = project_id ? `<option value="${project.id}" selected>${project.name}</option>` : '';
                        let html = `
                            <x-card-data-table title='{{ "Edit $main" }}'>
                                <x-slot name="header_content">

                                </x-slot>
                                <x-slot name="table_content">
                                    @include('components.validate-error')

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-select name="employee_id" label="pegawai" id="employee-select" required disabled>
                                                    <option value="${employee.id}" selected>${employee.name} - ${employee.NIK}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="date" id="" value="${localDate(date)}" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <x-select name="currency_id" label="currency" id="currency-select" required disabled>
                                                        <option value="${currency.id}" selected>${currency.kode} - ${currency.nama}</option>
                                                    </x-select>
                                                </div>
                                                <div class="col-md-6">`;
                        if (currency.is_local) {
                            html += `<x-input type="text" name="exchange_rate" id="exchange-rate" class="commas-form" value="1" required readonly />`;
                        } else {
                            html += `<x-input type="text" name="exchange_rate" id="exchange-rate" class="commas-form" value="${formatRupiahWithDecimal(CASH_BOND_RETURN_DATA.exchange_rate)}" required />`;
                        }

                        html += `</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        @if (get_current_branch()->is_primary)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <x-select name="branch_id" label="branch" id="branch-select" required disabled>
                                                        <option value="${branch.id}" selected>${branch.name}</option>
                                                    </x-select>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-select name="project-id" label="project" id="project-select" disabled>
                                                    ${project_html}
                                                </x-select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-select name="coa_id" label="Account Kas / Bank" id="coaParent-select" required>
                                                    <option value="${coa.id}" selected>${coa.account_code} - ${coa.name}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="${CASH_BOND_RETURN_DATA.bank_code_mutation}" readonly />
                                                <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                                            </div>
                                        </div>
                                    </div>
                                </x-slot>

                            </x-card-data-table>
                        `;

                        $('#first-card').append(html);

                        initDatePicker();
                    };

                    const initializeDisplaySecondCard = () => {

                        const displayDetailData = () => {
                            let html = `
                                <x-card-data-table title="cash bond">
                                    <x-slot name="table_content">
                                        <x-table id="cash-bond-data-resume">
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
                                    </x-slot>
                                </x-card-data-table>
                            `;

                            $('#second-card').html(html);

                            cash_bond_return_details.map((data, index) => {
                                let {
                                    amount,
                                    amount_to_return,
                                    outstanding_amount,
                                    balance,
                                    cash_bond,
                                    coa,
                                    note
                                } = data;

                                let html = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${cash_bond.date}</td>
                                        <td>${cash_bond.code}</td>
                                        <td>${currency.nama} / ${decimalFormatter(exchange_rate)}</td>
                                        <td>${currency.simbol} ${decimalFormatter(amount_to_return)}</td>
                                        <td>${currency.simbol} ${decimalFormatter(balance)}</td>
                                        <td>
                                            <x-button color="primary" icon="edit" fontawesome size="sm" id="btn-edit-cashBond-${index}"></x-button>
                                            <input type="hidden" name="cash_bond_ids[]" value="${cash_bond.id}" />
                                            <input type="hidden" name="cash_bond_return_amounts[]" value="${amount_to_return}" />
                                            <input type="hidden" name="note[]" value="${note}" />
                                        </td>
                                    </tr>
                                `;

                                $('#cash-bond-data-resume tbody').append(html);

                                $(`#btn-edit-cashBond-${index}`).click(function(e) {
                                    e.preventDefault();
                                    editDetailData(index);
                                });
                            });
                        };

                        const editDetailData = (index) => {
                            $('#edit-cash-bond').modal('show');

                            let single_data = CASH_BOND_RETURN_DATA.cash_bond_return_details[index];
                            let latest_amount = 0;

                            let {
                                cash_bond,
                                coa,
                                currency,
                                exchange_rate,
                                amount,
                                amount_to_return,
                                outstanding_amount,
                                balance,
                                note
                            } = single_data;

                            let {
                                // exchange_rate,
                                description,
                                // currency,
                                cash_bond_details
                            } = cash_bond

                            latest_amount = amount_to_return;

                            let debit = 0;
                            cash_bond_details.map((data) => {
                                if (data.type === 'cash_advance') {
                                    debit = data.debit;
                                }
                            });

                            cash_bond.outstanding = debit - cash_bond.total_returned_amount;

                            $('#cash-bond-date').val(cash_bond.date);
                            $('#cash-bond-trasaction-no').val(cash_bond.code);
                            $('#cash-bond-account-code').val(`${coa.account_code} - ${coa.name}`);
                            $('#cash-bond-currency').val(currency.nama);
                            $('#cash-bond-rate').val(formatRupiahWithDecimal(cash_bond.exchange_rate));
                            $('#cash-bond-total-amount').val(formatRupiahWithDecimal(debit));
                            $('#cash-bond-returned-amount').val(formatRupiahWithDecimal(cash_bond.total_returned_amount));

                            if (latest_amount != 0) {
                                $('#cash-bond-amount-to-return').val(formatRupiahWithDecimal(latest_amount));
                            } else {
                                $('#cash-bond-amount-to-return').val('');
                            }

                            $('#cash-bond-description').val(single_data.note);

                            $('#save-edit-cash-bond-data').click(function(e) {
                                e.preventDefault();

                                single_data.amount_to_return = thousandToFloat($('#cash-bond-amount-to-return').val());
                                single_data.balance = cash_bond.outstanding - single_data.amount_to_return;
                                single_data.note = $('#cash-bond-description').val();

                                $('#edit-cash-bond').modal('hide');
                            });

                            $('#edit-cash-bond').on('hide.bs.modal', function(e) {
                                $('#save-edit-cash-bond-data').unbind('click');
                                displayDetailData();
                            });
                        };

                        displayDetailData();
                    };

                    const initializeDisplayThirdCard = () => {

                        const initializeDisplay = () => {
                            displayData();
                            handleData();
                        };

                        const displayData = () => {
                            let html = `
                                <x-card-data-table title="other">
                                    <x-slot name="table_content">
                                        <div class="d-flex flex-column">
                                            <div >
                                                <x-button color="primary" class="float-end" id="add-other-transaction-modal-btn" label="Tambah transaksi lainnya" icon="plus" />
                                                <x-modal title="Tambah transaksi lainnya" headerColor="primary" id="add-other-transaction-modal" modalSize="900">
                                                    <x-slot name="modal_body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <x-select name="" id="other-transaction-account" label="nomor acccount">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-20">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <x-input type="text" name="" id="other-transaction-amount" label="amount" class="commas-form" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <x-text-area name="" id="other-transaction-description" label="deskripsi" labe="description" cols="30" rows="10"></x-text-area>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </x-slot>
                                                    <x-slot name="modal_footer">
                                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                                        <x-button type="button" color="primary" id="save-other-trasaction-data" label="Save" />
                                                    </x-slot>
                                                </x-modal>
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
                                        </div>
                                    </x-slot>
                                    <x-slot name="footer">
                                        <x-button type="submit" color="primary" class="float-end" label="Save" icon="plus" />
                                    </x-slot>
                                </x-card-data-table>
                            `;

                            $('#third-card').append(html);
                            cash_bond_return_others.map((data, index) => {
                                let {
                                    amount,
                                    description,
                                    coa
                                } = data;

                                $('#other-transaction-resume tbody').append(`
                                    <tr id="other-transaction-row-${index}">
                                        <td>${index + 1}</td>
                                        <td>${coa.account_code} - ${coa.name}</td>
                                        <td>${decimalFormatter(amount)}</td>
                                        <td>${description}</td>
                                        <td>
                                            <x-button color="primary" id="edit-other-transaction-btn-${index}" icon="pen-to-square" fontawesome size="sm" />
                                            <x-button color="danger" icon="trash" fontawesome size="sm" id="delete-other-trasaction-${index}" />

                                            <input type="hidden" name="other_coa_ids[]" value="${coa.id}" />
                                            <input type="hidden" name="other_amounts[]" value="${amount}" />
                                            <input type="hidden" name="other_descriptions[]" value="${description}" />
                                        </td>
                                    </tr>
                                `);

                                OTHER_LIST[OTHER_INDEX] = ({
                                    account: coa.id,
                                    account_description: `${coa.account_code} - ${coa.name}`,
                                    amount: amount,
                                    description: description
                                });

                                OTHER_INDEX++;
                            });
                        };

                        const handleData = () => {

                            const initializeOther = () => {

                                $('#add-other-transaction-modal-btn').click(function(e) {
                                    e.preventDefault();
                                    addOther(OTHER_INDEX);
                                });

                                renderOther();
                            };

                            const deleteOther = (other_index) => {
                                OTHER_LIST[other_index] = [];
                                $(`#other-transaction-row-${other_index}`).remove();
                            };

                            const renderOther = () => {

                                $('#other-transaction-resume tbody').html('');

                                OTHER_LIST.map((other, other_index) => {
                                    let {
                                        account,
                                        account_description,
                                        amount,
                                        description
                                    } = other;

                                    $('#other-transaction-resume tbody').append(`
                                        <tr id="other-transaction-row-${other_index}">
                                            <td>${other_index + 1}</td>
                                            <td>${account_description}</td>
                                            <td>${decimalFormatter(amount)}</td>
                                            <td>${description}</td>
                                            <td>
                                                <x-button color="primary" id="edit-other-transaction-btn-${other_index}" icon="pen-to-square" fontawesome size="sm" />
                                                <x-button color="danger" icon="trash" fontawesome size="sm" id="delete-other-trasaction-${other_index}" />

                                                <input type="hidden" name="other_coa_ids[]" value="${account}" />
                                                <input type="hidden" name="other_amounts[]" value="${amount}" />
                                                <input type="hidden" name="other_descriptions[]" value="${description}" />
                                            </td>
                                        </tr>
                                    `);

                                    $(`#edit-other-transaction-btn-${other_index}`).click(function(e) {
                                        e.preventDefault();
                                        editOther(other_index)
                                    });

                                    $(`#delete-other-trasaction-${other_index}`).click(function(e) {
                                        e.preventDefault();
                                        deleteOther(other_index)
                                    });

                                });
                            };

                            const addOther = (other_index) => {

                                $('#other-transaction-account').val(null);
                                $('#other-transaction-amount').val(null);
                                $('#other-transaction-description').val(null);
                                $('#other-transaction-account').html('');

                                initCoaSelectCashBondReturn('#other-transaction-account', '#add-other-transaction-modal')

                                $('#add-other-transaction-modal').modal('show');

                                $('#save-other-trasaction-data').click(function(e) {
                                    e.preventDefault();

                                    if ($('#other-transaction-account').val() == null || $('#other-transaction-description').val() == '') {
                                        alert("Tolong isi data coa dan deskripsi terlebih dahulu");
                                        return;
                                    }

                                    if (!$('#other-transaction-amount').val()) {
                                        alert("Tolong isi data amount terlebih dahulu");
                                        return;
                                    }

                                    OTHER_LIST[other_index] = {
                                        account: $('#other-transaction-account').val(),
                                        account_description: $('#other-transaction-account option:selected').text(),
                                        amount: thousandToFloat($('#other-transaction-amount').val()),
                                        description: $('#other-transaction-description').val(),
                                    }

                                    renderOther();

                                    OTHER_INDEX++;

                                    $('#add-other-transaction-modal').modal('hide');
                                });

                                $('#add-other-transaction-modal').on('hide.bs.modal', function() {
                                    $('#save-other-trasaction-data').unbind('click');
                                });
                            };

                            const editOther = (other_index) => {
                                let {
                                    account,
                                    account_description,
                                    amount,
                                    description
                                } = OTHER_LIST[other_index];

                                $('#edit-other-transaction-account').val(account);
                                $('#edit-other-transaction-amount').val(amount);
                                $('#edit-other-transaction-description').val(description);

                                $('#edit-other-transaction-account').html(`
                                    <option value="${account}" selected>${account_description}</option>
                                `);

                                initCoaSelectCashBondReturn('#other-transaction-account', '#add-other-transaction-modal')

                                $('#edit-other-transaction-modal').modal('show');
                                $('#save-other-trasaction-edit').click(function(e) {
                                    e.preventDefault();

                                    if (
                                        !$('#edit-other-transaction-account').val() ||
                                        !$('#edit-other-transaction-description').val()
                                    ) {
                                        alert("Tolong isi data coa dan deskripsi terlebih dahulu");
                                        return;
                                    }

                                    if (!$('#edit-other-transaction-amount').val()) {
                                        alert("Tolong isi data amount atau kredit terlebih dahulu");
                                        return;
                                    }

                                    OTHER_LIST[other_index] = {
                                        account: $('#edit-other-transaction-account').val(),
                                        account_description: $('#edit-other-transaction-account option:selected').text(),
                                        amount: thousandToFloat($('#edit-other-transaction-amount').val()),
                                        description: $('#edit-other-transaction-description').val(),
                                    }

                                    renderOther();

                                    $('#edit-other-transaction-modal').modal('hide');
                                });

                                $('#edit-other-transaction-modal').on('hide.bs.modal', function() {
                                    $('#save-other-trasaction-edit').unbind('click');
                                });
                            };

                            initializeOther();
                        };

                        initializeDisplay();
                    };

                    initDisplay();
                };

                const handleSubmit = () => {
                    $('#cash-bond-return-form').submit(function(e) {
                        e.preventDefault();

                        $(this).find('input[type=submit]').prop('disabled', true);
                        $(this).find('button[type=submit]').prop('disabled', true);

                        let cash_bond_credit_total = 0,
                            cash_bond_debit_total = 0;

                        // VALIDATE CASH BOND AND CALCULATE TOTAL CASH BOND
                        let selected_cash_bond = CASH_BOND_RETURN_DATA.cash_bond_return_details.map((cash_bond, cash_bond_index) => {
                            cash_bond_credit_total += cash_bond.amount_to_return;
                            cash_bond_debit_total += cash_bond.amount_to_return;
                        });

                        if (selected_cash_bond.length == 0) {
                            alert('Tolong pilih cash bond terlebih dahulu');
                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);
                            return;
                        }

                        // VALIDATE OTHER TRANSACTION AND CALCULATE TOTAL CREDIT AND DEBIT
                        OTHER_LIST.map((other, other_index) => {

                            if (other.amount < 0) {
                                cash_bond_credit_total += other.amount;
                                cash_bond_debit_total += other.amount;
                            }
                        });

                        if (cash_bond_credit_total != cash_bond_debit_total) {
                            alert('Total kredit dan debit tidak sama');
                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);
                            return;
                        }

                        $('#cash-bond-return-form').unbind('submit').submit();
                    });
                };

                init();
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#cash-bond-sidebar');
        sidebarActive('#cash-bond-return');
    </script>
@endsection
