@extends('layouts.admin.layout.index')

@php
    $main = 'labor-transfer-form';
    $title = 'Formulir Pemindahan Tenaga Kerja';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline("tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form id="form" action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table title="tambah {{ $title }}">
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row pb-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="employee" label="Karyawan" id="employee" hasError errorBorderId="errorSelectEmployee" errorMessageId="errorMsgSelectEmployee" errorMsg="Karyawan wajib dipilih." required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="submitted_by" label="Diajukan Oleh" id="submittedBy" hasError errorBorderId="errorSelectSubmittedBy" errorMessageId="errorMsgSelectSubmittedBy" errorMsg="Diajukan oleh wajib dipilih." required />
                            </div>
                        </div>
                    </div>
                    <div class="row border-top border-primary pt-20">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="from_company" label="Dari PT" id="fromCompany" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input name="to_company" label="Ke PT" id="toCompany" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="from_branch" label="Dari Cabang" id="from_branch" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="to_branch" label="Ke Cabang" id="to_branch" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="from_division" label="Dari Dep./Bagian" id="from_division" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-select name="to_division" label="Ke Dep./Bagian" id="to_division" required />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-text-area name="reason" label="Alasan" id="reason" value="" required />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <div class="box-footer text-end">
                <x-button id="btnBack" color="secondary" label="kembali" icon="x" fontawesome size="sm" />
                <x-button id="btnSave" color="primary" label="simpan" icon="save" fontawesome size="sm" />
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script>
        $(document).ready(function() {

            let previousUrl = "{{ URL::previous() }}";

            initSelectEmployee('#employee', null, false);
            initSelectEmployee('#submittedBy', null, false);

            $('#employee').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectEmployee').removeClass('border border-danger');
                    $('#errorMsgSelectEmployee').html('');
                }
            })

            $('#submittedBy').change(function() {
                let value = $(this).val();
                if (value) {
                    $('#errorSelectSubmittedBy').removeClass('border border-danger');
                    $('#errorMsgSelectSubmittedBy').html('');
                }
            })

            initSelect2Search('from_branch', `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });

            initSelect2Search('to_branch', `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });

            initSelect2Search('from_division', `{{ route('admin.select.division') }}`, {
                id: "id",
                text: "name"
            });

            initSelect2Search('to_division', `{{ route('admin.select.division') }}`, {
                id: "id",
                text: "name"
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#labor-transfer-form')
    </script>
@endsection
