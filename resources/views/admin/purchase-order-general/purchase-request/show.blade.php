@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-general';
    $title = Str::headline('Purchase Order general');
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
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline('purchase') }}</a>
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
    @can('view purchase-general')
        <div>
            <div class="box bg-gradient-success-dark text-white">
                <div class="box-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Purchase Order General</h4>
                            <h1 class="m-0">{{ $model->code }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_PO_general') }}</h5>
                                    <div class="badge badge-lg badge-{{ purchase_order_general_status()[$model->status]['color'] }}">
                                        {{ Str::headline(purchase_order_general_status()[$model->status]['label']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <x-card-data-table :title='"Detail $main"'>
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('branch') }}</label>
                                    <p>{{ $model->branch?->name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>
                            @if ($projects)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('project') }}</label>
                                        <br>
                                        @foreach ($projects ?? [] as $project)
                                            <a href="{{ route('admin.project.show', $project) }}">{{ $project->name }}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('vendor') }}</label>
                                    <p>{{ $model->vendor->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('mata uang / nilai tukar') }}</label>
                                    <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}
                                        / {{ formatNumber($model->exchange_rate) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('quotation') }}</label>
                                    <p>
                                        @if (is_null($model->quotation))
                                            <x-button color="danger" icon="eye-slash" fontawesome label="no file" badge size="sm" />
                                        @else
                                            <x-button color="info" icon="file" fontawesome :link="url('storage/' . $model->quotation)" size="sm" target="blank" />
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('term of payment') }}</label>
                                    <p>{{ $model->term_of_payment }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('term_of_payment day') }}</label>
                                    <p>{{ $model->term_of_payment_days }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ purchase_order_general_status()[$model->status]['color'] }} mb-1 text-wrap">
                                        {{ purchase_order_general_status()[$model->status]['label'] }} -
                                        {{ purchase_order_general_status()[$model->status]['text'] }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('dibuat pada') }}</label>
                                    <p>{{ toDayDateTimeString($model->created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button type="button" color='primary' fontawesome icon="history" label="riwayat transaksi" class="w-auto" size="sm" id="history-button" />
                            <x-modal title="riwayat transaksi" id="history-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Transaksi</th>
                                                    <th>Nomor</th>
                                                </tr>
                                            </thead>
                                            <tbody id="history-list">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-10 border-top pt-10">
                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                    </div>
                                </x-slot>
                            </x-modal>
                        </div>
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="Dari purcase order general">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Item') }}</th>
                                <th>{{ Str::headline('Harga Sebelum Diskon') }}</th>
                                <th>{{ Str::headline('Diskon') }}</th>
                                <th>{{ Str::headline('Harga') }}</th>
                                <th>{{ Str::headline('Qty') }}</th>
                                <th>{{ Str::headline('Qty diterima') }}</th>
                                <th>{{ Str::headline('Sub total') }}</th>
                                <th>{{ Str::headline('Tax') }}</th>
                                <th>{{ Str::headline('Value') }}</th>
                                <th>{{ Str::headline('Total') }}</th>
                                <th>{{ Str::headline('') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @php
                                    $key = 0;
                                @endphp
                                @foreach ($model->purchaseOrderGeneralDetails()->where('type', 'main')->get() as $key => $purchaseOrderGeneralDetail)
                                    @foreach ($purchaseOrderGeneralDetail->purchase_order_general_detail_items as $purchase_order_general_detail_item)
                                        <tr>
                                            <td>{{ $key += 1 }}</td>
                                            <td>{{ $purchase_order_general_detail_item?->item?->kode }} -
                                                {{ $purchase_order_general_detail_item?->item?->nama }}</td>
                                            <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($purchase_order_general_detail_item->price_before_discount) }}</td>
                                            <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($purchase_order_general_detail_item->discount) }}</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($purchase_order_general_detail_item->price) }} /
                                                {{ $purchase_order_general_detail_item->unit?->name }}</td>
                                            <td>{{ formatNumber($purchase_order_general_detail_item->quantity) }}
                                                {{ $purchase_order_general_detail_item->unit->name }}</td>
                                            <td>{{ formatNumber($purchase_order_general_detail_item->quantity_received) }}
                                                {{ $purchase_order_general_detail_item->unit->name }}</td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($purchase_order_general_detail_item->sub_total) }}</td>
                                            <td>
                                                @foreach ($purchase_order_general_detail_item->purchase_order_general_detail_item_taxes as $purchase_order_general_detail_item_tax)
                                                    <p class="my-0">{{ $purchase_order_general_detail_item_tax->tax->name }}
                                                        - {{ $purchase_order_general_detail_item_tax->value * 100 }}%</p>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($purchase_order_general_detail_item->purchase_order_general_detail_item_taxes as $purchase_order_general_detail_item_tax)
                                                    <p class="mb-0 text-end">{{ $model->currency->simbol }}
                                                        {{ formatNumber($purchase_order_general_detail_item_tax->total) }}</p>
                                                @endforeach
                                            </td>
                                            <td class="text-end">{{ $model->currency->simbol }}
                                                {{ formatNumber($purchase_order_general_detail_item->total) }}</td>
                                            <td>
                                                <x-button color="info" icon="eye" fontawesome size="sm" dataToggle="modal" dataTarget="#purchase-order-general-item-{{ $purchase_order_general_detail_item->id }}-modal" />

                                                <x-modal title="Detail Data" id="purchase-order-general-item-{{ $purchase_order_general_detail_item->id }}-modal" headerColor="info" modalSize="1000">
                                                    <x-slot name="modal_body">
                                                        <div class="row">

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">{{ Str::headline('kode') }}</label>
                                                                    <p>{{ $purchaseOrderGeneralDetail->purchase_request->kode }}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                                                    <p>{{ $purchaseOrderGeneralDetail->purchase_request->tanggal }}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">{{ Str::headline('divisi') }}</label>
                                                                    <p>{{ $purchaseOrderGeneralDetail->purchase_request->division?->name }}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="">{{ Str::headline('dibuat oleh') }}</label>
                                                                    <p>{{ $purchaseOrderGeneralDetail->purchase_request->created_by_user?->email }}
                                                                        -
                                                                        {{ $purchaseOrderGeneralDetail->purchase_request->created_by_user?->name }}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        @php
                                                            $status_logs = $purchase_order_general_detail_item->logs_data['status_logs'] ?? [];
                                                            $activity_logs = $purchase_order_general_detail_item->logs_data['activity_logs'] ?? [];
                                                        @endphp

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <ul class="list-group">
                                                                    @foreach ($status_logs as $item)
                                                                        <li class="list-group-item">
                                                                            <h5 class="fw-bold mb-0">From
                                                                                {{ Str::headline($item->from_status) }} To
                                                                                {{ Str::headline($item->to_status) }}</h5>
                                                                            <p class="mb-0">{{ Str::title($item->message) }}
                                                                            </p>
                                                                            <small class="text-secondary">{{ Str::headline($item->user?->name) }}
                                                                                -
                                                                                {{ toDayDateTimeString($item->created_at) }}</small>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <ul class="list-group">
                                                                    @foreach ($activity_logs as $item)
                                                                        <li class="list-group-item">
                                                                            <h5 class="fw-bold mb-0">
                                                                                {{ Str::headline($item->event) }}</h5>
                                                                            <p class="mb-0">
                                                                                {{ Str::title($item->description) }}</p>
                                                                            <small class="text-secondary">{{ Str::headline($item->user?->name) }}
                                                                                -
                                                                                {{ toDayDateTimeString($item->created_at) }}</small>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>

                                                    </x-slot>
                                                </x-modal>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <td class="fw-bold text-end" colspan="10">Total</td>
                                    <td class="bg-success text-white text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($model->total_main) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>

                    </x-slot>
                </x-card-data-table>

                @if ($model->purchaseOrderGeneralDetails()->where('type', 'additional')->count())
                    <x-card-data-table title="Additional item">
                        <x-slot name="header_content">

                        </x-slot>
                        <x-slot name="table_content">

                            <x-table>
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('Item') }}</th>
                                    <th>{{ Str::headline('Harga') }}</th>
                                    <th>{{ Str::headline('Qty') }}</th>
                                    <th>{{ Str::headline('Sub total') }}</th>
                                    <th>{{ Str::headline('Tax') }}</th>
                                    <th>{{ Str::headline('Value') }}</th>
                                    <th>{{ Str::headline('Total') }}</th>
                                    <th>{{ Str::headline('status') }}</th>
                                    <th>{{ Str::headline('') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @php
                                        $key = 0;
                                    @endphp
                                    @foreach ($model->purchaseOrderGeneralDetails()->where('type', 'additional')->get() as $purchaseOrderGeneralDetail)
                                        @foreach ($purchaseOrderGeneralDetail->purchase_order_general_detail_items as $purchase_order_general_detail_item)
                                            <tr>
                                                <td>{{ $key += 1 }}</td>
                                                <td>{{ $purchase_order_general_detail_item?->item?->kode }} -
                                                    {{ $purchase_order_general_detail_item?->item?->nama }}</td>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($purchase_order_general_detail_item->price) }} /
                                                    {{ $purchase_order_general_detail_item->unit?->name }}</td>
                                                <td>{{ formatNumber($purchase_order_general_detail_item->quantity) }}
                                                    {{ $purchase_order_general_detail_item->unit->name }}</td>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($purchase_order_general_detail_item->sub_total) }}</td>
                                                <td>
                                                    @foreach ($purchase_order_general_detail_item->purchase_order_general_detail_item_taxes as $purchase_order_general_detail_item_tax)
                                                        <p class="my-0">
                                                            {{ $purchase_order_general_detail_item_tax->tax->name }} -
                                                            {{ $purchase_order_general_detail_item_tax->value * 100 }}%</p>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($purchase_order_general_detail_item->purchase_order_general_detail_item_taxes as $purchase_order_general_detail_item_tax)
                                                        <p class="mb-0 text-end">{{ $model->currency->simbol }}
                                                            {{ formatNumber($purchase_order_general_detail_item_tax->total) }}
                                                        </p>
                                                    @endforeach
                                                </td>
                                                <td class="text-end">{{ $model->currency->simbol }}
                                                    {{ formatNumber($purchase_order_general_detail_item->total) }}</td>
                                                <td>

                                                </td>
                                                <td>
                                                    <x-button color="info" icon="eye" fontawesome size="sm" dataToggle="modal" dataTarget="#purchase-order-general-item-{{ $purchase_order_general_detail_item->id }}-modal" />

                                                    <x-modal title="Detail Data" id="purchase-order-general-item-{{ $purchase_order_general_detail_item->id }}-modal" headerColor="info" modalSize="1000">
                                                        <x-slot name="modal_body">
                                                            @php
                                                                $status_logs = $purchase_order_general_detail_item->logs_data['status_logs'] ?? [];
                                                                $activity_logs = $purchase_order_general_detail_item->logs_data['activity_logs'] ?? [];
                                                            @endphp

                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <ul class="list-group">
                                                                        @foreach ($status_logs as $item)
                                                                            <li class="list-group-item">
                                                                                <h5 class="fw-bold mb-0">From
                                                                                    {{ Str::headline($item->from_status) }} To
                                                                                    {{ Str::headline($item->to_status) }}</h5>
                                                                                <p class="mb-0">
                                                                                    {{ Str::title($item->message) }}</p>
                                                                                <small class="text-secondary">{{ Str::headline($item->user?->name) }}
                                                                                    -
                                                                                    {{ toDayDateTimeString($item->created_at) }}</small>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <ul class="list-group">
                                                                        @foreach ($activity_logs as $item)
                                                                            <li class="list-group-item">
                                                                                <h5 class="fw-bold mb-0">
                                                                                    {{ Str::headline($item->event) }}</h5>
                                                                                <p class="mb-0">
                                                                                    {{ Str::title($item->description) }}</p>
                                                                                <small class="text-secondary">{{ Str::headline($item->user?->name) }}
                                                                                    -
                                                                                    {{ toDayDateTimeString($item->created_at) }}</small>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </x-slot>
                                                    </x-modal>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <td class="fw-bold text-end" colspan="7">Total</td>
                                        <td class="bg-success text-white text-end">{{ $model->currency->simbol }}
                                            {{ formatNumber($model->total_additional) }}</td>
                                    </tr>
                                </x-slot>
                            </x-table>

                        </x-slot>
                    </x-card-data-table>
                @endif
            </div>

            <div class="col-md-3">
                {!! $authorization_log_view !!}

                <div id="print-request-container"></div>
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        @if ($model->check_available_date)
                            @if (in_array($model->status, ['partial', 'approve']))
                                @can('close purchase-general')
                                    <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                    <x-modal title="close purcase order general" id="close-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="close">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <x-input type="text" name="close_note" label="Message" placeholder="Message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        @endif
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table>
                    <x-slot name="table_content">
                        {{-- <x-button type="button" color="info" label="Export" target="_blank" icon="file" fontawesome soft block size="md" link="{{ route($main . '.export-pdf', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" /> --}}
                        <x-button-auth-print type="purchase_order_general" model="{{ \App\Models\PurchaseOrderGeneral::class }}" did="{{ $model->id }}" href="{{ route($main . '.export-pdf', ['id' => encryptId($model->id)]) }}" code="{{ $model->code }}" label="Export" link="" />
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($parent_status_logs as $item)
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
                            @foreach ($parent_activity_logs as $item)
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
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.purchase-order-general.history', $model->id) }}`,
                success: function({
                    data
                }) {
                    $('#history-list').html('');
                    $.each(data, function(key, value) {
                        let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                        $('#history-list').append(`
                                <tr>
                                    <td>${localDate(value.date)}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                    });

                    $('#history-modal').modal('show');
                }
            });
        });

        get_request_print_approval(`App\\Models\\PurchaseOrderGeneral`, '{{ $model->id }}', 'purchase_order_general');
    </script>
@endsection
