@extends('layouts.admin.layout.index')

@php
    $main = 'tax-reconciliation';
    $title = 'rekonsiliasi pajak';
@endphp

@section('title', Str::headline("edit $title") . ' - ')

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
                        {{ Str::headline('edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'edit ' . $title }}">
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('admin.' . $main . '.update', ['tax_reconciliation' => $model->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="tax_period" name="tax_period" label="masa pajak" required class="month-year-picker-input" value="{{ Carbon\Carbon::parse($model->tax_period)->format('m-Y') }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="date" name="date" label="tanggal pengerjaan" required class="datepicker-input" value="{{ localDate($model->date) }}" />
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
                        <table class="table table" id="table-invoice-tax">
                            <thead class="bg-info">
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">PPN KELUARAN</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="out-data">
                                @foreach ($model->tax_reconciliation_details()->where('type', 'invoice-tax')->get() as $key => $detail)
                                    <tr>
                                        <td class="align-top">
                                            <b> <i class="fa fa-file"></i> {{ $detail->reference_parent->code ?? $detail->reference_parent->kode }}</b><br>
                                            <span class="text-light">Sales Invoice</span><br>
                                            <span class="text-light">{{ localDate($detail->reference->date ?? '-') }}</span><br>
                                            <span class="text-light">{{ $detail->customer->name ?? $detail->customer->nama }}</span><br>
                                            <span class="text-light">{{ $detail->customer->npwp ?? 'Tidak Ada NPWP' }}</span><br>
                                            <span class="text-light">{{ $detail->tax_number ?? 'Tidak Ada Faktur Pajak' }}</span><br>
                                        </td>
                                        <td class="align-top">
                                            <span>DPP : <b>{{ formatNumber($detail->dpp) }}</b></span><br>
                                            <span>{{ $detail->tax->name }} {{ $detail->value * 100 }}% : <b>{{ formatNumber($detail->amount) }}</b></span><br>
                                            <span>NILAI SISA : <b>{{ formatNumber($detail->out) }}</b></span><br>
                                        </td>
                                        <td class="align-top text-end">
                                            <input type="checkbox" checked id="out_checkbox_{{ $key }}" class="out_checkbox" name="out_checkbox[{{ $key }}]" onclick="calculcateData();checkboxOutCheck($(this))" class="filled-in chk-col-primary" value="1" data-out="{{ $detail->out }}" data-dpp="{{ $detail->dpp }}" data-index="{{ $key }}">
                                            <label for="out_checkbox_{{ $key }}"></label>
                                            <input type="hidden" class="out_is_checked" name="out_is_checked[{{ $key }}]" value="true" id="out_is_checked_{{ $key }}">
                                            <input type="hidden" class="out" name="out[{{ $key }}]" value="{{ $detail->out }}">
                                            <input type="hidden" class="out_type" name="out_type[{{ $key }}]" value="journal">
                                            <input type="hidden" class="out_id" name="out_id[{{ $key }}]" value="{{ $detail->reference_id }}">
                                            <input type="hidden" class="out_tax_number" name="out_tax_number[{{ $key }}]" value="{{ $detail->tax_number }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 mb-3">
                        <table class="table" id="table-purchase-tax">
                            <thead class="bg-info">
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">PPN MASUKAN</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="in-data">
                                @foreach ($model->tax_reconciliation_details()->where('type', 'purchase-tax')->get() as $key => $detail)
                                    @if ($detail->vendor)
                                        <tr>
                                            <td class="align-top">
                                                <b> <i class="fa fa-file"></i> {{ $detail->reference_parent->code ?? $detail->reference_parent->kode }}</b><br>
                                                <span class="text-light">Purchase Invoice</span><br>
                                                <span class="text-light">{{ localDate($detail->reference->date ?? '-') }}</span><br>
                                                <span class="text-light">{{ $detail->vendor->name ?? $detail->vendor->nama }}</span><br>
                                                <span class="text-light">{{ $detail->vendor->npwp ?? 'Tidak Ada NPWP' }}</span><br>
                                                <span class="text-light">{{ $detail->tax_number ?? 'Tidak Ada Faktur Pajak' }}</span><br>
                                            </td>
                                            <td class="align-top">
                                                <span>DPP : <b>{{ formatNumber($detail->dpp) }}</b></span><br>
                                                <span>{{ $detail->tax->name }} {{ $detail->value * 100 }}% : <b>{{ formatNumber($detail->amount) }}</b></span><br>
                                                <span>NILAI SISA : <b>{{ formatNumber($detail->in) }}</b></span><br>
                                            </td>
                                            <td class="align-top text-end">
                                                <input type="checkbox" checked id="in_checkbox_{{ $key }}" class="in_checkbox" name="in_checkbox[{{ $key }}]" onclick="calculcateData();checkboxInCheck($(this))" class="filled-in chk-col-primary" value="1" data-in="{{ $detail->in }}" data-dpp="{{ $detail->dpp }}" data-index="{{ $key }}">
                                                <label for="in_checkbox_{{ $key }}"></label>
                                                <input type="hidden" class="in_is_checked" name="in_is_checked[{{ $key }}]" value="true" id="in_is_checked_{{ $key }}">
                                                <input type="hidden" class="in" name="in[{{ $key }}]" value="{{ $detail->in }}">
                                                <input type="hidden" class="in_type" name="in_type[{{ $key }}]" value="journal">
                                                <input type="hidden" class="in_id" name="in_id[{{ $key }}]" value="{{ $detail->reference_id }}">
                                                <input type="hidden" class="in_tax_number" name="in_tax_number[{{ $key }}]" value="{{ $detail->tax_number }}">
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="align-top">
                                                <b> <i class="fa fa-file"></i> {{ $detail->note }}</b><br>
                                                <span class="text-light">{{ localDate($detail->reference->date ?? '-') }}</span><br>
                                            </td>
                                            <td class="align-top">
                                                <span>NILAI : <b>{{ formatNumber($detail->in) }}</b></span><br>
                                            </td>
                                            <td class="align-top text-end">
                                                <input type="checkbox" checked id="in_checkbox_{{ $key }}" class="in_checkbox" name="in_checkbox[{{ $key }}]" onclick="calculcateData();checkboxInCheck($(this))" class="filled-in chk-col-primary" value="1" data-in="{{ $detail->in }}" data-dpp="{{ $detail->dpp }}" data-index="{{ $key }}">
                                                <label for="in_checkbox_{{ $key }}"></label>
                                                <input type="hidden" class="in_is_checked" name="in_is_checked[{{ $key }}]" value="true" id="in_is_checked_{{ $key }}">
                                                <input type="hidden" class="in" name="in[{{ $key }}]" value="{{ $detail->in }}">
                                                <input type="hidden" class="in_type" name="in_type[{{ $key }}]" value="journal">
                                                <input type="hidden" class="in_id" name="in_id[{{ $key }}]" value="{{ $detail->reference_id }}">
                                                <input type="hidden" class="in_tax_number" name="in_tax_number[{{ $key }}]" value="{{ $detail->tax_number }}">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
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
                                <tr id="coa_id_form" class="{{ $model->gap == 0 ? 'd-none' : '' }}">
                                    <th colspan="4"></th>
                                    <th>
                                        <label for="coa_id">Akun Selisih Pajak</label>
                                        <br>
                                        <select name="coa_id" id="coa_id" class="form-select">
                                            @if ($model->coa)
                                                <option value="{{ $model->coa_id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                            @endif
                                        </select>
                                        <br>
                                        <small class="text-danger" id="gap_alert"></small>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
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
    <script src="{{ asset('js/admin/tax-reconciliation/transaction.js') }}"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#asset-sidebar')
        sidebarActive('#tax-reconciliation')

        initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        });

        $("#table-invoice-tax").dataTable({
            searching: false,
            paging: true,
            destroy: true,
            responsive: true
        });
        $("#table-purchase-tax").dataTable({
            searching: false,
            paging: true,
            destroy: true,
            responsive: true
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

        MASUKAN = $('#in-data').html();
        PENGELUARAN = $('#out-data').html();

        calculcateData();
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
