@extends('layouts.admin.layout.index')

@php
    $main = 'fund-submission';
    $menu = 'pengajuan dana LPB';
@endphp

@section('title', Str::headline("tambah $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($menu) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route("admin.$main-lpb.store") }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="fund_submission_id" value="">
        <x-card-data-table title="{{ 'tambah ' . $menu }}">
            <x-slot name="table_content">
                <div id="errorRl" class="alert alert-danger" role="alert" style="display: none">
                    <span id="errorRlMessage"></span>
                </div>
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
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ date('d-m-Y') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="jenis pengajuan" name="item" id="item" required value="LPB" readonly />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <input type="hidden" name="to_model" value="App\Models\Vendor">
                                <x-select name="to_id" id="to_id" label="vendor/supplier" required onchange="getVendorBank($(this));get_vendor_customer($(this))">

                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="referensi" id="referensi" />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-12">
                                <div class="bg-light rounded-3 p-3 d-none mb-10" id="bank-info-card">
                                    <h4 class="mb-2"><i data-feather="info"></i> Bank Vendor </h4>
                                    <table class="table w-100" id="bank-info-detail">

                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="currency_id" id="currency_id" label="currency" required onchange="checkCurrency();initSupplierInvoiceCurrency()" readonly>
                                </x-select>
                                <input type="hidden" id="currency_id_symbol" value="{{ get_local_currency()->kode }}">
                                <input type="hidden" id="local_currency_id" value="{{ get_local_currency()->id }}">
                            </div>
                            <div class="col-md-6">
                                <x-select name="supplier_invoice_currency_id" id="supplier_invoice_currency_id" label="currency purchase invoice" required onchange="checkCurrency();initCurrency()">
                                </x-select>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly data-is-both-local="true" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="coa_id" id="coa_id" label="kas/bank" required onchange="get_coa_detail($(this))">

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <x-input type="text" name="parent_note" label="keterangan" id="note" />
                            </div>
                            <div class="col-md-12 giro-checkbox d-none">
                                <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                            </div>
                            <div class="col-md-12 d-none" id="giro-form">
                                <div class="row">
                                    @include('admin.fund-submission.__giro_form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        @include('admin.fund-submission.lpb.list_hutang_create')
        <div id="customer-section" class="d-none">
            <x-card-data-table title="list piutang">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <input type="hidden" name="customer_id" id="customer_id">
                            <x-button color="success" label="pilih piutang +" type="button" class="btn-sm mb-2" onclick="getInvoiceSelect()" />
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
                                                            <th class="text-end">Sisa</th>
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
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="bg-info">
                                    <tr>
                                        <th>{{ Str::headline('tanggal') }}</th>
                                        <th>{{ Str::headline('invoice') }}</th>
                                        <th>{{ Str::headline('cur.') }}</th>
                                        <th class="text-end">{{ Str::headline('rate') }}</th>
                                        <th class="text-end">{{ Str::headline('sisa') }} <span class="invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                        <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                        <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                        <th class="text-end">{{ Str::headline('selisih bayar') }}</th>
                                        <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                        <th>{{ Str::headline('ket') }}</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody id="selected_invoice_table">
                                    <tr class="empty_invoice_row">
                                        <td colspan="11" class="text-center">Belum ada invoice</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4">TOTAL</th>
                                        <th class="text-end" id="outstanding_amount_total_customer">0</th>
                                        <th class="text-end" id="receive_amount_total_customer">0</th>
                                        <th class="text-end d-none column-multi-currency" id="receive_amount_foreign_total_customer">0</th>
                                        <td></td>
                                        <th class="text-end" id="exchange_rate_gap_total_customer">0</th>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
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
                                <tbody id="fund-submission-supplier-other-data">
                                    <input type="hidden" id="count_rows" value="0">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end" id="debit_total">0</th>
                                        <input type="hidden" id="debit_total_hide">
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="3">
                                            <x-button color="success" label="Tambah Baris +" type="button" onclick="addFundSubmissionSupplierOtherRow()" class="btn-sm" />
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('total') }}</h5>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody id="">
                                    <tr>
                                        <td class="text-end">{{ Str::headline('total purchase invoice') }}</td>
                                        <td class="text-end" id="total-data-supplier-invoice"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">{{ Str::headline('total retur') }}</td>
                                        <td class="text-end" id="total-data-return"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">{{ Str::headline('total invoice') }}</td>
                                        <td class="text-end" id="total-data-invoice"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">{{ Str::headline('total lain lain') }}</td>
                                        <td class="text-end" id="total-data-adjustment"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">{{ Str::headline('total') }}</td>
                                        <td class="text-end" id="total-data-total"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('Payment Information') }}</h5>
            </div>
            <div class="box-body">
                <div class="accordion" id="accordionPaymentInformation"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-end">
                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                <x-button type="submit" color="primary" label="Save data" />
            </div>
        </div>
    </form>

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
                                <x-input type="text" label="tanggal" id="date_edit" class="datepicker-input" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="no. purchase invoice" id="code_edit" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="no. faktur pajak" id="reference_edit" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="akun hutang" id="coa_edit" required value="" readonly />
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="total" id="total_amount_edit" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="terbayar" id="paid_amount_edit" class="text-end" required value="" readonly />
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
                                            <th>Sisa</th>
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
                                <x-input type="text" label="sisa bayar" id="outstanding_amount_edit" class="text-end" required value="0" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_edit" class="form-label"></label>
                                <input type="text" label="jumlah bayar" id="amount_edit" required value="" class="form-control commas-form text-end" onkeyup="calculateGapEdit('local')" />
                            </div>
                        </div>
                        <div class="col-md-4" id="multi-currency-form">
                            <div class="form-group">
                                <label for="amount_foreign_edit" class="form-label"></label>
                                <input type="text" label="jumlah bayar" id="amount_foreign_edit" required value="" value="0" class="form-control commas-form text-end" onkeyup="calculateGapEdit('foreign')" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount_gap_foreign_edit" class="form-label">Selisih Bayar </label>
                                <input type="text" label="selisih bayar" id="amount_gap_foreign_edit" required value="" value="0" class="form-control commas-form text-end" readonly />
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-self-end">
                            <div class="form-group">
                                <x-input-checkbox label="lunas" name="clearing" id="clearing" onclick="clearing()" />
                                @php
                                    $clearing_coa = get_default_coa('finance', 'Selisih Bayar');
                                @endphp
                                <input type="hidden" id="default_clearing_coa_id" value="{{ $clearing_coa->coa->id ?? '' }}">
                                <input type="hidden" id="default_clearing_coa_name" value="{{ $clearing_coa->coa->account_code ?? '' }} - {{ $clearing_coa->coa->name ?? '' }}">
                            </div>
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
                    <input type="hidden" id="edited_supplier_invoice_parent_id">
                    <x-button color="info" label="simpan" type="button" onclick="" id="btn-update-selected-supplier-invoice" />
                </div>
            </div>
        </div>
    </div>

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
                                <x-input type="text" label="tanggal" id="date_edit_customer" class="datepicker-input" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="no. invoice" id="kode_edit_customer" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="akun piutang" id="coa_edit_customer" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="currency" id="currency_edit_customer" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" id="exchange_rate_edit_customer" class="text-end" required value="" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="total" id="total_amount_edit_customer" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="terbayar" id="paid_amount_edit_customer" class="text-end" required value="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="sisa bayar" id="outstanding_amount_edit_customer" class="text-end" required value="0" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receive_amount_edit" class="form-label">Jumlah Bayar</label>
                                <input type="text" label="jumlah bayar" id="receive_amount_edit_customer" required value="" value="" class="form-control commas-form text-end" onkeyup="calculateGapEditCustomer('local')" />
                            </div>
                        </div>
                        <div class="col-md-4" id="multi-currency-form-customer">
                            <div class="form-group">
                                <label for="receive_amount_foreign_edit" class="form-label">Jumlah Bayar </label>
                                <input type="text" label="jumlah bayar" id="receive_amount_foreign_edit_customer" required value="" value="" class="form-control commas-form text-end" onkeyup="calculateGapEditCustomer('foreign')" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="receive_amount_gap_foreign_edit" class="form-label">Selisih Bayar </label>
                                <input type="text" label="selisih bayar" id="receive_amount_gap_foreign_edit_customer" required value="" value="0" class="form-control commas-form text-end" readonly />
                            </div>
                        </div>
                        <div class="col-md-2 align-self-center">
                            <x-input-checkbox label="lunas" name="clearing" id="clearing_customer" onclick="clearingCustomer()" />
                            @php
                                $clearing_coa = get_default_coa('finance', 'Selisih Bayar');
                            @endphp
                            <input type="hidden" id="default_clearing_coa_id_customer" value="{{ $clearing_coa->coa->id ?? '' }}">
                            <input type="hidden" id="default_clearing_coa_name_customer" value="{{ $clearing_coa->coa->account_code ?? '' }} - {{ $clearing_coa->coa->name ?? '' }}">
                        </div>
                        <div id="clearing_coa_form_customer" class="col-md-12 d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-select name="clearing_coa_id" id="clearing_coa_id_customer" label="coa">
                                    </x-select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input type="text" label="keterangan selish bayar" id="clearing_note_edit_customer" required value="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" label="selisih kurs" id="exchange_rate_gap_edit_customer" class="commas-form text-end" required readonly value="0" />
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="exchange_rate_gap_form_customer">
                            <div class="form-group">
                                <x-input type="text" label="keterangan selish kurs" id="exchange_rate_gap_note_edit_customer" required value="" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-input type="text" label="keterangan pembayaran" id="note_edit_customer" required value="" />
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
@endsection
@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/fund-submission/lpb.js') }}?v=100"></script>
    <script src="{{ asset('js/admin/fund-submission/lpb-adjustment.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/fund-submission/invoice.js') }}?v=100"></script>
    <script src="{{ asset('js/admin/fund-submission/lpb-return.js') }}?v=100"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#fund-submission');
        $(document).ready(function() {
            initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
                id: "id",
                text: "kode,nama,negara"
            });

            initSelect2Search(`supplier_invoice_currency_id`, `{{ route('admin.select.currency') }}`, {
                id: "id",
                text: "kode,nama,negara"
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

            initSelect2SearchPagination(`clearing_coa_id`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                }, 0, [],
                '#supplierInvoiceEditModal'
            );

            initSelect2SearchPagination(`clearing_coa_id_customer`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                }, 0, [],
                '#invoiceEditModal'
            );

            $('#form-data').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "post",
                    url: `${base_url}/rate-limiter/ajax`,
                    data: {
                        _token: token,
                        key: "create: " + "{{ $main }}, " + $('#item').val(),
                        attempts: 2, // default is 2 attempts
                        decay_seconds: 3,
                    },
                    success: function(response) {
                        if (response.is_too_many_requests == true) {
                            let waitingTime = parseInt(response.available_at_time);

                            $('#errorRl').show();
                            $('#errorRlMessage').text('Terlalu banyak permintaan menyimpan data, harap tunggu ' + waitingTime + " detik lagi");

                            let showError = setInterval(() => {
                                waitingTime--;

                                if (waitingTime > 0 && waitingTime <= 60) {
                                    $('#errorRlMessage').text('Terlalu banyak permintaan menyimpan data, harap tunggu ' + waitingTime + " detik lagi");
                                }

                                if (waitingTime == 0) {
                                    $('#errorRl').hide();
                                    $('#btn-update-selected-supplier-invoice').prop('disabled', false);
                                    clearInterval(showError);
                                }
                            }, 1000);
                        } else {
                            $('#form-data').unbind('submit').submit();
                        }
                    }
                });
            });
        })

        function checkCurrency() {
            $('#selected_supplier_invoice_table').html('');
            $('#selected_invoice_table').html('');
            $('#selected_return_table').html('');

            calculateData();
            calculate_return_total();
            calculateDataCustomer();

            var selected_currency_id = $('#currency_id').val();
            var selected_invoice_currency_id = $('#supplier_invoice_currency_id').val();
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

                            $('.supplier_invoice_currency_id_symbol').text(data.kode);
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

        initSelect2Search(`to_id`, `{{ route('admin.select.vendor') }}`, {
            id: "id",
            text: "nama"
        }, 0, {
            has_unpaid_supplier_invoice: 1,
        });

        function initSupplierInvoiceCurrency() {
            let get_local_currency_id = $('#local_currency_id').val();
            let get_selected_currency_id = $('#currency_id').val();

            let allow_foreign = true;
            $('.column-multi-currency').addClass('d-none');
            if (get_local_currency_id != get_selected_currency_id) {
                allow_foreign = false;
                $('.column-multi-currency').removeClass('d-none');
            }

            initSelect2SearchCurrencyWithCondition(`supplier_invoice_currency_id`, `{{ route('admin.select.currency-with-condition') }}`, {
                id: "id",
                text: "kode,nama,negara"
            }, allow_foreign, get_selected_currency_id);
        }

        function initCurrency() {
            let get_local_currency_id = $('#local_currency_id').val();
            let get_selected_currency_id = $('#supplier_invoice_currency_id').val();

            let allow_foreign = true;
            $('.column-multi-currency').addClass('d-none');
            if (get_local_currency_id != get_selected_currency_id) {
                allow_foreign = false;
                $('.column-multi-currency').removeClass('d-none');
            }

            initSelect2SearchCurrencyWithCondition(`currency_id`, `{{ route('admin.select.currency-with-condition') }}`, {
                id: "id",
                text: "kode,nama,negara"
            }, allow_foreign, get_selected_currency_id);
        }
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
