@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-report';
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
                    {{-- LAPORAN LPB --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laporan-lpb-modal">{{ Str::headline('laporan-lpb') }}</a>
                            <div class="modal fade" id="laporan-lpb-modal" aria-hidden="true" aria-labelledby="laporan-lpb-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.purchase-order-report-all.report', ['type' => 'laporan-lpb']) }}" method="get" id="laporan-lpb-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="laporan-lpb-modalLabel">{{ Str::headline('laporan-lpb') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="laporan_lpb_vendor_id">Vendor</label>
                                                        <br>
                                                        <select name="vendor_id" id="laporan_lpb_vendor_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="laporan_lpb_currency_id">Currency</label>
                                                        <br>
                                                        <select name="currency_id" id="laporan_lpb_currency_id" class="form-select" autofocus></select>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-lpb-form').find(`input[name='format']`).val('preview');$('#laporan-lpb-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-lpb-form').find(`input[name='format']`).val('pdf');$('#laporan-lpb-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-lpb-form').find(`input[name='format']`).val('excel');$('#laporan-lpb-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END LAPORAN LPB --}}

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

                    {{-- SISA HUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-hutang-modal">{{ Str::headline('sisa-hutang') }}</a>
                            <div class="modal fade" id="sisa-hutang-modal" aria-hidden="true" aria-labelledby="sisa-hutang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-hutang']) }}" method="get" id="sisa-hutang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-hutang-modalLabel">{{ Str::headline('sisa-hutang') }}</h1>
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
                                                        <label class="form-label" for="sisa_hutang_currency_id">Mata Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_hutang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->kode }} - {{ get_local_currency()->nama }}</option>
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
                    </tr>
                    {{-- END SISA HUTANG --}}

                    {{-- SUMMARY KARTU HUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#kartu-hutang-modal">{{ Str::headline('kartu-hutang') }}</a>
                            <div class="modal fade" id="kartu-hutang-modal" aria-hidden="true" aria-labelledby="kartu-hutang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'kartu-hutang']) }}" method="get" id="kartu-hutang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="kartu-hutang-modalLabel">{{ Str::headline('kartu-hutang') }}</h1>
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
                    </tr>
                    {{-- END SUMMARY KARTU HUTANG --}}

                    {{-- SISA HUTANG PER VENDOR --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-hutang-per-vendor-modal">{{ Str::headline('sisa-hutang-per-vendor') }}</a>
                            <div class="modal fade" id="sisa-hutang-per-vendor-modal" aria-hidden="true" aria-labelledby="sisa-hutang-per-vendor-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-hutang-per-vendor']) }}" method="get" id="sisa-hutang-per-vendor-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-hutang-per-vendor-modalLabel">{{ Str::headline('sisa-hutang-per-vendor') }}</h1>
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
                    </tr>
                    {{-- END SISA HUTANG PER VENDOR --}}

                    {{-- UMUR hutang --}}
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
                    {{-- END UMUR hutang --}}
                </tbody>
            </table>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#purchase-menu')
        sidebarMenuOpen('#purchase-order-report')
        sidebarActive('#purchase-order-report-menu')

        // laporan lpb
        initSelect2Search(`laporan_lpb_vendor_id`, `{{ route('admin.select.vendor') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#laporan-lpb-modal');

        initSelect2Search(`laporan_lpb_currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        }, 0, {}, '#laporan-lpb-modal');

        // kartu hutang
        initSelect2Search(`kartu_hutang_vendor_id`, `{{ route('admin.select.vendor') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#kartu-hutang-modal');

        // sisa hutang
        initSelect2Search(`sisa_hutang_vendor_id`, `{{ route('admin.select.vendor') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#sisa-hutang-modal');

        // sisa hutang per vendor
        initSelect2Search(`sisa_hutang_per_vendor_id`, `{{ route('admin.select.vendor') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#sisa-hutang-per-vendor-modal');

        initSelect2Search(`sisa_hutang_per_currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        }, 0, {}, '#sisa-hutang-per-vendor-modal');
    </script>
@endsection
