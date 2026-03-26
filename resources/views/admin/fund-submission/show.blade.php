@extends('layouts.admin.layout.index')

@php
    $main = 'fund-submission';
    $giro_main = 'send-payment';
    $menu = 'pengajuan dana';
    $giro_menu = 'giro keluar';
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
            <x-card-data-table title="{{ 'Detail ' . $menu . ' ' . $model->item }}">
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Nomor</label>
                                        <p>{{ $model->code }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Tanggal</label>
                                        <p>{{ localDate($model->date) }}</p>
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
                                @if ($model->invoice_return)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="form-label">Pengembalian Retur</label>
                                            <p>{{ $model->invoice_return->code }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($model->cash_advance_receive)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="form-label">Pengembalian Uang Muka</label>
                                            <p>{{ $model->cash_advance_receive->code }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($model->item == 'lpb')
                                    @if ($model->fund_submission_customers->count() > 0)
                                        <div class="col-md-4">
                                            <span class="badge bg-success"><i class="fa fa-check"></i> Cross Hutang &
                                                Piutang</span>
                                            <div class="form-group">
                                                <label for="" class="form-label">Customer</label>
                                                <p>{{ $model->customer->nama ?? '' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">Currency</label>
                                        <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="" class="form-label">kurs</label>
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
                                @if ($model->tax_number)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Faktur Pajak</label>
                                            <p>{{ $model->tax_number }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($model->tax_attachment)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Lampiran</label>
                                            <br>
                                            <a href="{{ asset('storage/' . $model->tax_attachment) }}" target="_blank">Lihat file</a>
                                        </div>
                                    </div>
                                @endif
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
                                @if ($model->attachment)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <a href="{{ asset('storage/' . $model->attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> Lihat File Lampiran </a>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12 mb-10">
                                    <div class="form-group">
                                        <label for="" class="form-label">{{ Str::headline('status') }}</label>
                                        <br>
                                        <div class="badge badge-lg badge-{{ fund_submission_status()[$model->status]['color'] }} my-10">
                                            {{ fund_submission_status()[$model->status]['label'] }} -
                                            {{ fund_submission_status()[$model->status]['text'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                @if ($model->is_giro)
                                    <div class="col-md-12">
                                        @if ($model->send_payment)
                                            @include('admin.fund-submission.__giro_table', [
                                                'send_payment' => $model->send_payment,
                                            ])
                                        @else
                                            <h3>Informasi Giro</h3>
                                            <div class="badge badge-lg badge-danger">
                                                Giro batal cair, silahkan perbarui informasi giro!
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-12">
                            @if ($model->item == 'general')
                                <div class="col-md-3 mt-15">
                                    <div class="form-group">
                                        <label for="" class="form-label">Kas/Bank</label>
                                        <p>{{ $model->coa->account_code ?? '' }} - {{ $model->coa->name ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-15" id="form_item">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="bg-info">
                                                <tr>
                                                    <th>Akun</th>
                                                    <th>Keterangan</th>
                                                    <th class="text-end {{ !$model->currency->is_local ? 'bg-dark' : '  ' }}">
                                                        Jumlah {{ $model->currency->kode }}</th>
                                                    @if (!$model->currency->is_local)
                                                        <th class="text-end">Jumlah {{ get_local_currency()->kode }}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($model->fund_submission_generals as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->coa->account_code }} - {{ $item->coa->name }}
                                                        </td>
                                                        <td>
                                                            {{ $item->note }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $model->currency->simbol }}
                                                            {{ formatNumber($item->debit) }}
                                                        </td>
                                                        @if (!$model->currency->is_local)
                                                            <td class="text-end">
                                                                {{ get_local_currency()->simbol }}
                                                                {{ formatNumber($item->local_debit) }}
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
                                                        {{ formatNumber($model->fund_submission_generals()->sum('debit')) }}
                                                    </th>
                                                    @if (!$model->currency->is_local)
                                                        <th class="text-end">{{ get_local_currency()->simbol }}
                                                            {{ formatNumber($model->general_debit_total) }}</th>
                                                    @endif
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if ($model->item == 'dp')
                                <div class="col-md-12 mt-15">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">No. DP</label>
                                                <p>{{ $model->purchase ? $model->purchase_down_payment?->code : '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">No. Purchase Order</label>
                                                <p>{{ $model->purchase ? $model->purchase?->kode : '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Total</label>
                                                <p>{{ $model->purchase ? formatNumber($model->purchase?->reference?->total ?? 0) : 0 }}</p>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="bg-info">
                                                <tr>
                                                    <th>Akun</th>
                                                    <th>Keterangan</th>
                                                    <th class="text-end {{ !$model->currency->is_local ? 'bg-dark' : '' }}">
                                                        Debit {{ $model->currency->kode }}</th>
                                                    <th class="text-end {{ !$model->currency->is_local ? 'bg-dark' : '' }}">
                                                        Kredit {{ $model->currency->kode }}</th>
                                                    @if (!$model->currency->is_local)
                                                        <th class="text-end">Debit {{ get_local_currency()->kode }}</th>
                                                        <th class="text-end">Kredit {{ get_local_currency()->kode }}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($model->fund_submission_cash_advances as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->coa->account_code }} - {{ $item->coa->name }}
                                                        </td>
                                                        <td>
                                                            {{ $item->note }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $model->currency->simbol }}
                                                            {{ formatNumber($item->debit) }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $model->currency->simbol }}
                                                            {{ formatNumber($item->credit) }}
                                                        </td>
                                                        @if (!$model->currency->is_local)
                                                            <td class="text-end">
                                                                {{ get_local_currency()->simbol }}
                                                                {{ formatNumber($item->local_debit) }}
                                                            </td>
                                                            <td class="text-end">
                                                                {{ get_local_currency()->simbol }}
                                                                {{ formatNumber($item->local_credit) }}
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
                                                        {{ formatNumber($model->fund_submission_cash_advances()->sum('debit')) }}
                                                    </th>
                                                    <th class="text-end">{{ $model->currency->simbol }}
                                                        {{ formatNumber($model->fund_submission_cash_advances()->sum('credit')) }}
                                                    </th>
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
                            @endif
                            @if ($model->item == 'lpb')
                                @include('admin.fund-submission.lpb.show')
                            @endif
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-1">
                            {{-- @if ($model->item == 'lpb') --}}
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
                            {{-- @endif --}}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            {!! $auth_revert_void_button !!}
                            @if ($model->status != 'approve' && $model->status != 'reject' && $model->status != 'void')
                                @if (auth()->user()->id == $model->created_by && $can_edit_or_delete)
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endif
                            @endif
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
            @if ($model->item == 'lpb')
                <div class="table-responsive">
                    <x-card-data-table title="{{ 'Payment Information' }}">
                        <x-slot name="table_content">
                            @foreach ($model->fund_submission_supplier_details ?? [] as $fund_submission_supplier_detail)
                                <x-table theadColor='dark' width="100%">
                                    <x-slot name="table_body">
                                        <tr class="bg-dark">
                                            <th colspan="4" class="text-center">
                                                {{ $fund_submission_supplier_detail->supplier_invoice_parent->code }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>TANGGAL</th>
                                            <th>KET.</th>
                                            <th class="text-end">JUMLAH</th>
                                            <th class="text-end">BAYAR</th>
                                        </tr>
                                        @if ($fund_submission_supplier_detail->supplier_invoice_parent->type == 'general')
                                            @foreach ($fund_submission_supplier_detail->payment_informations as $payment_information)
                                                <tr>
                                                    <td class="text-center">{{ localDate($payment_information->date) }}
                                                    </td>
                                                    <td>{{ $payment_information->note }}</td>
                                                    <td class="text-end">
                                                        @if ($payment_information->amount_to_pay != 0)
                                                            {{ $model->fund_submission_supplier->currency->simbol }}
                                                            {{ floatDotFormat($payment_information->amount_to_pay) }}
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($payment_information->pay_amount != 0)
                                                            {{ $model->fund_submission_supplier->currency->simbol }}
                                                            {{ floatDotFormat($payment_information->pay_amount) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($fund_submission_supplier_detail->supplier_invoice_parent->reference_model->detail as $detail)
                                                <tr class="table-warning">
                                                    <th colspan="2">
                                                        {{ $detail->item_receiving_report->kode }}
                                                    </th>
                                                    <th class="text-end">
                                                        @if ($detail->item_receiving_report->total != 0)
                                                            {{ $model->fund_submission_supplier->currency->simbol }}
                                                            {{ floatDotFormat($detail->item_receiving_report->total) }}
                                                        @endif
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                @foreach ($detail->item_receiving_report->payment_informations as $payment_information)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ localDate($payment_information->date) }}</td>
                                                        <td>{{ $payment_information->note }}</td>
                                                        <td class="text-end">
                                                            @if ($payment_information->amount_to_pay != 0)
                                                                {{ $model->fund_submission_supplier->currency->simbol }}
                                                                {{ floatDotFormat($payment_information->amount_to_pay) }}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            @if ($payment_information->pay_amount != 0)
                                                                {{ $model->fund_submission_supplier->currency->simbol }}
                                                                {{ floatDotFormat($payment_information->pay_amount) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endif
                                        <tr>
                                            <th>TOTAL</th>
                                            <th></th>
                                            <th class="text-end">
                                                {{ $model->fund_submission_supplier->currency->simbol }}
                                                {{ floatDotFormat($fund_submission_supplier_detail->payment_informations->sum('amount_to_pay')) }}
                                            </th>
                                            <th class="text-end">
                                                {{ $model->fund_submission_supplier->currency->simbol }}
                                                {{ floatDotFormat($fund_submission_supplier_detail->payment_informations->sum('pay_amount')) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>SISA</th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-end">
                                                {{ $model->fund_submission_supplier->currency->simbol }}
                                                {{ floatDotFormat($fund_submission_supplier_detail->payment_informations->sum('amount_to_pay') - $fund_submission_supplier_detail->payment_informations->sum('pay_amount')) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            @endforeach
                        </x-slot>
                    </x-card-data-table>
                </div>
            @endif

            @if ($model->item == 'dp')
                <x-card-data-table title='Payment History'>
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>{{ Str::headline('no. dokumen') }}</th>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <th colspan="2" class="text-center">{{ Str::headline('jumlah') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <td>
                                        {{ $model->purchase ? $model->purchase?->kode : '-' }}
                                    </td>
                                    <td>{{ $model->purchase ? localDate($model->purchase?->tanggal) : '-' }}</td>
                                    <td align="right">{{ $model->purchase ? formatNumber($model->purchase?->reference?->total ?? 0) : 0 }}</td>
                                    <td></td>
                                </tr>
                                @foreach ($related_down_payments as $purchase_down_payment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.cash-advance-payment.show', $purchase_down_payment) }}" target="_blank">{{ $purchase_down_payment->bank_code_mutation }}</a>
                                        </td>
                                        <td>{{ localDate($purchase_down_payment->date) }}</td>
                                        <td></td>
                                        <td align="right">{{ formatNumber($purchase_down_payment->cash_advance_cash_advance->debit ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            @endif

        </div>
        <div class="col-md-3">
            {!! $authorization_log_view !!}
            <x-card-data-table title="{{ 'Action' }}">
                <x-slot name="table_content">
                    @if (!$check_can_void)
                        <div class="badge badge-lg badge-success">
                            <i class="fa fa-check"></i> Pengajuan Dana Telah Digunakan
                        </div>
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
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#fund-submission');

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.fund-submission.history', $model->id) }}`,
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
