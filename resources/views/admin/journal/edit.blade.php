@extends('layouts.admin.layout.index')

@php
    $main = 'journal';
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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <x-card-data-table title="{{ 'edit ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.update", $model) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="date" name="date" label="date" value="{{ localDate($model->date) }}" required autofucus onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-4">
                            <x-select name="currency_id" id="currency-id" label="currency" required>
                                <option value="{{ $model->currency_id }}">{{ $model->currency->nama }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                @if ($model->currency->is_local)
                                    <x-input type="text" class="commas-form" name="exchange_rate" id="exchange-rate" value="{{ formatNumber($model->exchange_rate) }}" label="kurs" required readonly />
                                @else
                                    <x-input type="text" class="commas-form" name="exchange_rate" id="exchange-rate" value="{{ formatNumber($model->exchange_rate) }}" label="kurs" required />
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <x-text-area name="reference" label="reference" id="reference" cols="30" rows="10">{{ $model->reference }}</x-text-area>
                        </div>
                        <div class="col-md-6">
                            <x-text-area name="remark" label="remark" id="remark" cols="30" rows="10">{{ $model->remark }}</x-text-area>
                        </div>
                    </div>

                    <hr class="mt-30">
                    <div class="mt-20" id="form-journal-details">
                        @foreach ($model->journal_details as $item)
                            <div class="row {{ $loop->index == 0 ? 'mt-20' : '' }}" id="form-details-{{ $loop->index }}">
                                <div class="col-md-4">
                                    <x-select name="account_id[]" id="account-id-{{ $loop->index }}" label="account / coa" required>
                                        <option value="{{ $item->coa_id }}">{{ $item->coa->account_code }} - {{ $item->coa->name }}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-3">
                                    <x-input type="text" name="remark_detail[]" label="remark" id="remark" value="{{ $item->remark }}" />
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <x-input type="text" class="commas-form text-end debit" name="debit[]" id="debit-{{ $loop->index }}" label="debit" value="{{ formatNumber($item->debit) }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <x-input type="text" class="commas-form text-end credit" name="credit[]" id="credit-{{ $loop->index }}" label="credit" value="{{ formatNumber($item->credit) }}" />
                                            </div>
                                        </div>
                                        @if ($loop->index == 0)
                                            <div class="col-md-2 row align-items-end">
                                                <div class="form-group">
                                                    <x-button color="info" size="sm" id="add-forms" icon="plus" fontawesome block />
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-2 row align-items-end">
                                                <div class="form-group">
                                                    <x-button color="danger" size="sm" id="delete-form-{{ $loop->index }}" class="delete-forms" icon="trash" fontawesome block onclick="delete_details" />
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr class="mt-30">
                    <div class="row mt-30">
                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="total_debit" label="total_debit" id="total_debit" class="text-end" value="{{ formatNumber($model->debit_total) }}" disabled textColor="primary" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="total_credit" label="total_credit" id="total_credit" class="text-end" value="{{ formatNumber($model->credit_total) }}" disabled textColor="primary" />
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

            // init select 2 ===================================================================================================================================================
            initSelect2Search('currency-id', "{{ route('admin.select.currency') }}", {
                id: "id",
                text: "nama"
            });

            @foreach ($model->journal_details as $item)
                initCoaSelect(`#account-id-{{ $loop->index }}`)
            @endforeach
            // / init select 2 ===================================================================================================================================================

            let FORM_COUNT = '{{ $model->journal_details->count() }}',
                CREDIT_LIST = [],
                DEBIT_LIST = [],
                CREDIT_TOTAL = '{{ $model->total_credit }}',
                DEBIT_TOTAL = '{{ $model->total_debit }}';

            const delete_details = (e) => {
                e.preventDefault();
                let index = e.target.id.split('-')[2];
                $(`#form-details-${index}`).remove();

                CREDIT_LIST[index] = 0;
                DEBIT_LIST[index] = 0;
            }

            $('.delete-forms').map((index, item) => {
                $(`#${item.id}`).on('click', (e) => {
                    e.preventDefault();
                    delete_details(e)
                });
            });

            $('.debit').map((index, item) => {
                if (item.value == "") {
                    DEBIT_LIST.push(0);
                } else {
                    DEBIT_LIST.push(thousandToFloat(item.value));
                }

                $(`#${item.id}`).keyup(function(e) {
                    e.preventDefault();
                    DEBIT_LIST[index] = thousandToFloat(this.value);
                    CALCULATE()
                });
            });

            $('.credit').map((index, item) => {
                if (item.value == "") {
                    CREDIT_LIST.push(0);
                } else {
                    CREDIT_LIST.push(thousandToFloat(item.value));
                }

                $(`#${item.id}`).keyup(function(e) {
                    e.preventDefault();
                    CREDIT_LIST[index] = thousandToFloat(this.value);;
                    CALCULATE()
                });
            });

            const CALCULATE = () => {
                CREDIT_TOTAL = 0;
                DEBIT_TOTAL = 0;

                CREDIT_TOTAL = CREDIT_LIST.reduce((a, b) => (parseFloat(a) + parseFloat(b)), 0);
                DEBIT_TOTAL = DEBIT_LIST.reduce((a, b) => (parseFloat(a) + parseFloat(b)), 0);

                $('#total_debit').val(formatRupiahWithDecimal(DEBIT_TOTAL));
                $('#total_credit').val(formatRupiahWithDecimal(CREDIT_TOTAL));
            }

            const addJournalDetails = (index) => {
                $(`#form-journal-details`).append(`
                    <div class="row" id="form-details-${index}">
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
                                <div class="col-md-2 row align-items-end">
                                    <div class="form-group">
                                        <x-button color="danger" size="sm" id="delete-form-${index}" icon="trash" fontawesome block/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                initCommasForm();

                $(`#delete-form-${index}`).click(function(e) {
                    e.preventDefault();
                    $(`#form-details-${index}`).remove();
                    DEBIT_LIST[index] = 0;
                    CREDIT_LIST[index] = 0;
                    CALCULATE();
                });

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
            }

            $('#add-forms').click(function(e) {
                e.preventDefault();
                addJournalDetails(FORM_COUNT);
            });

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
