@extends('layouts.admin.layout.index')

@php
    $main = 'cash-bond';
    $title = 'kasbon';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

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
                        {{ Str::headline("tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <x-card-data-table title='{{ "tambah $title" }}'>
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <form action="{{ route('admin.cash-bond.store') }}" method="post" id="form-create-cash-bond">
                    @csrf
                    <div class="border-bottom border-primary pb-20">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="employee_id" label="pegawai" id="employee-select" required>

                                    </x-select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="date" label="tanggal" id="date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-select name="currency_id" label="currency" id="currency-select" required>
                                                <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input type="text" name="exchange_rate" id="exchange-rate" value="1" class="commas-form" required readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
                                    <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="branch_id" label="branch" id="branch-select" required>
                                        <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="project_id" label="project" id="project-select"></x-select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <input type="hidden" name="type[]" value="cash_bank">
                                <div class="form-group">
                                    <x-select name="coa_id[]" label="akun cash bank" id="coaCashBank-select" required>

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="debit[]" value="0">
                                    <input type="hidden" name="credit[]" id="amount-parent-credit" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="note[]" id="" value="kas bank" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-20">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="hidden" name="type[]" value="cash_advance">
                                <div class="form-group">
                                    @php
                                        $default_cashbond_coa = get_default_coa('finance', 'Kasbon')->coa ?? null;
                                    @endphp
                                    <x-select name="coa_id[]" label="akun piutang" id="coaEmployee-select" required>
                                        @if ($default_cashbond_coa)
                                            <option value="{{ $default_cashbond_coa->id }}">{{ $default_cashbond_coa->account_code }} - {{ $default_cashbond_coa->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="credit[]" value="0">
                                    <x-input type="text" name="debit[]" label="jumlah" id="amount-parent-debit" class="commas-form" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="note[]" label="keterangan" id="" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="my-20 py-20 border-top border-bottom border-primary" id="other-card">

                    </div> --}}

                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <x-text-area name="description" label="deskripsi" id="" cols="30" rows="10"></x-text-area>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.cash-bond.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <x-button color="primary" icon="primary" label="Save data" id="handle-form" icon="save" fontawesome iconLeft class="float-end" />
                        </div>
                    </div>

                </form>

            </x-slot>

        </x-card-data-table>
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

            const init = () => {
                mainCard();
                otherTransaction();
                handleSubmit();
                initCommasForm();
                checkClosingPeriod($('#date'));
            };

            let otherRow = 0;
            let otherRowList = [];

            let parentCurrency = JSON.parse('{!! get_local_currency() !!}'),
                CoaBankCurrency = null,
                localCurrency = JSON.parse('{!! get_local_currency() !!}');

            // * initialize select2
            // * initialize currency
            const mainCard = () => {

                initSelectEmployee('#employee-select');

                initSelect2Search('currency-select', "{{ route('admin.select.currency') }}", {
                    id: "id",
                    text: "kode,nama,negara"
                });

                initProjectSelect('#project-select');

                initSelect2SearchPagination(`coaCashBank-select`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                }, 0, {
                    account_type: "Cash & Bank",
                    // currency_id: function() {
                    //     return $('#currency-select').val();
                    // }
                });

                initCoaSelect('#coaEmployee-select');

                $('#currency-select').change(function(e) {
                    e.preventDefault();

                    $('#coaCashBank-select').val(null).trigger('change');

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
            };

            // * other transaction function
            const otherTransaction = () => {

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

                // addCard(otherRow);
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

                        $('#form-create-cash-bond').submit(function() {
                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);
                        });
                    } else {
                        $('#form-create-cash-bond').submit(function() {
                            $(this).find('input[type=submit]').prop('disabled', true);
                            $(this).find('button[type=submit]').prop('disabled', true);
                        });

                        $('#form-create-cash-bond').unbind('submit').submit();
                    }

                };

                $('#form-create-cash-bond').submit(function(e) {
                    e.preventDefault();
                    validateData();
                });
            };

            init();

            $('#amount-parent-debit').on('keyup', function() {
                $('#amount-parent-credit').val(thousandToFloat($(this).val()));
            });
        });

        $('#sequence_code').on('blur', function() {
            check_bank_code(
                '#coaCashBank-select',
                '#sequence_code',
                '#date',
                'in'
            );
        });
    </script>
    @if (get_current_branch()->is_primary)
        <script>
            $(document).ready(function() {
                initSelect2Search('branch-select', "{{ route('admin.select.branch') }}", {
                    id: "id",
                    text: "name"
                });
            });
        </script>
    @endif

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#cash-bond-sidebar');
        sidebarActive('#cash-bond');
    </script>
@endsection
