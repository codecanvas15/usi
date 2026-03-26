<x-card-data-table title="list hutang & retur">
    <x-slot name="table_content">
        <div class="row">
            <div class="col text-end">
                <x-button color="success" label="Pilih Purchase Invoice" type="button" class="btn-sm mb-3" onclick="getSupplierInvoiceSelect()" />
                <div class="modal fade" id="supplierInvoiceSelectModal" aria-hidden="true" aria-labelledby="supplierInvoiceSelectModalLabel" tabindex="-1">
                    <div class="modal-dialog modal-xl">
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
                                <th class="text-end">{{ Str::headline('sisa') }} <span class="supplier_invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="supplier_invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                <th class="text-end">{{ Str::headline('selisih bayar') }}</th>
                                <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                <th>{{ Str::headline('ket') }}</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody id="selected_supplier_invoice_table">
                            <tr id="row_supplier_invoice_empty">
                                <td colspan="11" class="text-center">Belum ada data yang dipilih</td>
                            </tr>
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
                                <th class="text-end">{{ Str::headline('sisa') }} <span class="supplier_invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                <th class="text-end">{{ Str::headline('bayar') }} <span class="currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                <th class="text-end d-none column-multi-currency">{{ Str::headline('bayar') }} <span class="supplier_invoice_currency_id_symbol">{{ get_local_currency()->kode }}</span></th>
                                <th class="text-end">{{ Str::headline('selisih kurs') }}</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody id="selected_return_table">
                            <tr id="row_return_empty">
                                <td colspan="9" class="text-center">Belum ada data yang dipilih</td>
                            </tr>
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
