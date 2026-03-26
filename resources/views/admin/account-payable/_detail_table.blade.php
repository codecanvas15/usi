<div class="table-responsive">
    <h4>Hutang</h4>
    <table class="table table-stripped">
        <tbody>
            @foreach ($model->fund_submission_supplier_details as $fund_submission_supplier_detail)
                <tr class="bg-dark">
                    <th>Tanggal</th>
                    <th>Purchase Invoice</th>
                    <th>Cur.</th>
                    <th class="text-end">Kurs</th>
                    <th class="text-end">Total {{ $model->fund_submission_supplier->currency->kode }}</th>
                    <th class="text-end">Sisa {{ $model->fund_submission_supplier->currency->kode }}</th>
                    <th>Ket.</th>
                </tr>
                <tr>
                    <td>{{ localDate($fund_submission_supplier_detail->supplier_invoice_parent->date) }}</td>
                    <td>{{ $fund_submission_supplier_detail->supplier_invoice_parent->code ?? $fund_submission_supplier_detail->supplier_invoice_parent->reference }}</td>
                    <td>{{ $fund_submission_supplier_detail->supplier_invoice_parent->currency->kode }}</td>
                    <td class="text-end">{{ formatNumber($fund_submission_supplier_detail->supplier_invoice_parent->exchange_rate) }}</td>
                    <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }} {{ formatNumber($fund_submission_supplier_detail->supplier_invoice_parent->total) }}</td>
                    <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }} {{ formatNumber($fund_submission_supplier_detail->outstanding_amount) }}</td>
                    <th></th>
                </tr>
                @if (count($fund_submission_supplier_detail->fund_submission_supplier_lpbs) > 0)
                    <tr class="table-warning">
                        <th></th>
                        <th>No. LPB</th>
                        <th></th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Sisa</th>
                        <th class="text-end">Bayar</th>
                        <th></th>
                    </tr>
                    @foreach ($fund_submission_supplier_detail->fund_submission_supplier_lpbs as $lpb)
                        <tr class="table-warning">
                            <td></td>
                            <td>{{ $lpb->item_receiving_report->kode }}</td>
                            <td></td>
                            <td class="text-end">{{ formatNumber($lpb->item_receiving_report->total) }}</td>
                            <td class="text-end">{{ formatNumber($lpb->outstanding) }}</td>
                            <td class="text-end">{{ formatNumber($lpb->amount_foreign) }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-end"><b>Bayar {{ $model->currency->kode }}</b></td>
                    <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($fund_submission_supplier_detail->amount) }}</td>
                    <td>{{ $fund_submission_supplier_detail->note }}</td>
                </tr>
                @if ($model->currency_id != $model->fund_submission_supplier->currency_id)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Bayar {{ $model->fund_submission_supplier->currency->kode }}</b></td>
                        <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }} {{ formatNumber($fund_submission_supplier_detail->amount_foreign) }}</td>
                        <td>{{ $fund_submission_supplier_detail->note }}</td>
                    </tr>
                @endif
                @if ($fund_submission_supplier_detail->is_clearing)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Selisih Bayar <br> {{ $fund_submission_supplier_detail->coa->account_code ?? '' }} - {{ $fund_submission_supplier_detail->coa->name ?? '' }}</b></td>
                        <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }} {{ formatNumber($fund_submission_supplier_detail->amount_gap_foreign) }}</td>
                        <td>{{ $fund_submission_supplier_detail->clearing_note }}</td>
                    </tr>
                @endif
                @if ($fund_submission_supplier_detail->exchange_rate_gap_idr != 0)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Selisih Kurs</b></td>
                        <td class="text-end">{{ get_local_currency()->simbol }} {{ formatNumber($fund_submission_supplier_detail->exchange_rate_gap_idr) }}</td>
                        <td>{{ $fund_submission_supplier_detail->exchange_rate_gap_note }}</td>
                    </tr>
                @endif
            @endforeach
            <tr class="table-info">
                <td><b>TOTAL {{ $model->currency->kode }}</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end"><b>{{ $model->currency->simbol }} {{ formatNumber($model->fund_submission_supplier_details->sum('amount')) }}</b></td>
                <td></td>
            </tr>
            @if ($model->currency_id != $model->fund_submission_supplier->currency_id)
                <tr class="table-info">
                    <td><b>TOTAL {{ $model->fund_submission_supplier->currency->kode }}</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-end"><b>{{ $model->fund_submission_supplier->currency->simbol }} {{ formatNumber($model->fund_submission_supplier_details->sum('amount_foreign')) }}</b></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@if (count($model->fund_submission_purchase_returns) > 0)
    <div class="col-md-12 mt-3">
        <h4>Retur</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="bg-info">
                    <tr>
                        <th>{{ Str::headline('tanggal') }}</th>
                        <th>{{ Str::headline('no retur') }}</th>
                        <th>{{ Str::headline('currency') }}</th>
                        <th class="text-end">{{ Str::headline('rate') }}</th>
                        <th class="text-end">{{ Str::headline('sisa') }} <span class="supplier_invoice_currency_id_symbol">{{ $model->fund_submission_supplier->currency->kode }}</span></th>
                        <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ $model->currency->kode }}</span></th>
                        @if (!$model->currency->is_local)
                            <th class="text-end">{{ Str::headline('bayar') }} <span class="supplier_invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                        @endif
                        <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                    </tr>
                </thead>
                <tbody id="selected_return_table">
                    @forelse ($model->fund_submission_purchase_returns as $fund_submission_purchase_return)
                        <tr id="selected_return_row_{{ $fund_submission_purchase_return->purchase_return_id }}">
                            <td>
                                {{ localDate($fund_submission_purchase_return->purchase_return->date) }}
                            </td>
                            <td>{{ $fund_submission_purchase_return->purchase_return->code }}</td>
                            <td>{{ $fund_submission_purchase_return->purchase_return->currency->nama }}</td>
                            <td class="text-end">
                                {{ formatNumber($fund_submission_purchase_return->exchange_rate ?? 0) }}
                            </td>
                            <td class="text-end">
                                {{ formatNumber($fund_submission_purchase_return->outstanding_amount ?? 0) }}
                            </td>
                            <td class="text-end">
                                {{ formatNumber($fund_submission_purchase_return->amount) }}
                            </td>
                            @if (!$model->currency->is_local)
                                <td class="text-end">
                                    {{ formatNumber($fund_submission_purchase_return->amount * $model->exchange_rate) }}
                                </td>
                            @endif
                            <td class="text-end">
                                {{ formatNumber($fund_submission_purchase_return->exchange_rate_gap_idr) }}
                            </td>
                        </tr>
                    @empty
                        <tr id="row_return_empty">
                            <td colspan="9" class="text-center">Belum ada data yang dipilih</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">TOTAL</th>
                        <th class="text-end" id="return_outstanding_amount_total"></th>
                        <th class="text-end" id="return_amount_total">{{ floatDotFormat($model->fund_submission_purchase_returns->sum('amount')) }}</th>
                        @if (!$model->currency->is_local)
                            <th class="text-end column-multi-currency" id="return_amount_foreign_total">{{ floatDotFormat($model->fund_submission_purchase_returns->sum('amount') * $model->exchange_rate) }}</th>
                        @endif
                        <th class="text-end" id="return_exchange_rate_gap_total"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
