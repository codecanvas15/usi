@extends('layouts.admin.layout.index')

@php
    $main = 'account-payable';
    $menu = 'pembayaran supplier';
@endphp

@section('title', Str::headline("edit $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.outgoing-payment.index') }}?tab=account-payable">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.update", ['account_payable' => $model->id]) }}" method="post">
        @csrf
        @method('PUT')
        <x-card-data-table title="{{ 'edit ' . $menu }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h3 for="">No. <span>{{ $model->bankCodeMutation }}</span></h3>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <x-select name="vendor_id" id="vendor_id" label="Pilih Vendor" required autofocus>
                                <option value="{{ $model->vendor->id }}">{{ $model->vendor->nama }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <x-select name="fund_submission_id" id="fund_submission_id" label="Pilih Pengajuan Dana" required autofocus onchange="getFundSubmission($(this))">
                                <option value="{{ $model->fund_submission->id }}">{{ $model->fund_submission->code }}</option>
                            </x-select>
                            <input type="hidden" id="local_currency_id" value="{{ get_local_currency()->id }}">
                        </div>
                        <div class="col-md-3">
                            <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ localDate($model->date) }}" />
                        </div>
                    </div>
                    <div id="data-detail">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Branch</label>
                                    <p type="text" id="branch_name">{{ $model->branch->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Pengajuan Dana</label>
                                    <p type="text" id="date">{{ localDate($model->fund_submission_date) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Currency</label>
                                    <p type="text" id="currency_name">{{ $model->currency->kode }} - {{ $model->currency->nama }}</p>
                                    <input type="hidden" id="currency_id" value="{{ $model->currency->id }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Purchase Invoice Currency</label>
                                    <p type="text" invoice" id="supplier_invoice_currency">{{ $model->supplier_invoice_currency->kode }} - {{ $model->supplier_invoice_currency->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">kurs</label>
                                    <x-input id="exchange_rate" name="exchange_rate" class="commas-form text-end" onkeyup="countExchangeRateGap()" value="{{ formatNumber($model->exchange_rate) }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Reference</label>
                                    <p type="text" id="reference">{{ $model->reference }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Project</label>
                                    <p type="text" id="project">{{ $model->project->name ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">COA</label>
                                    <p type="text" id="coa_id">{{ $model->coa->account_code }} - {{ $model->coa->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="{{ $model->bank_code_mutation }}" readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <p type="text" id="parent_note">{{ $model->note }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Purchase Invoice</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="bg-info">
                                            <tr>
                                                <th>{{ Str::headline('tanggal') }}</th>
                                                <th>{{ Str::headline('purchase invoice') }}</th>
                                                <th>{{ Str::headline('currency') }}</th>
                                                <th class="text-end">{{ Str::headline('kurs') }}</th>
                                                <th class="text-end">{{ Str::headline('total') }} <span class="supplier_invoice_currency_id_code"></span></th>
                                                <th class="text-end">{{ Str::headline('sisa') }} <span class="supplier_invoice_currency_id_code"></span></th>
                                                <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_code"></span></th>
                                                <th class="text-end">{{ Str::headline('bayar') }} <span class="supplier_invoice_currency_id_code"></span></th>
                                                <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                                <th>{{ Str::headline('ket') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selected_supplier_invoice_table">
                                            @foreach ($model->account_payable_details as $payable_detail)
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="account_payable_detail_id[]" value="{{ $payable_detail->id }}">
                                                        {{ localDate($payable_detail->supplier_invoice_parent->date) }}
                                                        <input type="hidden" name="fund_submission_supplier_detail_id[]" value="{{ $payable_detail->id }}">
                                                    </td>
                                                    <td>{{ $payable_detail->supplier_invoice_parent->code }}</td>
                                                    <td>
                                                        {{ $payable_detail->supplier_invoice_parent->currency->kode }} - {{ $payable_detail->supplier_invoice_parent->currency->nama }}
                                                        <input type="hidden" id="currency_id_{{ $payable_detail->id }}" value="{{ $payable_detail->supplier_invoice_parent->currency_id }}">
                                                    </td>
                                                    <td class="text-end">
                                                        {{ formatNumber($payable_detail->supplier_invoice_parent->exchange_rate) }}
                                                        <input type="hidden" id="exchange_rate_{{ $payable_detail->id }}" value="{{ thousand_to_float(formatNumber($payable_detail->supplier_invoice_parent->exchange_rate)) }}">
                                                    </td>
                                                    <td class="text-end">
                                                        {{ formatNumber($payable_detail->supplier_invoice_parent->total) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ formatNumber($payable_detail->outstanding_amount) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ formatNumber($payable_detail->amount) }}
                                                        <input type="hidden" id="amount_{{ $payable_detail->id }}" value="{{ thousand_to_float(formatNumber($payable_detail->amount)) }}">
                                                    </td>
                                                    <td class="text-end">
                                                        <span id="amount_foreign_text_{{ $payable_detail->id }}">{{ formatNumber($payable_detail->amount_foreign) }}</span>
                                                        <input type="hidden" id="amount_foreign_{{ $payable_detail->id }}" value="{{ thousand_to_float(formatNumber($payable_detail->amount_foreign)) }}">
                                                    </td>
                                                    <td class="text-end">
                                                        <span id="exchange_rate_gap_text_{{ $payable_detail->id }}">{{ formatNumber($payable_detail->exchange_rate_gap) }}</span>
                                                        <input type="hidden" id="exchange_rate_gap_{{ $payable_detail->id }}" value="{{ thousand_to_float(formatNumber($payable_detail->exchange_rate_gap)) }}">
                                                    </td>
                                                    <td>
                                                        {{ $payable_detail->note }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h3>Lain - Lain</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="bg-info">
                                            <tr>
                                                <th>{{ Str::headline('akun') }}</th>
                                                <th>{{ Str::headline('keterangan') }}</th>
                                                <th class="text-end">{{ Str::headline('jumlah') }} <span class="supplier_invoice_currency_id_code"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="fund_submission_supplier_other_data">
                                            @foreach ($model->account_payable_others as $other)
                                                <tr>
                                                    <td>{{ $other->coa->account_code }} - {{ $other->coa->name }}</td>
                                                    <td>{{ $other->note }}</td>
                                                    <td class="text-end">{{ formatNumber($other->debit) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>TOTAL</th>
                                                <th></th>
                                                <th class="text-end" id="debit_total"><span class="currency_id_symbol"></span> {{ formatNumber($model->account_payable_others->sum('credit')) }}</th>
                                                <input type="hidden" id="debit_total_hide">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ route('admin.outgoing-payment.index') }}?tab=account-payable" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/account-payable/transaction.js') }}"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment');

        initSelect2Search('vendor_id', `${base_url}/select/vendor`, {
            id: "id",
            text: "nama"
        });

        initSelect2Search('fund_submission_id', `${base_url}/select/fund-submission`, {
            id: "id",
            text: "to_name,code"
        }, 0, {
            item: "lpb",
            available: true,
            to_id: function() {
                return $('#vendor_id').val();
            }
        });

        $('.currency_id_symbol').text('{{ $model->currency->simbol }}');
        $('.supplier_invoice_currency_id_symbol').text('{{ $model->supplier_invoice_currency->simbol }}');
        $('.currency_id_code').text('{{ $model->currency->kode }}');
        $('.supplier_invoice_currency_id_code').text('{{ $model->supplier_invoice_currency->kode }}');
    </script>
@endsection
