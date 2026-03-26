@extends('layouts.admin.layout.index')

@php
    $main = 'legality-document';
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
                        {{ Str::headline('HRD') }}
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
    @can('view asset-document')
        @php
            $tab_active = 'asset';
        @endphp
    @elsecan('view lease-documnt')
        @php
            $tab_active = 'lease';
        @endphp
    @else
        @php
            $tab_active = 'company';
        @endphp
    @endcan

    <div class="box">
        <div class="box-body border-0">
            <ul class="nav nav-tabs customtab2" role="tablist">
                @can('view asset-document')
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'asset' ? 'active' : '' }}" data-bs-toggle="tab" href="#asset-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Asset</span>
                        </a>
                    </li>
                @endcan

                @can('view lease-document')
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'lease' ? 'active' : '' }}" data-bs-toggle="tab" href="#lease-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Biaya Dibayar Dimuka</span>
                        </a>
                    </li>
                @endcan
                @can('view legality-document')
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'company' ? 'active' : '' }}" data-bs-toggle="tab" href="#company-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Legalitas Perusahaan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'finance' ? 'active' : '' }}" data-bs-toggle="tab" href="#finance-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Legalitas Keuangan</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
    <div class="tab-content">
        @can('view asset-document')
            <div class="tab-pane {{ $tab_active == 'asset' ? 'active' : '' }}" id="asset-tab" role="tabpanel">
                <div class="box">
                    <div class="box-body border-0">
                        <ul class="nav nav-tabs customtab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link rounded active" data-bs-toggle="tab" href="#asset-list-tab" role="tab">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">List Asset</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded" data-bs-toggle="tab" href="#asset-document-tab" role="tab">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">List Dokumen</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="asset-list-tab" role="tabpanel">
                        <x-card-data-table title="">
                            <x-slot name="header_content">
                            </x-slot>
                            <x-slot name="table_content">
                                <x-table theadColor="" id="asset_list_table">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('nama aset') }}</th>
                                        <th>{{ Str::headline('action') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </x-slot>
                        </x-card-data-table>
                    </div>
                    <div class="tab-pane " id="asset-document-tab" role="tabpanel">
                        <x-card-data-table title="">
                            <x-slot name="header_content">
                            </x-slot>
                            <x-slot name="table_content">
                                <x-table theadColor="" id="asset_legality_table">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('nama aset') }}</th>
                                        <th>{{ Str::headline('nama dokumen') }}</th>
                                        <th>{{ Str::headline('tanggal berlaku') }}</th>
                                        <th>{{ Str::headline('tanggal berakhir') }}</th>
                                        <th></th>
                                        <th>{{ Str::headline('action') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </x-slot>
                        </x-card-data-table>
                    </div>
                </div>
            </div>
        @endcan

        @can('view lease-document')
            <div class="tab-pane {{ $tab_active == 'lease' ? 'active' : '' }}" id="lease-tab" role="tabpanel">
                <div class="box">
                    <div class="box-body border-0">
                        <ul class="nav nav-tabs customtab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link rounded active" data-bs-toggle="tab" href="#lease-list-tab" role="tab">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">List Biaya Dibayar Dimuka</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded" data-bs-toggle="tab" href="#lease-document-tab" role="tab">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">List Dokumen</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="lease-list-tab" role="tabpanel">
                        <x-card-data-table title="">
                            <x-slot name="header_content">
                            </x-slot>
                            <x-slot name="table_content">
                                <x-table theadColor="" id="lease_list_table">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('nama biaya dibayar dimuka') }}</th>
                                        <th>{{ Str::headline('action') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </x-slot>
                        </x-card-data-table>
                    </div>
                    <div class="tab-pane " id="lease-document-tab" role="tabpanel">
                        <x-card-data-table title="legalitas biaya dibayar dimuka">
                            <x-slot name="header_content">
                            </x-slot>
                            <x-slot name="table_content">
                                <x-table theadColor="" id="lease_legality_table">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('nama biaya dibayar dimuka') }}</th>
                                        <th>{{ Str::headline('nama dokumen') }}</th>
                                        <th>{{ Str::headline('tanggal berlaku') }}</th>
                                        <th>{{ Str::headline('tanggal berakhir') }}</th>
                                        <th></th>
                                        <th>{{ Str::headline('action') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </x-slot>
                        </x-card-data-table>
                    </div>
                </div>

            </div>
        @endcan

        <div class="tab-pane {{ $tab_active == 'company' ? 'active' : '' }}" id="company-tab" role="tabpanel">
            <x-card-data-table title="legalitas perusahaan">
                <x-slot name="header_content">
                    <div class="text-end">
                        @can("create $main")
                            <x-button type="button" color="info" class="mb-2" label="Tambah Dokumen" onclick="show_create_modal('company')" />
                        @endcan
                    </div>
                </x-slot>
                <x-slot name="table_content">
                    <x-table theadColor="" id="company_legality_table">
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>{{ Str::headline('nama dokumen') }}</th>
                            <th>{{ Str::headline('tanggal berlaku') }}</th>
                            <th>{{ Str::headline('tanggal berakhir') }}</th>
                            <th></th>
                            <th>{{ Str::headline('action') }}</th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="tab-pane {{ $tab_active == 'finance' ? 'active' : '' }}" id="finance-tab" role="tabpanel">
            <x-card-data-table title="legalitas keungan">
                <x-slot name="header_content">
                    <div class="text-end">
                        @can("create $main")
                            <x-button type="button" color="info" class="mb-2" label="Tambah Dokumen" onclick="show_create_modal('finance')" />
                        @endcan
                    </div>
                </x-slot>
                <x-slot name="table_content">
                    <x-table theadColor="" id="finance_legality_table">
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>{{ Str::headline('nama dokumen') }}</th>
                            <th>{{ Str::headline('tanggal berlaku') }}</th>
                            <th>{{ Str::headline('tanggal berakhir') }}</th>
                            <th></th>
                            <th>{{ Str::headline('action') }}</th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>

    <x-modal title="" headerColor="primary" id="document-form-modal" modalSize="900">
        <x-slot name="modal_body">
            <form action="" id="document-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="name" label="nama dokumen" type="text" required></x-input>
                            <small class="validation-error-message text-danger" id="name_error"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="transaction_date" label="tanggal transaksi" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="transaction_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="effective_date" label="tanggal berlaku" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="effective_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="end_date" label="tanggal berakhir" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="end_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" class="commas-form" name="due_date" label="reminder sebelum" helpers="Hari" required></x-input>
                            <small class="validation-error-message text-danger" id="due_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="file" name="file" label="lampiran dokumen"></x-input>
                            <small class="validation-error-message text-danger" id="file_error"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-text-area name="description" label="keterangan" cols="30" rows="10"></x-text-area>
                            <small class="validation-error-message text-danger" id="description_error"></small>
                        </div>
                    </div>
                </div>
                <div class="row text-end">
                    <div class="col-md-12">
                        <input type="hidden" name="type">
                        <input type="hidden" name="_method">
                        <x-button type="button" color="secondary" label="Batal" dataDismiss="modal" />
                        <x-button type="submit" color="primary" label="Simpan" />
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal>

    <x-modal title="" headerColor="primary" id="asset-document-form-modal" modalSize="900">
        <x-slot name="modal_body">
            <form action="" id="asset-document-form">
                <input type="hidden" name="asset_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="name" label="nama dokumen" type="text" required></x-input>
                            <small class="validation-error-message text-danger" id="asset_name_error"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="transaction_date" label="tanggal transaksi" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="asset_transaction_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="effective_date" label="tanggal berlaku" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="asset_effective_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="end_date" label="tanggal berakhir" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="asset_end_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" class="commas-form" name="due_date" label="reminder sebelum" helpers="Hari" required></x-input>
                            <small class="validation-error-message text-danger" id="asset_due_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="file" name="file" label="lampiran dokumen"></x-input>
                            <small class="validation-error-message text-danger" id="asset_file_error"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-text-area name="description" label="keterangan" cols="30" rows="10"></x-text-area>
                            <small class="validation-error-message text-danger" id="asset_description_error"></small>
                        </div>
                    </div>
                </div>
                <div class="row text-end">
                    <div class="col-md-12">
                        <input type="hidden" name="type">
                        <input type="hidden" name="_method">
                        <x-button type="button" color="secondary" label="Batal" dataDismiss="modal" />
                        <x-button type="submit" color="primary" label="Simpan" />
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal>

    <x-modal title="" headerColor="primary" id="lease-document-form-modal" modalSize="900">
        <x-slot name="modal_body">
            <form action="" id="lease-document-form">
                <input type="hidden" name="lease_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="name" label="nama dokumen" type="text" required></x-input>
                            <small class="validation-error-message text-danger" id="lease_name_error"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="transaction_date" label="tanggal transaksi" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="lease_transaction_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="effective_date" label="tanggal berlaku" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="lease_effective_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input name="end_date" label="tanggal berakhir" class="datepicker-input" required></x-input>
                            <small class="validation-error-message text-danger" id="lease_end_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="text" class="commas-form" name="due_date" label="reminder sebelum" helpers="Hari" required></x-input>
                            <small class="validation-error-message text-danger" id="lease_due_date_error"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-input type="file" name="file" label="lampiran dokumen"></x-input>
                            <small class="validation-error-message text-danger" id="lease_file_error"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-text-area name="description" label="keterangan" cols="30" rows="10"></x-text-area>
                            <small class="validation-error-message text-danger" id="lease_description_error"></small>
                        </div>
                    </div>
                </div>
                <div class="row text-end">
                    <div class="col-md-12">
                        <input type="hidden" name="type">
                        <input type="hidden" name="_method">
                        <x-button type="button" color="secondary" label="Batal" dataDismiss="modal" />
                        <x-button type="submit" color="primary" label="Simpan" />
                    </div>
                </div>
            </form>
        </x-slot>
    </x-modal>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/legality-document/company.js') }}"></script>
    <script src="{{ asset('js/admin/legality-document/finance.js') }}"></script>

    @can('view asset-document')
        <script src="{{ asset('js/admin/legality-document/asset.js') }}"></script>
        <script>
            init_asset_document()
        </script>
    @endcan

    @can('view lease-document')
        <script src="{{ asset('js/admin/legality-document/lease.js') }}"></script>
        <script>
            init_lease_document()
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#hrd');
        sidebarActive('#legality-document');
        init_company_document()
        init_finance_document()
    </script>
@endsection
