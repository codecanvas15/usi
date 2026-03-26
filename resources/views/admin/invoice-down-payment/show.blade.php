@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-down-payment';
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
                        <a href="{{ route('admin.invoice.index') }}">{{ Str::headline($main) }}</a>
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
                        <h4 class="m-0">Detail Invoice Down Payment</h4>
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
                                <th>{{ Str::headline('customer') }}</th>
                                <td>{{ $model->customer?->nama }} - {{ $model->customer?->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('sales order') }}</th>
                                <td>
                                    <a class="text-primary" href="{{ route('admin.sales-order.show', $model->sale_order_reference) }}" target="_blank" rel="noopener noreferrer">
                                        {{ $model->sale_order_reference->kode ?? ($model->sale_order_reference->nomor_so ?? '') }}
                                    </a>
                                    
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
                            @if ($model->tax_attachment)
                                <tr>
                                    <th>{{ Str::headline('lampiran') }}</th>
                                    <td>
                                        <a href="{{ asset('storage/' . $model->tax_attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> File</a>
                                    </td>
                                </tr>
                            @endif
                            @if ($model->tax_number)
                                <tr>
                                    <th>{{ Str::headline('no. faktur pajak') }}</th>
                                    <td>{{ $model->tax_number }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="bg-light">
                                        <form action="{{ route('admin.invoice-down-payment.update-tax', $model) }}" enctype="multipart/form-data" method="POST">
                                            @csrf
                                            <div class="row mt-3">
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
                                    </td>
                                </tr>
                            @endif
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
                                        @can('edit faktur pajak invoice-general')
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
                        {!! $auth_revert_void_button !!}

                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route('admin.invoice.index') }}' />

                        @if (in_array($model->status, ['pending', 'revert']))
                            @if ($model->check_available_date)
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan
                            @endif
                        @endif
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
                            @foreach ($model->invoice_down_payment_taxes as $invoice_down_payment_tax)
                                <tr>
                                    <td></td>
                                    <td class="text-end">{{ $invoice_down_payment_tax->tax->tax_name_with_percent }}</td>
                                    <td class="text-end">{{ formatNumber($invoice_down_payment_tax->amount) }}</td>
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
                            @foreach ($model->invoice_payment() as $invoice_payment)
                                <tr>
                                    <td>{{ localDate($invoice_payment->date) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($invoice_payment->amount_to_receive) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($invoice_payment->receive_amount) }}</td>
                                    <td>{{ $invoice_payment->note }}</td>
                                </tr>
                            @endforeach
                        </x-slot>
                        <x-slot name="table_foot">
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->invoice_payment()->sum('amount_to_receive')) }}</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->invoice_payment()->sum('receive_amount')) }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>SISA</th>
                                <th class="text-end"></th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->invoice_payment()->sum('amount_to_receive') - $model->invoice_payment()->sum('receive_amount')) }}
                                </th>
                                <th></th>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
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
                                <x-modal title="close invoice" id="close-modal" headerColor="success">
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
                    <a target="_blank" class="btn btn-info mb-1" href="{{ route('admin.invoice-down-payment.export.id', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_with_do')
                        data-model="{{ \App\Models\InvoiceDownPaymentController::class }}" data-id="{{ $model->id }}"
                        data-print-type="invoice_with_do"
                        data-link="{{ route('admin.invoice-downpayment.show', ['invoice_downpayment' => $model->id]) }}"
                        data-code="{{ $model->kode }}" @endauthorize_print>
                        Export
                    </a>
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
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        initMaskTaxReference();
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading')

        function handleClickEditTaxReference() {
            $('#reference-text-container').addClass('d-none');
            $('#reference-text-container').removeClass('d-flex');
            $('#reference-input-container').removeClass('d-none');
            $('#reference-input-container').addClass('d-flex');

            initMaskTaxReference()
        }

        function handleSubmitEditTaxReference() {
            $.post('{{ route('admin.invoice-general.update-reference', $model->id) }}', {
                    _token: '{{ csrf_token() }}',
                    reference: $('#reference-input').val(),
                    id: '{{ $model->id }}'
                }, function({
                    status,
                    message
                }) {
                    $('#reference-text').text($('#reference-input').val());
                    $('#reference-text-container').addClass('d-flex');
                    $('#reference-text-container').removeClass('d-none');
                    $('#reference-input-container').removeClass('d-flex');
                    $('#reference-input-container').addClass('d-none');

                    showAlert(status, message, 'success')
                })
                .catch(({
                    responseJSON
                }) => showAlert('error', responseJSON.message))
        }

        get_request_print_approval(`App\\Models\\InvoiceDownPayment`, '{{ $model->id }}', 'invoice_down_payment');
    </script>
@endsection
