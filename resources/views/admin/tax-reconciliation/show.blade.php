@extends('layouts.admin.layout.index')

@php
    $main = 'tax-reconciliation';
    $title = 'rekonsiliasi pajak';
@endphp

@section('title', Str::headline("detail $main") . ' - ')

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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-9">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date">Masa Pajak</label>
                                    <p>{{ Carbon\Carbon::parse($model->tax_period)->format('m-Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date">Tanggal Pengerjaan</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>

                            @if ($model->coa)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="from_date">Akun Selisih Pajak</label>
                                        <p>{{ $model->coa?->account_code }} - {{ $model->coa->name }}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="form-label">Status</label>
                                    <br>
                                    <div class="badge badge-lg badge-{{ fund_submission_status()[$model->status]['color'] }}">
                                        {{ fund_submission_status()[$model->status]['label'] }} -
                                        {{ fund_submission_status()[$model->status]['text'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-30">
                            <div class="col-md-12 mb-3">
                                <table class="table table" id="table-invoice-tax">
                                    <thead class="bg-info">
                                        <tr>
                                            <th class="text-center">PPN KELUARAN</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="out-data">
                                        @foreach ($model->tax_reconciliation_details()->where('type', 'invoice-tax')->get() as $key => $detail)
                                            <tr>
                                                <td class="align-top">
                                                    <b> <i class="fa fa-file"></i>
                                                        {{ $detail->reference_parent?->code ?? $detail->reference_parent?->kode }}</b><br>
                                                    <span class="text-light">Sales Invoice</span><br>
                                                    <span class="text-light">{{ $detail->reference?->date ?? '-' }}</span><br>
                                                    <span class="text-light">{{ $detail->customer?->name ?? $detail->customer?->nama }}</span><br>
                                                    <span class="text-light">{{ $detail->customer?->npwp ?? 'Tidak Ada NPWP' }}</span><br>
                                                    <span class="text-light">{{ $detail->tax_number ?? 'Tidak Ada Faktur Pajak' }}</span><br>
                                                </td>
                                                <td class="align-top">
                                                    <span>DPP : <b>{{ formatNumber($detail->dpp) }}</b></span><br>
                                                    <span>{{ $detail->tax->name }} {{ $detail->value * 100 }}% :
                                                        <b>{{ formatNumber($detail->amount) }}</b></span><br>
                                                    <span>NILAI SISA : <b>{{ formatNumber($detail->out) }}</b></span><br>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 mb-3">
                                <table class="table" id="table-purchase-tax">
                                    <thead class="bg-info">
                                        <tr>
                                            <th class="text-center">PPN MASUKAN</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="in-data">
                                        @foreach ($model->tax_reconciliation_details()->where('type', 'purchase-tax')->get() as $key => $detail)
                                            @if ($detail->vendor)
                                                <tr>
                                                    <td class="align-top">
                                                        <b> <i class="fa fa-file"></i>
                                                            {{ $detail->reference_parent?->code ?? $detail->reference_parent?->kode }}</b><br>
                                                        <span class="text-light">Purchase Invoice</span><br>
                                                        <span class="text-light">{{ $detail->reference?->date ?? '-' }}</span><br>
                                                        <span class="text-light">{{ $detail->vendor?->name ?? $detail->vendor?->nama }}</span><br>
                                                        <span class="text-light">{{ $detail->vendor?->npwp ?? 'Tidak Ada NPWP' }}</span><br>
                                                        <span class="text-light">{{ $detail->tax_number ?? 'Tidak Ada Faktur Pajak' }}</span><br>
                                                    </td>
                                                    <td class="align-top">
                                                        <span>DPP : <b>{{ formatNumber($detail->dpp) }}</b></span><br>
                                                        <span>{{ $detail->tax->name }} {{ $detail->value * 100 }}% :
                                                            <b>{{ formatNumber($detail->amount) }}</b></span><br>
                                                        <span>NILAI SISA : <b>{{ formatNumber($detail->in) }}</b></span><br>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="align-top">
                                                        <b> <i class="fa fa-file"></i>
                                                            {{ $detail->note }}</b><br>
                                                        <span class="text-light">{{ localDate($detail->reference?->date ?? '') }}</span><br>
                                                    </td>
                                                    <td class="align-top">
                                                        <span>NILAI : <b>{{ formatNumber($detail->in) }}</b></span><br>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered w-full">
                                    <thead>
                                        <tr>
                                            <th colspan="2">PPN Keluaran</th>
                                            <th colspan="2">PPN Masukan</th>
                                            <th>Selisih Pajak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="20%">
                                                <span style="white-space: normal">Total DPP Faktur Penjualan</span>
                                                <h5 id="checked_dpp_out_total_text">
                                                    {{ formatNumber($model->tax_reconciliation_details()->where('type', 'invoice-tax')->sum('dpp')) }}
                                                </h5>
                                            </td>
                                            <td width="20%">
                                                <span style="white-space: normal">Total Pajak Faktur Penjualan</span>
                                                <h5 id="checked_out_total_text">{{ formatNumber($model->total_out) }}</h5>
                                            </td>
                                            <td width="20%">
                                                <span style="white-space: normal">Total DPP Faktur Pembelian</span>
                                                <h5 id="checked_dpp_in_total_text">
                                                    {{ formatNumber($model->tax_reconciliation_details()->where('type', 'purchase-tax')->sum('dpp')) }}
                                                </h5>
                                            </td>
                                            <td width="20%">
                                                <span style="white-space: normal">Total Pajak Faktur Pembelian</span>
                                                <h5 id="checked_in_total_text">{{ formatNumber($model->total_in) }}</h5>
                                            </td>
                                            <td width="20%">
                                                <span style="white-space: normal">Total Selisih Pajak</span>
                                                <h5 id="gap_text">{{ formatNumber($model->gap * -1) }}</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if ($model->check_available_date)
                                @if ($model->status != 'approve' && $model->status != 'reject' && $model->status != 'void')
                                    @can("edit $main")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan
                                    @can("delete $main")
                                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>
                </x-card-data-table>

                @can('view journal')
                    @include('components.journal-table')
                @endcan
            </div>
            <div class="col-md-3">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="table_content">
                        @if ($model->check_available_date)
                            @if ($model->status == 'approve')
                                @can("void $main")
                                    <x-button color="danger" icon="trash" fontawesome label="void" size="sm" dataToggle="modal" dataTarget="#void-modal" />
                                    <x-modal title="void {{ $title }}" id="void-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="void">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Void" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        @endif
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Status title' }}">
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @forelse ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    <h5 class="fw-bold">Empty</h5>
                                </li>
                            @endforelse
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data title' }}">
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @forelse ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    <h5 class="fw-bold">Empty</h5>
                                </li>
                            @endforelse
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#tax-reconciliation');

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
    </script>

    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\TaxReconciliation`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
