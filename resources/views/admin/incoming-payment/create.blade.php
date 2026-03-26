@extends('layouts.admin.layout.index')

@php
    $main = 'incoming-payment';
    $folder = 'penerimaan dana';
@endphp

@section('title', Str::headline("tambah $folder") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($folder) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $folder) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route("admin.$main.store") }}" method="post">
        @csrf
        <x-card-data-table title="{{ 'tambah ' . $folder }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row" id="form_detail">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-select name="branch_id" id="branch_id" label="branch" required>
                                            <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" id="date" name="date" label="Tanggal" required value="{{ date('d-m-Y') }}" onchange="checkClosingPeriod($(this))" class="datepicker-input" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="terima dari" name="from_name" id="from_name" required />
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-select name="coa_id" id="coa_id" required label="kas/bank" onchange="get_coa_detail($(this))">
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
                                        <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <x-select name="purchase_return_id" id="purchase_return_id" label="penerimaan retur">
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <x-select name="cash_advance_payment_id" id="cash_advance_payment_id" label="pengembalian uang muka">
                                    </x-select>
                                </div>

                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <x-select name="currency_id" id="currency_id" label="currency" required>
                                        <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-select name="project_id" id="project_id" label="project">

                                        </x-select>
                                    </div>
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
                                <div class="col-md-6 giro-checkbox d-none">
                                    <div class="form-group">
                                        <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                                    </div>
                                </div>
                                <div class="col-md-6 giro-form d-none">
                                    <x-select name="receive_payment_id" id="receive_payment_id" label="giro masuk" onchange="get_receive_payment($(this))">

                                    </x-select>
                                </div>
                                <div class="col-md-12 giro-form d-none">
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
                        <div class="col-md-12 mt-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ Str::headline('akun') }}</th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                        <th class="text-end">{{ Str::headline('jumlah') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="incoming-payment-detail-data">
                                    <input type="hidden" id="count_rows" value="0">
                                    <tr id="incoming-payment-detail-0">
                                        <td>
                                            <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control" required autofocus style="width:100%">
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" id="note_0" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" />
                                        </td>
                                        <td>
                                            <input type="text" id="credit_0" name="credit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="" />
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end" id="credit_total">0</th>
                                        <input type="hidden" id="credit_total_hide">
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="3">
                                            <x-button color="success" label="Tambah Baris +" type="button" onclick="addNewIncomingPaymentDetailRow()" class="btn-sm" />
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/incoming-payment/index.js') }}"></script>
    <script>
        var key = 0;
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#incoming-payment')

        checkClosingPeriod($('#date'));
        initSelect2Search('currency_id', "{{ route('admin.select.currency') }}", {
            id: "id",
            text: "kode,nama,negara"
        });

        initSelect2SearchPagination(`coa_detail_id_0`, `${base_url}/select/coa`, {
            id: "id",
            text: "account_code,name"
        });

        initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
            id: "id",
            text: "code,name"
        }, 2, {
            branch_id: function() {
                return $('#branch_id').val();
            }
        });

        initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: "Cash & Bank",
            currency_id: function() {
                return $('#currency_id').val();
            },
        });

        if (branchIsPrimary == 1) {
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        }

        initSelect2SearchPaginationData(`cash_advance_payment_id`, `${base_url}/select/cash-advance-payment`, {
            id: "id",
            text: "code,to_name"
        }, 0, {
            currency_id: function() {
                return $('#currency_id').val();
            },
            branch_id: function() {
                return $('#branch_id').val();
            },
        });


        initCommasForm();

        $('#currency_id').change(function(e) {
            e.preventDefault();
            $('#coa_id').val(null).trigger('change');
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

        initSelect2Search(`receive_payment_id`, `{{ route('admin.select.receive-payment') }}`, {
            id: "id",
            text: "from_name,from_bank,cheque_no"
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
            status: 'approve',
            pay_from: 'other',
        });

        $('#sequence_code').on('blur', function() {
            check_bank_code(
                '#coa_id',
                '#sequence_code',
                '#date',
                'in'
            );
        });
    </script>
@endsection
