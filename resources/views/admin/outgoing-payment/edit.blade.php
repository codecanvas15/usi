@extends('layouts.admin.layout.index')

@php
    $main = 'pengeluaran dana';
    $folder = 'outgoing-payment';
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
                        <a href="{{ route("admin.$folder.index") }}">{{ Str::headline($main) }}</a>
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
    @can('edit outgoing-payment')
        <form id="form-data" action="{{ route("admin.$folder.update", ['outgoing_payment' => $model->id]) }}" method="post">
            @method('PUT')
            @csrf
            <x-card-data-table title="{{ 'Edit ' . $main }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <form action="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h3 for="">No. <span>{{ $model->bankCodeMutation }}</span></h3>
                                    <input type="hidden" name="from" id="from" value="general">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" id="date" name="date" label="tanggal bayar" required value="{{ localDate($model->date) }}" onchange="checkClosingPeriod($(this))" class="datepicker-input" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="from" id="from" label="referensi" required onchange="initReference()">
                                        <option {{ $model->fund_submission_id ? '' : 'selected' }} value="general">General</option>
                                        <option {{ $model->fund_submission_id ? 'selected' : '' }} value="fund_submission">Pengajuan Dana</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3 @if (!$model->fund_submission_id) d-none @endif" id="form-fund-submission">
                                <div class="form-group">
                                    <x-select name="fund_submission_id" id="fund_submission_id" label="pengajuan dana" onchange="getFundSubmission($(this))">
                                        @if ($model->fund_submission)
                                            <option value="{{ $model->fund_submission_id }}">{{ $model->fund_submission->code }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="form_detail">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="branch_id" id="branch_id" label="branch" required>
                                        <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                            @if ($model->fund_submission)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="fund_submission_date" label="tanggal pengajuan dana" id="fund_submission_date" value="{{ localDate($model->fund_submission->date) }}" required readonly />
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group">
                                    @if ($model->invoice_return || $model->cash_advance_receive)
                                        <x-input type="text" name="to_name" label="kepada" id="to_name" value="{{ $model->to_name }}" required readonly />
                                    @else
                                        <x-input type="text" name="to_name" label="kepada" id="to_name" value="{{ $model->to_name }}" required />
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="coa_id" id="coa_id" required label="kas/bank">
                                        @if ($model->coa)
                                            <option value="{{ $model->coa_id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="{{ $model->bank_code_mutation }}" readonly />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <x-select name="invoice_return_id" id="invoice_return_id" label="penerimaan retur">
                                    @if ($model->invoice_return)
                                        <option value="{{ $model->invoice_return_id }}" selected>{{ $model->invoice_return->customer->nama }} - {{ $model->invoice_return->code }}</option>
                                    @endif
                                </x-select>
                            </div>
                            <div class="col-md-3">
                                <x-select name="cash_advance_receive_id" id="cash_advance_receive_id" label="pengembalian uang muka">
                                    @if ($model->cash_advance_receive)
                                        <option value="{{ $model->cash_advance_receive_id }}" selected>{{ $model->cash_advance_receive->customer->nama }} - {{ $model->cash_advance_receive->code }}</option>
                                    @endif
                                </x-select>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <x-select name="currency_id" id="currency_id" label="currency" required>
                                    <option value="{{ $model->currency->id }}" selected>{{ $model->currency->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-3">
                                @if ($model->currency->is_local)
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                                @else
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
                                @endif
                            </div>

                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="project_id" id="project_id" label="project">
                                        @if ($model->project)
                                            <option value="{{ $model->project_id }}">{{ $model->project->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="reference" id="reference" value="{!! $model->reference !!}" />
                                </div>
                            </div>
                            @if ($model->fund_submission)
                                @if ($model->is_giro)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="giro_number" label="nomor giro" id="giro_number" value="{{ $model->fund_submission->giro_number }}" required readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="giro_liquid_date" label="tanggal cair giro" id="giro_liquid_date" value="{{ localDate($model->fund_submission->giro_liquid_date) }}" required readonly />
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <div class="col-md-12 mt-4">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ Str::headline('akun') }}</th>
                                                <th>{{ Str::headline('keterangan') }}</th>
                                                <th class="text-end">{{ Str::headline('jumlah') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="outgoing-payment-detail-data">
                                            <input type="hidden" id="count_rows" value="{{ count($model->outgoing_payment_details) }}">
                                            @foreach ($model->outgoing_payment_details as $key => $outgoing_payment_detail)
                                                <tr id="outgoing-payment-detail-{{ $key }}_edit" class="{{ $outgoing_payment_detail->type }}_row">
                                                    <td>
                                                        <input type="hidden" name="is_return[]" value="{{ $outgoing_payment_detail->invoice_return ? 'true' : '' }}" />
                                                        <input type="hidden" name="type[]" value="{{ $outgoing_payment_detail->type }}" />
                                                        <input type="hidden" name="outgoing_payment_detail_id[]" value="{{ $outgoing_payment_detail->id }}" />
                                                        <select name="coa_detail_id[]" id="coa_detail_id_{{ $key }}_edit" class="form-control" required autofocus style="width:100%">
                                                            <option value="{{ $outgoing_payment_detail->coa_id }}">{{ $outgoing_payment_detail->coa->account_code }} - {{ $outgoing_payment_detail->coa->name }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="note_{{ $key }}_edit" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $outgoing_payment_detail->note }}" {{ $readonly }} />
                                                    </td>
                                                    <td>
                                                        <input type="text" id="debit_{{ $key }}_edit" name="debit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($outgoing_payment_detail->debit) }}" {{ $readonly }} />
                                                    </td>
                                                    <td>
                                                        @if ($readonly != 'readonly')
                                                            @if ($key != 0 || !in_array($outgoing_payment_detail->type, ['return', 'cash_advance']))
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#outgoing-payment-detail-{{ $key }}_edit').remove();countTotal()"><i class="fa fa-times"></i></button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">TOTAL</th>
                                                <th class="text-end" id="debit_total">{{ formatNumber($model->outgoing_payment_details()->sum('debit')) }}</th>
                                                <input type="hidden" id="debit_total_hide" value="{{ $model->outgoing_payment_details()->sum('debit') }}">
                                            </tr>
                                            <tr>
                                                <th class="text-end" colspan="5">
                                                    @if ($readonly != 'readonly')
                                                        <x-button color="success" label="Tambah Baris +" type="button" onclick="addNewOutgoingPaymentDetailRow()" class="btn-sm" />
                                                    @endif
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <a href="{{ route('admin.' . $folder . '.index') }}" class="btn btn-secondary">Cancel</a>
                                @if ($model->status != 'approve')
                                    <x-button type="submit" color="primary" label="Save data" />
                                @endif
                            </div>
                        </div>
                    </form>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    @can('edit outgoing-payment')
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/admin/outgoing-payment/index.js') }}?v=11.12.2025"></script>
        <script src="{{ asset('js/admin/outgoing-payment/edit.js') }}"></script>
        <script src="{{ asset('js/admin/select/coa.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script>
            var key = '{{ count($model->outgoing_payment_details) }}';

            sidebarMenuOpen('#finance-main-sidebar');
            sidebarActive('#outgoing-payment-sidebar');
            sidebarActive('#outgoing-payment');
            checkClosingPeriod($('#date'));

            $('#currency_id').change(function(e) {
                e.preventDefault();
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
        </script>

        @if ($readonly != 'readonly')
            <script>
                initCoaSelect(`select[name="coa_detail_id[]"]`);

                initSelect2Search('currency_id', "{{ route('admin.select.currency') }}", {
                    id: "id",
                    text: "kode,nama"
                });

                initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                }, 0, {
                    account_type: "Cash & Bank",
                    currency_id: function() {
                        return $('#currency_id').val()
                    }
                });


                initSelect2Search('project_id', "{{ route('admin.select.project') }}", {
                    id: "id",
                    text: "code,name"
                }, 0, {
                    branch_id: function() {
                        return $('#branch_id').val()
                    }
                });
            </script>
        @endif
    @endcan

    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
