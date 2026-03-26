@extends('layouts.admin.layout.index')

@php
    $main = 'cash-advance-payment';
    $menu = 'pembayaran deposit';
@endphp

@section('title', Str::headline("Tambah $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.outgoing-payment.index') }}?tab=deposite">{{ Str::headline($menu) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Create ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.' . $main . '.store') }}" method="post">
        @csrf
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ Str::headline('tambah ' . $menu) }}</h3>
            </div>
            <div class="box-body">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" id="date" name="date" label="tanggal bayar" required value="{{ date('d-m-Y') }}" class="datepicker-input" />
                        </div>
                    </div>
                    <div class="col-md-4" id="form-fund-submission">
                        <div class="form-group">
                            <x-select name="fund_submission_id" id="fund_submission_id" label="pengajuan dana" required onchange="getFundSubmission($(this))">
                            </x-select>
                        </div>
                    </div>
                </div>
                <div id="form_detail">

                </div>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.outgoing-payment.index') }}?tab=deposite" class="btn btn-secondary">Cancel</a>
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/fund-submission/dp.js') }}"></script>
    <script src="{{ asset('js/admin/cash-advance-payment/index.js') }}"></script>
    <script>
        $(document).ready(function() {
            checkClosingPeriod($('#date'))
            sidebarMenuOpen('#finance-main-sidebar');
            sidebarActive('#outgoing-payment-sidebar');
            sidebarActive('#outgoing-payment');

            $('#date').change(function() {
                checkClosingPeriod($(this))
                checkFundSubmissionDate($(this))
            })
        });
    </script>
@endsection
