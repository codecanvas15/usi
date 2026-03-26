<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <x-select name="branch_id" id="branch_id" label="branch" required>
                    <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                </x-select>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input type="text" name="fund_submission_date" label="tanggal pengajuan dana" id="fund_submission_date" value="{{ localDate($model->date) }}" required readonly />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input type="text" name="to_name" label="kepada" id="to_name" value="{{ $model->to_name }}" required readonly />
                </div>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-6 mb-5">
                <x-select name="coa_id" id="coa_id" required label="kas/bank">
                    @if ($model->coa)
                        <option value="{{ $model->coa->id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                    @endif
                </x-select>
                <x-button type="button" color="info" icon="pen-to-square" label="edit" size="sm" dataToggle="modal" dataTarget="#edit-bank-modal" />
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
                    <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                </div>
            </div>
            <div class="col-md-6">
                <x-select name="invoice_return_id" id="invoice_return_id" label="penerimaan retur">
                    @if ($model->invoice_return)
                        <option value="{{ $model->invoice_return_id }}">{{ $model->invoice_return->customer->nama }} - {{ $model->invoice_return->code }}</option>
                    @endif
                </x-select>
            </div>
            <div class="col-md-6">
                <x-select name="cash_advance_receive_id" id="cash_advance_receive_id" label="pengembalian uang muka">
                    @if ($model->cash_advance_receive)
                        <option value="{{ $model->cash_advance_receive_id }}">{{ $model->cash_advance_receive->code }}</option>
                    @endif
                </x-select>
            </div>
            <div class="col-md-6">
                <x-select name="currency_id" id="currency_id" label="currency" required>
                    <option value="{{ $model->currency->id }}" selected>{{ $model->currency->nama }}</option>
                </x-select>
            </div>
            <div class="col-md-6">
                @if ($model->currency->is_local)
                    <div class="form-group">
                        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                    </div>
                @else
                    <div class="form-group">
                        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <x-select name="project_id" id="project_id" label="project">
                    @if ($model->project)
                        <option value="{{ $model->project_id }}">{{ $model->project->name }}</option>
                    @endif
                </x-select>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <x-input type="text" label="note" name="reference" id="reference" value="{!! $model->reference !!}" readonly />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
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
    <div class="col-md-12 mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ Str::headline('akun') }}</th>
                    <th>{{ Str::headline('keterangan') }}</th>
                    <th class="text-end">{{ Str::headline('jumlah') }} {{ $model->currency->kode }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="outgoing-payment-detail-data">
                <input type="hidden" id="count_rows" value="{{ count($model->fund_submission_generals) }}">
                @foreach ($model->fund_submission_generals as $key => $fund_submission_general)
                    <tr id="outgoing-payment-detail-{{ $key }}_edit">
                        <td>
                            <input type="hidden" name="is_return[]" value="{{ $fund_submission_general->invoice_return ? 'true' : '' }}" />
                            <input type="hidden" name="type[]" value="{{ $fund_submission_general->type }}" />
                            <input type="hidden" name="fund_submission_general_id[]" value="{{ $fund_submission_general->id }}" />
                            <select name="coa_detail_id[]" id="coa_detail_id_{{ $key }}_edit" class="form-control" required autofocus style="width:100%">
                                <option value="{{ $fund_submission_general->coa_id }}">{{ $fund_submission_general->coa->account_code }} - {{ $fund_submission_general->coa->name }}</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="note_{{ $key }}_edit" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $fund_submission_general->note }}" readonly />
                        </td>
                        <td>
                            <input type="text" id="debit_{{ $key }}_edit" name="debit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($fund_submission_general->debit) }}" readonly />
                        </td>
                        <td>

                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">TOTAL</th>
                    <th class="text-end" id="debit_total">{{ formatNumber($model->fund_submission_generals()->sum('debit')) }}</th>
                    <input type="hidden" id="debit_total_hide" value="{{ $model->fund_submission_generals()->sum('debit') }}">
                </tr>
                <tr>
                    <th class="text-end" colspan="3">
                        <x-button color="success" label="Tambah Baris +" type="button" onclick="addNewOutgoingPaymentDetailRow()" class="btn-sm" />
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
                <x-input name="change_bank_reason" id="change-bank-reason" label="alasan ganti bank"></x-input>
            </div>
        </div>

        <x-slot name="modal_footer">
            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" dataDismiss="modal" />
            <x-button type="button" color="primary" label="Save data" id="save-edit-bank-modal" />
        </x-slot>
    </x-slot>
</x-modal>

<script>
    $(document).ready(function() {
        initSelect2SearchPagination(`coa-bank-edit-select`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: "Cash & Bank",
            currency_id: '{{ $model->currency_id }}'
        }, '#edit-bank-modal');

        $("#save-edit-bank-modal").click(function(e) {
            e.preventDefault();

            if ($('#coa-bank-edit-select').val() != {{ $model->coa_id }}) {
                if (!$("#change-bank-reason").val()) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Tolong masukkan alasan ganti bank!",
                    });

                    return;
                }

                if (!$("#coa-bank-edit-select").val()) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Tolong pilih bank baru!",
                    });

                    return;
                }
            }

            $('#coa_id').val($("#coa-bank-edit-select").val());
            let coaData = $("#coa-bank-edit-select").select2("data");

            $("#coa_id").html(`
                            <option value="${$("#coa-bank-edit-select").val()}">${coaData[0].text}</value>
                        `);

            $("#change-bank-reason-input").val(
                $("#change-bank-reason").val()
            );

            $("#sequence_code").val(null);
            $("#sequence_code").trigger("blur");

            $("#edit-bank-modal").modal("hide");
        });

    });
</script>
