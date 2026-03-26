@extends('layouts.admin.layout.index')

@php
    $main = 'incoming-payment';
    $folder = 'penerimaan dana';
@endphp

@section('title', Str::headline("edit $folder") . ' - ')

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
                        {{ Str::headline('edit ' . $folder) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route("admin.$main.update", ['incoming_payment' => $model->id]) }}" method="post">
        @method('PUT')
        @csrf
        <x-card-data-table title="{{ 'edit ' . $folder }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h3 for="">No. <span>{{ $model->bankCodeMutation }}</span></h3>
                            </div>
                        </div>

                    </div>
                    <div class="row" id="form_detail">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-select name="branch_id" id="branch_id" label="branch" required>
                                            <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" id="date" name="date" label="Tanggal" required value="{{ localDate($model->date) }}" onchange="checkClosingPeriod($(this))" class="datepicker-input" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        @if ($model->purchase_return)
                                            <x-input type="text" label="terima dari" name="from_name" id="from_name" required value="{{ $model->from_name }}" readonly />
                                        @else
                                            <x-input type="text" label="terima dari" name="from_name" id="from_name" required value="{{ $model->from_name }}" />
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-select name="coa_id" id="coa_id" required label="kas/bank">
                                            @if ($model->coa)
                                                <option value="{{ $model->coa->id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="{{ $model->bank_code_mutation }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <x-select name="purchase_return_id" id="purchase_return_id" label="penerimaan retur">
                                        @if ($model->purchase_return)
                                            <option value="{{ $model->purchase_return_id }}">{{ $model->purchase_return->vendor->nama }} - {{ $model->purchase_return->code }}</option>
                                        @endif
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <x-select name="cash_advance_payment_id" id="cash_advance_payment_id" label="pengembalian uang muka" data-allow-clear="true">
                                        @if ($model->cash_advance_payment)
                                            <option value="{{ $model->cash_advance_payment_id }}">{{ $model->cash_advance_payment->model_reference->nama }} {{ $model->cash_advance_payment->code }}</option>
                                        @endif
                                    </x-select>
                                </div>

                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <x-select name="currency_id" id="currency_id" label="currency" required>
                                        <option value="{{ $model->currency->id }}" selected>{{ $model->currency->nama }}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    @if ($model->currency->is_local)
                                        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                                    @else
                                        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
                                    @endif
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-select name="project_id" id="project_id" label="project">
                                            @if ($model->project)
                                                <option value="{{ $model->project_id }}">{{ $model->project->code }} - {{ $model->project->name }}</option>
                                            @endif
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="note" name="reference" id="reference" value="{!! $model->reference !!}" />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        @if ($model->receive_payment_id)
                                            <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" checked />
                                        @else
                                            <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 giro-form {{ $model->receive_payment_id ? '' : 'd-none' }}">
                                    <x-select name="receive_payment_id" id="receive_payment_id" label="giro masuk" onchange="get_receive_payment($(this))">
                                        @if ($model->receive_payment)
                                            <option value="{{ $model->receive_payment_id }}">{{ $model->receive_payment->from_bank }} - {{ $model->receive_payment->cheque_no }}</option>
                                        @endif
                                    </x-select>
                                </div>

                                <div class="col-md-12 giro-form {{ $model->receive_payment_id ? '' : 'd-none' }}">
                                    <table class="table">
                                        <input type="hidden" name="giro_outstanding_amount" id="giro_outstanding_amount" value="{{ $model->receive_payment ? $model->receive_payment->outstanding_amount + $model->total : 0 }}">
                                        <tbody>
                                            <tr class="bg-dark">
                                                <td colspan="2">
                                                    <b>INFORMASI GIRO</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>No Cheque</b></td>
                                                <td id="cheque_no">
                                                    @if ($model->receive_payment)
                                                        {{ $model->receive_payment->cheque_no }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Jatuh Tempo</b></td>
                                                <td id="due_date">
                                                    @if ($model->receive_payment)
                                                        {{ localDate($model->receive_payment->due_date) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>BG Mundur Bank</b></td>
                                                <td id="from_bank">
                                                    @if ($model->receive_payment)
                                                        {{ $model->receive_payment->from_bank }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Bank Pencairan</b></td>
                                                <td id="realization_bank">
                                                    @if ($model->receive_payment)
                                                        {{ $model->receive_payment->realization_bank }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Nominal</b></td>
                                                <td id="giro_amount">
                                                    @if ($model->receive_payment)
                                                        {{ formatNumber($model->receive_payment->amount) }}
                                                    @endif
                                                </td>
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
                                    <input type="hidden" id="count_rows" value="{{ count($model->incoming_payment_details) }}">
                                    @foreach ($model->incoming_payment_details as $key => $incoming_payment_detail)
                                        <tr id="incoming-payment-detail-{{ $key }}_edit" class="{{ $incoming_payment_detail->type }}_row">
                                            <td>
                                                <input type="hidden" name="is_return[]" value="{{ $incoming_payment_detail->purchase_return ? 'true' : '' }}" />
                                                <input type="hidden" name="incoming_payment_detail_id[]" value="{{ $incoming_payment_detail->id }}" />
                                                <input type="hidden" name="type[]" value="{{ $incoming_payment_detail->type }}" />

                                                <select name="coa_detail_id[]" id="coa_detail_id_{{ $key }}_edit_return" class="form-control" required autofocus style="width:100%">
                                                    <option value="{{ $incoming_payment_detail->coa_id }}">{{ $incoming_payment_detail->coa->account_code }} - {{ $incoming_payment_detail->coa->name }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="note_{{ $key }}_edit" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $incoming_payment_detail->note }}" />
                                            </td>
                                            <td>
                                                <input type="text" id="credit_{{ $key }}_edit" name="credit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($incoming_payment_detail->credit) }}" />
                                            </td>
                                            <td>
                                                @if ($key != 0 || !in_array($incoming_payment_detail->type, ['return', 'cash_advance']))
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="$('#incoming-payment-detail-{{ $key }}_edit').remove();countTotal()"><i class="fa fa-times"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end" id="credit_total">{{ formatNumber($model->incoming_payment_details()->sum('credit')) }}</th>
                                        <input type="hidden" id="credit_total_hide" value="{{ $model->incoming_payment_details()->sum('credit') }}">
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="4">
                                            <x-button color="success" label="Tambah Baris +" type="button" onclick="addNewIncomingPaymentDetailRow()" class="btn-sm" />
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                            @if ($model->status != 'approve')
                                <x-button type="submit" color="primary" label="Save data" />
                            @endif
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/incoming-payment/index.js?v=1.0.0') }}"></script>
    <script>
        var key = '{{ count($model->incoming_payment_details) }}';
        const is_use_purchase_return = '{{ $model->purchase_return ? 'true' : 'false' }}';
        const is_use_cash_advance = '{{ $model->cash_advance_payment ? 'true' : 'false' }}';

        if (is_use_purchase_return == 'true') {
            $('#cash_advance_payment_id').prop('disabled', true);
        } else if (is_use_cash_advance == 'true') {
            $('#purchase_return_id').prop('disabled', true);
        }

        checkClosingPeriod($('#date'));
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment')
        sidebarActive('#incoming-payment-sidebar');

        initCoaSelect(`select[name="coa_detail_id[]"]`);

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
    </script>
@endsection
