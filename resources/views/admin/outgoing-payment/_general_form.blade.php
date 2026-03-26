<div class="row">
    <div class="col-md-3">
        <x-select name="branch_id" id="branch_id" label="branch" required>
            <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
        </x-select>
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-3">
        <div class="form-group">
            <x-input type="text" name="to_name" label="kepada" id="to_name" value="" required />
        </div>
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-3">
        <x-select name="coa_id" id="coa_id" required label="kas/bank" onchange="get_coa_detail($(this))">
        </x-select>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
            <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
        </div>
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-3">
        <x-select name="invoice_return_id" id="invoice_return_id" label="pembayaran retur">
        </x-select>
    </div>
    <div class="col-md-3">
        <x-select name="cash_advance_receive_id" id="cash_advance_receive_id" label="pengembalian uang muka">
        </x-select>
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-3">
        <x-select name="currency_id" id="currency_id" label="currency" required>
            <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
        </x-select>
    </div>
    <div class="col-md-3">
        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-3">
        <x-select name="project_id" id="project_id" label="project">

        </x-select>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <x-input type="text" label="note" name="reference" id="reference" />
        </div>
    </div>
    <div class="col-md-12 mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ Str::headline('akun') }}</th>
                    <th>{{ Str::headline('keterangan') }}</th>
                    <th class="text-end">{{ Str::headline('jumlah') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="outgoing-payment-detail-data">
                <input type="hidden" id="count_rows" value="0">
                <tr id="outgoing-payment-detail-0">
                    <td>
                        <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control" required autofocus style="width:100%">
                        </select>
                    </td>
                    <td>
                        <input type="text" id="note_0" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" />
                    </td>
                    <td>
                        <input type="text" id="debit_0" name="debit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="" />
                    </td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">TOTAL</th>
                    <th class="text-end" id="debit_total">0</th>
                    <th></th>
                    <input type="hidden" id="debit_total_hide">
                </tr>
                <tr>
                    <th class="text-end" colspan="3">
                        <x-button color="success" label="Tambah Baris +" type="button" onclick="addNewOutgoingPaymentDetailRow()" class="btn-sm" />
                    </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
