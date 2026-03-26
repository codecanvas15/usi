@extends('layouts.admin.layout.index')

@php
    $main = 'receive-payment';
    $menu = 'giro masuk';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($menu) }}</a>
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
                            <div class="form-group">
                                <h3 for="">No. <span>{{ $model->code }}</span></h3>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Branch</label>
                                <p>{{ $model->branch->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Terima Dari</label>
                                <p>{{ $model->customer->nama ?? $model->from_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Tanggal</label>
                                <p>{{ localDate($model->date) }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Tanggal Jatuh Tempo</label>
                                <p>{{ localDate($model->due_date) }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Tanggal Cair</label>
                                <p>{{ $model->realization_date ? localDate($model->realization_date) : '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Curreny - Rate</label>
                                <p>{{ $model->currency->kode }} - {{ formatNumber($model->exchange_rate) }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Jumlah</label>
                                <p>{{ formatNumber($model->amount) }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">No Cheque</label>
                                <p class="text-uppercase">{{ $model->cheque_no }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">BG Mundur Bank</label>
                                <p class="text-uppercase">{{ $model->from_bank }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="form-label">Bank Pencairan</label>
                                <p class="text-uppercase">{{ $model->realization_bank }}</p>
                            </div>
                        </div>
                        <div class="col-md-12 mb-10">
                            <div class="form-group">
                                <label for="" class="form-label">Status</label>
                                <br>
                                <div class="badge badge-lg badge-{{ fund_submission_status()[$model->status]['color'] }} my-10">
                                    {{ fund_submission_status()[$model->status]['label'] }} -
                                    {{ fund_submission_status()[$model->status]['text'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if ($model->check_available_date)
                                @if (!in_array($model->status, ['approve', 'reject', 'cancel']))
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
        </div>
        <div class="col-md-3">
            {!! $authorization_log_view !!}
            <x-card-data-table title="{{ 'Action' }}">
                <x-slot name="table_content">
                    @if ($model->check_available_date)
                        @if ($model->status == 'approve' && $model->status != 'cancel')
                            @can("cancel $main")
                                <x-button color="warning" icon="ban " fontawesome label="batal cair" size="sm" dataToggle="modal" dataTarget="#cancel-modal" />
                                <x-modal title="{{ $menu }} tidak cair" id="cancel-modal" headerColor="danger">
                                    <x-slot name="modal_body">
                                        <h4>Pembayaran yang menggunakan Giro ini akan dibatalkan!</h4>
                                        <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="cancel">
                                            <div class="mt-10">
                                                <div class="form-group">
                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                </div>
                                            </div>
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="simpan" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                        @endif
                        @if ($model->status == 'approve')
                            @if (count($model->receivables_payments) != 0 || count($model->incoming_payments) != 0)
                                <div class="badge badge-lg badge-success">
                                    <i class="fa fa-check"></i> sudah dibayarkan di AR / Penerimaan Dana
                                </div>
                            @endif
                        @endif
                    @endif
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
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#receive-payment');
    </script>
@endsection
