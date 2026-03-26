@extends('layouts.admin.layout.index')

@php
    $main = 'cash-advance-payment';
    $menu = 'pembayaran deposit';
@endphp

@section('title', Str::headline("detail $menu") . ' - ')

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
                        {{ Str::headline('Detail ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'Detail ' . $menu }}">
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Tanggal Bayar</label>
                                        <p>{{ localDate($model->date) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">No Bukti</label>
                                        <p>{{ $model->bank_code_mutation }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Branch</label>
                                        <p>{{ $model->branch->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Bayar Ke</label>
                                        <p>{{ $model->to_name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">No. Purchase Order</label>
                                        <p>{{ $model->purchase?->kode }}</p>
                                    </div>
                                </div>
                                @if ($model->change_reason_bank)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Alasan ganti bank</label>
                                            <p>{{ $model->change_reason_bank }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Tanggal Pengajuan Dana</label>
                                        <p>{{ localDate($model->fund_submission->date) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">No. Pengajuan Dana</label>
                                        <br>
                                        <a href="{{ route('admin.fund-submission.show', ['fund_submission' => $model->fund_submission_id]) }}" target="_blank">{{ $model->fund_submission->code }}</a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Currency</label>
                                        <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Kurs</label>
                                        <p>{{ formatNumber($model->exchange_rate) }}</p>
                                    </div>
                                </div>
                                @if ($model->tax)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="form-label">Pajak</label>
                                            <p>{{ $model->tax->tax_name_with_percent }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if (!$model->tax_number)
                                    <div class="col-md-12 bg-light rounded mb-3">
                                        <form action="{{ route('admin.cash-advance-payment.update-tax', $model) }}" enctype="multipart/form-data" method="POST">
                                            @csrf
                                            <div class="row my-3">
                                                <div class="col-md-4">
                                                    <x-input type="text" label="pajak" name="tax_number" id="tax_number" value="" class="tax-reference-mask" />
                                                </div>
                                                <div class="col-md-4">
                                                    <x-input type="file" label="lampiran" name="tax_attachment" id="tax_attachment" />
                                                </div>
                                                <div class="col d-flex align-items-end">
                                                    <x-button color="danger" label="Update Pajak" class="btn-sm" type="submit" />
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    @if ($model->tax_number)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="" class="form-label">Faktur Pajak</label>
                                                <p>{{ $model->tax_number }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($model->tax_attachment)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="" class="form-label">Lampiran Pajak</label>
                                                <br>
                                                <a href="{{ asset('storage/' . $model->tax_attachment) }}" target="_blank">Lihat Lampiran</a>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <div class="col-md-12"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Note</label>
                                        <p>{{ $model->reference }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Project</label>
                                        <p>{{ $model->project->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                        </div>
                        <div class="col-md-6">
                            @if ($model->fund_submission->send_payment ?? null)
                                @include('admin.fund-submission.__giro_table', [
                                    'send_payment' => $model->fund_submission->send_payment,
                                ])
                            @endif
                        </div>
                        <div class="col-md-12 mt-15">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="bg-info">
                                        <tr>
                                            <th>Akun</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Kredit</th>
                                            @if (!$model->currency->is_local)
                                                <th class="text-end">Debit</th>
                                                <th class="text-end">Kredit</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($model->cash_advance_payment_details as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->coa->name }}
                                                </td>
                                                <td>
                                                    {{ $item->note }}
                                                </td>
                                                <td class="text-end">
                                                    {{ $model->currency->simbol }} {{ formatNumber($item->debit) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ $model->currency->simbol }} {{ formatNumber($item->credit) }}
                                                </td>
                                                @if (!$model->currency->is_local)
                                                    <td class="text-end">
                                                        {{ get_local_currency()->simbol }}
                                                        {{ formatNumber($item->debit * $model->exchange_rate) }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ get_local_currency()->simbol }}
                                                        {{ formatNumber($item->credit * $model->exchange_rate) }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th class="text-end">TOTAL</th>
                                            <th class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->cash_advance_payment_details()->sum('debit')) }}</th>
                                            <th class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($model->cash_advance_payment_details()->sum('credit')) }}</th>
                                            @if (!$model->currency->is_local)
                                                <th class="text-end">{{ get_local_currency()->simbol }}
                                                    {{ formatNumber($model->cash_advance_debit_total) }}</th>
                                                <th class="text-end">{{ get_local_currency()->simbol }}
                                                    {{ formatNumber($model->cash_advance_credit_total) }}</th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link="{{ route('admin.outgoing-payment.index') }}?tab=deposite" />
                            @if ($model->status != 'approve' && $model->status != 'reject' && $model->status != 'void')
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan
                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
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
            <x-card-data-table title="{{ 'Action' }}">
                <x-slot name="table_content">

                </x-slot>
            </x-card-data-table>
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
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        initMaskTaxReference();
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\CashAdvancePayment`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
