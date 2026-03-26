<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <x-select name="branch_id" id="branch_id" label="branch" required>
                <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
            </x-select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <x-input type="text" label="tanggal pengajuan dana" name="fund_submission_date" id="fund_submission_date" value="{{ $model->date }}" readonly />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
            <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
        </div>
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-4">
        <x-select name="to_model" id="to_model" label="Bayar Ke" required onchange="initPaymentTo($(this))">
            <option @if ($model->to_model == 'App\Models\Employee') selected @endif value="App\Models\Employee">Karyawan</option>
            <option @if ($model->to_model == 'App\Models\Vendor') selected @endif value="App\Models\Vendor">Vendor</option>
        </x-select>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="to_id" class="form-label">Vendor/Supplier</label>
            <select name="to_id" id="to_id" class="form-control">
                <option value="{{ $model->model_reference->id }}">{{ $model->model_reference->name ?? $model->model_reference->nama }}</option>
            </select>
        </div>
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-4">
        <x-select name="currencies" id="currencies" label="currency" required>
            <option value="{{ $model->currency->id }}" selected>{{ $model->currency->kode }}</option>
        </x-select>
    </div>
    <div class="col-md-4">
        @if ($model->currency->is_local)
            <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
        @else
            <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
        @endif
    </div>
    <div class="col-md-12"></div>
    <div class="col-md-4">
        <div class="form-group">
            <x-input type="text" label="note" name="referensi" id="referensi" value="{!! $model->reference !!}" readonly />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <x-select name="project_id" id="project_id" label="project">
                @if ($model->project)
                    <option value="{{ $model->project_id }}">{{ $model->project->name }}</option>
                @endif
            </x-select>
        </div>
    </div>
    <div class="col-md-12"></div>
    @if ($model->tax)
        <div class="col-md-4">
            <x-input type="text" label="pajak" name="tax" id="tax" value="{!! $model->tax->tax_name_with_percent !!}" readonly />
        </div>
    @endif
    <div class="col-md-4">
        @if ($model->tax_number)
            <label for="tax" class="form-label">Faktur Pajak</label>
            <p>{{ $model->tax_number }}</p>
        @else
            <x-input type="text" label="pajak" name="tax" id="tax" value="{!! $model->tax_number !!}" class="tax-reference-mask" />
        @endif
    </div>
    <div class="col-md-4">
        @if ($model->tax_attachment)
            <label for="tax" class="form-label">Lampiran</label>
            <br>
            <a href="{{ asset('storage/' . $model->tax_attachment) }}" target="_blank">Lihat Lampiran</a>
        @else
            <x-input type="file" label="lampiran" name="tax_attachment" id="tax_attachment" />
        @endif
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-5">
        @if ($model->is_giro)
            @if ($model->send_payment)
                @include('admin.fund-submission.__giro_table', ['send_payment' => $model->send_payment])
                @if (!$model->send_payment->due_status_by_date['is_due'])
                    <div class="badge badge-danger d-block">
                        <h4>{{ $model->send_payment->due_status_by_date['message'] }}</h4>
                    </div>
                @endif
            @else
                <h3>Informasi Giro</h3>
                <div class="badge badge-lg badge-danger">
                    Giro batal cair, silahkan perbarui informasi giro!
                </div>
            @endif
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12 mt-4">
        <table class="table table-striped">
            <thead>
                <tr id="cash-advance-detail-0">
                    <input type="hidden" name="type[]" value="cash_bank">
                    <input type="hidden" name="position[]" value="credit">
                    <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $model->cash_advance_cash_bank->id }}">
                    <td>
                        <div class="form-group">
                            <label for="coa_detail_id_0" class="form-label">Akun Kas/Bank</label><br>
                            <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control cash_bank_coa_id" required autofocus style="width:100%">
                                <option value="{{ $model->cash_advance_cash_bank->coa_id }}">{{ $model->cash_advance_cash_bank->coa->account_code }} - {{ $model->cash_advance_cash_bank->coa->name }}</option>
                            </select>
                            <x-button type="button" color="info" icon="pen-to-square" label="edit" size="sm" dataToggle="modal" dataTarget="#edit-bank-modal" />
                        </div>
                        <input type="hidden" name="change_reason_bank" id="change-bank-reason-input">
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="note_0" class="form-label">Keterangan</label>
                            <input type="text" id="note_0" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $model->cash_advance_cash_bank->note }}" readonly />
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="note_0" class="form-label">Jumlah</label>
                            <input type="text" id="amount_0" name="amount[]" class="form-control commas-form text-end cash_bank_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($model->cash_advance_cash_bank->credit) }}" readonly />
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr id="cash-advance-detail-1">
                    <input type="hidden" name="type[]" value="cash_advance">
                    <input type="hidden" name="position[]" value="debit">
                    <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $model->cash_advance_cash_advance->id }}">
                    <td>
                        <div class="form-group">
                            <label for="coa_detail_id_1" class="form-label">Akun Uang Muka</label><br>
                            <select name="coa_detail_id[]" id="coa_detail_id_1" class="form-control cash_advance_coa_id" required autofocus style="width:100%">
                                <option value="{{ $model->cash_advance_cash_advance->coa_id }}">{{ $model->cash_advance_cash_advance->coa->account_code }} - {{ $model->cash_advance_cash_advance->coa->name }}</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="note_1" class="form-label">Keterangan</label>
                            <input type="text" id="note_1" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $model->cash_advance_cash_advance->note }}" readonly />
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="note_1" class="form-label">Jumlah</label>
                            <input type="text" id="amount_1" name="amount[]" class="form-control commas-form text-end cash_advance_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($model->cash_advance_cash_advance->debit) }}" readonly />
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr class="bg-info">
                    <th colspan="4">{{ Str::headline('biaya lain - lain') }}</th>
                </tr>
                <tr>
                    <th>{{ Str::headline('akun') }}</th>
                    <th>{{ Str::headline('keterangan') }}</th>
                    <th class="text-end">{{ Str::headline('jumlah') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="cash-advance-detail-data">
                <input type="hidden" id="count_rows" value="{{ count($model->fund_submission_cash_advances) }}">
                @forelse ($model->cash_advance_others as $key => $item)
                    <tr id="cash-advance-detail-{{ $key + 2 }}">
                        <input type="hidden" name="type[]" value="{{ $item->type }}">
                        <input type="hidden" name="position[]" value="debit">
                        <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $item->id }}">
                        <td>
                            <select name="coa_detail_id[]" id="coa_detail_id_{{ $key + 2 }}" class="form-control other_coa_id" required autofocus style="width:100%">
                                <option value="{{ $item->coa_id }}">{{ $item->coa->account_code }} - {{ $item->coa->name }}</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="note_{{ $key + 2 }}" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $item->note }}" readonly />
                        </td>
                        <td>
                            <input type="text" id="amount_{{ $key + 2 }}" name="amount[]" class="form-control commas-form text-end other_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($item->debit) }}" readonly />
                        </td>
                        <td>

                        </td>
                    </tr>
                @empty
                    <tr id="empty-row">
                        <td colspan="4" class="text-center">Tidak Ada Data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2"></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-end" colspan="4">
                        <x-button color="success" label="Tambah Baris +" type="button" onclick="addOtherCost()" class="btn-sm" />
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<x-modal title="edit bank" id="edit-bank-modal" headerColor="info">
    <x-slot name="modal_body">
        <div class="row">
            <div class="col-md-12">
                <x-select name="coa_id_bank_select" id="coa-bank-edit-select" label="kas/bank">

                </x-select>
            </div>
            <div class="col-md-12">
                <x-input name="bank_change_reason" id="change-bank-reason" label="alasan ganti bank"></x-input>
            </div>
        </div>

        <x-slot name="modal_footer">
            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" dataDismiss="modal" />
            <x-button type="button" color="primary" label="Save data" id="save-edit-bank-modal" />
        </x-slot>
    </x-slot>
</x-modal>
