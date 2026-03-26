@extends('layouts.admin.layout.index')

@php
    $main = 'tax-reconciliation';
    $title = 'rekonsiliasi pajak';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

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
                        {{ Str::headline('asset') }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'tambah ' . $title }}">
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('admin.' . $main . '.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="tax_period" name="tax_period" label="masa pajak" required class="month-year-picker-input" value="{{ date('m-Y') }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="date" name="date" label="tanggal pengerjaan" onchange="checkClosingPeriod($(this))" required class="datepicker-input" value="{{ date('d-m-Y') }}" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <button class="btn btn-primary" type="button" onclick="getData()">Ambil Data</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-30">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <input type="checkbox" id="in-check-all" class="filled-in chk-col-primary">
                        <label for="in-check-all">Select All</label>
                    </div>
                    <div class="col-md-6 mb-3">
                        <table id="table-invoice-tax" class="table">
                            <thead class="bg-info">
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">PPN KELUARAN</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="out-data">

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="table-responsive">
                            <table id="table-purchase-tax" class="table">
                                <thead class="bg-info">
                                    <tr>
                                        <th class="text-center"></th>
                                        <th class="text-center">PPN MASUKAN</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="in-data">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">PPN Keluaran</th>
                                        <th colspan="2">PPN Masukan</th>
                                        <th>Selisih Pajak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span>Total DPP Faktur Penjualan</span>
                                            <h5 id="checked_dpp_out_total_text">0</h5>
                                            <input type="hidden" id="checked_dpp_out_total" value="0">
                                        </td>
                                        <td>
                                            <span>Total Pajak Faktur Penjualan</span>
                                            <h5 id="checked_out_total_text">0</h5>
                                            <input type="hidden" id="checked_out_total" value="0">
                                        </td>
                                        <td>
                                            <span>Total DPP Faktur Pembelian</span>
                                            <h5 id="checked_dpp_in_total_text">0</h5>
                                            <input type="hidden" id="checked_dpp_in_total" value="0">
                                        </td>
                                        <td>
                                            <span>Total Pajak Faktur Pembelian</span>
                                            <h5 id="checked_in_total_text">0</h5>
                                            <input type="hidden" id="checked_in_total" value="0">
                                        </td>
                                        <td>
                                            <span>Total Selisih Pajak</span>
                                            <h5 id="gap_text">0</h5>
                                            <input type="hidden" id="gap" value="0">
                                        </td>
                                    </tr>
                                    <tr id="coa_id_form" class="d-none">
                                        <th colspan="4"></th>
                                        <th>
                                            <label for="coa_id">Akun Selisih Pajak</label>
                                            <br>
                                            <select name="coa_id" id="coa_id" class="form-select"></select>
                                            <br>
                                            <small class="text-danger" id="gap_alert"></small>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/tax-reconciliation/transaction.js') }}?v=3"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#asset-sidebar')
        sidebarActive('#tax-reconciliation')
        checkClosingPeriod($('#date'))

        initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        });

        $('form').submit(function(e) {
            e.preventDefault();
            let tax_period = $('#tax_period').val();
            tax_period = tax_period.split('-');
            tax_period = tax_period[1] + tax_period[0];
            let date = $('#date').val()
            date = date.split('-');
            date = date[2] + date[1];

            if (date < tax_period) {
                e.preventDefault();

                Swal.fire({
                    icon: 'error',
                    title: '',
                    text: 'Tanggal pengerjaan tidak boleh kurang dari periode pajak!',
                })

                setTimeout(() => {
                    $(this).find('input[type=submit]').prop('disabled', false);
                    $(this).find('button[type=submit]').prop('disabled', false);
                }, 2000);
                return false;
            }

            var table_in;
            table_in = $('#table-purchase-tax').DataTable().destroy();

            var table_out;
            table_out = $('#table-invoice-tax').DataTable().destroy();

            setTimeout(() => {
                $(this).unbind('submit').submit();
            }, 1000);
        })
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
