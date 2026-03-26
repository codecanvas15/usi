@extends('layouts.admin.layout.index')

@php
    $main = 'outgoing-payment';
    $menu = 'pengeluaran dana';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $menu) }}
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
                <x-card-data-table title="{{ 'detail ' . $menu }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <div class="row">
                            <div class="col-md-12">
                                <x-table theadColor='danger'>
                                    <x-slot name="table_head">
                                        <th></th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <td>{{ $model->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('branch') }}</th>
                                            <td>{{ $model->branch->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('tanggal') }}</th>
                                            <td>{{ localDate($model->date) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('sumber') }}</th>
                                            <td>
                                                {{ $model->fund_submission ? 'Pengajuan Dana' : 'General' }}
                                                @if ($model->fund_submission)
                                                    / <a href="{{ route('admin.fund-submission.show', ['fund_submission' => $model->fund_submission_id]) }}" target="_blank"> {{ $model->fund_submission->code }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('kepada') }}</th>
                                            <td>{{ $model->to_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('kas/bank') }}</th>
                                            <td>{{ $model->coa->account_code }} - {{ $model->coa->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('no bukti') }}</th>
                                            <td>{{ $model->bank_code_mutation }}</td>
                                        </tr>

                                        @if ($model->invoice_return)
                                            <tr>
                                                <th>{{ Str::headline('pengembalian retur') }}</th>
                                                <td>{{ $model->invoice_return->code }}</td>
                                            </tr>
                                        @endif
                                        @if ($model->cash_advance_receive)
                                            <tr>
                                                <th>{{ Str::headline('pengembalian uang muka') }}</th>
                                                <td>{{ $model->cash_advance_receive->code }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>{{ Str::headline('currency') }}</th>
                                            <td>{{ $model->currency->kode }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('kurs') }}</th>
                                            <td>{{ floatDotFormat($model->exchange_rate) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('project') }}</th>
                                            <td>{{ $model->project->code ?? '' }} - {{ $model->project->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('note') }}</th>
                                            <td>{{ $model->reference }}</td>
                                        </tr>
                                        @if ($model->change_bank_reason)
                                            <tr>
                                                <th>{{ Str::headline('alasan ganti bank') }}</th>
                                                <td>{{ $model->change_bank_reason }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>{{ Str::headline('status') }}</th>
                                            <th>
                                                <div class="badge badge-lg badge-{{ incoming_payment_status()[$model->status]['color'] }}">
                                                    {{ Str::headline(incoming_payment_status()[$model->status]['text']) }} -
                                                    {{ Str::headline(incoming_payment_status()[$model->status]['label']) }}
                                                </div>
                                            </th>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                            <div class="col-md-6">
                                @if ($model->fund_submission->send_payment ?? null)
                                    @include('admin.fund-submission.__giro_table', [
                                        'send_payment' => $model->fund_submission->send_payment,
                                    ])
                                @endif
                            </div>
                            <div class="col-md-12">
                                <x-table theadColor='dark'>
                                    <x-slot name="table_head">
                                        <th>Akun</th>
                                        <th>Keterangan</th>
                                        <th class="text-end {{ !$model->currency->is_local ? 'bg-info' : '' }}">Jumlah
                                            {{ $model->currency->kode }}</th>
                                        @if (!$model->currency->is_local)
                                            <th class="text-end">Jumlah {{ get_local_currency()->kode }}</th>
                                        @endif
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->outgoing_payment_details ?? [] as $outgoing_payment_detail)
                                            <tr>
                                                <th>{{ $outgoing_payment_detail->coa->account_code }} -
                                                    {{ Str::headline($outgoing_payment_detail->coa->name) }}</th>
                                                <td>{{ $outgoing_payment_detail->note }}</td>
                                                <td class="text-end"> {{ $model->currency->simbol }}
                                                    {{ floatDotFormat($outgoing_payment_detail->debit) }}</td>
                                                @if (!$model->currency->is_local)
                                                    <td class="text-end">{{ get_local_currency()->simbol }}
                                                        {{ floatDotFormat($outgoing_payment_detail->debit_local) }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <th>TOTAL</th>
                                            <th></th>
                                            <th class="text-end">{{ $model->currency->simbol }}
                                                {{ floatDotFormat($model->debit_total) }}</th>
                                            @if (!$model->currency->is_local)
                                                <th class="text-end">{{ get_local_currency()->simbol }}
                                                    {{ floatDotFormat($model->local_debit_total) }}</th>
                                            @endif
                                        </tr>
                                    </x-slot>

                                </x-table>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan
                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
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
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment')
    </script>

    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\OutgoingPayment`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
