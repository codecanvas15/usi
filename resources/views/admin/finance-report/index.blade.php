@extends('layouts.admin.layout.index')

@php
    $main = 'finance-report';
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
                    {{-- HARIAN KAS BANK --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#harian-kas-bank-modal">{{ Str::headline('laporan harian kas bank') }}</a>
                            <div class="modal fade" id="harian-kas-bank-modal" aria-hidden="true" aria-labelledby="harian-kas-bank-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'harian-kas-bank']) }}" method="get" id="harian-kas-bank-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="harian-kas-bank-modalLabel">
                                                    {{ Str::headline('laporan harian kas bank') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="harian_kas_bank_coa_id">Kas/Bank</label>
                                                            <br>
                                                            <select name="coa_id" id="harian_kas_bank_coa_id" class="form-select" label="kas/bank" autofocus></select>
                                                        </div>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#harian-kas-bank-form').find(`input[name='format']`).val('preview');$('#harian-kas-bank-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#harian-kas-bank-form').find(`input[name='format']`).val('pdf');$('#harian-kas-bank-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#harian-kas-bank-form').find(`input[name='format']`).val('excel');$('#harian-kas-bank-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>

                        @push('script')
                            <script>
                                initSelect2SearchPagination(`harian_kas_bank_coa_id`, `{{ route('admin.select.coa') }}`, {
                                    id: "id",
                                    text: "account_code,name"
                                }, 0, {
                                    account_type: "Cash & Bank"
                                }, '#harian-kas-bank-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END HARIAN KAS BANK --}}

                    {{-- HARIAN KAS BANK DETAIL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#harian-kas-bank-detail-modal">{{ Str::headline('laporan harian kas bank detail') }}</a>
                            <div class="modal fade" id="harian-kas-bank-detail-modal" aria-hidden="true" aria-labelledby="harian-kas-bank-detail-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'harian-kas-bank-detail']) }}" method="get" id="harian-kas-bank-detail-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="harian-kas-bank-detail-modalLabel">
                                                    {{ Str::headline('laporan harian kas bank detail') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="harian_kas_bank_detail_coa_id">Kas/Bank</label>
                                                            <br>
                                                            <select name="coa_id" id="harian_kas_bank_detail_coa_id" class="form-select" label="kas/bank" autofocus></select>
                                                        </div>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#harian-kas-bank-detail-form').find(`input[name='format']`).val('preview');$('#harian-kas-bank-detail-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#harian-kas-bank-detail-form').find(`input[name='format']`).val('pdf');$('#harian-kas-bank-detail-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#harian-kas-bank-detail-form').find(`input[name='format']`).val('excel');$('#harian-kas-bank-detail-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>

                        @push('script')
                            <script>
                                initSelect2SearchPagination(`harian_kas_bank_detail_coa_id`, `{{ route('admin.select.coa') }}`, {
                                    id: "id",
                                    text: "account_code,name"
                                }, 0, {
                                    account_type: "Cash & Bank"
                                }, '#harian-kas-bank-detail-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END HARIAN KAS BANK DETAIL --}}

                    {{-- KASBON --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#cash-bond-modal">{{ Str::headline('kasbon') }}</a>
                            <div class="modal fade" id="cash-bond-modal" aria-hidden="true" aria-labelledby="cash-bond-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'cash-bond']) }}" method="get" id="cash-bond-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="cash-bond-modalLabel">
                                                    {{ Str::headline('kasbon') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <x-select name="branch_id" useBr id="cash-bond-branch" label="cabang">
                                                            <option value="{{ get_current_branch()->id }}">
                                                                {{ get_current_branch()->name }}</option>
                                                        </x-select>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="employee_id" useBr id="cash-bond-employee" label="karyawan"></x-select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr id="cash-bond-status" label="status">
                                                                <option value="">------</option>
                                                                @foreach (cash_bond_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ Str::headline($item['label']) }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#cash-bond-form').find(`input[name='format']`).val('preview');$('#cash-bond-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#cash-bond-form').find(`input[name='format']`).val('pdf');$('#cash-bond-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#cash-bond-form').find(`input[name='format']`).val('excel');$('#cash-bond-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            @if (get_current_branch()->is_primary)
                                <script>
                                    initSelect2Search(`cash-bond-branch`, `{{ route('admin.select.branch') }}`, {
                                        id: "id",
                                        text: "name"
                                    }, 0, {}, '#cash-bond-modal');
                                </script>
                            @endif
                            <script>
                                initSelect2Search(`cash-bond-employee`, `{{ route('admin.select.employee') }}`, {
                                    id: "id",
                                    text: "name,NIK"
                                }, 0, {}, '#cash-bond-modal');

                                $('#cash-bond-status').select2({
                                    dropdownParent: $('#cash-bond-modal'),
                                    width: '100%',
                                });
                            </script>
                        @endpush
                    </tr>
                    {{-- END KASBON --}}

                    {{-- SUMMARY UANG MUKA PENJUALAN --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-uang-muka-penjualan-modal">{{ Str::headline('summary-uang-muka-penjualan') }}</a>
                            <div class="modal fade" id="summary-uang-muka-penjualan-modal" aria-hidden="true" aria-labelledby="summary-uang-muka-penjualan-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'summary-uang-muka-penjualan']) }}" method="get" id="summary-uang-muka-penjualan-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="summary-uang-muka-penjualan-modalLabel">
                                                    {{ Str::headline('summary-uang-muka-penjualan') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="summary_uang_muka_penjualan_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="summary_uang_muka_penjualan_customer_id" class="form-select" autofocus></select>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-uang-muka-penjualan-form').find(`input[name='format']`).val('preview');$('#summary-uang-muka-penjualan-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-uang-muka-penjualan-form').find(`input[name='format']`).val('pdf');$('#summary-uang-muka-penjualan-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-uang-muka-penjualan-form').find(`input[name='format']`).val('excel');$('#summary-uang-muka-penjualan-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`summary_uang_muka_penjualan_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#summary-uang-muka-penjualan-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SUMMARY UANG MUKA PEMBELIAN --}}

                    {{-- SUMMARY UANG MUKA PEMBELIAN --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-uang-muka-pembelian-modal">{{ Str::headline('summary-uang-muka-pembelian') }}</a>
                            <div class="modal fade" id="summary-uang-muka-pembelian-modal" aria-hidden="true" aria-labelledby="summary-uang-muka-pembelian-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'summary-uang-muka-pembelian']) }}" method="get" id="summary-uang-muka-pembelian-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="summary-uang-muka-pembelian-modalLabel">
                                                    {{ Str::headline('summary-uang-muka-pembelian') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="summary_uang_muka_pembelian_vendor_id">vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="summary_uang_muka_pembelian_vendor_id" class="form-select" autofocus></select>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-uang-muka-pembelian-form').find(`input[name='format']`).val('preview');$('#summary-uang-muka-pembelian-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-uang-muka-pembelian-form').find(`input[name='format']`).val('pdf');$('#summary-uang-muka-pembelian-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-uang-muka-pembelian-form').find(`input[name='format']`).val('excel');$('#summary-uang-muka-pembelian-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`summary_uang_muka_pembelian_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#summary-uang-muka-pembelian-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SUMMARY UANG MUKA PEMBELIAN --}}

                    {{-- KARTU PIUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debtCardTradingModal">{{ Str::headline('kartu piutang') }}</a>
                            <div class="modal fade" id="debtCardTradingModal" aria-hidden="true" aria-labelledby="debtCardTradingModal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'debt-card-trading']) }}" method="get" id="debtCardTrading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('kartu piutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-debtCardTrading" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="customer-type">Jenis Transaksi</label>
                                                            <br>
                                                            <select name="type" id="customer-type" class="form-select" name="customer-type">
                                                                <option value="">Semua</option>
                                                                <option value="{{ App\Models\InvoiceGeneral::class }}">
                                                                    General</option>
                                                                <option value="{{ App\Models\InvoiceTrading::class }}">
                                                                    Trading</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active1" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debtCardTrading-form').find(`input[name='format']`).val('preview');$('#debtCardTrading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debtCardTrading-form').find(`input[name='format']`).val('pdf');$('#debtCardTrading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debtCardTrading-form').find(`input[name='format']`).val('excel');$('#debtCardTrading-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                const initializeDebtCardReportTrading = () => {
                                    initSelect2Search(`customer-debtCardTrading`, `{{ route('admin.select.customer') }}`, {
                                        id: "id",
                                        text: "nama"
                                    }, 0, {}, '#debtCardTradingModal');
                                };

                                initializeDebtCardReportTrading();
                            </script>
                        @endpush
                    </tr>
                    {{-- END KARTU PIUTANG --}}

                    {{-- SISA PIUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-piutang-modal">{{ Str::headline('sisa-piutang') }}</a>
                            <div class="modal fade" id="sisa-piutang-modal" aria-hidden="true" aria-labelledby="sisa-piutang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-piutang']) }}" method="get" id="sisa-piutang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-piutang-modalLabel">
                                                    {{ Str::headline('sisa-piutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="sisa_piutang_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_currency_id">Mata
                                                            Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_piutang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">
                                                                {{ get_local_currency()->kode }} -
                                                                {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active3" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sisa-piutang-form').find(`input[name='format']`).val('preview');$('#sisa-piutang-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sisa-piutang-form').find(`input[name='format']`).val('pdf');$('#sisa-piutang-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sisa-piutang-form').find(`input[name='format']`).val('excel');$('#sisa-piutang-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`sisa_piutang_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#sisa-piutang-modal');

                                initSelect2Search(`sisa_piutang_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#sisa-piutang-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SISA PIUTANG --}}

                    {{-- SISA PIUTANG PER CUSTOMER --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-piutang-per-customer-modal">{{ Str::headline('sisa-piutang-per-customer') }}</a>
                            <div class="modal fade" id="sisa-piutang-per-customer-modal" aria-hidden="true" aria-labelledby="sisa-piutang-per-customer-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-piutang-per-customer']) }}" method="get" id="sisa-piutang-per-customer-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-piutang-per-customer-modalLabel">
                                                    {{ Str::headline('sisa-piutang-per-customer') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_per_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="sisa_piutang_per_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    {{-- <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_per_customer_currency_id">Mata Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_piutang_per_customer_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->kode }} - {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div> --}}
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active4" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sisa-piutang-per-customer-form').find(`input[name='format']`).val('preview');$('#sisa-piutang-per-customer-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sisa-piutang-per-customer-form').find(`input[name='format']`).val('pdf');$('#sisa-piutang-per-customer-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sisa-piutang-per-customer-form').find(`input[name='format']`).val('excel');$('#sisa-piutang-per-customer-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`sisa_piutang_per_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#sisa-piutang-per-customer-modal');

                                initSelect2Search(`sisa_piutang_per_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#sisa-piutang-per-customer-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SISA PIUTANG PER CUSTOMER --}}

                    {{-- SUMMARY PIUTANG DAGANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-piutang-dagang-modal">{{ Str::headline('summary-piutang-dagang') }}</a>
                            <div class="modal fade" id="summary-piutang-dagang-modal" aria-hidden="true" aria-labelledby="summary-piutang-dagang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'summary-piutang-dagang']) }}" method="get" id="summary-piutang-dagang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="summary-piutang-dagang-modalLabel">
                                                    {{ Str::headline('summary-piutang-dagang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="summary_piutang_dagang_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="summary_piutang_dagang_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    {{-- <div class="col-md-4">
                                                        <label class="form-label" for="summary_piutang_dagang_currency_id">Mata Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="summary_piutang_dagang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->kode }} - {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div> --}}
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="start_period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="end_period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active2" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-piutang-dagang-form').find(`input[name='format']`).val('preview');$('#summary-piutang-dagang-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-piutang-dagang-form').find(`input[name='format']`).val('pdf');$('#summary-piutang-dagang-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-piutang-dagang-form').find(`input[name='format']`).val('excel');$('#summary-piutang-dagang-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2SearchPaginationData(`summary_piutang_dagang_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#summary-piutang-dagang-modal');

                                initSelect2SearchPaginationData(`summary_piutang_dagang_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#summary-piutang-dagang-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SUMMARY PIUTANG DAGANG --}}

                    {{-- PELUNASAN PIUTANG DETAIL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#pelunasan-piutang-detail-modal">{{ Str::headline('laporan pelunasan piutang detail') }}</a>
                            <div class="modal fade" id="pelunasan-piutang-detail-modal" aria-hidden="true" aria-labelledby="pelunasan-piutang-detail-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'pelunasan-piutang-detail']) }}" method="get" id="pelunasan-piutang-detail-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="pelunasan-piutang-detail-modalLabel">
                                                    {{ Str::headline('laporan pelunasan piutang detail') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_status">Status
                                                                Transaksi</label>
                                                            <br>
                                                            <select name="payment_status" id="pelunasan_piutang_detail_status" class="form-select" label="status" autofocus>
                                                                <option value="">Semua</option>
                                                                <option value="unpaid">Belum Lunas</option>
                                                                <option value="partial-paid">Sebagian</option>
                                                                <option value="paid">Lunas</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_customer_id">Customer</label>
                                                            <br>
                                                            <select name="customer_id" id="pelunasan_piutang_detail_customer_id" class="form-select" label="customer" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_invoice_parent_id">No.
                                                                Dokumen</label>
                                                            <br>
                                                            <select name="invoice_parent_id" id="pelunasan_piutang_detail_invoice_parent_id" class="form-select" label="no. dokumen" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_coa_id">Kas/Bank</label>
                                                            <br>
                                                            <select name="coa_id" id="pelunasan_piutang_detail_coa_id" class="form-select" label="kas/bank" autofocus></select>
                                                        </div>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#pelunasan-piutang-detail-form').find(`input[name='format']`).val('preview');$('#pelunasan-piutang-detail-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#pelunasan-piutang-detail-form').find(`input[name='format']`).val('pdf');$('#pelunasan-piutang-detail-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#pelunasan-piutang-detail-form').find(`input[name='format']`).val('excel');$('#pelunasan-piutang-detail-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2SearchPagination(`pelunasan_piutang_detail_coa_id`, `{{ route('admin.select.coa') }}`, {
                                    id: "id",
                                    text: "account_code,name"
                                }, 0, {
                                    account_type: "Cash & Bank"
                                }, '#pelunasan-piutang-detail-modal');

                                initSelect2Search(`pelunasan_piutang_detail_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#pelunasan-piutang-detail-modal');

                                initSelect2Search(`pelunasan_piutang_detail_invoice_parent_id`, `{{ route('admin.select.invoice') }}`, {
                                    id: "id",
                                    text: "code"
                                }, 0, {
                                    payment_status: function() {
                                        return $('#pelunasan_piutang_detail_status').val();
                                    },
                                    customer_id: function() {
                                        return $('#pelunasan_piutang_detail_customer_id').val();
                                    },
                                    status: 'approve',
                                }, '#pelunasan-piutang-detail-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END PELUNASAN PIUTANG DETAIL --}}

                    {{-- UMUR PIUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#receivable-aging-modal">{{ Str::headline('laporan umur piutang') }}</a>
                            <div class="modal fade" id="receivable-aging-modal" aria-hidden="true" aria-labelledby="receivable-aging-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'receivable-aging']) }}" method="POST" id="receivable-aging-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="receivable-aging-modalLabel">
                                                    {{ Str::headline('laporan umur piutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="receivable_aging_customer_id">Customer</label>
                                                        <br>
                                                        <select name="customer_id" id="receivable_aging_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="receivable_aging_customer_id">Tipe Transaksi</label>
                                                        <br>
                                                        <select name="type" id="receivable_aging_type" class="form-select" autofocus>
                                                            <option value="">Semua</option>
                                                            <option value="general">General</option>
                                                            <option value="trading">Trading</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active5" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#receivable-aging-form').find(`input[name='format']`).val('preview');$('#receivable-aging-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#receivable-aging-form').find(`input[name='format']`).val('pdf');$('#receivable-aging-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#receivable-aging-form').find(`input[name='format']`).val('excel');$('#receivable-aging-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`receivable_aging_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#receivable-aging-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END UMUR PIUTANG --}}

                    {{-- SUMMARY KARTU HUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kartu-hutang-modal">{{ Str::headline('kartu-hutang') }}</a>
                            <div class="modal fade" id="kartu-hutang-modal" aria-hidden="true" aria-labelledby="kartu-hutang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'kartu-hutang']) }}" method="get" id="kartu-hutang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="kartu-hutang-modalLabel">
                                                    {{ Str::headline('kartu-hutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="kartu_hutang_vendor_id">vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="kartu_hutang_vendor_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="kartu_hutang_currency_id">Mata
                                                            Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="kartu_hutang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">
                                                                {{ get_local_currency()->kode }} -
                                                                {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#kartu-hutang-form').find(`input[name='format']`).val('preview');$('#kartu-hutang-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#kartu-hutang-form').find(`input[name='format']`).val('pdf');$('#kartu-hutang-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#kartu-hutang-form').find(`input[name='format']`).val('excel');$('#kartu-hutang-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`kartu_hutang_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#kartu-hutang-modal');

                                initSelect2Search(`kartu_hutang_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#kartu-hutang-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SUMMARY KARTU HUTANG --}}

                    {{-- SISA HUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-hutang-modal">{{ Str::headline('sisa-hutang') }}</a>
                            <div class="modal fade" id="sisa-hutang-modal" aria-hidden="true" aria-labelledby="sisa-hutang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-hutang']) }}" method="get" id="sisa-hutang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-hutang-modalLabel">
                                                    {{ Str::headline('sisa-hutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_hutang_vendor_id">vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="sisa_hutang_vendor_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_hutang_currency_id">Mata
                                                            Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_hutang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">
                                                                {{ get_local_currency()->kode }} -
                                                                {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sisa-hutang-form').find(`input[name='format']`).val('preview');$('#sisa-hutang-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sisa-hutang-form').find(`input[name='format']`).val('pdf');$('#sisa-hutang-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sisa-hutang-form').find(`input[name='format']`).val('excel');$('#sisa-hutang-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`sisa_hutang_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#sisa-hutang-modal');

                                initSelect2Search(`sisa_hutang_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#sisa-hutang-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SISA HUTANG --}}

                    {{-- PELUNASAN HUTANG DETAIL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#pelunasan-hutang-detail-modal">{{ Str::headline('laporan pelunasan hutang detail') }}</a>
                            <div class="modal fade" id="pelunasan-hutang-detail-modal" aria-hidden="true" aria-labelledby="pelunasan-hutang-detail-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'pelunasan-hutang-detail']) }}" method="get" id="pelunasan-hutang-detail-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="pelunasan-hutang-detail-modalLabel">
                                                    {{ Str::headline('laporan pelunasan hutang detail') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_hutang_detail_status">Status
                                                                Transaksi</label>
                                                            <br>
                                                            <select name="payment_status" id="pelunasan_hutang_detail_status" class="form-select" label="status" autofocus>
                                                                <option value="">Semua</option>
                                                                <option value="unpaid">Belum Lunas</option>
                                                                <option value="partial-paid">Sebagian</option>
                                                                <option value="paid">Lunas</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_hutang_detail_vendor_id">Vendor</label>
                                                            <br>
                                                            <select name="vendor_id" id="pelunasan_hutang_detail_vendor_id" class="form-select" label="vendor" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_hutang_detail_invoice_parent_id">No.
                                                                Dokumen</label>
                                                            <br>
                                                            <select name="invoice_parent_id" id="pelunasan_hutang_detail_invoice_parent_id" class="form-select" label="no. dokumen" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_hutang_detail_coa_id">Kas/Bank</label>
                                                            <br>
                                                            <select name="coa_id" id="pelunasan_hutang_detail_coa_id" class="form-select" label="kas/bank" autofocus></select>
                                                        </div>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#pelunasan-hutang-detail-form').find(`input[name='format']`).val('preview');$('#pelunasan-hutang-detail-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#pelunasan-hutang-detail-form').find(`input[name='format']`).val('pdf');$('#pelunasan-hutang-detail-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#pelunasan-hutang-detail-form').find(`input[name='format']`).val('excel');$('#pelunasan-hutang-detail-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2SearchPagination(`pelunasan_hutang_detail_coa_id`, `{{ route('admin.select.coa') }}`, {
                                    id: "id",
                                    text: "account_code,name"
                                }, 0, {
                                    account_type: "Cash & Bank"
                                }, '#pelunasan-hutang-detail-modal');

                                initSelect2Search(`pelunasan_hutang_detail_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#pelunasan-hutang-detail-modal');

                                initSelect2Search(`pelunasan_hutang_detail_invoice_parent_id`, `{{ route('admin.select.invoice') }}`, {
                                    id: "id",
                                    text: "code"
                                }, 0, {
                                    payment_status: function() {
                                        return $('#pelunasan_hutang_detail_status').val();
                                    },
                                    customer_id: function() {
                                        return $('#pelunasan_hutang_detail_customer_id').val();
                                    },
                                    status: 'approve',
                                }, '#pelunasan-hutang-detail-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END PELUNASAN HUTANG DETAIL --}}

                    {{-- SISA HUTANG PER VENDOR --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-hutang-per-vendor-modal">{{ Str::headline('sisa-hutang-per-vendor') }}</a>
                            <div class="modal fade" id="sisa-hutang-per-vendor-modal" aria-hidden="true" aria-labelledby="sisa-hutang-per-vendor-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-hutang-per-vendor']) }}" method="get" id="sisa-hutang-per-vendor-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-hutang-per-vendor-modalLabel">
                                                    {{ Str::headline('sisa-hutang-per-vendor') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_hutang_per_vendor_id">vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="sisa_hutang_per_vendor_id" class="form-select" autofocus></select>
                                                    </div>
                                                    {{-- <div class="col-md-4">
                                                        <label class="form-label" for="sisa_hutang_per_vendor_currency_id">Mata Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_hutang_per_vendor_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->kode }} - {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div> --}}
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sisa-hutang-per-vendor-form').find(`input[name='format']`).val('preview');$('#sisa-hutang-per-vendor-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sisa-hutang-per-vendor-form').find(`input[name='format']`).val('pdf');$('#sisa-hutang-per-vendor-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sisa-hutang-per-vendor-form').find(`input[name='format']`).val('excel');$('#sisa-hutang-per-vendor-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`sisa_hutang_per_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#sisa-hutang-per-vendor-modal');

                                initSelect2Search(`sisa_hutang_per_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#sisa-hutang-per-vendor-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SISA HUTANG PER VENDOR --}}

                    {{-- SUMMARY HUTANG DAGANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-hutang-dagang-modal">{{ Str::headline('summary-hutang-dagang') }}</a>
                            <div class="modal fade" id="summary-hutang-dagang-modal" aria-hidden="true" aria-labelledby="summary-hutang-dagang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'summary-hutang-dagang']) }}" method="get" id="summary-hutang-dagang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="summary-hutang-dagang-modalLabel">
                                                    {{ Str::headline('summary-hutang-dagang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="summary_hutang_dagang_vendor_id">vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="summary_hutang_dagang_vendor_id" class="form-select" autofocus></select>
                                                    </div>
                                                    {{-- <div class="col-md-4">
                                                        <label class="form-label" for="summary_hutang_dagang_currency_id">Mata Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="summary_hutang_dagang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->kode }} - {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div> --}}
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="start_period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="end_period" class="month-year-picker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-hutang-dagang-form').find(`input[name='format']`).val('preview');$('#summary-hutang-dagang-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-hutang-dagang-form').find(`input[name='format']`).val('pdf');$('#summary-hutang-dagang-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-hutang-dagang-form').find(`input[name='format']`).val('excel');$('#summary-hutang-dagang-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>

                        @push('script')
                            <script>
                                initSelect2Search(`summary_hutang_dagang_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#summary-hutang-dagang-modal');

                                initSelect2Search(`summary_hutang_dagang_currency_id`, `{{ route('admin.select.currency') }}`, {
                                    id: "id",
                                    text: "kode,nama"
                                }, 0, {}, '#summary-hutang-dagang-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END SUMMARY HUTANG DAGANG --}}


                    {{-- UMUR HUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#payable-aging-modal">{{ Str::headline('laporan umur hutang') }}</a>
                            <div class="modal fade" id="payable-aging-modal" aria-hidden="true" aria-labelledby="payable-aging-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.purchase-order-report-all.report', ['type' => 'payable-aging']) }}" method="GET" id="payable-aging-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="payable-aging-modalLabel">
                                                    {{ Str::headline('laporan umur hutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="payable_aging_vendor_id">vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="payable_aging_vendor_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#payable-aging-form').find(`input[name='format']`).val('preview');$('#payable-aging-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#payable-aging-form').find(`input[name='format']`).val('pdf');$('#payable-aging-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#payable-aging-form').find(`input[name='format']`).val('excel');$('#payable-aging-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`payable_aging_vendor_id`, `{{ route('admin.select.vendor') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#payable-aging-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END UMUR HUTANG --}}


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
        sidebarActive('#finance-report')
    </script>
@endsection