@if ($model->fund_submission_customers->count() > 0)
    <div class="table-responsive">
        <h4>Piutang</h4>
        <div class="row">
            <div class="col-md-4">
                <x-input label="customer" readonly value="{{ $model->customer->nama }}" />
            </div>
        </div>
        <table class="table table-stripped mt-2">
            <tbody>
                @foreach ($model->fund_submission_customers as $fund_submission_customer)
                    <tr class="bg-dark">
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Cur.</th>
                        <th class="text-end">Kurs</th>
                        <th class="text-end">Total {{ $model->fund_submission_supplier->currency->kode }}</th>
                        <th class="text-end">Sisa {{ $model->fund_submission_supplier->currency->kode }}</th>
                        <th>Ket.</th>
                    </tr>
                    <tr>
                        <td>{{ localDate($fund_submission_customer->invoice_parent->date) }}</td>
                        <td>{{ $fund_submission_customer->invoice_parent->code ?? $fund_submission_customer->invoice_parent->reference }}
                        </td>
                        <td>{{ $fund_submission_customer->invoice_parent->currency->kode }}</td>
                        <td class="text-end">
                            {{ formatNumber($fund_submission_customer->invoice_parent->exchange_rate) }}
                        </td>
                        <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }}
                            {{ formatNumber($fund_submission_customer->invoice_parent->total) }}
                        </td>
                        <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }}
                            {{ formatNumber($fund_submission_customer->outstanding_amount) }}
                        </td>
                        <th></th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Bayar {{ $model->currency->kode }}</b></td>
                        <td class="text-end">{{ $model->currency->simbol }}
                            {{ formatNumber($fund_submission_customer->receive_amount) }}</td>
                        <td>{{ $fund_submission_customer->note }}</td>
                    </tr>
                    @if ($model->currency_id != $model->fund_submission_supplier->currency_id)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end"><b>Bayar {{ $model->fund_submission_supplier->currency->kode }}</b>
                            </td>
                            <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }}
                                {{ formatNumber($fund_submission_customer->receive_amount_foreign) }}
                            </td>
                            <td>{{ $fund_submission_customer->note }}</td>
                        </tr>
                    @endif
                    @if ($fund_submission_customer->is_clearing)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end"><b>Selisih Bayar <br> {{ $fund_submission_customer->coa->account_code ?? '' }} - {{ $fund_submission_customer->coa->name ?? '' }}</b></td>
                            <td class="text-end">{{ $model->fund_submission_supplier->currency->simbol }}
                                {{ formatNumber($fund_submission_customer->receive_amount_gap_foreign) }}
                            </td>
                            <td>{{ $fund_submission_customer->clearing_note }}</td>
                        </tr>
                    @endif

                    @if ($fund_submission_customer->exchange_rate_gap_idr != 0)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end"><b>Selisih Kurs</b></td>
                            <td class="text-end">{{ get_local_currency()->simbol }}
                                {{ formatNumber($fund_submission_customer->exchange_rate_gap_idr) }}
                            </td>
                            <td>{{ $fund_submission_customer->exchange_rate_gap_note }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr class="table-info">
                    <td><b>TOTAL {{ $model->currency->kode }}</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-end"><b>{{ $model->currency->simbol }}
                            {{ formatNumber($model->fund_submission_customers->sum('receive_amount')) }}</b>
                    </td>
                    <td></td>
                </tr>
                @if ($model->currency_id != $model->fund_submission_supplier->currency_id)
                    <tr class="table-info">
                        <td><b>TOTAL {{ $model->fund_submission_supplier->currency->kode }}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>{{ $model->fund_submission_supplier->currency->simbol }}
                                {{ formatNumber($model->fund_submission_customers->sum('receive_amount_foreign')) }}</b>
                        </td>
                        <td></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endif
