@extends('layouts.admin.layout.index')

@php
    $title = 'pengembalian uang muka customer';
    $route = 'cash-advance-return-customer';
    $main = 'cash-advance-return';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

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
                        <a href="{{ route("admin.$route.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Detail $title") }}
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
                <x-card-data-table title='{{ "Detail $title" }}'>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('customer') }}</label>
                                    <p>{{ $model->reference?->nama ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Branch') }}</label>
                                    <p>{{ $model->branch?->name ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Project') }}</label>
                                    <p>{{ $model->project?->name ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('mata uang') }}</label>
                                    @if ($model->currency)
                                        <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}
                                        </p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nilai tukar') }}</label>
                                    <p class="text-end">{{ formatNumber($model->exchange_rate) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ cash_advance_return()[$model->status]['color'] }} text-wrap">
                                        {{ cash_advance_return()[$model->status]['label'] }} -
                                        {{ cash_advance_return()[$model->status]['text'] }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if (count($model->cashAdvancedReturnDetails))
                            <div class="mt-20">
                                <h4>{{ Str::headline('pengembalian uang muka') }}</h4>
                                <x-table theadColor='danger'>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('tanggal') }}</th>
                                        <th>{{ Str::headline('nomor transaksi') }}</th>
                                        <th>{{ Str::headline('nomor account') }}</th>
                                        <th>{{ Str::headline('mata uang / nilai tukar') }}</th>
                                        <th>{{ Str::headline('nilai') }}</th>
                                        <th>{{ Str::headline('outstanding amount') }}</th>
                                        <th>{{ Str::headline('nilai dikembalikan') }}</th>
                                        <th>{{ Str::headline('balance') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->cashAdvancedReturnDetails as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ localDate($item->date) }}</td>
                                                <td>{{ $item->reference->bank_code_mutation ?? $item->transaction_code }}</td>
                                                <td>{{ $item->coa?->account_code }} - {{ $item->coa?->name }}</td>
                                                <td class="text-end">{{ $item->currency?->nama }} /
                                                    {{ formatNumber($item->exchange_rate) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }}
                                                    {{ formatNumber($item->amount) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }}
                                                    {{ formatNumber($item->outstanding_amount) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }}
                                                    {{ formatNumber($item->amount_to_return) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }}
                                                    {{ formatNumber($item->balance) }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <th colspan="5" class="text-end">Total</th>
                                            <th class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnDetails->sum('amount')) }}</th>
                                            <th class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnDetails->sum('outstanding_amount')) }}
                                            </th>
                                            <th class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnDetails->sum('amount_to_return')) }}
                                            </th>
                                            <th class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnDetails->sum('balance')) }}</th>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        @endif

                        @if (count($model->cashAdvancedReturnInvoices))
                            <div class="mt-20">
                                <h4>{{ Str::headline('pengembalian uang muka Invoice') }}</h4>
                                <x-table theadColor='danger'>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('tanggal') }}</th>
                                        <th>{{ Str::headline('nomor transaksi') }}</th>
                                        <th>{{ Str::headline('mata uang / nilai tukar') }}</th>
                                        <th>{{ Str::headline('total invoice') }}</th>
                                        <th>{{ Str::headline('sisa invoice') }}</th>
                                        <th>{{ Str::headline('nilai dikembalikan') }}</th>
                                        <th>{{ Str::headline('selisih bayar') }}</th>
                                        <th>{{ Str::headline('exchage_rate_gap') }}</th>
                                        <th>{{ Str::headline('deskripsi') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->cashAdvancedReturnInvoices as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ localDate($item->date) }}</td>
                                                <td>{{ $item->transaction_code }}</td>
                                                <td class="text-end">{{ $item->currency?->nama }} / {{ formatNumber($item->exchange_rate ?? 0) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }} {{ formatNumber($item->reference->total ?? 0) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }} {{ formatNumber($item->outstanding_amount ?? 0) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }} {{ formatNumber($item->amount_to_paid_or_return ?? 0) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }} {{ formatNumber($item->outstanding_amount - $item->amount_to_paid_or_return ?? 0) }}</td>
                                                <td class="text-end">{{ $item->currency?->simbol }} {{ formatNumber($item->exchange_rate_gap ?? 0) }}</td>
                                                <td>{{ $item->description ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <th colspan="5" class="text-end">Total</th>
                                            <th class="text-end">{{ $model->invoice_currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnInvoices->sum('outstanding_amount')) }}
                                            </th>
                                            <th class="text-end">{{ $model->invoice_currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnInvoices->sum('amount_to_paid_or_return')) }}
                                            </th>
                                            <th></th>
                                            <th class="text-end">{{ $model->invoice_currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnInvoices->sum('exchange_rate_gap')) }}
                                            </th>
                                            <th></th>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        @endif

                        @if (count($model->cashAdvancedReturnTransactions))
                            <div class="mt-20">
                                <h4>{{ Str::headline('pengembalian uang muka transaksi lain') }}</h4>
                                <x-table theadColor='danger'>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('nomor account') }}</th>
                                        <th>{{ Str::headline('credit') }}</th>
                                        <th>{{ Str::headline('debit') }}</th>
                                        <th>{{ Str::headline('deskripsi') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->cashAdvancedReturnTransactions as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->coa?->account_code }} - {{ $item->coa?->name }}</td>
                                                <td class="text-end">{{ $model->currency?->simbol }}
                                                    {{ formatNumber($item->credit ?? 0) }}</td>
                                                <td class="text-end">{{ $model->currency?->simbol }}
                                                    {{ formatNumber($item->debit ?? 0) }}</td>
                                                <td>{{ $item->description ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <th colspan="2" class="text-end">Total</th>
                                            <th class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnTransactions->sum('credit')) }}</th>
                                            <th class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->cashAdvancedReturnTransactions->sum('debit')) }}</th>
                                            <th></th>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        @endif
                    </x-slot>
                    <x-slot name="footer">
                        {!! $auth_revert_void_button !!}
                        @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                            @can("edit $main")
                                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link="{{ route('admin.cash-advance-return-customer.edit', $model) }}" />
                            @endcan
                        @endif
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="invoice payment information">
                    <x-slot name="table_content">
                        <div class="d-flex flex-column">
                            <div class="mt-10">
                                <x-table id="invoice-payment-information">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('tanggal') }}</th>
                                        <th>{{ Str::headline('total') }}</th>
                                        <th>{{ Str::headline('bayar') }}</th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </div>
                        </div>
                    </x-slot>
                </x-card-data-table>
                @can('view journal')
                    @include('components.journal-table')
                @endcan
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
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse')
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#incoming-payment-sidebar');
        sidebarActive('#cash-advance-return-customer');
        const displayInvoicePaymentInformation = () => {
            $('#invoice-payment-information tbody').html('');
            $.ajax({
                url: `${base_url}/invoice-payment-information`,
                type: 'post',
                data: {
                    _token: token,
                    invoice_ids: JSON.parse('{!! $invoice_parent_ids !!}'),
                    date: '{{ $model->date }}'
                },
                success: function(response) {
                    $.each(response, function(key, value) {
                        $('#invoice-payment-information tbody').append(`
                                    <tr>
                                        <td colspan="5" class="bg-info">${value.code}</td>
                                    </tr>
                                    `);

                        $.each(value.payment_informations, function(key2, value2) {
                            $('#invoice-payment-information tbody').append(`
                                            <tr>
                                                <td>${key2 + 1}</td>
                                                <td>${localDate(value2.date)}</td>
                                                <td>${value2.currency.simbol} ${formatRupiahWithDecimal(value2.amount_to_receive)}</td>
                                                <td>${value2.currency.simbol} ${formatRupiahWithDecimal(value2.receive_amount)}</td>
                                                <td>${value2.note}</td>
                                            </tr>
                                            `);
                        });
                    })
                }
            });
        }

        displayInvoicePaymentInformation();
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\CashAdvancedReturn`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
