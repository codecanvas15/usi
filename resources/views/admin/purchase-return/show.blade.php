@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-return';
    $title = 'retur pembelian';
@endphp

@section('title', Str::headline("detail $title") . ' - ')

@section('breadcrumbs')
    <div>
        <div class="box text-white" style="background-image: linear-gradient(90deg, rgb(74, 74, 74), skyblue)">
            <div class="box-body">
                <div class="row justify-content-end">
                    <div class="col-md-6 align-self-center">
                        <h4 class="m-0">Detail Purchase Return</h4>
                        <h1 class="m-0">{{ $model->code }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status_' . $title) }}</h5>
                                <div class="badge badge-lg badge-{{ fund_submission_status()[$model->status]['color'] }}">
                                    {{ Str::headline(fund_submission_status()[$model->status]['label']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        Retur/Adjustment
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
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
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'detail ' . $title }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Branch</label>
                                        <p>{{ $model->branch?->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Tanggal</label>
                                        <p>{{ localDate($model->date) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Vendor</label>
                                        <p>{{ $model->vendor?->nama }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">No LPB</label><br>
                                        @php
                                            $type = $model->item_receiving_report?->tipe;

                                            if ($type == 'jasa') {
                                                $type = 'item-receiving-report-service';
                                            } elseif ($type == 'general') {
                                                $type = 'item-receiving-report-general';
                                            } elseif ($type == 'trading') {
                                                $type = 'item-receiving-report-trading';
                                            } elseif ($type == 'transport') {
                                                $type = 'item-receiving-report-transport';
                                            }
                                        @endphp
                                        <a href="{{ route('admin.' . $type . '.show', $model->item_receiving_report) }}" target="_blank">{{ $model->item_receiving_report?->kode }}</a>
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Currency</label>
                                        <p>{{ $model->currency?->kode }} {{ $model->currency?->nama }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Exhange Rate</label>
                                        <p>{{ formatNumber($model->exchange_rate) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Nomor Faktur</label>
                                        <p>{{ $model->tax_number }}</p>
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Project</label>
                                        @if ($model->project)
                                            <p>{{ $model->project?->name }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Referensi</label>
                                        <p>{{ $model->reference }}</p>
                                    </div>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="form-label">Gudang</label>
                                        <p>{{ $model->ware_house?->nama }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">Status</label>
                                <br>
                                <div class="badge badge-lg badge-{{ fund_submission_status()[$model->status]['color'] }}">
                                    {{ fund_submission_status()[$model->status]['label'] }} -
                                    {{ fund_submission_status()[$model->status]['text'] }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <table class="responsive">
                                <table class="table">
                                    <thead class="bg-info">
                                        <tr>
                                            <th>{{ Str::headline('item') }}</th>
                                            <th>{{ Str::headline('unit') }}</th>
                                            <th>{{ Str::headline('qty LPB') }}</th>
                                            <th class="text-end">{{ Str::headline('qty') }}</th>
                                            <th class="text-end"></span> {{ Str::headline('price') }} <span class="currency_kode">{{ $model->currency?->kode }}</th>
                                            <th class="text-end"></span> {{ Str::headline('subtotal') }} <span class="currency_kode">{{ $model->currency?->kode }}</th>
                                            <th class="text-end"></span> {{ Str::headline('pajak') }} <span class="currency_kode">{{ $model->currency?->kode }}</th>
                                            <th class="text-end"></span> {{ Str::headline('total') }} <span class="currency_kode">{{ $model->currency?->kode }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="return-data">
                                        @foreach ($model->purchase_return_details as $key => $purchase_return_detail)
                                            <tr id="row-{{ $key }}">
                                                <td class="align-top">
                                                    <input type="hidden" name="purchase_return_detail_id[]" value="{{ $purchase_return_detail->id }}">
                                                    <input type="hidden" name="reference_model[]" value="{{ $purchase_return_detail->reference_model }}">
                                                    <input type="hidden" name="reference_id[]" value="{{ $purchase_return_detail->reference_id }}">
                                                    <input type="hidden" name="item_id[]" value="{{ $purchase_return_detail->item?->id }}">
                                                    <input type="hidden" name="unit_id[]" value="{{ $purchase_return_detail->unit?->id }}">
                                                    {{ $purchase_return_detail->item?->nama }} <br>
                                                    {{ $purchase_return_detail->item?->kode }}
                                                </td>

                                                <td class="align-top">
                                                    {{ $purchase_return_detail->unit?->name }}
                                                </td>
                                                <td class="text-end align-top">
                                                    {{ formatNumber($purchase_return_detail->lpb_qty) }}
                                                </td>
                                                <td class="text-end align-top">
                                                    {{ formatNumber($purchase_return_detail->qty) }}
                                                </td>
                                                <td class="text-end align-top">
                                                    {{ formatNumber($purchase_return_detail->price) }}
                                                </td>
                                                <td class="text-end align-top">
                                                    {{ formatNumber($purchase_return_detail->subtotal) }}
                                                </td>
                                                <td>
                                                    @foreach ($purchase_return_detail->purchase_return_taxes as $key_tax => $purchase_return_tax)
                                                        <div class="mb-1">
                                                            <div class="row">
                                                                <div class="col-md-12 text-end">
                                                                    <span class="badge badge-success">{{ $purchase_return_tax->tax?->name ?? $purchase_return_tax->tax_trading?->name }}
                                                                        {{ formatNumber($purchase_return_tax->value * 100) }}%</span>
                                                                </div>
                                                                <div class="col-md-12 text-end">
                                                                    {{ formatNumber($purchase_return_tax->amount) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td class="text-end align-top">
                                                    {{ formatNumber($purchase_return_detail->total) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th class="text-end">TOTAL</th>
                                            <th class="text-end" id="grand_subtotal_text">
                                                {{ formatNumber($model->subtotal) }}</th>
                                            <th class="text-end" id="grand_tax_amount_text">
                                                {{ formatNumber($model->tax_total) }}</th>
                                            <th class="text-end" id="grand_total_text">{{ formatNumber($model->total) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-1">
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            {!! $auth_revert_void_button !!}
                            @role('super_admin')
                                @if (in_array($model->status, ['approve', 'done']) && checkAvailableDate($model->date_receive))
                                    @include('components.generate_journal_button', ['model' => get_class($model), 'id' => $model->id, 'type' => 'purchase-return'])
                                @endif
                            @endrole
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
            <x-card-data-table>
                <x-slot name="table_content">
                    <x-button type="button" color="info" label="Export" target="_blank" icon="file" fontawesome soft block size="md" link="{{ route($main . '.export', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" />
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
                                <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
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
                                <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
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
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#debit-note');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\PurchaseReturn`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
