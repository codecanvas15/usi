@extends('layouts.admin.layout.index')

@php
    $main = 'supplier-invoice';
    $title = 'Purchase Invoice (LPB)';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
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
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'detail ' . $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('No. Purchase Invoice') }}</th>
                                <td>{{ $model->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Tgl. Dokumen') }}</th>
                                <td>{{ Carbon\Carbon::parse($model->date)->translatedFormat('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Tgl. Dokumen Diterima') }}</th>
                                <td>{{ Carbon\Carbon::parse($model->accepted_doc_date)->translatedFormat('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('vendor') }}</th>
                                <td>{{ $model->vendor->nama }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Currency') }}</th>
                                <td>{{ $model->currency->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Kurs') }}</th>
                                <td>{{ formatNumber($model->exchange_rate) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('term of payment') }}</th>
                                <td>
                                    {{ ucwords($model->term_of_payment) }}
                                    @if ($model->top_days)
                                        {{ $model->top_days }} hari
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('jatuh tempo') }}</th>
                                <td>{{ Carbon\Carbon::parse($model->top_due_date)->translatedFormat('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('branch') }}</th>
                                <td>{{ ucwords($model->branch->name) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('No. Invoice/Nota/Kwitansi ') }}</th>
                                <td>{{ $model->reference }}</td>
                            </tr>

                            <tr>
                                <th>{{ Str::headline('lampiran') }}</th>
                                <td>
                                    @if ($model->file)
                                        <x-button color="info" link="{{ url('storage/' . $model->file) }}" size="sm" icon="file" label="show file" fontawesome target="_blank" />
                                    @else
                                        <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                                    @endif
                                </td>
                            </tr>
                            @if ($model->tax_reference)
                                <tr>
                                    <th>{{ Str::headline('no. faktur pajak') }}</th>
                                    <td>{{ $model->tax_reference }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('lampiran pajak') }}</th>
                                    <td>
                                        @if ($model->tax_file)
                                            <x-button color="info" link="{{ url('storage/' . $model->tax_file) }}" size="sm" icon="file" label="show file" fontawesome target="_blank" />
                                        @else
                                            <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="bg-light">
                                        <form action="{{ route('admin.supplier-invoice.update-tax', $model) }}" enctype="multipart/form-data" method="POST">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <x-input type="text" label="pajak" name="tax_reference" id="tax_reference" value="" class="tax-reference-mask" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-input type="file" label="lampiran" name="tax_file" id="tax_file" />
                                                </div>
                                                <div class="col-md-12 text-end mt-3">
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
                                    @if ($model->status == 'pending' or $model->status == 'revert')
                                        @if ($model->status == 'pending')
                                            <span class="badge badge-warning">Pending - waiting approval</span>
                                        @else
                                            <span class="badge badge-dark">Revert - Purchase Invoice has been
                                                reverted.</span>
                                        @endif
                                    @elseif ($model->status == 'approve')
                                        <span class="badge badge-success">Approve - Purchase Invoice has been
                                            approved.</span>
                                    @else
                                        <span class="badge badge-dark">Reject - Purchase Invoice rejected.</span>
                                    @endif
                                </td>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-between gap-1">
                        @can("lock $main")
                            @if ($model->supplier_invoice_parent()->lock_status)
                                <x-input-checkbox name="lock_status" id="lock_status" label="lock invoice" value="1" checked />
                            @else
                                <x-input-checkbox name="lock_status" id="lock_status" label="lock invoice" value="1" />
                            @endif
                        @endcan
                        <div>
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
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if ($model->status == 'pending' or $model->status == 'revert')
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
            <div class="box">
                <div class="box-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="box-title fw-bold">LPB</h5>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tgl. LPB</th>
                                        <th>Kode</th>
                                        <th>Kode DO</th>
                                        <th>Item Amount</th>
                                        <th>Tax Amount</th>
                                        <th>Total</th>
                                        <th>Keterangan</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody id="lpbDetail">
                                    @foreach ($model->detail as $key => $detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ Carbon\Carbon::parse($detail->item_receiving_report->date_receive ?? '')->format('d-m-Y') }}
                                            </td>
                                            <td>{{ $detail->item_receiving_report->kode ?? '' }}</td>
                                            <td>{{ $detail->item_receiving_report->do_code_external }}</td>
                                            <td>{{ $model->currency?->simbol }} {{ formatNumber($detail->sub_total) }}
                                            </td>
                                            <td>{{ $model->currency?->simbol }} {{ formatNumber($detail->tax) }}</td>
                                            <td class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($detail->total) }}</td>
                                            <td>{{ $detail->notes }}</td>
                                            <td>
                                                <x-button link="{{ url('storage/' . $detail->item_receiving_report->file) }}" label="file" color="info" size="sm" target="blank" />
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th class="text-end" colspan="6">DPP</th>
                                        <td class="text-end">
                                            <span class="span-dpp">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->sub_total) }}</span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="6">Tax Total</th>
                                        <td class="text-end">
                                            <span class="span-tax-total">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->tax_total) }}</span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="6">Grand Total</th>
                                        <td class="text-end">
                                            <span class="span-grand-total">{{ $model->currency?->simbol }}
                                                {{ formatNumber($model->grand_total) }}</span>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <x-card-data-table title="payment information">
                <x-slot name="table_content">
                    <x-table theadColor=''>
                        <x-slot name="table_head">
                            <th>Tgl Bayar</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Keterangan</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->supplier_invoice_payment() as $supplier_invoice_payment)
                                <tr>
                                    <td>{{ localDate($supplier_invoice_payment->date) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($supplier_invoice_payment->amount_to_pay) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($supplier_invoice_payment->pay_amount) }}</td>
                                    <td>{{ $supplier_invoice_payment->note }}</td>
                                </tr>
                            @endforeach
                        </x-slot>
                        <x-slot name="table_foot">
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->supplier_invoice_payment()->sum('amount_to_pay')) }}</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->supplier_invoice_payment()->sum('pay_amount')) }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>SISA</th>
                                <th class="text-end"></th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->supplier_invoice_payment()->sum('amount_to_pay') - $model->supplier_invoice_payment()->sum('pay_amount')) }}
                                </th>
                                <th></th>
                            </tr>
                        </x-slot>
                    </x-table>

                    @if ($model->supplier_invoice_down_payments->count() > 0)
                        <h2>Down Payment</h2>
                        <x-table theadColor=''>
                            <x-slot name="table_head">
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->supplier_invoice_down_payments as $down_payment)
                                    <tr>
                                        <td>{{ localDate($down_payment->cash_advance_payment->date) }}</td>
                                        <td class="text-end">{{ $down_payment->cash_advance_payment->currency->simbol }} {{ formatNumber($down_payment->cash_advance_payment->cash_advance_cash_advance->debit ?? 0) }}</td>
                                        <td>{{ $down_payment->cash_advance_payment->reference }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    @endif
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
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#supplier-invoice-sidebar');
        sidebarActive('#supplier-invoice');

        initMaskTaxReference();

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.supplier-invoice.history', $model->id) }}`,
                success: function({
                    data
                }) {
                    $('#history-list').html('');
                    $.each(data, function(key, value) {
                        let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                        $('#history-list').append(`
                                <tr>
                                    <td>${value.date}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                    });

                    $('#history-modal').modal('show');
                }
            });
        });

        $('#lock_status').click(function(el) {
            $.ajax({
                url: "{{ route('admin.supplier-invoice.lock', $model->id) }}",
                method: "POST",
                data: {
                    _token: token
                },
                beforeSend: function() {
                    el.target.disabled = true;
                },
                success: function(res) {
                    el.target.disabled = false;
                },
                error: function(xhr, status, error) {
                    el.target.disabled = false;

                    Swal.fire({
                        icon: 'error',
                        title: '',
                        text: xhr.responseJSON.message,
                    });
                }
            })
        })
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\SupplierInvoice`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
