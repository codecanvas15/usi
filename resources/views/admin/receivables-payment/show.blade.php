@extends('layouts.admin.layout.index')

@php
    $main = 'receivables-payment';
    $menu = 'pembayaran customer';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.incoming-payment.index') }}?tab=receivable-payment-tab">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-9">
                <x-card-data-table title="{{ 'detail ' . $menu }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <div class="row">
                            <div class="col-md-12">
                                <x-table theadColor='danger'>
                                    <x-slot name="table_head">
                                        <th></th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <th>{{ Str::headline('branch') }}</th>
                                            <td>{{ $model->branch->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <td>{{ $model->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('customer') }}</th>
                                            <td>{{ $model->customer?->nama }}</td>
                                        </tr>
                                        @if ($model->receivables_payment_vendors->count() > 0)
                                            <tr>
                                                <th></th>
                                                <td>
                                                    <span class="badge pill-rounded bg-success"><i class="fa fa-check"></i>
                                                        Cross Piutang & Hutang</span><br>
                                                    {{ $model->vendor?->nama }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>{{ Str::headline('no bukti') }}</th>
                                            <td>{{ $model->bank_code_mutation }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('tanggal') }}</th>
                                            <td>{{ localDate($model->date) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('kas/bank') }}</th>
                                            <td>{{ $model->coa?->account_code }} - {{ $model->coa?->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('currency') }}</th>
                                            <td>{{ $model->currency?->kode }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('currency invoice') }}</th>
                                            <td>{{ $model->invoice_currency?->kode }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('kurs') }}</th>
                                            <td>{{ floatDotFormat($model->exchange_rate) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('project') }}</th>
                                            <td>{{ $model->project?->code ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('reference') }}</th>
                                            <td>{{ $model->reference }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('status') }}</th>
                                            <th>
                                                <div class="badge badge-lg badge-{{ incoming_payment_status()[$model->status]['color'] }}">
                                                    {{ Str::headline(incoming_payment_status()[$model->status]['text']) }} -
                                                    {{ Str::headline(incoming_payment_status()[$model->status]['label']) }}
                                                </div>
                                            </th>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <div class="col-md-12 giro-form {{ $model->receive_payment_id ? '' : 'd-none' }}">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <input type="hidden" name="giro_outstanding_amount" id="giro_outstanding_amount" value="{{ $model->receive_payment ? $model->receive_payment->outstanding_amount + $model->total : 0 }}">
                                            <tbody>
                                                <tr class="bg-info">
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
                                                            {{ $model->receive_payment->due_date }}
                                                            @if ($model->receive_payment->due_status['is_due'])
                                                                <br><span class="badge badge-success">{{ $model->receive_payment->due_status['message'] }}</span>
                                                            @else
                                                                <br><span class="badge badge-danger">{{ $model->receive_payment->due_status['message'] }}</span>
                                                            @endif
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
                            <div class="col-md-12">
                                <h4>Piutang</h4>
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <tbody>
                                            @foreach ($model->receivables_payment_details as $receivables_payment_detail)
                                                <tr class="bg-info">
                                                    <th>Tanggal</th>
                                                    <th>Invoice</th>
                                                    <th>Cur.</th>
                                                    <th class="text-end">Kurs</th>
                                                    <th class="text-end">Total {{ $model->invoice_currency->kode }}</th>
                                                    <th class="text-end">Piutang {{ $model->invoice_currency->kode }}</th>
                                                    <th>Ket.</th>
                                                </tr>
                                                <tr>
                                                    <td>{{ localDate($receivables_payment_detail->invoice_parent->date) }}</td>
                                                    <td>{{ $receivables_payment_detail->invoice_parent->code ?? $receivables_payment_detail->invoice_parent->reference }}
                                                    </td>
                                                    <td>{{ $receivables_payment_detail->invoice_parent->currency->kode }}</td>
                                                    <td class="text-end">
                                                        {{ formatNumber($receivables_payment_detail->invoice_parent->exchange_rate) }}
                                                    </td>
                                                    <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                        {{ formatNumber($receivables_payment_detail->invoice_parent->total) }}
                                                    </td>
                                                    <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                        {{ formatNumber($receivables_payment_detail->outstanding_amount) }}
                                                    </td>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-end"><b>Bayar {{ $model->currency->kode }}</b></td>
                                                    <td class="text-end">{{ $model->currency->simbol }}
                                                        {{ formatNumber($receivables_payment_detail->receive_amount) }}</td>
                                                    <td>{{ $receivables_payment_detail->note }}</td>
                                                </tr>
                                                @if ($model->currency_id != $model->invoice_currency_id)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end"><b>Bayar {{ $model->invoice_currency->kode }}</b>
                                                        </td>
                                                        <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                            {{ formatNumber($receivables_payment_detail->receive_amount_foreign) }}
                                                        </td>
                                                        <td>{{ $receivables_payment_detail->note }}</td>
                                                    </tr>
                                                @endif
                                                @if ($receivables_payment_detail->is_clearing)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end"><b>Selisih Bayar</b> <br> {{ $receivables_payment_detail->coa->account_code . ' - ' . $receivables_payment_detail->coa->name }} </td>
                                                        <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                            {{ formatNumber($receivables_payment_detail->receive_amount_gap_foreign) }}
                                                        </td>
                                                        <td>{{ $receivables_payment_detail->clearing_note }}</td>
                                                    </tr>
                                                @endif

                                                @if ($receivables_payment_detail->exchange_rate_gap_idr != 0)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end"><b>Selisih Kurs</b></td>
                                                        <td class="text-end">{{ get_local_currency()->simbol }}
                                                            {{ formatNumber($receivables_payment_detail->exchange_rate_gap_idr) }}
                                                        </td>
                                                        <td>{{ $receivables_payment_detail->exchange_rate_gap_note }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr class="table-info">
                                                <td><b>TOTAL {{ $model->currency->kode }}</b></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end"><b>{{ $model->currency->simbol }}
                                                        {{ formatNumber($model->receivables_payment_details->sum('receive_amount')) }}</b>
                                                </td>
                                                <td></td>
                                            </tr>
                                            @if ($model->currency_id != $model->invoice_currency_id)
                                                <tr class="table-info">
                                                    <td><b>TOTAL {{ $model->invoice_currency->kode }}</b></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-end"><b>{{ $model->invoice_currency->simbol }}
                                                            {{ formatNumber($model->receivables_payment_details->sum('receive_amount_foreign')) }}</b>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if (count($model->receivables_payment_invoice_returns) > 0)
                                <div class="col-md-12 mt-3">
                                    <h4>Retur</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="bg-info">
                                                <tr>
                                                    <th>{{ Str::headline('tanggal') }}</th>
                                                    <th>{{ Str::headline('no retur') }}</th>
                                                    <th>{{ Str::headline('currency') }}</th>
                                                    <th class="text-end">{{ Str::headline('rate') }}</th>
                                                    <th class="text-end">{{ Str::headline('piutang') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                                    <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                                    @if (!$model->currency->is_local)
                                                        <th class="text-end">{{ Str::headline('bayar') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                                    @endif
                                                    <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="selected_return_table">
                                                @forelse ($model->receivables_payment_invoice_returns as $receivables_payment_invoice_return)
                                                    <tr id="selected_return_row_{{ $receivables_payment_invoice_return->invoice_return_id }}">
                                                        <td>
                                                            {{ localDate($receivables_payment_invoice_return->invoice_return->date) }}
                                                        </td>
                                                        <td>{{ $receivables_payment_invoice_return->invoice_return->code }}</td>
                                                        <td>{{ $receivables_payment_invoice_return->invoice_return->currency->nama }}</td>
                                                        <td class="text-end">
                                                            {{ formatNumber($receivables_payment_invoice_return->exchange_rate ?? 0) }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ formatNumber($receivables_payment_invoice_return->outstanding_amount ?? 0) }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ formatNumber($receivables_payment_invoice_return->amount) }}
                                                        </td>
                                                        @if (!$model->currency->is_local)
                                                            <td class="text-end">
                                                                {{ formatNumber($receivables_payment_invoice_return->amount_foreign) }}
                                                            </td>
                                                        @endif
                                                        <td class="text-end">
                                                            {{ formatNumber($receivables_payment_invoice_return->exchange_rate_gap_idr) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr id="row_return_empty">
                                                        <td colspan="9" class="text-center">Belum ada data yang dipilih</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4">TOTAL</th>
                                                    <th class="text-end" id="return_outstanding_amount_total"></th>
                                                    <th class="text-end" id="return_amount_total">{{ floatDotFormat($model->receivables_payment_invoice_returns->sum('amount')) }}</th>
                                                    @if (!$model->currency->is_local)
                                                        <th class="text-end d-none column-multi-currency" id="return_amount_foreign_total">{{ floatDotFormat($model->receivables_payment_invoice_returns->sum('amount_foreign')) }}</th>
                                                    @endif
                                                    <th class="text-end" id="return_exchange_rate_gap_total"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if ($model->receivables_payment_vendors->count() > 0)
                                <div class="col-md-12">
                                    <h4>Utang</h4>
                                    <div class="table-responsive">
                                        <table class="table table-stripped">
                                            <tbody>
                                                @foreach ($model->receivables_payment_vendors as $receivables_payment_vendor)
                                                    <tr class="bg-info">
                                                        <th>Tanggal</th>
                                                        <th>Purchase Invoice</th>
                                                        <th>Cur.</th>
                                                        <th class="text-end">Kurs</th>
                                                        <th class="text-end">Total {{ $model->invoice_currency->kode }}</th>
                                                        <th class="text-end">Hutang {{ $model->invoice_currency->kode }}</th>
                                                        <th>Ket.</th>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ localDate($receivables_payment_vendor->supplier_invoice_parent->date) }}
                                                        </td>
                                                        <td>{{ $receivables_payment_vendor->supplier_invoice_parent->code ?? $receivables_payment_vendor->supplier_invoice_parent->reference }}
                                                        </td>
                                                        <td>{{ $receivables_payment_vendor->supplier_invoice_parent->currency->kode }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ formatNumber($receivables_payment_vendor->supplier_invoice_parent->exchange_rate) }}
                                                        </td>
                                                        <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                            {{ formatNumber($receivables_payment_vendor->supplier_invoice_parent->total) }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $model->invoice_currency->simbol }}
                                                            {{ formatNumber($receivables_payment_vendor->outstanding_amount) }}
                                                        </td>
                                                        <th></th>
                                                    </tr>
                                                    @if (count($receivables_payment_vendor->receivables_payment_vendor_lpbs) > 0)
                                                        <tr class="table-warning">
                                                            <th></th>
                                                            <th>No. LPB</th>
                                                            <th></th>
                                                            <th class="text-end">Total</th>
                                                            <th class="text-end">Hutang</th>
                                                            <th class="text-end">Bayar</th>
                                                            <th></th>
                                                        </tr>
                                                        @foreach ($receivables_payment_vendor->receivables_payment_vendor_lpbs as $lpb)
                                                            <tr class="table-warning">
                                                                <td></td>
                                                                <td>{{ $lpb->item_receiving_report->kode }}</td>
                                                                <td></td>
                                                                <td class="text-end">
                                                                    {{ formatNumber($lpb->item_receiving_report->total) }}</td>
                                                                <td class="text-end">{{ formatNumber($lpb->outstanding) }}</td>
                                                                <td class="text-end">{{ formatNumber($lpb->amount_foreign) }}
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end"><b>Bayar {{ $model->currency->kode }}</b></td>
                                                        <td class="text-end">{{ $model->currency->simbol }}
                                                            {{ formatNumber($receivables_payment_vendor->amount) }}</td>
                                                        <td>{{ $receivables_payment_vendor->note }}</td>
                                                    </tr>
                                                    @if ($model->currency_id != $model->invoice_currency_id)
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-end"><b>Bayar
                                                                    {{ $model->invoice_currency->kode }}</b></td>
                                                            <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                                {{ formatNumber($receivables_payment_vendor->amount_foreign) }}
                                                            </td>
                                                            <td>{{ $receivables_payment_vendor->note }}</td>
                                                        </tr>
                                                    @endif
                                                    @if ($receivables_payment_vendor->is_clearing)
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-end"><b>Selisih Bayar</b> <br> {{ $receivables_payment_vendor->coa->account_code . ' - ' . $receivables_payment_vendor->coa->name }} </td>
                                                            <td class="text-end">{{ $model->invoice_currency->simbol }}
                                                                {{ formatNumber($receivables_payment_vendor->amount_gap_foreign) }}
                                                            </td>
                                                            <td>{{ $receivables_payment_vendor->clearing_note }}</td>
                                                        </tr>
                                                    @endif
                                                    @if ($receivables_payment_vendor->exchange_rate_gap_idr != 0)
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-end"><b>Selisih Kurs</b></td>
                                                            <td class="text-end">{{ get_local_currency()->simbol }}
                                                                {{ formatNumber($receivables_payment_vendor->exchange_rate_gap_idr) }}
                                                            </td>
                                                            <td>{{ $receivables_payment_vendor->exchange_rate_gap_note }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                <tr class="table-info">
                                                    <td><b>TOTAL {{ $model->currency->kode }}</b></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-end"><b>{{ $model->currency->simbol }}
                                                            {{ formatNumber($model->receivables_payment_vendors->sum('amount')) }}</b>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                @if ($model->currency_id != $model->invoice_currency_id)
                                                    <tr class="table-info">
                                                        <td><b>TOTAL {{ $model->invoice_currency->kode }}</b></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end"><b>{{ $model->invoice_currency->simbol }}
                                                                {{ formatNumber($model->receivables_payment_vendors->sum('amount_foreign')) }}</b>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if ($model->receivables_payment_others->count() > 0)
                                <div class="col-md-12">
                                    <h4>Lain - Lain</h4>
                                    <x-table theadColor='info' width="100%">
                                        <x-slot name="table_head">
                                            <th>Akun</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Jumlah</th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                            @foreach ($model->receivables_payment_others ?? [] as $detail)
                                                <tr>
                                                    <td>{{ $detail->coa->account_code }} - {{ $detail->coa->name }}</td>
                                                    <td>{{ $detail->note }}</td>
                                                    <td class="text-end">{{ $model->currency->simbol }}
                                                        {{ floatDotFormat($detail->credit) }}</td>
                                                </tr>
                                            @endforeach
                                        </x-slot>
                                        <x-slot name="table_foot">
                                            <tr>
                                                <th>TOTAL</th>
                                                <th></th>
                                                <th class="text-end">{{ $model->currency->simbol }}
                                                    {{ floatDotFormat($model->receivables_payment_others->sum('credit')) }}</th>
                                            </tr>
                                        </x-slot>
                                    </x-table>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-info">
                                        <tr>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="receivables-payment-other-data">
                                        <tr>
                                            <td class="text-end">{{ Str::headline('total invoice') }}</td>
                                            <td class="text-end" id="total-data-invoice">
                                                {{ formatNumber($model->receivables_payment_details->sum('receive_amount')) }}
                                            </td>
                                        </tr>
                                        @if ($model->receivables_payment_invoice_returns->count() > 0)
                                            <tr>
                                                <td class="text-end">{{ Str::headline('total retur') }}</td>
                                                <td class="text-end" id="total-data-return">
                                                    {{ formatNumber($model->receivables_payment_invoice_returns->sum('amount') * -1) }}</td>
                                            </tr>
                                        @endif
                                        @if ($model->receivables_payment_vendors->count() > 0)
                                            <tr>
                                                <td class="text-end">{{ Str::headline('total purchase invoice') }}</td>
                                                <td class="text-end" id="total-data-supplier-invoice">
                                                    {{ formatNumber($model->receivables_payment_vendors->sum('amount') * -1) }}</td>
                                            </tr>
                                        @endif
                                        @if ($model->receivables_payment_others->count() > 0)
                                            <tr>
                                                <td class="text-end">{{ Str::headline('total lain lain') }}</td>
                                                <td class="text-end" id="total-data-adjustment">
                                                    {{ floatDotFormat($model->receivables_payment_others->sum('credit')) }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end">{{ Str::headline('total') }}</td>
                                            <td class="text-end" id="total-data-total">
                                                {{ floatDotFormat($model->receivables_payment_others->sum('credit') + $model->receivables_payment_details->sum('receive_amount') - $model->receivables_payment_vendors->sum('amount') - $model->receivables_payment_invoice_returns->sum('amount')) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @can('view journal')
                            @include('components.journal-table')
                        @endcan
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button type="button" color='primary' fontawesome icon="history" label="riwayat transaksi" class="w-auto" size="sm" id="history-button" />
                            <x-modal title="riwayat transaksi" id="history-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Transaksi</th>
                                                    <th>Nomor</th>
                                                </tr>
                                            </thead>
                                            <tbody id="history-list">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-10 border-top pt-10">
                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                    </div>
                                </x-slot>
                            </x-modal>
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link="{{ route('admin.incoming-payment.index') }}?tab=receivable-payment-tab" />
                            @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                                @if ($model->check_available_date)
                                    @can("edit $main")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan
                                    @can("delete $main")
                                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                    @endcan
                                @endif
                            @endif

                        </div>
                    </x-slot>
                </x-card-data-table>
                @if ($is_payment_history)
                    <x-card-data-table title="{{ 'Payment Information' }}">
                        <x-slot name="table_content">
                            <x-table theadColor='dark' width="100%">
                                <x-slot name="table_body">
                                    @foreach ($receivables_payment_details ?? [] as $receivables_payment_detail)
                                        <tr class="bg-dark">
                                            <th colspan="4" class="text-center">
                                                {{ $receivables_payment_detail->invoice_parent->code }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Ket.</th>
                                            <th class="text-end">Jumlah</th>
                                            <th class="text-end">Bayar</th>
                                        </tr>
                                        @foreach ($receivables_payment_detail->payment_informations as $payment_information)
                                            <tr>
                                                <td>{{ localDate($payment_information->date) }}</td>
                                                <td>{{ $payment_information->note }}</td>
                                                <td class="text-end">
                                                    @if ($payment_information->amount_to_receive != 0)
                                                        {{ $model->invoice_currency->simbol }}
                                                        {{ floatDotFormat($payment_information->amount_to_receive) }}
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if ($payment_information->receive_amount != 0)
                                                        {{ $model->invoice_currency->simbol }}
                                                        {{ floatDotFormat($payment_information->receive_amount) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th>TOTAL</th>
                                            <th></th>
                                            <th class="text-end">{{ $model->invoice_currency->simbol }}
                                                {{ floatDotFormat($receivables_payment_detail->payment_informations->sum('amount_to_receive')) }}
                                            </th>
                                            <th class="text-end">{{ $model->invoice_currency->simbol }}
                                                {{ floatDotFormat($receivables_payment_detail->payment_informations->sum('receive_amount')) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>SISA</th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-end">{{ $model->invoice_currency->simbol }}
                                                {{ floatDotFormat($receivables_payment_detail->payment_informations->sum('amount_to_receive') - $receivables_payment_detail->payment_informations->sum('receive_amount')) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </x-slot>
                    </x-card-data-table>
                @endif

            </div>
            <div class="col-md-3">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Status Logs' }}">
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @forelse ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    <h5 class="fw-bold">Empty</h5>
                                </li>
                            @endforelse
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @forelse ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    <h5 class="fw-bold">Empty</h5>
                                </li>
                            @endforelse
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#receivables-payment')

        $('#history-button').on('click', function() {
            $.ajax({
                url: '{{ route("admin.$main.history", $model->id) }}',
                success: function({
                    data
                }) {
                    $('#history-list').html('');
                    $.each(data, function(key, value) {
                        let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                        $('#history-list').append(`
                                <tr>
                                    <td>${localDate(value.date)}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                    });

                    $('#history-modal').modal('show');
                }
            });
        });
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\ReceivablesPayment`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
