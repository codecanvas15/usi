@extends('layouts.admin.layout.index')

@php
    $main = 'pengeluaran dana';
    $folder = 'outgoing-payment';
@endphp

@section('title', Str::headline("Tambah $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$folder.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Tambah ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('create outgoing-payment')
        <form id="form-data" action="{{ route("admin.$folder.store") }}" method="post">
            @csrf
            <x-card-data-table title="{{ 'Tambah ' . $main }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row mb-20">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="date_input" name="date" label="tanggal" required value="{{ date('d-m-Y') }}" onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-select name="from" id="from" label="referensi" required onchange="initReference()">
                                <option value="general">General</option>
                                <option value="fund_submission">Pengajuan Dana</option>
                            </x-select>
                        </div>
                        <div class="col-md-3 d-none" id="form-fund-submission">
                            <x-select name="fund_submission_id" id="fund_submission_id" label="pengajuan dana" required onchange="getFundSubmission($(this))">
                            </x-select>
                        </div>
                    </div>
                    <div id="form_detail">

                    </div>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.' . $folder . '.index') }}" class="btn btn-secondary">Cancel</a>
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    @can('create outgoing-payment')
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/admin/select/coa.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/outgoing-payment/index.js') }}?v=11.12.2025"></script>
        <script>
            var key = 0;

            sidebarMenuOpen('#finance-main-sidebar');
            sidebarActive('#outgoing-payment-sidebar');
            sidebarActive('#outgoing-payment');
            checkClosingPeriod($('#date_input'));

            initSelect2Search('currency_id', "{{ route('admin.select.currency') }}", {
                id: "id",
                text: "nama"
            });

            initReference($('#from'));
        </script>
    @endcan
@endsection
