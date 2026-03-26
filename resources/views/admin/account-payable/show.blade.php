@extends('layouts.admin.layout.index')

@php
    $main = 'account-payable';
    $menu = 'pembayaran vendor';
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
                        <a href="{{ route('admin.outgoing-payment.index') }}?tab=account-payable">{{ Str::headline($menu) }}</a>
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
    <x-card-data-table title="{{ 'Detail ' . $menu }}">
        <x-slot name="header_content">
        </x-slot>
        <x-slot name="table_content">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Tanggal Bayar</label>
                                <p>{{ localDate($model->date) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Nomor Bukti</label>
                                <p>{{ $model->bank_code_mutation }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Branch</label>
                                <p>{{ $model->branch->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Bayar Ke</label>
                                <p>{{ $model->vendor->nama }}</p>
                            </div>
                        </div>
                        @if ($model->customer)
                            <div class="col-md-6">
                                <span class="badge bg-success"> <i class="fa fa-check"></i> Cross Hutang & Piutang</span>
                                <div class="form-group">
                                    <label for="" class="form-label">Customer</label>
                                    <p>{{ $model->customer->nama }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Tanggal Pengajuan Dana</label>
                                <p>{{ localDate($model->fund_submission->date) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">No. Pengajuan Dana</label>
                                <br>
                                <a href="{{ route('admin.fund-submission.show', ['fund_submission' => $model->fund_submission_id]) }}" target="_blank">{{ $model->fund_submission->code }}</a>
                            </div>
                        </div>
                        @if ($model->fund_submission->is_giro)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Nomor Giro</label>
                                    <p>{{ $model->fund_submission->giro_number }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Tanggal Giro</label>
                                    <p>{{ localDate($model->fund_submission->giro_liquid_date) }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Currency</label>
                                <p>{{ $model->currency->nama }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Currency SI</label>
                                <p>{{ $model->supplier_invoice_currency->nama }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Kurs</label>
                                <p>{{ formatNumber($model->exchange_rate) }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Note</label>
                                <p>{{ $model->reference }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Project</label>
                                <p>{{ $model->project->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Kas/Bank</label>
                                <p>{{ $model->coa->account_code }} - {{ $model->coa->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Keterangan</label>
                                <p>{{ $model->note }}</p>
                            </div>
                        </div>
                        @if ($model->change_bank_reason)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="form-label">Alasan ganti bank</label>
                                    <p>{{ $model->change_bank_reason }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    @if ($model->fund_submission->send_payment ?? null)
                        @include('admin.fund-submission.__giro_table', ['send_payment' => $model->fund_submission->send_payment])
                    @endif

                    {!! $authorization_log_view !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="badge badge-lg badge-{{ incoming_payment_status()[$model->status]['color'] }}">
                        {{ incoming_payment_status()[$model->status]['label'] }} - {{ incoming_payment_status()[$model->status]['text'] }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 pt-20">
                        <h4>Purchase Invoice</h4>
                        @include('admin.account-payable._show_detail_table', ['model' => $model])
                    </div>
                    @if ($model->account_payable_others->count() > 0)
                        <div class="col-md-12">
                            <h4>Lain - Lain</h4>
                            <x-table theadColor='dark'>
                                <x-slot name="table_head">
                                    <th>Akun</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Jumlah</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->account_payable_others ?? [] as $detail)
                                        <tr>
                                            <td>{{ $detail->coa->account_code }} - {{ $detail->coa->name }}</td>
                                            <td>{{ $detail->note }}</td>
                                            <td class="text-end">{{ $model->currency->simbol }} {{ floatDotFormat($detail->debit) }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-end">{{ $model->currency->simbol }} {{ floatDotFormat($model->account_payable_others->sum('debit')) }}</th>
                                        <th></th>
                                    </tr>
                                </x-slot>

                            </x-table>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <x-table theadColor='dark'>
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <td></td>
                                    <td class="text-end">{{ Str::headline('total pembayaran hutang') }}</td>
                                    <td class="text-end">{{ formatNumber($model->account_payable_details->sum('amount')) }}</td>
                                </tr>
                                @if ($model->account_payable_purchase_returns->count() > 0)
                                    <tr>
                                        <td></td>
                                        <td class="text-end">{{ Str::headline('total retur') }}</td>
                                        <td class="text-end">{{ formatNumber($model->account_payable_purchase_returns->sum('amount') * -1) }}</td>
                                    </tr>
                                @endif
                                @if ($model->account_payable_customers->count() > 0)
                                    <tr>
                                        <td></td>
                                        <td class="text-end">{{ Str::headline('total pembayaran piutang') }}</td>
                                        <td class="text-end">{{ formatNumber($model->account_payable_customers->sum('receive_amount') * -1) }}</td>
                                    </tr>
                                @endif
                                @if ($model->account_payable_others->count() > 0)
                                    <tr>
                                        <td></td>
                                        <td class="text-end">{{ Str::headline('total lain lain') }}</td>
                                        <td class="text-end">{{ formatNumber($model->account_payable_others->sum('debit')) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td></td>
                                    <td class="text-end">{{ Str::headline('total') }}</td>
                                    <td class="text-end">{{ formatNumber($model->account_payable_details->sum('amount') - $model->account_payable_customers->sum('receive_amount') + $model->account_payable_others->sum('debit') - $model->account_payable_purchase_returns->sum('amount')) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </div>
            <div class="box-footer">
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
                    @role('super_admin')
                        @if (in_array($model->status, ['approve', 'done']) && checkAvailableDate($model->date))
                            @include('components.generate_journal_button', ['model' => get_class($model), 'id' => $model->id, 'type' => 'account-payable'])
                        @endif
                    @endrole
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route('admin.outgoing-payment.index') }}?tab=account-payable' />
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
            </div>
        </x-slot>
    </x-card-data-table>
    <x-card-data-table title="{{ 'Payment Information' }}">
        <x-slot name="table_content">
            <div class="table-responsive">
                @foreach ($model->account_payable_details ?? [] as $account_payable_detail)
                    <x-table theadColor='dark' width="100%">
                        <x-slot name="table_body">
                            <tr class="bg-dark">
                                <th colspan="4" class="text-center">
                                    {{ $account_payable_detail->supplier_invoice_parent->code }}
                                </th>
                            </tr>
                            <tr>
                                <th>TANGGAL</th>
                                <th>KET.</th>
                                <th class="text-end">JUMLAH</th>
                                <th class="text-end">BAYAR</th>
                            </tr>
                            @if ($account_payable_detail->supplier_invoice_parent->type == 'general')
                                @foreach ($account_payable_detail->payment_informations as $payment_information)
                                    <tr>
                                        <td>{{ localDate($payment_information->date) }}</td>
                                        <td>{{ $payment_information->note }}</td>
                                        <td class="text-end">
                                            @if ($payment_information->amount_to_pay != 0)
                                                {{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($payment_information->amount_to_pay) }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($payment_information->pay_amount != 0)
                                                {{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($payment_information->pay_amount) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach ($account_payable_detail->supplier_invoice_parent->reference_model->detail as $detail)
                                    <tr class="table-warning">
                                        <th colspan="2">
                                            {{ $detail->item_receiving_report->kode }}
                                        </th>
                                        <th class="text-end">
                                            @if ($detail->item_receiving_report->total != 0)
                                                {{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($detail->item_receiving_report->total) }}
                                            @endif
                                        </th>
                                        <th></th>
                                    </tr>
                                    @foreach ($detail->item_receiving_report->payment_informations as $payment_information)
                                        <tr>
                                            <td>{{ localDate($payment_information->date) }}</td>
                                            <td>{{ $payment_information->note }}</td>
                                            <td class="text-end">
                                                @if ($payment_information->amount_to_pay != 0)
                                                    {{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($payment_information->amount_to_pay) }}
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($payment_information->pay_amount != 0)
                                                    {{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($payment_information->pay_amount) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                            <tr>
                                <th>TOTAL</th>
                                <th></th>
                                <th class="text-end">{{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($account_payable_detail->payment_informations->sum('amount_to_pay')) }}</th>
                                <th class="text-end">{{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($account_payable_detail->payment_informations->sum('pay_amount')) }}</th>
                            </tr>
                            <tr>
                                <th>SISA</th>
                                <th></th>
                                <th></th>
                                <th class="text-end">{{ $model->supplier_invoice_currency->simbol }} {{ floatDotFormat($account_payable_detail->payment_informations->sum('amount_to_pay') - $account_payable_detail->payment_informations->sum('pay_amount')) }}</th>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                            </tr>
                        </x-slot>
                    </x-table>
                @endforeach
            </div>
        </x-slot>
    </x-card-data-table>
    @can('view journal')
        @include('components.journal-table')
    @endcan
@endsection
@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment');

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.account-payable.history', $model->id) }}`,
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
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\AccountPayable`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
