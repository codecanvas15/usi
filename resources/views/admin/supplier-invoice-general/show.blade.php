@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice-general';
    $title = 'Purchase Invoice (Non LPB)';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'detail ' . $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <td>{{ Carbon\Carbon::parse($model->date)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('vendor') }}</th>
                                <td>{{ $model->vendor->nama }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('term of payment') }}</th>
                                <td>
                                    {{ ucwords($model->term_of_payment) }}
                                    @if ($model->top_days)
                                        {{ $model->top_days }} hari
                                    @endif
                                </td>
                            </tr>
                            @if ($model->term_of_payment == 'by days')
                                <tr>
                                    <th>{{ Str::headline('jatuh tempo') }}</th>
                                    <td>{{ Carbon\Carbon::now()->addDays($model->top_days)->format('d-m-Y') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ Str::headline('branch') }}</th>
                                <td>{{ ucwords($model->branch->name) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('kode') }}</th>
                                <td>{{ $model->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('status') }}</th>
                                <td>
                                    @if ($model->status == 'approve')
                                        <span class="badge badge-info">Approved</span>
                                    @elseif ($model->status == 'pending')
                                        <span class="badge badge-warning">Pending - Waiting Approval</span>
                                    @elseif ($model->status == 'rejected')
                                        <span class="badge badge-dark">Reject - Purchase Invoice (Non LPB) Rejected</span>
                                    @elseif ($model->status == 'void')
                                        <span class="badge badge-dark">Void - Purchase Invoice (Non LPB) Void</span>
                                    @else
                                        <span class="badge badge-dark">Revert - Purchase Invoice (Non LPB) Reverted</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('currency') }}</th>
                                <td>{{ $model->currency->kode }} - {{ $model->currency->nama }} - {{ $model->currency->negara }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('exchange rate') }}</th>
                                <td>{{ formatNumber($model->exchange_rate) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('debit') }}</th>
                                <td>{{ formatNumber($model->debit) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('credit') }}</th>
                                <td>{{ formatNumber($model->credit) }}</td>
                            </tr>
                            {{-- <tr>
                                <th>{{ Str::headline('created_at') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <th>{{ Str::headline('last medified') }}</th>
                                <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                            </tr> --}}
                            @if ($model->attachment)
                                <tr>
                                    <th>{{ Str::headline('Lampiran') }}</th>
                                    <td><a href="{{ asset('storage/' . $model->attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> Lihat File Lampiran </a></td>
                                </tr>
                            @endif
                        </x-slot>
                    </x-table>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
                        <div>
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                                @if ($model->check_available_date)
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
                    </div>
                </x-slot>
            </x-card-data-table>
            @php
                $details = [];
                foreach ($model->detail as $detail) {
                    if ($detail->type == 'general') {
                        array_push($details, $detail);
                    }
                }
            @endphp
            <div class="box">
                <div class="box-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="box-title fw-bold">Detail</h5>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Account</th>
                                        <th>Keterangan</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $key => $detail)
                                        <tr>
                                            <td>{{ $key++ + 1 }}</td>
                                            <td>{{ $detail->coa->account_code . ' - ' . $detail->coa->name }}</td>
                                            <td>{{ $detail->notes }}</td>
                                            <td>{{ formatNumber($detail->debit) }}</td>
                                            <td>{{ formatNumber($detail->credit) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $adjustments = [];
                foreach ($model->detail as $detail) {
                    if ($detail->type == 'journal') {
                        array_push($adjustments, $detail);
                    }
                }
            @endphp
            @if (count($adjustments) > 0)
                <div class="box">
                    <div class="box-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="box-title fw-bold">Adjustment Journal</h5>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped mt-10 mb-10">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Account</th>
                                            <th>Keterangan</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($adjustments as $key => $adjustment)
                                            <tr>
                                                <td>{{ $key++ + 1 }}</td>
                                                <td>{{ $adjustment->coa->account_code . ' - ' . $adjustment->coa->name }}
                                                </td>
                                                <td>{{ $adjustment->notes }}</td>
                                                <td>{{ formatNumber($adjustment->debit) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <x-card-data-table title="payment information">
                <x-slot name="table_content">
                    <x-table theadColor=''>
                        <x-slot name="table_head">
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Keterangan</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->supplier_invoice_payment() as $supplier_invoice_payment)
                                <tr>
                                    <td>{{ localDate($supplier_invoice_payment->date) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($supplier_invoice_payment->amount_to_pay) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($supplier_invoice_payment->pay_amount) }}</td>
                                    <td>{{ $supplier_invoice_payment->note }}</td>
                                </tr>
                            @endforeach
                        </x-slot>
                        <x-slot name="table_foot">
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->supplier_invoice_payment()->sum('amount_to_pay')) }}</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->supplier_invoice_payment()->sum('pay_amount')) }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>SISA</th>
                                <th class="text-end"></th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->supplier_invoice_payment()->sum('amount_to_pay') - $model->supplier_invoice_payment()->sum('pay_amount')) }}
                                </th>
                                <th></th>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="col-md-3">
            {!! $authorization_log_view !!}
            <x-card-data-table title="{{ 'Status Logs' }}">
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
            <x-card-data-table title="{{ 'Data Log' }}">
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

@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse')
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#supplier-invoice-sidebar');
        sidebarActive('#supplier-invoice-general');
    </script>
@endsection
