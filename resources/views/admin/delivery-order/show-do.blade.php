@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
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
                        <a href="{{ route('admin.delivery.index') }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.show", $model->so_trading_id) }}">{{ Str::headline("List $main") }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div>
        <div class="box bg-gradient-danger-dark text-white">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 align-self-center">
                        <h4 class="m-0">Detail Delivery Order</h4>
                        <h1 class="m-0">{{ $model->code }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status_delivery_order') }}</h5>
                                <div class="badge badge-lg badge-{{ get_delivery_order_status()[$model->status]['color'] }}">
                                    {{ Str::headline(get_delivery_order_status()[$model->status]['label']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <x-card-data-table title="{{ 'detail ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('customer') }}</label>
                                <p>{{ $model->so_trading->customer->nama }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('nomor_do') }}</label>
                                <p>{{ $model->code }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('nomor so') }}</label>
                                <p><a class="text-primary text-decoration-underline hover_text-dark" href='{{ route('admin.sales-order.show', $model->so_trading) }}' target="_blank">{{ $model->so_trading->nomor_so }}</a></p>
                            </div>
                        </div>
                        @if ($model->purchase_transport_double_handling)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nomor po double handling') }}</label>
                                    <p><a class="text-primary text-decoration-underline hover_text-dark" href='{{ route('admin.purchase-order-transport-double-handling.show', $model->purchase_transport_double_handling) }}' target="_blank">{{ $model->purchase_transport_double_handling->code }}</a></p>
                                </div>
                            </div>
                        @endif
                        @if ($model->item_receiving_report)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Ambil dari stock lpb') }}</label>
                                    <p>{{ $model->item_receiving_report->kode }} - {{ $model->item_receiving_report->item_receiving_report_po_trading->loading_order ?? '' }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($model->ware_house_id)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Ambil dari stock') }}</label>
                                    <p>{{ $model->ware_house?->nama }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($model->fleet)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kendaraan') }}</label>
                                    <p>{{ $model->fleet->name }} - {{ formatNumber($model->fleet->quantity) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($model->purchase_transport)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('purchase transport') }}</label>
                                    <p>
                                        <a href="{{ route('admin.purchase-order-transport.show', $model->purchase_transport) }}" target="_blank" rel="noopener noreferrer">
                                            {{ $model->purchase_transport->kode }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nomor do external') }}</label>
                                    <p>
                                        {{ $model->external_number }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nama driver') }}</label>
                                    <p>
                                        {{ $model->driver_name }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nomor hp driver ') }}</label>
                                    <p>
                                        {{ $model->driver_phone }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('informasi kendaran') }}</label>
                                    <p>
                                        {{ $model->vehicle_information }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        @if (!$model->purchase_transport)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('driver') }}</label>
                                    <p>
                                        {{ $model->employee?->name }} - {{ $model->employee?->NIK }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        @if ($model->delivery_order)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Do Pertama') }}</label>
                                    <p>
                                        <a href="{{ route('admin.delivery-order.list-delivery-order.show', ['delivery_order_id' => $model->delivery_order_id, 'sale_order_id' => $model->so_trading_id]) }}" target="_blank" rel="noopener noreferrer">{{ $model->delivery_order?->code }}</a>
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('sh No.') }}</label>
                                <p>
                                    {{ $model->sh_number->kode }}
                                </p>
                            </div>
                        </div>
                        @foreach ($model->sh_number->sh_number_details as $item)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline($item->type) }}</label>
                                    <p>
                                        {{ $item->alamat }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('target delivery') }}</label>
                                <p>
                                    {{ $model->target_delivery ? localDate($model->target_delivery) : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('tanggal muat') }}</label>
                                <p>
                                    {{ $model->load_date ? localDate($model->load_date) : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('kapasitas muat') }}</label>
                                <p>
                                    {{ formatNumber($model->load_quantity) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('kapasitas muat realisasi') }}</label>
                                <p>
                                    {{ formatNumber($model->load_quantity_realization) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('tanggal bongkar') }}</label>
                                <p>
                                    {{ $model->unload_date ? localDate($model->unload_date) : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('kapasitas bongkar realisasi') }}</label>
                                <p>
                                    {{ formatNumber($model->unload_quantity_realization) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        @if (is_null($model->delivery_order_id) && !is_null($model->purchase_transport_id) && $model->type == 'delivery-order-2')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kuantitas digunakan') }}</label>
                                    <p>
                                        {{ formatNumber($model->quantity_used) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        @if (is_null($model->delivery_order_id) && !is_null($model->purchase_transport_id) && $model->type == 'delivery-order-2' and $model->status == 'approve')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kuantitas hilang') }}</label>
                                    <p>
                                        {{ formatNumber($model->load_quantity_realization - $model->unload_quantity_realizatio) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('status') }}</label>
                                <p>
                                <div class="badge badge-lg badge-{{ get_delivery_order_status()[$model->status]['color'] }} mb-1">
                                    {{ get_delivery_order_status()[$model->status]['label'] }} -
                                    {{ get_delivery_order_status()[$model->status]['text'] }}
                                </div>

                                </p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('deskripsi') }}</label>
                                <p>
                                    {{ $model->description }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('segel atas') }}</label>
                                <p>
                                    {{ $model->top_seal }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('segel bawah') }}</label>
                                <p>
                                    {{ $model->bottom_seal }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('temperatur') }}</label>
                                <p>
                                    {{ $model->temperature }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('meter awal') }}</label>
                                <p>
                                    {{ $model->initial_meter }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('meter akhir') }}</label>
                                <p>
                                    {{ $model->initial_final }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('sg_meter') }}</label>
                                <p>
                                    {{ $model->sg_meter }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('file') }}</label>
                                <p>
                                    @if ($model->file)
                                        <x-button type="button" color="info" label="file" size="sm" icon="file" label="view_file" link='{{ url("storage/$model->file") }}' fontawesome target="_blank" />
                                    @else
                                        <x-button badge color="danger" icon="eye-slash" size="sm" label="file not available" fontawesome />
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('created_at') }}</label>
                                <p>
                                    {{ toDayDateTimeString($model->created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('last medified') }}</label>
                                <p>
                                    {{ toDayDateTimeString($model->updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
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
                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                        @if (!in_array($model->status, ['reject', 'void', 'cancel', 'done']) && $model->check_available_date && $is_can_edit_data)
                            <x-button color='warning' fontawesome icon="edit" label="Check" class="w-auto" size="sm" link="{{ route('admin.delivery-order.list-delivery-order.edit', ['delivery_order_id' => $model->id, 'sale_order_id' => $model->so_trading_id]) }}" />
                        @endif
                    </div>
                </x-slot>

            </x-card-data-table>

            @can('view journal')
                @include('components.journal-table')
            @endcan
        </div>
        <div class="col-md-4">
            @if ($model->check_available_date)
                {!! $authorization_log_view !!}
            @endif

            <div id="print-request-container"></div>

            <x-card-data-table title="action">
                <x-slot name="table_content">
                    @if ($model->check_available_date)
                        @if ($model->status == 'approve')
                            @can("close $main")
                                <x-button size="md" class="py-2 px-3" color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                <x-modal title="close delivery order" id="close-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="done">
                                            <div class="mt-10">
                                                <div class="form-group">
                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                </div>
                                            </div>
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="close" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                            @if (!$is_has_delivery_order)
                                @can("void $main")
                                    <x-button size="md" class="py-2 px-3" color="danger" icon="circle-xmark" fontawesome label="void" size="sm" dataToggle="modal" dataTarget="#void-modal" />
                                    <x-modal title="close delivery order" id="void-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="void">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="void" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        @endif
                        @if ($model->status == 'done' && $is_can_edit_data)
                            @can("close $main")
                                <x-button size="md" class="py-2 px-3" color="success" icon="circle-xmark" fontawesome label="kembalikan ke approve" size="sm" dataToggle="modal" dataTarget="#back-to-approve-modal" />
                                <x-modal title="Kembalikan ke approve" id="back-to-approve-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="approve">
                                            <div class="mt-10">
                                                <div class="form-group">
                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                </div>
                                            </div>
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="close" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                        @endif

                        @can("approve $main")
                            @if ($model->status == 'submitted')
                                <x-button size="md" class="py-2 px-3" color="success" icon="check" fontawesome label="approve submitted" dataToggle="modal" dataTarget="#approve-submitted-modal" />
                                <x-modal title="approve-submitted delivery order" id="approve-submitted-modal" headerColor="warning">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="done">
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="approve" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>

                                <x-button size="md" class="py-2 px-3" color="dark" icon="x" fontawesome label="reject submitted" size="sm" dataToggle="modal" dataTarget="#reject-submitted-modal" />
                                <x-modal title="reject-submitted delivery order" id="reject-submitted-modal" headerColor="warning">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="submit-rejected">
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="reject" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endif
                        @endcan

                        @if ($model->status == 'request-print')
                            @can("approve $main")
                                <x-button size="md" class="py-2 px-3" color="success" icon="check" fontawesome label="approve-request-print" dataToggle="modal" dataTarget="#approve-request-print-modal" />
                                <x-modal title="approve-request-print delivery order" id="approve-request-print-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.approve-print-request", ['sale_order_id' => $model->so_trading, 'id' => $model]) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="approve-request-print">

                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                            @can("reject $main")
                                <x-button size="md" class="py-2 px-3" color="danger" icon="print" fontawesome label="reject print" size="sm" dataToggle="modal" dataTarget="#print-modal-reject-{{ $model->id }}" />
                                <x-modal title="reject request print" id="print-modal-reject-{{ $model->id }}" headerColor="danger">
                                    <x-slot name="modal_body">
                                        <form action='{{ route('admin.delivery-order.approve-print-request', ['sale_order_id' => $model->so_trading, 'id' => $model]) }}' method="post">
                                            @csrf

                                            <input type="hidden" name="status" value="reject-request-print">
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
                    <button target="_blank" class="btn btn-info" href="{{ route('delivery-order.export.id', ['id' => encryptId($model->id)]) }}" onclick="showPrintOption(event)" @authorize_print('delivery_order_trading') data-model="{{ \App\Models\DeliveryOrder::class }}" data-id="{{ $model->id }}" data-print-type="delivery_order_trading" data-link="{{ route('admin.delivery-order.show', ['delivery_order' => $model->id]) }}" data-code="{{ $model->code }}" @endauthorize_print><i class="fa fa-file"></i> Export</button>
                </x-slot>
            </x-card-data-table>

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
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
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
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '') }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order');
        $('body').addClass('sidebar-collapse');

        $('#history-button').on('click', function() {
            $.ajax({
                url: '{{ route("admin.$main.history", $model->id) }}',
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


        get_request_print_approval(`App\\Models\\DeliveryOrder`, '{{ $model->id }}', 'delivery_order_trading');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\DeliveryOrder`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
