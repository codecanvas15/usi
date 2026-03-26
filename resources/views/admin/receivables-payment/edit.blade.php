@extends('layouts.admin.layout.index')

@php
    $main = 'receivables-payment';
    $menu = 'penerimaan customer';
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
                        Account Receivable
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.incoming-payment.index') }}?tab=receivable-payment-tab">{{ Str::headline($main) }}</a>
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
    <form id="form-data" action="{{ route("admin.$main.update", ['receivables_payment' => $model->id]) }}" method="post">
        @method('put')
        @csrf
        <input type="hidden" id="receivables_payment_id" value="{{ $model->id }}">
        <x-card-data-table title="{{ 'edit ' . $menu }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h3 for="">No. <span>{{ $model->bankCodeMutation }}</span></h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <x-select name="branch_id" id="branch_id" label="branch" required>
                                    <option value="{{ $model->branch->id }}">{{ $model->branch->name }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ localDate($model->date) }}" onchange="checkClosingPeriod($(this))" />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="customer_id" id="customer_id" label="customer" required onchange="$('#selected_invoice_table').html('');calculateData();get_customer_vendor($(this))">
                                    <option value="{{ $model->customer->id }}">{{ $model->customer->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="{{ $model->bank_code_mutation }}" readonly />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="currency_id" id="currency_id" label="cur." required onchange="checkCurrency();initInvoiceCurrency()">
                                    <option value="{{ $model->currency->id }}">{{ $model->currency->nama }}</option>
                                </x-select>
                                <input type="hidden" id="currency_id_symbol" value="{{ $model->currency->kode }}">
                                <input type="hidden" id="local_currency_id" value="{{ get_local_currency()->id }}">
                            </div>
                            <div class="col-md-6">
                                <x-select name="invoice_currency_id" id="invoice_currency_id" label="cur. invoice" required onchange="checkCurrency();initCurrency()">
                                    <option value="{{ $model->invoice_currency->id }}">{{ $model->invoice_currency->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                @if ($model->currency->is_local && $model->invoice_currency->is_local)
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly data-is-both-local="true" />
                                @else
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required data-is-both-local="false" />
                                @endif
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="project_id" id="project_id" label="project">
                                    @if ($model->project)
                                        <option value="{{ $model->project->id }}">{{ $model->project->name }}</option>
                                    @endif
                                </x-select>
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
                                <x-select name="coa_id" id="coa_id" label="kas/bank" required onchange="get_coa_detail($(this))">
                                    <option value="{{ $model->coa->id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                </x-select>
                            </div>
                            <div class="col-md- giro-checkbox d-none align-self-center6">
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
                                <div class="table-responsive">
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
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        <x-card-data-table title="piutang">
            <x-slot name="table_content">
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="row">
                            <div class="col text-end">
                                <x-button color="success" label="pilih invoice +" type="button" class="btn-sm mb-2" onclick="getInvoiceSelect()" />
                                <div class="modal fade" id="invoiceSelectModal" aria-hidden="true" aria-labelledby="invoiceSelectModalLabel" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="invoiceSelectModalLabel">Pilih Invoice</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped" id="invoice_select_table">
                                                        <thead class="bg-info">
                                                            <tr>
                                                                <th class="text-center">Tanggal</th>
                                                                <th class="text-center">Invoice</th>
                                                                <th class="text-center">Jatuh Tempo</th>
                                                                <th class="text-center">Cur.</th>
                                                                <th class="text-end">Kurs</th>
                                                                <th class="text-end">Total</th>
                                                                <th class="text-end">Piutang</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="data_invoice_select">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <x-button color="info" label="simpan" type="button" onclick="saveSelectedInvoice()" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="bg-info">
                                    <tr>
                                        <th>{{ Str::headline('tanggal') }}</th>
                                        <th>{{ Str::headline('invoice') }}</th>
                                        <th>{{ Str::headline('currency') }}</th>
                                        <th class="text-end">{{ Str::headline('rate') }}</th>
                                        <th class="text-end">{{ Str::headline('piutang') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                        <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ $model->currency->kode }}</span></th>
                                        <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                        <th class="text-end">{{ Str::headline('selisih bayar') }}</th>
                                        <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                        <th>{{ Str::headline('ket') }}</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody id="selected_invoice_table">
                                    @foreach ($model->receivables_payment_details as $detail)
                                        <tr id="selected_invoice_row_{{ $detail->invoice_parent->id }}">
                                            <td>
                                                {{ localDate($detail->invoice_parent->date) }}
                                                <input type="hidden" name="type[]" value="{{ $detail->invoice_parent->type }}">
                                                <input type="hidden" name="receivables_payment_detail_id[]" value="{{ $detail->id }}">
                                                <input type="hidden" name="invoice_id[]" value="{{ $detail->invoice_parent->id }}">
                                            </td>
                                            <td>{{ $detail->invoice_parent->code }} <br> ({{ $detail->invoice_parent->type }})</td>
                                            <td>{{ $detail->invoice_parent->currency->kode }}</td>
                                            <td class="text-end">
                                                {{ formatNumber($detail->exchange_rate) }}
                                                <input type="hidden" id="exchange_rate_{{ $detail->invoice_parent->id }}" value="{{ $detail->invoice_parent->exchange_rate }}">
                                            </td>
                                            <td class="text-end">
                                                <span id="outstanding_amount_text_{{ $detail->invoice_parent->id }}">{{ formatNumber($detail->outstanding_amount) }}</span>
                                                <input type="hidden" id="outstanding_amount_{{ $detail->invoice_parent->id }}" name="outstanding_amount[]" value="{{ thousand_to_float(formatNumber($detail->outstanding_amount)) }}">
                                            </td>
                                            <td class="text-end">
                                                <span id="receive_amount_text_{{ $detail->invoice_parent->id }}">{{ formatNumber($detail->receive_amount) }}</span>
                                                <input type="hidden" id="receive_amount_{{ $detail->invoice_parent->id }}" name="receive_amount[]" value="{{ thousand_to_float(formatNumber($detail->receive_amount)) }}">
                                            </td>
                                            <td class="text-end d-none column-multi-currency">
                                                <span id="receive_amount_foreign_text_{{ $detail->invoice_parent->id }}">{{ formatNumber($detail->receive_amount_foreign) }}</span>
                                                <input type="hidden" id="receive_amount_foreign_{{ $detail->invoice_parent->id }}" name="receive_amount_foreign[]" value="{{ thousand_to_float(formatNumber($detail->receive_amount_foreign)) }}">
                                                <input type="hidden" id="receive_amount_gap_foreign_{{ $detail->invoice_parent->id }}" name="receive_amount_gap_foreign[]" value="{{ thousand_to_float(formatNumber($detail->receive_amount_gap_foreign)) }}">
                                                <input type="hidden" id="is_clearing_{{ $detail->invoice_parent->id }}" name="is_clearing[]" value="{{ $detail->is_clearing }}">
                                                <input type="hidden" id="clearing_coa_id_{{ $detail->invoice_parent->id }}" name="clearing_coa_id[]" value="{{ $detail->coa->id ?? '' }}">
                                                <input type="hidden" id="clearing_coa_name_{{ $detail->invoice_parent->id }}" value="{{ $detail->coa->account_code ?? '' }} - {{ $detail->coa->name ?? '' }}">
                                            </td>
                                            <td class="text-end">
                                                <span id="receive_amount_gap_foreign_text_{{ $detail->invoice_parent->id }}">{{ formatNumber($detail->receive_amount_gap_foreign) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span id="exchange_rate_gap_text_{{ $detail->invoice_parent->id }}">{{ formatNumber($detail->exchange_rate_gap_idr) }}</span>
                                                <input type="hidden" id="exchange_rate_gap_{{ $detail->invoice_parent->id }}" name="exchange_rate_gap[]" value="{{ thousand_to_float(formatNumber($detail->exchange_rate_gap_idr)) }}">
                                            </td>
                                            <td>
                                                <span id="note_text_{{ $detail->invoice_parent->id }}">{{ $detail->note }}</span>
                                                <input type="hidden" id="note_{{ $detail->invoice_parent->id }}" name="note[]" value="{{ $detail->note }}">
                                                <input type="hidden" id="clearing_note_{{ $detail->invoice_parent->id }}" name="clearing_note[]" value="{{ $detail->clearing_note }}">
                                                <input type="hidden" id="exchange_rate_gap_note_{{ $detail->invoice_parent->id }}" name="exchange_rate_gap_note[]" value="{{ $detail->exchange_rate_gap_note }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="editSelectedInvoice({{ $detail->invoice_parent->id }})">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_invoice_row_{{ $detail->invoice_parent->id }}').remove();calculateData()">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">TOTAL</th>
                                        <th class="text-end" id="outstanding_amount_total">0</th>
                                        <th class="text-end" id="receive_amount_total">0</th>
                                        <th class="text-end d-none column-multi-currency" id="receive_amount_foreign_total">0</th>
                                        <th class="text-end" id="exchange_rate_gap_total">0</th>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-end">
                        <x-button color="success" label="Pilih Retur" type="button" class="btn-sm mb-3" onclick="getReturnSelect()" />
                        <div class="modal fade" id="returnSelectModal" aria-hidden="true" aria-labelledby="returnSelectModalLabel" tabindex="-1">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="returnSelectModalLabel">Pilih Retur</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body table-responsive">
                                        <table class="table table-striped" id="return_select_table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Tanggal</th>
                                                    <th class="text-center">Nomor Retur</th>
                                                    <th class="text-center">Currency</th>
                                                    <th class="text-center">Rate</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">Nominal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="data_return_select">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <x-button color="info" label="simpan" type="button" onclick="saveSelectedReturn()" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
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
                                        <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                        <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody id="selected_return_table">
                                    @forelse ($model->receivables_payment_invoice_returns as $receivables_payment_invoice_return)
                                        <tr id="selected_return_row_{{ $receivables_payment_invoice_return->invoice_return_id }}">
                                            <td>
                                                {{ $receivables_payment_invoice_return->invoice_return->date }}
                                                <input type="hidden" name="receivables_payment_invoice_return_id[]" value="{{ $receivables_payment_invoice_return->id }}">
                                                <input type="hidden" name="invoice_return_id[]" value="{{ $receivables_payment_invoice_return->invoice_return_id }}">
                                                <input type="hidden" name="return_total[]" id="return_total_{{ $receivables_payment_invoice_return->invoice_return_id }}" value="{{ formatNumber($receivables_payment_invoice_return->invoice_return->total) }}">
                                            </td>
                                            <td>{{ $receivables_payment_invoice_return->invoice_return->code }}</td>
                                            <td>{{ $receivables_payment_invoice_return->invoice_return->currency->nama }}</td>
                                            <td class="text-end">
                                                <input type="text" class="form-control commas-form text-end" id="return_exchange_rate_{{ $receivables_payment_invoice_return->invoice_return_id }}" value="{{ formatNumber($receivables_payment_invoice_return->exchange_rate ?? 0) }}" readonly>
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control commas-form text-end" id="return_outstanding_amount_{{ $receivables_payment_invoice_return->invoice_return_id }}" name="return_outstanding_amount[]" value="{{ formatNumber($receivables_payment_invoice_return->outstanding_amount ?? 0) }}" readonly>
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control commas-form text-end" id="return_amount_{{ $receivables_payment_invoice_return->invoice_return_id }}" name="return_amount[]" value="{{ formatNumber($receivables_payment_invoice_return->amount) }}" onkeyup="calculate_row_return({{ $receivables_payment_invoice_return->invoice_return_id }}, false)">
                                            </td>
                                            <td class="text-end d-none column-multi-currency">
                                                <input type="text" class="form-control commas-form text-end" id="return_amount_foreign_{{ $receivables_payment_invoice_return->invoice_return_id }}" name="return_amount_foreign[]" value="{{ formatNumber($receivables_payment_invoice_return->amount_foreign) }}" onkeyup="calculate_row_return({{ $receivables_payment_invoice_return->invoice_return_id }}, true)">
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control commas-form text-end" id="return_exchange_rate_gap_{{ $receivables_payment_invoice_return->invoice_return_id }}" name="return_exchange_rate_gap[]" value="{{ formatNumber($receivables_payment_invoice_return->exchange_rate_gap_idr) }}" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_return_row_{{ $receivables_payment_invoice_return->invoice_return_id }}').remove();calculate_final_total();calculate_return_total()">
                                                    <i class="fa fa-trash"></i>
                                                </button>
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
                                        <th class="text-end" id="return_outstanding_amount_total">0</th>
                                        <th class="text-end" id="return_amount_total">0</th>
                                        <th class="text-end d-none column-multi-currency" id="return_amount_foreign_total">0</th>
                                        <th class="text-end" id="return_exchange_rate_gap_total">0</th>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        {{-- <div id="vendor-section" class="{{ !$model->vendor ? 'd-none' : '' }}"> --}}
        <div id="vendor-section">
            <x-card-data-table title="hutang">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-12 mt-4">
                            <div class="modal fade" id="supplierInvoiceSelectModal" aria-hidden="true" aria-labelledby="supplierInvoiceSelectModalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="supplierInvoiceSelectModalLabel">Pilih Purchase Invoice</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="supplier_invoice_select_table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tanggal</th>
                                                            <th class="text-center">Purchase Invoice</th>
                                                            <th class="text-center">Jatuh Tempo</th>
                                                            <th class="text-center">Currency</th>
                                                            <th class="text-center">Rate</th>
                                                            <th class="text-end">Total</th>
                                                            <th class="text-end">Piutang</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="data_supplier_invoice_select">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <x-button color="info" label="simpan" type="button" onclick="saveSelectedSupplierInvoice()" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <div class="col-md-12 text-end">
                                    <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $model->vendor_id ?? '' }}">
                                    <x-button color="success" label="pilih hutang +" type="button" class="btn-sm mb-3" onclick="getSupplierInvoiceSelect()" />
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="bg-info">
                                            <tr>
                                                <th>{{ Str::headline('tanggal') }}</th>
                                                <th>{{ Str::headline('purchase invoice') }}</th>
                                                <th>{{ Str::headline('currency') }}</th>
                                                <th class="text-end">{{ Str::headline('rate') }}</th>
                                                <th class="text-end">{{ Str::headline('piutang') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                                <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ $model->currency->kode }}</span></th>
                                                <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="invoice_currency_id_symbol">{{ $model->invoice_currency->kode }}</span></th>
                                                <th class="text-end">{{ Str::headline('selisih bayar') }}</th>
                                                <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                                <th>{{ Str::headline('ket') }}</th>
                                                <th class="text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="selected_supplier_invoice_table">
                                            @foreach ($receivables_payment_vendors as $receivables_payment_vendor)
                                                <tr id="selected_supplier_invoice_row_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}">
                                                    <td>
                                                        {{ localDate($receivables_payment_vendor->supplier_invoice_parent->date) }}
                                                        <input type="hidden" name="receivables_payment_vendor_id[]" value="{{ $receivables_payment_vendor->id }}">
                                                        <input type="hidden" name="supplier_invoice_parent_id[]" value="{{ $receivables_payment_vendor->supplier_invoice_parent_id }}">
                                                        <input type="hidden" id="item_receiving_reports_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" name="item_receiving_reports[]" value="{{ $receivables_payment_vendor->receivables_payment_vendor_lpbs }}">
                                                    </td>
                                                    <td>{{ $receivables_payment_vendor->supplier_invoice_parent->code }}</td>
                                                    <td>{{ $receivables_payment_vendor->supplier_invoice_parent->currency->nama }}</td>
                                                    <td class="text-end">
                                                        {{ formatNumber($receivables_payment_vendor->supplier_invoice_parent->exchange_rate) }}
                                                        <input type="hidden" id="exchange_rate_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" value="{{ $receivables_payment_vendor->supplier_invoice_parent->exchange_rate }}">
                                                    </td>
                                                    <td class="text-end">
                                                        <span id="outstanding_amount_text_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}">{{ formatNumber($receivables_payment_vendor->outstanding_amount) }}</span>
                                                        <input type="hidden" id="outstanding_amount_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" name="outstanding_amount_vendor[]" value="{{ thousand_to_float(formatNumber($receivables_payment_vendor->outstanding_amount)) }}">
                                                    </td>
                                                    <td class="text-end">
                                                        <span id="amount_text_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}">{{ formatNumber($receivables_payment_vendor->amount) }}</span>
                                                        <input type="hidden" id="amount_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" name="amount_vendor[]" value="{{ thousand_to_float(formatNumber($receivables_payment_vendor->amount)) }}">
                                                    </td>
                                                    <td class="text-end d-none column-multi-currency">
                                                        <span id="amount_foreign_text_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}">{{ formatNumber($receivables_payment_vendor->amount_foreign) }}</span>
                                                        <input type="hidden" id="amount_foreign_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}" name="amount_foreign_vendor[]" value="{{ thousand_to_float(formatNumber($receivables_payment_vendor->amount_foreign)) }}">
                                                        <input type="hidden" id="amount_gap_foreign_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}" name="amount_gap_foreign_vendor[]" value="{{ thousand_to_float(formatNumber($receivables_payment_vendor->amount_gap_foreign)) }}">
                                                        <input type="hidden" id="is_clearing_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}" name="is_clearing_vendor[]" value="{{ $receivables_payment_vendor->is_clearing }}">
                                                        <input type="hidden" id="clearing_coa_id_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}" name="clearing_coa_id_vendor[]" value="{{ $receivables_payment_vendor->coa->id ?? '' }}">
                                                        <input type="hidden" id="clearing_coa_name_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}" value="{{ $receivables_payment_vendor->coa->account_code ?? '' }} - {{ $receivables_payment_vendor->coa->name ?? '' }}">
                                                    </td>
                                                    <td class="text-end">
                                                        <span id="amount_gap_foreign_text_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}">{{ formatNumber($receivables_payment_vendor->amount_gap_foreign) }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span id="exchange_rate_gap_text_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}">{{ formatNumber($receivables_payment_vendor->exchange_rate_gap_idr) }}</span>
                                                        <input type="hidden" id="exchange_rate_gap_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent->id }}" name="exchange_rate_gap_vendor[]" value="{{ thousand_to_float(formatNumber($receivables_payment_vendor->exchange_rate_gap_idr)) }}">
                                                    </td>
                                                    <td>
                                                        <span id="note_text_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}"> {{ $receivables_payment_vendor->note }}</span>
                                                        <input type="hidden" id="note_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" name="note_vendor[]" value="{{ $receivables_payment_vendor->note }}">
                                                        <input type="hidden" id="clearing_note_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" name="clearing_note_vendor[]" value="{{ $receivables_payment_vendor->clearing_note }}">
                                                        <input type="hidden" id="exchange_rate_gap_note_vendor_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}" name="exchange_rate_gap_note_vendor[]" value="{{ $receivables_payment_vendor->exchange_rate_gap_note }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary" onclick="editSelectedSupplierInvoice({{ $receivables_payment_vendor->supplier_invoice_parent_id }})">
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_supplier_invoice_row_{{ $receivables_payment_vendor->supplier_invoice_parent_id }}').remove();calculateData()">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4">TOTAL</th>
                                                <th class="text-end" id="outstanding_amount_total_vendor">0</th>
                                                <th class="text-end" id="amount_total_vendor">0</th>
                                                <th class="text-end d-none column-multi-currency" id="amount_foreign_total_vendor">0</th>
                                                <td></td>
                                                <th class="text-end" id="exchange_rate_gap_total_vendor">0</th>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
        </div>
        <x-card-data-table title="lain - lain">
            <x-slot name="table_content">
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="bg-info">
                                    <tr>
                                        <th>{{ Str::headline('akun') }}</th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                        <th class="text-end">{{ Str::headline('jumlah') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="receivables-payment-other-data">
                                    <input type="hidden" id="count_rows" value="{{ count($model->receivables_payment_others) }}">
                                    @foreach ($model->receivables_payment_others as $key => $receivables_payment_other)
                                        <tr id="receivables-payment-other-{{ $key }}_edit">
                                            <td>
                                                <input type="hidden" name="receivables_payment_other_id[]" value="{{ $receivables_payment_other->id }}" />
                                                <select name="coa_detail_id[]" id="coa_detail_id_{{ $key }}_edit" class="form-control" required autofocus style="width:100%">
                                                    <option value="{{ $receivables_payment_other->coa_id }}">{{ $receivables_payment_other->coa->account_code }} - {{ $receivables_payment_other->coa->name }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="note_other_{{ $key }}_edit" name="note_other[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $receivables_payment_other->note }}" />
                                            </td>
                                            <td>
                                                <input type="text" id="credit_{{ $key }}_edit" name="credit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($receivables_payment_other->credit) }}" />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#receivables-payment-other-{{ $key }}_edit').remove();countTotal()"><i class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end" id="credit_total">0</th>
                                        <input type="hidden" id="credit_total_hide" value="0">
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="3">
                                            <x-button color="success" label="Tambah Baris +" type="button" onclick="addReceivablesPaymentOtherRow()" class="btn-sm" />
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
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
                                <td class="text-end" id="total-data-invoice"></td>
                            </tr>
                            <tr>
                                <td class="text-end">{{ Str::headline('total retur') }}</td>
                                <td class="text-end" id="total-data-return"></td>
                            </tr>
                            <tr>
                                <td class="text-end">{{ Str::headline('total purchase invoice') }}</td>
                                <td class="text-end" id="total-data-supplier-invoice"></td>
                            </tr>
                            <tr>
                                <td class="text-end">{{ Str::headline('total adjustment') }}</td>
                                <td class="text-end" id="total-data-adjustment"></td>
                            </tr>
                            <tr>
                                <td class="text-end">{{ Str::headline('total') }}</td>
                                <td class="text-end" id="total-data-total"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                        @if ($model->status != 'approve' && $model->status != 'reject')
                            <x-button type="submit" color="primary" label="Save data" />
                        @endif
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>

    <div class="modal fade" id="invoiceEditModal" aria-hidden="true" aria-labelledby="invoiceEditModalLabel" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="invoiceEditModalLabel">Edit Invoice</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="tanggal" id="date_edit" class="datepicker-input" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="no. invoice" id="kode_edit" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="akun piutang" id="coa_edit" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="currency" id="currency_edit" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" id="exchange_rate_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="total" id="total_amount_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="terbayar" id="paid_amount_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="sisa bayar" id="outstanding_amount_edit" class="text-end" required value="0" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receive_amount_edit" class="form-label">Jumlah Bayar {{ $model->currency->code }}</label>
                                <input type="text" label="jumlah bayar" id="receive_amount_edit" required value="" value="0" class="form-control commas-form text-end" onkeyup="calculateGapEdit('local')" />
                            </div>
                        </div>
                        <div class="col-md-4" id="multi-currency-form">
                            <div class="form-group">
                                <label for="receive_amount_foreign_edit" class="form-label">Jumlah Bayar {{ $model->invoice_currency->code }} </label>
                                <input type="text" label="jumlah bayar" id="receive_amount_foreign_edit" required value="" value="0" class="form-control commas-form text-end" onkeyup="calculateGapEdit('foreign')" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receive_amount_gap_foreign_edit" class="form-label">Selisih Bayar </label>
                                <input type="text" label="selisih bayar" id="receive_amount_gap_foreign_edit" required value="" value="0" class="form-control commas-form text-end" readonly />
                            </div>
                        </div>
                        <div class="col-md-2 align-self-center">
                            <x-input-checkbox label="lunas" name="clearing" id="clearing" onclick="clearing()" />
                            @php
                                $clearing_coa = get_default_coa('finance', 'Selisih Bayar');
                            @endphp
                            <input type="hidden" id="default_clearing_coa_id" value="{{ $clearing_coa->coa->id ?? '' }}">
                            <input type="hidden" id="default_clearing_coa_name" value="{{ $clearing_coa->coa->account_code ?? '' }} - {{ $clearing_coa->coa->name ?? '' }}">
                        </div>
                        <div id="clearing_coa_form" class="col-md-12 d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-select name="clearing_coa_id" id="clearing_coa_id" label="coa">
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="keterangan selish bayar" id="clearing_note_edit" required value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="selisih kurs" id="exchange_rate_gap_edit" class="commas-form text-end" required readonly value="0" />
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="exchange_rate_gap_form">
                            <div class="form-group">
                                <x-input type="text" label="keterangan selish kurs" id="exchange_rate_gap_note_edit" required value="" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-input type="text" label="keterangan pembayaran" id="note_edit" required value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="edited_invoice_id">
                    <x-button color="info" label="simpan" type="button" onclick="" id="btn-update-selected-invoice" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="supplierInvoiceEditModal" aria-hidden="true" aria-labelledby="supplierInvoiceEditModalLabel" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="supplierInvoiceEditModalLabel">Edit Purchase Invoice</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="tanggal" id="date_edit_vendor" class="datepicker-input" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="no. purchase invoice" id="code_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="no. faktur pajak" id="reference_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="akun hutang" id="coa_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="currency" id="currency_edit_vendor" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" id="exchange_rate_edit_vendor" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="total" id="total_amount_edit_vendor" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="terbayar" id="paid_amount_edit_vendor" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table d-none" id="lpb-table">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>No LPB</th>
                                            <th>Hutang</th>
                                            <th class="label-lpb-amount">Jumlah</th>
                                            <th class="label-lpb-foreign column-multi-currency">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lpb-table-body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="sisa bayar" id="outstanding_amount_edit_vendor" class="text-end" required value="0" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_edit" class="form-label"></label>
                                <input type="text" label="jumlah bayar" id="amount_edit_vendor" required value="" class="form-control commas-form text-end" onkeyup="calculateGapEditVendor('local')" />
                            </div>
                        </div>
                        <div class="col-md-4" id="multi-currency-form-vendor">
                            <div class="form-group">
                                <label for="amount_foreign_edit" class="form-label"></label>
                                <input type="text" label="jumlah bayar" id="amount_foreign_edit_vendor" required value="" value="0" class="form-control commas-form text-end" onkeyup="calculateGapEditVendor('foreign')" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_gap_foreign_edit" class="form-label">Selisih Bayar </label>
                                <input type="text" label="selisih bayar" id="amount_gap_foreign_edit_vendor" required value="" value="0" class="form-control commas-form text-end" readonly />
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-self-end">
                            <div class="form-group">
                                <x-input-checkbox label="lunas" name="clearing" id="clearing_vendor" onclick="clearingVendor()" />
                                @php
                                    $clearing_coa = get_default_coa('finance', 'Selisih Bayar');
                                @endphp
                                <input type="hidden" id="default_clearing_coa_id_vendor" value="{{ $clearing_coa->coa->id ?? '' }}">
                                <input type="hidden" id="default_clearing_coa_name_vendor" value="{{ $clearing_coa->coa->account_code ?? '' }} - {{ $clearing_coa->coa->name ?? '' }}">
                            </div>
                        </div>
                        <div id="clearing_coa_form_vendor" class="col-md-12 d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-select name="clearing_coa_id_vendor" id="clearing_coa_id_vendor" label="coa">
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="keterangan selish bayar" id="clearing_note_edit_vendor" required value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="selisih kurs" id="exchange_rate_gap_edit_vendor" class="commas-form text-end" required readonly value="0" />
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="exchange_rate_gap_form_vendor">
                            <div class="form-group">
                                <x-input type="text" label="keterangan selish kurs" id="exchange_rate_gap_note_edit_vendor" required value="" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-input type="text" label="keterangan pembayaran" id="note_edit_vendor" required value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="edited_supplier_invoice_parent_id_vendor">
                    <x-button color="info" label="simpan" type="button" onclick="" id="btn-update-selected-supplier-invoice" />
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/receivables-payment/transaction.js') }}?v=9.12."></script>
    <script src="{{ asset('js/admin/receivables-payment/adjustment.js') }}"></script>
    <script src="{{ asset('js/admin/receivables-payment/lpb.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/receivables-payment/invoice-return.js') }}"></script>
    <script>
        checkClosingPeriod($('#date'));
        var key = '{{ count($model->receivables_payment_others) }}';
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#receivables-payment')

        initCoaSelect(`select[name="coa_detail_id[]"]`);

        initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        });

        initSelect2Search(`invoice_currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        });

        initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: "Cash & Bank",
            currency_id: function() {
                return $('#currency_id').val();
            }
        });

        initSelect2Search(`receive_payment_id`, `{{ route('admin.select.receive-payment') }}`, {
            id: "id",
            text: "from_bank,cheque_no"
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
            status: 'approve'
        });

        initSelect2SearchPagination(`clearing_coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, [],
            '#invoiceEditModal'
        );

        initSelect2SearchPagination(`clearing_coa_id_vendor`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, [],
            '#supplierInvoiceEditModal'
        );

        function checkCurrency() {
            $('#selected_invoice_table').html('');
            calculateData();

            var selected_currency_id = $('#currency_id').val();
            var selected_invoice_currency_id = $('#invoice_currency_id').val();
            var is_selected_currency_id_local = false;
            var is_selected_invoice_currency_id_local = false;

            $.ajax({
                type: "get",
                url: `{{ route('admin.currency.detail') }}/${selected_currency_id}`,
                success: function({
                    data
                }) {
                    if (data.is_local) {
                        is_selected_currency_id_local = true;
                    } else {
                        is_selected_currency_id_local = false;
                    }
                    $('#currency_id_symbol').val(data.kode);
                    $('.currency_id_symbol').text(data.kode);
                },
                complete: function(data) {
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.currency.detail') }}/${selected_invoice_currency_id}`,
                        success: function({
                            data
                        }) {
                            if (data.is_local) {
                                is_selected_invoice_currency_id_local = true;
                            } else {
                                is_selected_invoice_currency_id_local = false;
                            }

                            $('.invoice_currency_id_symbol').text(data.kode);
                        },
                        complete: function(data) {
                            if (is_selected_currency_id_local && is_selected_invoice_currency_id_local) {
                                $('#exchange_rate').val(1);
                                $('#exchange_rate').attr('readonly', 'readonly');
                                $('#exchange_rate').data('is-both-local', 'true');
                            } else {
                                $('#exchange_rate').removeAttr('readonly');
                                $('#exchange_rate').attr('readonly', false);
                                $('#exchange_rate').data('is-both-local', 'false');
                            }
                        }
                    });
                }
            });
        }

        initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
            id: "id",
            text: "code,name"
        }, 2, {
            branch_id: function() {
                return $('#branch_id').val();
            }
        });

        initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        });

        function initInvoiceCurrency() {
            let get_local_currency_id = $('#local_currency_id').val();
            let get_selected_currency_id = $('#currency_id').val();

            let allow_foreign = true;
            $('.column-multi-currency').addClass('d-none');
            if (get_local_currency_id != get_selected_currency_id) {
                allow_foreign = false;
                $('.column-multi-currency').removeClass('d-none');
            }

            initSelect2SearchCurrencyWithCondition(`invoice_currency_id`, `{{ route('admin.select.currency-with-condition') }}`, {
                id: "id",
                text: "kode,negara"
            }, allow_foreign, get_selected_currency_id);
        }

        function initCurrency() {
            let get_local_currency_id = $('#local_currency_id').val();
            let get_selected_currency_id = $('#invoice_currency_id').val();

            let allow_foreign = true;
            $('.column-multi-currency').addClass('d-none');
            if (get_local_currency_id != get_selected_currency_id) {
                allow_foreign = false;
                $('.column-multi-currency').removeClass('d-none');
            }

            initSelect2SearchCurrencyWithCondition(`currency_id`, `{{ route('admin.select.currency-with-condition') }}`, {
                id: "id",
                text: "kode,nama"
            }, allow_foreign, get_selected_currency_id);
        }

        calculateData();
        calculateDataVendor();
        countTotal();
        calculate_return_total();
        calculate_final_total();

        if (
            currency_id.val() == local_currency_id.val() &&
            currency_id.val() == invoice_currency_id.val()
        ) {
            $(".column-multi-currency").addClass("d-none");
        } else {
            $(".column-multi-currency").removeClass("d-none");
        }

        $('#sequence_code').on('blur', function() {
            check_bank_code(
                '#coa_id',
                '#sequence_code',
                '#date',
                'in'
            );
        });

        $('#currency_id').change(function() {
            $('#coa_id').val(null).trigger('change');
        });
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
