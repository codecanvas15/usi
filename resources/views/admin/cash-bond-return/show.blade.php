@extends('layouts.admin.layout.index')

@php
    $main = 'cash-bond-return';
    $title = 'pengembalian kasbon';
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
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('karyawan') }}</label>
                                    <p>{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('project') }}</label>
                                    <p>{{ $model->project?->code ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('currency / kurs') }}</label>
                                    @if ($model->currency)
                                        <p>{{ $model->currency->kode . ' - ' . $model->currency->nama . ' - ' . $model->currency->negara }}
                                            / {{ formatNumber($model->exchange_rate) }}</p>
                                    @else
                                        <p>- / {{ formatNumber($model->exchange_rate) }} </p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('account kas / bank') }}</label>
                                    <p>
                                        {{ $model->coa?->account_code }} - {{ $model->coa?->name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ cash_bond_return_status()[$model->status]['color'] }}">
                                        {{ cash_bond_return_status()[$model->status]['label'] }} -
                                        {{ cash_bond_return_status()[$model->status]['text'] }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <h4>{{ Str::headline('pengembalian cash bond') }}</h4>
                            <x-table>
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('nomor transaksi') }}</th>
                                    <th>{{ Str::headline('nomor account') }}</th>
                                    <th>{{ Str::headline('currenct / kurs') }}</th>
                                    <th>{{ Str::headline('nilai') }}</th>
                                    <th>{{ Str::headline('nilai dikembalikan') }}</th>
                                    <th>{{ Str::headline('outstanding') }}</th>
                                    <th>{{ Str::headline('balance') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->cashBondReturnDetails as $cashBondReturnDetail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ localDate($cashBondReturnDetail->date) }}</td>
                                            <td>{{ $cashBondReturnDetail->cash_bond->bank_code_mutation }}</td>
                                            <td>{{ $cashBondReturnDetail->coa?->account_code }} -
                                                {{ $cashBondReturnDetail->coa?->name }}</td>
                                            <td>{{ $cashBondReturnDetail->currency?->nama }} /
                                                {{ formatNumber($cashBondReturnDetail->exchange_rate) }}</td>
                                            <td>{{ $cashBondReturnDetail->currency?->simbol }}
                                                {{ formatNumber($cashBondReturnDetail->amount) }}</td>
                                            <td>{{ $cashBondReturnDetail->currency?->simbol }}
                                                {{ formatNumber($cashBondReturnDetail->amount_to_return) }}</td>
                                            <td>{{ $cashBondReturnDetail->currency?->simbol }}
                                                {{ formatNumber($cashBondReturnDetail->outstanding_amount) }}</td>
                                            <td>{{ $cashBondReturnDetail->currency?->simbol }}
                                                {{ formatNumber($cashBondReturnDetail->balance) }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>

                        @if ($model->cashBondReturnOthers)
                            <div class="mt-10">
                                <h4>{{ Str::headline('adjustment') }}</h4>
                                <x-table>
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('nomor akun') }}</th>
                                        <th>{{ Str::headline('amount') }}</th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->cashBondReturnOthers as $cashBondReturnOther)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $cashBondReturnOther->coa?->account_code }} -
                                                    {{ $cashBondReturnOther->coa?->name }}</td>
                                                <td>{{ $model->currency?->simbol }}
                                                    {{ formatNumber($cashBondReturnOther->amount) }}</td>
                                                <td>{{ $cashBondReturnOther->description }}</td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                </x-table>
                            </div>
                        @endif

                    </x-slot>

                    <x-slot name="footer">
                        {!! $auth_revert_void_button !!}
                    </x-slot>

                </x-card-data-table>

                @can('view journal')
                    @include('components.journal-table')
                @endcan
            </div>
            <div class="col-md-3">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
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
        sidebarMenuOpen('#cash-bond-sidebar');
        sidebarActive('#cash-bond-return')
    </script>

    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\CashBondReturn`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
