@extends('layouts.admin.layout.index')

@php
    $main = 'accounting-report';
@endphp

@section('title', Str::headline($main) . ' - ')

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
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $main }}">
        <x-slot name="header_content">
        </x-slot>
        <x-slot name="table_content">
            <table class="table table-bordered">
                <tbody>
                    {{-- NERACA --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#neraca-modal">{{ Str::headline('neraca') }}</a>
                            <div class="modal fade" id="neraca-modal" aria-hidden="true" aria-labelledby="neraca-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'neraca']) }}" method="get" id="neraca-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="neraca-modalLabel">{{ Str::headline('neraca') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="folder">Format</label>
                                                            <br>
                                                            <select name="folder" id="folder" class="form-select" autofocus>
                                                                <option value="landscape">Ke Kanan</option>
                                                                <option value="potrait">Ke Bawah</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#neraca-form').find(`input[name='format']`).val('preview');$('#neraca-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#neraca-form').find(`input[name='format']`).val('pdf');$('#neraca-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#neraca-form').find(`input[name='format']`).val('excel');$('#neraca-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`neraca_branch_id`, `{{ route('admin.select.branch') }}`, {
                                    id: "id",
                                    text: "name"
                                }, 0, {}, '#neraca-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END NERACA --}}

                    {{-- NERACA SALDO --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#neraca-saldo-modal">{{ Str::headline('neraca saldo') }}</a>
                            <div class="modal fade" id="neraca-saldo-modal" aria-hidden="true" aria-labelledby="neraca-saldo-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'neraca-saldo']) }}" method="get" id="neraca-saldo-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="neraca-modalLabel">{{ Str::headline('neraca saldo') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#neraca-saldo-form').find(`input[name='format']`).val('preview');$('#neraca-saldo-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#neraca-saldo-form').find(`input[name='format']`).val('pdf');$('#neraca-saldo-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#neraca-saldo-form').find(`input[name='format']`).val('excel');$('#neraca-saldo-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END NERACA SALDO --}}

                    {{-- LABA RUGI multi period --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#neraca-multiperiod-modal">{{ Str::headline('neraca multi period') }}</a>
                            <div class="modal fade" id="neraca-multiperiod-modal" aria-hidden="true" aria-labelledby="neraca-multiperiod-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'neraca-multiperiod']) }}" method="get" id="neraca-multiperiod-form" target="_blank">
                                        in
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="neraca-multiperiod-modalLabel">{{ Str::headline('neraca') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="period" class="year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#neraca-multiperiod-form').find(`input[name='format']`).val('preview');$('#neraca-multiperiod-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#neraca-multiperiod-form').find(`input[name='format']`).val('pdf');$('#neraca-multiperiod-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#neraca-multiperiod-form').find(`input[name='format']`).val('excel');$('#neraca-multiperiod-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`profit_loss_multiperiod_branch_id`, `{{ route('admin.select.branch') }}`, {
                                    id: "id",
                                    text: "name"
                                }, 0, {}, '#profit-loss-multiperiod-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END LABA RUGI --}}

                    {{-- LABA RUGI --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laba-rugi-modal">{{ Str::headline('laba rugi') }}</a>
                            <div class="modal fade" id="laba-rugi-modal" aria-hidden="true" aria-labelledby="laba-rugi-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'laba-rugi']) }}" method="get" id="laba-rugi-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="laba-rugi-modalLabel">{{ Str::headline('laba rugi') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="laba_rugi_branch_id">Branch</label>
                                                        <br>
                                                        <select name="branch_id" id="laba_rugi_branch_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laba-rugi-form').find(`input[name='format']`).val('preview');$('#laba-rugi-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laba-rugi-form').find(`input[name='format']`).val('pdf');$('#laba-rugi-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laba-rugi-form').find(`input[name='format']`).val('excel');$('#laba-rugi-form').submit()" />
                                                    <x-button target="_blank" link="{{ route('admin.profit-loss-setting.index') }}" color="dark" label="setting" class="btn-sm" type="button" fontawesome icon="gear" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`laba_rugi_branch_id`, `{{ route('admin.select.branch') }}`, {
                                    id: "id",
                                    text: "name"
                                }, 0, {}, '#laba-rugi-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END LABA RUGI --}}

                    {{-- TRANSAKSI JURNAL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#transaksi-jurnal-modal">{{ Str::headline('transaksi-jurnal') }}</a>
                            <div class="modal fade" id="transaksi-jurnal-modal" aria-hidden="true" aria-labelledby="transaksi-jurnal-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'transaksi-jurnal']) }}" method="get" id="transaksi-jurnal-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="transaksi-jurnal-modalLabel">{{ Str::headline('transaksi-jurnal') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#transaksi-jurnal-form').find(`input[name='format']`).val('preview');$('#transaksi-jurnal-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#transaksi-jurnal-form').find(`input[name='format']`).val('pdf');$('#transaksi-jurnal-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#transaksi-jurnal-form').find(`input[name='format']`).val('excel');$('#transaksi-jurnal-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END TRANSAKSI JURNAL --}}

                    {{-- HISTORI DOKUMEN PURCHASE INVOICE --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#document-history-purchase-invoice-modal">{{ Str::headline('histori dokumen purchase invoice') }}</a>
                            <div class="modal fade" id="document-history-purchase-invoice-modal" aria-hidden="true" aria-labelledby="document-history-purchase-invoice-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'document-history-purchase-invoice']) }}" method="get" id="document-history-purchase-invoice-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="document-history-purchase-invoice-modalLabel">{{ Str::headline('histori dokumen purchase invoice') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#document-history-purchase-invoice-form').find(`input[name='format']`).val('preview');$('#document-history-purchase-invoice-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#document-history-purchase-invoice-form').find(`input[name='format']`).val('pdf');$('#document-history-purchase-invoice-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#document-history-purchase-invoice-form').find(`input[name='format']`).val('excel');$('#document-history-purchase-invoice-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END HISTORI DOKUMEN PURCHASE INVOICE --}}

                    {{-- PURCHASE JURNAL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-journal-modal">{{ Str::headline('purchase-journal') }}</a>
                            <div class="modal fade" id="purchase-journal-modal" aria-hidden="true" aria-labelledby="purchase-journal-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'purchasing-journal-report']) }}" method="get" id="purchase-journal-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="purchase-journal-modalLabel">{{ Str::headline('purchase-journal') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#purchase-journal-form').find(`input[name='format']`).val('preview');$('#purchase-journal-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#purchase-journal-form').find(`input[name='format']`).val('pdf');$('#purchase-journal-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#purchase-journal-form').find(`input[name='format']`).val('excel');$('#purchase-journal-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END PURCHASE JURNAL --}}

                    {{-- SALE JURNAL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-journal-report-modal">{{ Str::headline('sale-journal-report') }}</a>
                            <div class="modal fade" id="sale-journal-report-modal" aria-hidden="true" aria-labelledby="sale-journal-report-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sale-journal-report']) }}" method="get" id="sale-journal-report-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sale-journal-report-modalLabel">{{ Str::headline('sale-journal-report') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sale-journal-report-form').find(`input[name='format']`).val('preview');$('#sale-journal-report-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sale-journal-report-form').find(`input[name='format']`).val('pdf');$('#sale-journal-report-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sale-journal-report-form').find(`input[name='format']`).val('excel');$('#sale-journal-report-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END SALE JURNAL --}}

                    {{-- SUMMARY BUKU BESAR --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#buku-besar-modal">{{ Str::headline('buku-besar') }}</a>
                            <div class="modal fade" id="buku-besar-modal" aria-hidden="true" aria-labelledby="buku-besar-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'buku-besar']) }}" method="get" id="buku-besar-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="buku-besar-modalLabel">{{ Str::headline('buku-besar') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="buku_besar_coa_id_start">coa Start</label>
                                                        <br>
                                                        <select name="coa_id_start" id="buku_besar_coa_id_start" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label" for="buku_besar_coa_id_end">coa End</label>
                                                        <br>
                                                        <select name="coa_id_end" id="buku_besar_coa_id_end" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#buku-besar-form').find(`input[name='format']`).val('preview');$('#buku-besar-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#buku-besar-form').find(`input[name='format']`).val('pdf');$('#buku-besar-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#buku-besar-form').find(`input[name='format']`).val('excel');$('#buku-besar-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const coaStartId = 'buku_besar_coa_id_start';
                                    const coaEndId = 'buku_besar_coa_id_end';

                                    initSelect2SearchPagination(coaStartId, `{{ route('admin.select.coa') }}`, {
                                        id: "id",
                                        text: "account_code,name"
                                    }, 0, {}, '#buku-besar-modal');

                                    initSelect2SearchPagination(coaEndId, `{{ route('admin.select.coa') }}`, {
                                        id: "id",
                                        text: "account_code,name"
                                    }, 0, {}, '#buku-besar-modal');

                                    // On start selected
                                    $('#' + coaStartId).on('select2:select', function (e) {
                                        const selectedId = e.params.data.id;

                                        // Clear and destroy old select2 instance
                                        $('#' + coaEndId).val(null).trigger('change');
                                        $('#' + coaEndId).select2('destroy');
                                        $('#' + coaEndId).empty();

                                        // Re-init with filter
                                        initSelect2SearchPagination(coaEndId, `{{ route('admin.select.coa') }}`, {
                                            id: "id",
                                            text: "account_code,name"
                                        }, 0, { selected_start_id: selectedId }, '#buku-besar-modal');
                                    });
                                });
                            </script>
                        @endpush
                    </tr>
                    {{-- END SUMMARY BUKU BESAR --}}

                    {{-- Daftar Aktifa Tetap --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#daftar-aktifa-tetap">{{ Str::headline('daftar-aktifa-tetap') }}</a>
                            <div class="modal fade" id="daftar-aktifa-tetap" aria-hidden="true" aria-labelledby="daftar-aktifa-tetapLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'daftar-aktifa-tetap']) }}" method="get" id="daftar-aktifa-tetap-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="daftar-aktifa-tetapLabel">{{ Str::headline('daftar-aktifa-tetap') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="date" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required />
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="date" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="coa_id" label="Coa" id="daftar-aktifa-tetap-coa-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#daftar-aktifa-tetap-form').find(`input[name='format']`).val('preview');$('#daftar-aktifa-tetap-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#daftar-aktifa-tetap-form').find(`input[name='format']`).val('pdf');$('#daftar-aktifa-tetap-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#daftar-aktifa-tetap-form').find(`input[name='format']`).val('excel');$('#daftar-aktifa-tetap-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2SearchPagination(`daftar-aktifa-tetap-coa-select`, `{{ route('admin.select.coa') }}`, {
                                    id: "id",
                                    text: "account_code,name"
                                }, 0, {}, '#daftar-aktifa-tetap');
                            </script>
                        @endpush
                    </tr>
                    {{-- End Daftar Aktifa Tetap --}}

                    {{-- Biaya dibayar dimuka --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#biaya-dibayar-dimuka">{{ Str::headline('biaya-dibayar-dimuka') }}</a>
                            <div class="modal fade" id="biaya-dibayar-dimuka" aria-hidden="true" aria-labelledby="biaya-dibayar-dimukaLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'biaya-dibayar-dimuka']) }}" method="get" id="biaya-dibayar-dimuka-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="biaya-dibayar-dimukaLabel">{{ Str::headline('biaya-dibayar-dimuka') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="date" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required />
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="date" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="coa_id" label="Coa" id="coa-bdm-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#biaya-dibayar-dimuka-form').find(`input[name='format']`).val('preview');$('#biaya-dibayar-dimuka-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#biaya-dibayar-dimuka-form').find(`input[name='format']`).val('pdf');$('#biaya-dibayar-dimuka-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#biaya-dibayar-dimuka-form').find(`input[name='format']`).val('excel');$('#biaya-dibayar-dimuka-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2SearchPagination(`coa-bdm-select`, `{{ route('admin.select.coa') }}`, {
                                    id: "id",
                                    text: "account_code,name"
                                }, 0, {}, '#biaya-dibayar-dimuka');
                            </script>
                        @endpush
                    </tr>
                    {{-- End biaya dibayar dimuka --}}

                    {{-- LABA RUGI multi period --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#profit-loss-multiperiod-modal">{{ Str::headline('laba rugi multi period') }}</a>
                            <div class="modal fade" id="profit-loss-multiperiod-modal" aria-hidden="true" aria-labelledby="profit-loss-multiperiod-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'profit-loss-multiperiod']) }}" method="get" id="profit-loss-multiperiod-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="profit-loss-multiperiod-modalLabel">{{ Str::headline('laba rugi') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="profit_loss_multiperiod_branch_id">Branch</label>
                                                        <br>
                                                        <select name="branch_id" id="profit_loss_multiperiod_branch_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="year" class="year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#profit-loss-multiperiod-form').find(`input[name='format']`).val('preview');$('#profit-loss-multiperiod-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#profit-loss-multiperiod-form').find(`input[name='format']`).val('pdf');$('#profit-loss-multiperiod-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#profit-loss-multiperiod-form').find(`input[name='format']`).val('excel');$('#profit-loss-multiperiod-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`profit_loss_multiperiod_branch_id`, `{{ route('admin.select.branch') }}`, {
                                    id: "id",
                                    text: "name"
                                }, 0, {}, '#profit-loss-multiperiod-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END LABA RUGI --}}

                    {{-- Laporan Perbandingan Penjualan Trading Dengan HPP --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sales-report-by-trading-period-modal">{{ Str::headline('Laporan Perbandingan Penjualan Trading Dengan HPP') }}</a>
                            <div class="modal fade" id="sales-report-by-trading-period-modal" aria-hidden="true" aria-labelledby="sales-report-by-trading-period-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.sales-report-by-trading-period.report', ['type' => 'sales-report-by-trading-period']) }}" method="get" id="sales-report-by-trading-period-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sales-report-by-trading-period-modalLabel">{{ Str::headline('Laporan Perbandingan Penjualan Trading Dengan HPP') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sales-report-by-trading-period-form').find(`input[name='format']`).val('preview');$('#sales-report-by-trading-period-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sales-report-by-trading-period-form').find(`input[name='format']`).val('pdf');$('#sales-report-by-trading-period-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sales-report-by-trading-period-form').find(`input[name='format']`).val('excel');$('#sales-report-by-trading-period-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        {{-- @push('script')
                            <script>
                                initSelect2Search(`profit_loss_multiperiod_branch_id`, `{{ route('admin.select.branch') }}`, {
                                    id: "id",
                                    text: "name"
                                }, 0, {}, '#profit-loss-multiperiod-modal');
                            </script>
                        @endpush --}}
                    </tr>
                    {{-- END Laporan Perbandingan Penjualan Trading Dengan HPP --}}
                </tbody>
            </table>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar')
        sidebarActive('#accounting-report')
    </script>
@endsection
