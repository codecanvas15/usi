@extends('layouts.admin.layout.index')

@php
    $main = 'journal';
@endphp

@section('title', Str::headline("Create $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <x-card-data-table title="{{ 'create ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.store") }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="date" name="date" label="date" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" required autofucus onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-4">
                            <x-select name="currency_id" id="currency-id" label="currency" required>

                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" class="commas-form" name="exchange_rate" id="exchange-rate" label="kurs" required />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <x-text-area name="reference" label="reference" id="reference" cols="30" rows="10"></x-text-area>
                        </div>
                        <div class="col-md-6">
                            <x-text-area name="remark" label="remark" id="remark" cols="30" rows="10"></x-text-area>
                        </div>
                    </div>

                    <hr class="mt-30">

                    <div class="mt-30" id="second-step">
                        <div id="journal-details">

                        </div>

                        <hr class="mt-30">
                        <div class="row mt-30">
                            <div class="row justify-content-end">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="total_debit" class="text-end" label="total_debit" id="total_debit" disabled required textColor="primary" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="total_credit" class="text-end" label="total_credit" id="total_credit" disabled required textColor="primary" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="d-flex justify-content-end gap-3">
                                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                                <x-button type="submit" color="primary" label="Save data" />
                            </div>
                        </div>
                    </div>

                </form>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {

            let FORM_COUNT = 2,
                CREDIT_LIST = [],
                DEBIT_LIST = [],
                CREDIT_TOTAL = 0,
                DEBIT_TOTAL = 0;


            const CALCULATE = () => {
                CREDIT_TOTAL = 0;
                DEBIT_TOTAL = 0;

                CREDIT_TOTAL = CREDIT_LIST.reduce((a, b) => (parseFloat(a) + parseFloat(b)), 0);
                DEBIT_TOTAL = DEBIT_LIST.reduce((a, b) => (parseFloat(a) + parseFloat(b)), 0);

                $('#total_debit').val(formatRupiahWithDecimal(DEBIT_TOTAL));
                $('#total_credit').val(formatRupiahWithDecimal(CREDIT_TOTAL));
            }

            $('#total_debit').val(formatRupiahWithDecimal(DEBIT_TOTAL));
            $('#total_credit').val(formatRupiahWithDecimal(CREDIT_TOTAL));

            // init select 2 ===================================================================================================================================================
            initSelect2Search('currency-id', "{{ route('admin.select.currency') }}", {
                id: "id",
                text: "nama"
            });

            $('#currency-id').change(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.currency.detail') }}/${$(this).val()}`,
                    success: function({
                        data
                    }) {
                        if (data.is_local) {
                            $('#exchange-rate').val(1);
                            $('#exchange-rate').attr('readonly', true);
                        } else {
                            $('#exchange-rate').val(0);
                            $('#exchange-rate').attr('readonly', false);
                        }
                    }
                });
            });
            // / init select 2 ===================================================================================================================================================

            const add_journal_details = (index) => {
                let btn = ``;
                if (index == 1) {
                    btn = `<div class="col-md-2 row align-items-end">
                                <div class="form-group">
                                    <x-button color="info" size="sm" id="add-forms" icon="plus" fontawesome block/>
                                </div>
                            </div>`
                } else {
                    btn = `<div class="col-md-2 row align-items-end">
                                <div class="form-group">
                                    <x-button color="danger" size="sm" id="delete-form-${index}" icon="trash" fontawesome block/>
                                </div>
                            </div>`
                }

                $('#journal-details').append(`
                    <div class="row ${index != 1 ? 'mt-20' : ''}" id="form-details-${index}">
                        <div class="col-md-4">
                            <x-select name="account_id[]" id="account-id-${index}" label="account / coa" required>

                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <x-input type="text" name="remark_detail[]" label="remark" id="remark" />
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <x-input type="text" class="commas-form text-end" name="debit[]" id="debit-${index}" label="debit" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <x-input type="text" class="commas-form text-end" name="credit[]" id="credit-${index}" label="credit" />
                                    </div>
                                </div>
                                ${btn}
                            </div>
                        </div>
                    </div>
                `);

                initCommasForm()

                // * button delete and add event
                if (index == 1) {
                    $('#add-forms').click(function(e) {
                        e.preventDefault();
                        add_journal_details(FORM_COUNT);
                    });
                } else {
                    $(`#delete-form-${index}`).click(function(e) {
                        e.preventDefault();
                        $(`#form-details-${index}`).remove();
                        DEBIT_LIST[index] = 0;
                        CREDIT_LIST[index] = 0;
                        CALCULATE();
                    });
                }

                // coa =================================================================================================================================================================================
                initCoaSelect(`#account-id-${index}`);
                // coa =================================================================================================================================================================================

                // debit and credit event =================================================================================================================================================================================
                $(`#debit-${index}`).keyup(function(e) {
                    if (this.value) {
                        DEBIT_LIST[index] = thousandToFloat($(this).val());
                    } else {
                        DEBIT_LIST[index] = 0;
                    }
                    CALCULATE();
                });
                $(`#credit-${index}`).keyup(function(e) {
                    if (this.value) {
                        CREDIT_LIST[index] = thousandToFloat($(this).val());
                    } else {
                        CREDIT_LIST[index] = 0;
                    }
                    CALCULATE();
                });

                FORM_COUNT++;
                // debit and credit event =================================================================================================================================================================================
            }
            add_journal_details(1);
            // second step =================================================================================================================================================================================

            // check debit and credit balance
            $('form').submit(function(e) {
                if (thousandToFloat($('#total_debit').val()) != thousandToFloat($('#total_credit').val())) {
                    e.preventDefault();
                    $(this).find('input[type=submit]').prop('disabled', false);
                    $(this).find('button[type=submit]').prop('disabled', false);
                    showAlert('', 'Jumlah Debit dan Kredit tidak sama!', 'warning');
                }
            })
        });
    </script>

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#journal')
    </script>
@endsection
