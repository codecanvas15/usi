@extends('layouts.admin.layout.index')

@php
    $main = 'cashier-report';
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
                    {{-- HARIAN KAS BANK DETAIL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#harian-kas-bank-detail-modal">{{ Str::headline('laporan harian kas bank detail') }}</a>
                            <div class="modal fade" id="harian-kas-bank-detail-modal" aria-hidden="true" aria-labelledby="harian-kas-bank-detail-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'harian-kas-bank-detail']) }}" method="get" id="harian-kas-bank-detail-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="harian-kas-bank-detail-modalLabel">{{ Str::headline('laporan harian kas bank detail') }}</h1>
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
                                                <h1 class="modal-title fs-5" id="cash-bond-modalLabel">{{ Str::headline('kasbon') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <x-select name="branch_id" useBr id="cash-bond-branch" label="cabang">
                                                            <option value="{{ get_current_branch()->id }}">{{ get_current_branch()->name }}</option>
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
                                                                    <option value="{{ $key }}">{{ Str::headline($item['label']) }}</option>
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
        sidebarActive('#cashier-report')
    </script>
@endsection
