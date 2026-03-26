@extends('layouts.admin.layout.index')

@php
    $main = 'cash-bond';
    $title = 'kasbon';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

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
                        <a href="{{ route('admin.cash-bond.index') }}">{{ Str::headline("$title") }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Edit $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <form action="{{ route('admin.cash-bond.update', $model) }}" method="post" id="edit-cash-bond-form">

            <x-card-data-table id="loading-card">
                <x-slot name="table_content">
                    <h2 class="text-center">Loading...</h2>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'Edit ' . $title }}" id="edit-card">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    @csrf
                    @method('PUT')

                    <div id="edit-form"></div>
                    {{-- <div class="my-20 py-20 border-top border-bottom border-primary" id="other-card"></div> --}}
                    <div id="description-card"></div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.cash-bond.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <x-button type="submit" color="primary" icon="primary" label="Save data" id="handle-form" icon="save" fontawesome iconLeft class="float-end" />
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/helpers/ValidateCurrencies.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/project.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>

    <script>
        $(document).ready(function() {

            let otherRow = 0;
            let otherRowList = [];
            let cashBondData = [];

            let parentCurrency = JSON.parse('{!! $parentCurrency !!}'),
                CoaBankCurrency = JSON.parse('{!! $cashBankCurrency !!}'),
                localCurrency = JSON.parse('{!! get_local_currency() !!}');

            $('#edit-card').hide();

            const init = () => {
                getData();
                handleSubmit();
            };

            const getData = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.cash-bond.edit', $model) }}",
                    success: function({
                        data
                    }) {
                        cashBondData = data;

                        $('#loading-card').fadeOut(600);
                        $('#edit-card').fadeIn(500);

                        displayData();
                    }
                });
            };

            const displayData = () => {

                let {
                    branch_id,
                    branch,
                    cash_bond_details,
                    currency_id,
                    currency,
                    code,
                    date,
                    description,
                    employee,
                    employee_id,
                    exchange_rate,
                    project_id,
                    project,
                    bank_code_mutation
                } = cashBondData;

                const initializeDisplayData = () => {
                    initializeFirstSection();
                    initializeSecondSection();
                    // initializeThirdSection();
                    initializeDisplayDescription();
                    initCommasForm()

                    $('#amount-parent-debit').on('keyup', function() {
                        $('#amount-parent-credit').val(thousandToFloat($(this).val()));
                    });
                };

                const initializeFirstSection = () => {
                    let project_value = project_id !== null ? project.code + ' - ' + project.name : '';
                    let cash_bank_data = cash_bond_details.filter(({
                        type
                    }) => type === 'cash_bank');

                    $('#edit-form').append(`
                        <div class="border-bottom border-primary pb-20">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="employee_id" label="pegawai" id="employee-select" required>
                                            <option value="${employee_id}" selected>${employee.name} - ${employee.NIK}</option>
                                        </x-select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="date" label="tanggal" id="" value="${localDate(date)}" required disabled />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-select name="currency_id" label="currency" id="currency-select" required>
                                                    <option value="${currency_id}" selected>${currency.kode} - ${currency.nama} - ${currency.negara}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input type="text" name="exchange_rate" id="exchange-rate" value="${formatRupiahWithDecimal(exchange_rate, 2)}" class="commas-form" required readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="${bank_code_mutation}" readonly />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="branch" id="branch" value="${branch.name}" required disabled />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="project" id="project" value="${project_value}" disabled />
                                    </div>
                                </div>
                            </div>

                             <div class="row">
                                <div class="col-md-4">
                                    <input type="hidden" name="type[]" value="cash_bank">
                                    <div class="form-group">
                                        <x-select name="coa_id[]" label="akun cash bank" id="coaCashBank-select" required>
                                            <option value="${cash_bank_data[0].coa_id}" selected>${cash_bank_data[0].coa.account_code} - ${cash_bank_data[0].coa.name}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" name="debit[]" value="${formatRupiahWithDecimal(cash_bank_data[0].debit)}">
                                        <input type="hidden" name="credit[]" value="${formatRupiahWithDecimal(cash_bank_data[0].credit)}" id="amount-parent-credit">
                                        <input type="hidden" name="note[]" id="" value="kas bank" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                    initDatePicker();

                    initSelect2Search('currency-select', "{{ route('admin.select.currency') }}", {
                        id: "id",
                        text: "kode,nama,negara"
                    }, 0, {}, "", true);

                    $('#currency-select').attr('disabled', false);

                    initSelectEmployee('#employee-select');

                    $('#currency-select').change(function(e) {
                        e.preventDefault();

                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.currency.detail') }}/" + $(this).val(),
                            success: function({
                                data
                            }) {
                                if (data.is_local) {
                                    $('#exchange-rate').val(1);
                                    $('#exchange-rate').attr('readonly', true);
                                } else {
                                    $('#exchange-rate').val(1);
                                    $('#exchange-rate').attr('readonly', false);
                                }

                                parentCurrency = data;
                                validateCurrencies(parentCurrency, CoaBankCurrency, '#exchange-rate', '#currency-select', '#coaCashBank-select')
                            }
                        });
                    });
                };

                const initializeSecondSection = () => {

                    let cash_advance_data = cash_bond_details.filter(({
                        type
                    }) => type === 'cash_advance');

                    let cash_bond_details_html = `
                        <div class="mt-20">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="hidden" name="type[]" value="cash_advance">
                                    <div class="form-group">
                                        <x-select name="coa_id[]" label="akun piutang" id="coaEmployee-select" required>
                                            <option value="${cash_advance_data[0].coa_id}" selected>${cash_advance_data[0].coa.account_code} - ${cash_advance_data[0].coa.name}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" name="credit[]" value="${formatRupiahWithDecimal(cash_advance_data[0].credit)}">
                                        <x-input type="text" name="debit[]" label="jumlah" id="amount-parent-debit" class="commas-form" value="${formatRupiahWithDecimal(cash_advance_data[0].debit)}" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="note[]" label="keterangan" value="${cash_advance_data[0].note}" id="" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;


                    $('#edit-form').append(cash_bond_details_html);

                    initSelect2SearchPagination(`coaCashBank-select`, `{{ route('admin.select.coa') }}`, {
                        id: "id",
                        text: "account_code,name"
                    }, 0, {
                        account_type: "Cash & Bank"
                    });

                    $('#coaCashBank-select').change(function(e) {
                        e.preventDefault();
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.coa.detail') }}/${$(this).val()}`,
                            success: function({
                                data
                            }) {
                                let {
                                    currency,
                                    id
                                } = data;

                                if (currency === null) {
                                    CoaBankCurrency = localCurrency;
                                } else {
                                    CoaBankCurrency = currency;
                                }

                                validateCurrencies(parentCurrency, CoaBankCurrency, '#exchange-rate', '#currency-select', '#coaCashBank-select')
                            }
                        });
                    });

                    initCoaSelect('#coaEmployee-select');
                };

                const initializeThirdSection = () => {

                    const displayCard = () => {

                        let cash_bond_detail_data = cash_bond_details.filter(({
                            type
                        }) => type === 'other');

                        if (cash_bond_detail_data.length > 0) {
                            cash_bond_detail_data.map((data, index) => {

                                otherRow++;

                                otherRowList[index] = parseFloat(data.debit);

                                let btn = '',
                                    html = '';

                                if (index == 0) {
                                    btn = `<x-button color="primary" icon="plus" id="other-add" fontawesome size="sm" />`;
                                } else {
                                    btn = `<x-button color="danger" icon="trash" id="other-remove-${index}" fontawesome size="sm" />`;
                                }

                                html = `<div class="row" id="otherRow-${index}">
                                            <div class="col-md-4">
                                                <input type="hidden" name="type[]" value="other">
                                                <div class="form-group">
                                                    <x-select name="coa_id[]" label="other" id="other-select-${index}">
                                                        <option value="${data.coa_id}" selected>${data.coa.account_code} - ${data.coa.name}</option>
                                                    </x-select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <div class="form-group">
                                                            <input type="hidden" name="credit[]" value="${formatRupiahWithDecimal(data.credit)}">
                                                            <x-input type="text" name="debit[]" label="jumlah" id="other-debit-${index}" value="${formatRupiahWithDecimal(data.debit)}" class="commas-form" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-self-end">
                                                        <div class="form-group">
                                                            ${btn}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <x-input type="text" name="note[]" label="keterangan" value="${data.note}" id="" />
                                                </div>
                                            </div>
                                        </div>
                                    `;


                                $('#other-card').append(html);

                                if (index == 0) {
                                    $('#other-add').click(function(e) {
                                        e.preventDefault();
                                        addCard(otherRow)
                                    });
                                } else {
                                    $(`#other-remove-${index}`).click(function(e) {
                                        e.preventDefault();
                                        deleteCard(index);
                                    });
                                }

                                // select2
                                initCoaSelect(`#other-select-${index}`);

                                // trigger form
                                $(`#other-debit-${index}`).on('keyup', function() {
                                    otherRowList[index] = thousandToFloat($(this).val());
                                });

                                initCommasForm()
                            });
                        } else {
                            addCard(0);
                        }
                    };

                    const deleteCard = (row_index) => {
                        $(`#otherRow-${row_index}`).remove();
                        otherRowList[row_index] = 0;
                    };

                    const addCard = (row_index) => {
                        otherRow++;

                        let btn = '',
                            html = '';

                        if (row_index == 0) {
                            btn = `<x-button color="primary" icon="plus" id="other-add" fontawesome size="sm" />`;
                        } else {
                            btn = `<x-button color="danger" icon="trash" id="other-remove-${row_index}" fontawesome size="sm" />`;
                        }

                        html = `
                            <div class="row" id="otherRow-${row_index}">
                                <div class="col-md-4">
                                    <input type="hidden" name="type[]" value="other">
                                    <div class="form-group">
                                        <x-select name="coa_id[]" label="other" id="other-select-${row_index}">

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <input type="hidden" name="credit[]" value="0">
                                                <x-input type="text" name="debit[]" label="jumlah" id="other-debit-${row_index}" class="commas-form" />
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-self-end">
                                            <div class="form-group">
                                                ${btn}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="note[]" label="keterangan" id="" />
                                    </div>
                                </div>
                            </div>
                        `;


                        $('#other-card').append(html);

                        if (row_index == 0) {
                            $('#other-add').click(function(e) {
                                e.preventDefault();
                                addCard(otherRow)
                            });
                        } else {
                            $(`#other-remove-${row_index}`).click(function(e) {
                                e.preventDefault();
                                deleteCard(row_index);
                            });
                        }

                        // select2
                        initCoaSelect(`#other-select-${row_index}`);

                        // trigger form
                        otherRowList[row_index] = 0;
                        $(`#other-debit-${row_index}`).on('keyup', function() {
                            otherRowList[row_index] = thousandToFloat($(this).val());
                        });

                        initCommasForm()
                    };

                    displayCard();
                };

                const initializeDisplayDescription = () => {
                    $('#description-card').append(`
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <x-text-area name="description" label="deskripsi" id="" cols="30" rows="10">${description ?? ''}</x-text-area>
                            </div>
                        </div>
                    `);
                };

                initializeDisplayData();
            };

            const handleSubmit = () => {
                const validateData = () => {
                    let credit = thousandToFloat($('#amount-parent-credit').val());
                    let debit = thousandToFloat($('#amount-parent-debit').val());
                    let other = otherRowList.reduce((a, b) => a + b, 0);

                    debit += other;

                    if (credit != debit) {
                        alert('amount credit and debit must be same');
                        $(this).find('input[type=submit]').prop('disabled', false);
                        $(this).find('button[type=submit]').prop('disabled', false);

                        $('#edit-cash-bond-form').submit(function() {
                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);
                        });
                    } else {
                        $('#edit-cash-bond-form').submit(function() {
                            $(this).find('input[type=submit]').prop('disabled', true);
                            $(this).find('button[type=submit]').prop('disabled', true);
                        });

                        $('select').prop('disabled', false);
                        $('#edit-cash-bond-form').unbind('submit').submit();
                    }

                };

                $('#edit-cash-bond-form').submit(function(e) {
                    e.preventDefault();
                    validateData();
                });
            };

            init();
        });
    </script>

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#cash-bond-sidebar');
        sidebarActive('#cash-bond');
    </script>
@endsection
