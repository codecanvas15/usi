@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-down-payment';
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
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline($main) }}</a>
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
        <div class="box bg-gradient-secondary-dark text-white">
            <div class="box-body">
                <div class="row justify-content-end">
                    <div class="col-md-6 align-self-center">
                        <h4 class="m-0">Detail Purchase Down Payment</h4>
                        <h1 class="m-0">{{ $model->code }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status') }}</h5>
                                <div class="badge badge-lg badge-{{ get_invoice_status()[$model->status]['color'] }}">
                                    {{ Str::headline(get_invoice_status()[$model->status]['label']) }}
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
            <x-card-data-table title='{{ "Detail $main" }}'>
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <x-table theadColor="danger">
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
                                <th>{{ Str::headline('vendor') }}</th>
                                <td>{{ $model->vendor?->nama }} - {{ $model->vendor?->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('no. purchase order') }}</th>
                                <td>
                                    @php
                                        $po_code = '';
                                        if ($model->purchase?->general) {
                                            $po_code = $model->purchase?->general->code;
                                        } elseif ($model->purchase?->trading) {
                                            $po_code = $model->purchase?->trading->nomor_po;
                                        } elseif ($model->purchase?->service) {
                                            $po_code = $model->purchase?->service->code;
                                        } elseif ($model->purchase?->transport) {
                                            $po_code = $model->purchase?->transport->kode;
                                        } else {
                                            $po_code = $model->purchase?->nomor_so ?? '';
                                        }
                                    @endphp
                                    {{ $po_code }}
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <td>{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('due date') }}</th>
                                <td>{{ localDate($model->due_date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('mata uang') }}</th>
                                <td>{{ $model->currency?->nama }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('nilai tukar') }}</th>
                                <td>{{ formatNumber($model->exchange_rate) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('no. faktur pajak') }}</th>
                                <td>{{ $model->tax_number }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Include Pajak') }}</th>
                                <td>{{ $model->is_include_tax ? 'Ya' : 'Tidak' }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('lampiran') }}</th>
                                <td>
                                    @if ($model->tax_attachment)
                                        <a href="{{ asset('storage/' . $model->tax_attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> File</a>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('status') }}</th>
                                <td>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->status]['text']) }} -
                                        {{ Str::headline(get_invoice_status()[$model->status]['label']) }}
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('payment_status') }}</th>
                                <td>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->payment_status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['text']) }} -
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['label']) }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr>
                            {{-- <tr>
                                <th>{{ Str::headline('faktur pajak') }}</th>
                                <td>
                                    <div id="reference-text-container" class="d-flex">
                                        <p id="reference-text">{{ $model->reference }}</p>
                                        @can('edit faktur pajak purchase-general')
                                            <button class="btn btn-warning btn-xs ml-2 align-self-start" onclick="handleClickEditTaxReference()"><i class="fas fa-pencil"></i></button>
                                        @endcan
                                    </div>
                                    <div id="reference-input-container" class="d-none">
                                        <input type="text" name="reference" id="reference-input" class="form-control tax-reference-mask" value="{{ $model->reference }}" />
                                        &nbsp;
                                        <button class="btn btn-primary btn-sm ml-2 align-self-start" onclick="handleSubmitEditTaxReference()"><i class="fas fa-check"></i></button>
                                    </div>
                                </td>
                            </tr> --}}
                        </x-slot>
                    </x-table>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
                        @if ($model->fund_submissions()->whereIn('status', ['pending', 'revert', 'approve'])->count() == 0)
                            {!! $auth_revert_void_button !!}
                        @endif

                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route('admin.purchase.index') }}' />

                        @if (in_array($model->status, ['pending', 'revert']))
                            @if ($model->check_available_date)
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan
                            @endif
                        @endif

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

            <x-card-data-table title=''>
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    <x-table>
                        <x-slot name="table_head">
                            <th>{{ Str::headline('#') }}</th>
                            <th>{{ Str::headline('keterangan') }}</th>
                            <th>{{ Str::headline('nominal') }}</th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <td>1</td>
                                <td>{{ $model->note }}</td>
                                <td class="text-end">{{ formatNumber($model->total_amount) }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-end">Down Payment</td>
                                <td class="text-end">{{ formatNumber($model->down_payment) }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-end">Subtotal</td>
                                <td class="text-end">{{ formatNumber($model->subtotal) }}</td>
                            </tr>
                            @foreach ($model->purchase_down_payment_taxes as $purchase_down_payment_tax)
                                <tr>
                                    <td></td>
                                    <td class="text-end">{{ $purchase_down_payment_tax->tax->tax_name_with_percent }}</td>
                                    <td class="text-end">{{ formatNumber($purchase_down_payment_tax->amount) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th></th>
                                <th class="text-end"><b>Grand Total</b></th>
                                <th class="text-end"><b>{{ formatNumber($model->grand_total) }}</b></th>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>

            @if ($related_down_payments->count() > 0)
                <x-card-data-table title='Payment History'>
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>{{ Str::headline('no. dokumen') }}</th>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <th colspan="2">{{ Str::headline('jumlah') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <td>{{ $model->purchase->kode }}</td>
                                    <td>{{ localDate($model->purchase->tanggal) }}</td>
                                    <td align="right">{{ formatNumber($model->purchase->reference->total) }}</td>
                                    <td></td>
                                </tr>
                                @foreach ($related_down_payments as $purchase_down_payment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.purchase-down-payment.show', $purchase_down_payment) }}" target="_blank">{{ $purchase_down_payment->code }}</a>
                                        </td>
                                        <td>{{ localDate($purchase_down_payment->date) }}</td>
                                        <td></td>
                                        <td align="right">{{ formatNumber($purchase_down_payment->grand_total) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>Sisa PO</b></td>
                                    <td></td>
                                    <td></td>
                                    <td align="right"><b>{{ formatNumber($model->purchase->reference->total - $related_down_payments->sum('grand_total')) }}</b></td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            @endif
        </div>
        <div class="col-md-4">
            {!! $authorization_log_view !!}

            <div id="print-request-container"></div>
            <div id="receipt-print-request"></div>
            <div id="tax-print-request"></div>

            <x-card-data-table title="{{ 'Action' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @if ($model->check_available_date)
                        @if ($model->status == 'approve')
                            @can("close $main")
                                <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                <x-modal title="close purchase" id="close-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
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
                        @endif
                    @endif

                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">

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
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
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
    <script src="{{ asset('js/helpers/helpers.js?v=1.2') }}"></script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');

        function handleClickEditTaxReference() {
            $('#reference-text-container').addClass('d-none');
            $('#reference-text-container').removeClass('d-flex');
            $('#reference-input-container').removeClass('d-none');
            $('#reference-input-container').addClass('d-flex');

            initMaskTaxReference()
        }

        get_request_print_approval(`App\\Models\\PurchaseDownPayemnt`, '{{ $model->id }}', 'purchase_down_payment');

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.purchase-down-payment.history', $model->id) }}`,
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
    </script>
@endsection
