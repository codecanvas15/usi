    <x-card-data-table title="list hutang & retur">
        <x-slot name="table_content">
            <div class="row">
                <div class="col text-end">
                    <x-button color="success" label="Pilih Purchase Invoice" type="button" class="btn-sm mb-3" onclick="getSupplierInvoiceSelect()" />
                    <div class="modal fade" id="supplierInvoiceSelectModal" aria-hidden="true" aria-labelledby="supplierInvoiceSelectModalLabel" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="supplierInvoiceSelectModalLabel">Pilih Purchase Invoice</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="supplier_invoice_select_table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Tanggal</th>
                                                    <th class="text-center">Purchase Invoice</th>
                                                    <th class="text-center">Jatuh Tempo</th>
                                                    <th class="text-center">Currency</th>
                                                    <th class="text-center">Rate</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">Sisa</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="data_supplier_invoice_select">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <x-button color="info" label="simpan" type="button" onclick="saveSelectedSupplierInvoice()" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="bg-info">
                                <tr>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('purchase invoice') }}</th>
                                    <th>{{ Str::headline('currency') }}</th>
                                    <th class="text-end">{{ Str::headline('rate') }}</th>
                                    <th class="text-end">{{ Str::headline('sisa') }} <span class="supplier_invoice_currency_id_symbol">{{ $model->fund_submission_supplier->currency->kode }}</span></th>
                                    <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ $model->currency->kode }}</span></th>
                                    <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="supplier_invoice_currency_id_symbol">{{ $model->fund_submission_supplier->currency->kode }}</span></th>
                                    <th class="text-end">{{ Str::headline('selisih bayar') }}</th>
                                    <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                    <th>{{ Str::headline('ket') }}</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody id="selected_supplier_invoice_table">
                                @foreach ($fund_submission_supplier_details as $supplier_invoice)
                                    <tr id="selected_supplier_invoice_row_{{ $supplier_invoice->supplier_invoice_parent_id }}">
                                        <td>
                                            {{ localDate($supplier_invoice->supplier_invoice_parent->date) }}
                                            <input type="hidden" name="fund_submission_supplier_detail_id[]" value="{{ $supplier_invoice->id }}">
                                            <input type="hidden" name="supplier_invoice_parent_id[]" value="{{ $supplier_invoice->supplier_invoice_parent_id }}">
                                            <input type="hidden" id="item_receiving_reports_{{ $supplier_invoice->supplier_invoice_parent_id }}" name="item_receiving_reports[]" value="{{ $supplier_invoice->fund_submission_supplier_lpbs }}">
                                        </td>
                                        <td>{{ $supplier_invoice->supplier_invoice_parent->code }}</td>
                                        <td>{{ $supplier_invoice->supplier_invoice_parent->currency->nama }}</td>
                                        <td class="text-end">
                                            {{ formatNumber($supplier_invoice->supplier_invoice_parent->exchange_rate) }}
                                            <input type="hidden" id="exchange_rate_{{ $supplier_invoice->supplier_invoice_parent_id }}" value="{{ $supplier_invoice->supplier_invoice_parent->exchange_rate }}">
                                        </td>
                                        <td class="text-end">
                                            <span id="outstanding_amount_text_{{ $supplier_invoice->supplier_invoice_parent_id }}">{{ formatNumber($supplier_invoice->outstanding_amount) }}</span>
                                            @if (floatFormat($supplier_invoice->outstanding_amount) != floatFormat($supplier_invoice->original_outstanding))
                                                <br><span class="badge bg-warning">({{ $supplier_invoice->supplier_invoice_parent->currency->simbol }} {{ formatNumber($supplier_invoice->original_outstanding) }})</span>
                                            @endif
                                            <input type="hidden" id="outstanding_amount_{{ $supplier_invoice->supplier_invoice_parent_id }}" name="outstanding_amount[]" value="{{ thousand_to_float(formatNumber($supplier_invoice->outstanding_amount)) }}">
                                        </td>
                                        <td class="text-end">
                                            <span id="amount_text_{{ $supplier_invoice->supplier_invoice_parent_id }}">{{ formatNumber($supplier_invoice->amount) }}</span>
                                            <input type="hidden" id="amount_{{ $supplier_invoice->supplier_invoice_parent_id }}" name="amount[]" value="{{ thousand_to_float(formatNumber($supplier_invoice->amount)) }}">
                                        </td>
                                        <td class="text-end d-none column-multi-currency">
                                            <span id="amount_foreign_text_{{ $supplier_invoice->supplier_invoice_parent->id }}">{{ formatNumber($supplier_invoice->amount_foreign) }}</span>
                                            <input type="hidden" id="amount_foreign_{{ $supplier_invoice->supplier_invoice_parent->id }}" name="amount_foreign[]" value="{{ thousand_to_float(formatNumber($supplier_invoice->amount_foreign)) }}">
                                            <input type="hidden" id="amount_gap_foreign_{{ $supplier_invoice->supplier_invoice_parent->id }}" name="amount_gap_foreign[]" value="{{ thousand_to_float(formatNumber($supplier_invoice->amount_gap_foreign)) }}">
                                            <input type="hidden" id="is_clearing_{{ $supplier_invoice->supplier_invoice_parent->id }}" name="is_clearing[]" value="{{ $supplier_invoice->is_clearing }}">
                                            <input type="hidden" id="clearing_coa_id_{{ $supplier_invoice->supplier_invoice_parent->id }}" name="clearing_coa_id[]" value="{{ $supplier_invoice->coa->id ?? '' }}">
                                            <input type="hidden" id="clearing_coa_name_{{ $supplier_invoice->supplier_invoice_parent->id }}" value="{{ $supplier_invoice->coa->account_code ?? '' }} - {{ $supplier_invoice->coa->name ?? '' }}">
                                        </td>
                                        <td class="text-end">
                                            <span id="amount_gap_foreign_text_{{ $supplier_invoice->supplier_invoice_parent->id }}">{{ formatNumber($supplier_invoice->amount_gap_foreign) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span id="exchange_rate_gap_text_{{ $supplier_invoice->supplier_invoice_parent->id }}">{{ formatNumber($supplier_invoice->exchange_rate_gap_idr) }}</span>
                                            <input type="hidden" id="exchange_rate_gap_{{ $supplier_invoice->supplier_invoice_parent->id }}" name="exchange_rate_gap[]" value="{{ thousand_to_float(formatNumber($supplier_invoice->exchange_rate_gap_idr)) }}">
                                        </td>
                                        <td>
                                            <span id="note_text_{{ $supplier_invoice->supplier_invoice_parent_id }}"> {{ $supplier_invoice->note }}</span>
                                            <input type="hidden" id="note_{{ $supplier_invoice->supplier_invoice_parent_id }}" name="note[]" value="{{ $supplier_invoice->note }}">
                                            <input type="hidden" id="clearing_note_{{ $supplier_invoice->supplier_invoice_parent_id }}" name="clearing_note[]" value="{{ $supplier_invoice->clearing_note }}">
                                            <input type="hidden" id="exchange_rate_gap_note_{{ $supplier_invoice->supplier_invoice_parent_id }}" name="exchange_rate_gap_note[]" value="{{ $supplier_invoice->exchange_rate_gap_note }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editSelectedSupplierInvoice({{ $supplier_invoice->supplier_invoice_parent_id }})">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_supplier_invoice_row_{{ $supplier_invoice->supplier_invoice_parent_id }}').remove();calculateData()">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">TOTAL</th>
                                    <th class="text-end" id="outstanding_amount_total">0</th>
                                    <th class="text-end" id="amount_total">0</th>
                                    <th class="text-end d-none column-multi-currency" id="amount_foreign_total">0</th>
                                    <td></td>
                                    <th class="text-end" id="exchange_rate_gap_total">0</th>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col text-end">
                    <x-button color="success" label="Pilih Retur" type="button" class="btn-sm mb-3" onclick="getReturnSelect()" />
                    <div class="modal fade" id="returnSelectModal" aria-hidden="true" aria-labelledby="returnSelectModalLabel" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="returnSelectModalLabel">Pilih Retur</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body table-responsive">
                                    <table class="table table-striped" id="return_select_table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Nomor Retur</th>
                                                <th class="text-center">Currency</th>
                                                <th class="text-center">Rate</th>
                                                <th class="text-end">Total</th>
                                                <th class="text-end">Sisa</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="data_return_select">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <x-button color="info" label="simpan" type="button" onclick="saveSelectedReturn()" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
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
                                    <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="supplier_invoice_currency_id_symbol">{{ $model->fund_submission_supplier->currency->kode }}</span></th>
                                    <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody id="selected_return_table">
                                @forelse ($model->fund_submission_purchase_returns as $fund_submission_purchase_return)
                                    <tr id="selected_return_row_{{ $fund_submission_purchase_return->purchase_return_id }}">
                                        <td>
                                            {{ $fund_submission_purchase_return->purchase_return->date }}
                                            <input type="hidden" name="fund_submission_return_id[]" value="{{ $fund_submission_purchase_return->id }}">
                                            <input type="hidden" name="purchase_return_id[]" value="{{ $fund_submission_purchase_return->purchase_return_id }}">
                                            <input type="hidden" name="return_total[]" id="return_total_{{ $fund_submission_purchase_return->purchase_return_id }}" value="{{ formatNumber($fund_submission_purchase_return->purchase_return->total) }}">
                                        </td>
                                        <td>{{ $fund_submission_purchase_return->purchase_return->code }}</td>
                                        <td>{{ $fund_submission_purchase_return->purchase_return->currency->nama }}</td>
                                        <td class="text-end">
                                            <input type="text" class="form-control commas-form text-end" id="return_exchange_rate_{{ $fund_submission_purchase_return->purchase_return_id }}" value="{{ formatNumber($fund_submission_purchase_return->exchange_rate ?? 0) }}" readonly>
                                        </td>
                                        <td class="text-end">
                                            <input type="text" class="form-control commas-form text-end" id="return_outstanding_amount_{{ $fund_submission_purchase_return->purchase_return_id }}" name="return_outstanding_amount[]" value="{{ formatNumber($fund_submission_purchase_return->outstanding_amount ?? 0) }}" readonly>
                                        </td>
                                        <td class="text-end">
                                            <input type="text" class="form-control commas-form text-end" id="return_amount_{{ $fund_submission_purchase_return->purchase_return_id }}" name="return_amount[]" value="{{ formatNumber($fund_submission_purchase_return->amount) }}" onkeyup="calculate_row_return({{ $fund_submission_purchase_return->purchase_return_id }}, false)">
                                        </td>
                                        <td class="text-end d-none column-multi-currency">
                                            <input type="text" class="form-control commas-form text-end" id="return_amount_foreign_{{ $fund_submission_purchase_return->purchase_return_id }}" name="return_amount_foreign[]" value="{{ formatNumber($fund_submission_purchase_return->amount_foreign) }}" onkeyup="calculate_row_return({{ $fund_submission_purchase_return->purchase_return_id }}, true)">
                                        </td>
                                        <td class="text-end">
                                            <input type="text" class="form-control commas-form text-end" id="return_exchange_rate_gap_{{ $fund_submission_purchase_return->purchase_return_id }}" name="return_exchange_rate_gap[]" value="{{ formatNumber($fund_submission_purchase_return->exchange_rate_gap_idr) }}" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_return_row_{{ $fund_submission_purchase_return->purchase_return_id }}').remove();">
                                                <i class="fa fa-trash"></i>
                                            </button>
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
                                    <th class="text-end" id="return_outstanding_amount_total">0</th>
                                    <th class="text-end" id="return_amount_total">0</th>
                                    <th class="text-end d-none column-multi-currency" id="return_amount_foreign_total">0</th>
                                    <th class="text-end" id="return_exchange_rate_gap_total">0</th>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-card-data-table>
