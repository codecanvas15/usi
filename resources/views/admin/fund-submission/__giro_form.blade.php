<div class="col-md-12">
    <div class="form-group">
        @if (isset($data))
            <x-input type="text" id="due_date" name="due_date" label="tanggal jatuh tempo" class="datepicker-input" value="{{ isset($data) ? localDate($data->due_date) : date('d-m-Y') }}" onchange="checkGiroDate()" required />
        @else
            <x-input type="text" id="due_date" name="due_date" label="tanggal jatuh tempo" class="datepicker-input" value="{{ isset($data) ? localDate($data->due_date) : date('d-m-Y') }}" onchange="checkGiroDate()" />
        @endif
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        @if (isset($data))
            <x-input type="text" id="cheque_no" name="cheque_no" label="no cheque" value="{{ isset($data) ? $data->cheque_no : '' }}" required />
        @else
            <x-input type="text" id="cheque_no" name="cheque_no" label="no cheque" value="{{ isset($data) ? $data->cheque_no : '' }}" />
        @endif
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        @if (isset($data))
            <x-input type="text" id="realization_bank" name="realization_bank" label="bank pencairan" value="{{ isset($data) ? $data->realization_bank : '' }}" required />
        @else
            <x-input type="text" id="realization_bank" name="realization_bank" label="bank pencairan" value="{{ isset($data) ? $data->realization_bank : '' }}" />
        @endif
    </div>
</div>

@push('script')
    <script>
        // Check tanggal giro tidak boleh kurang dari pengajuan dana
        function checkGiroDate(element = $('#date'), element2 = $('#due_date')) {
            var parseDate = element.val().split('-');
            var reformateDate = parseDate[2] + '-' + parseDate[1] + '-' + parseDate[0];

            var parseDate2 = element2.val().split('-');
            var reformateDate2 = parseDate2[2] + '-' + parseDate2[1] + '-' + parseDate2[0];

            var date = new Date(reformateDate);
            var date2 = new Date(reformateDate2);

            // check if giro date is less than fund submission date then call show alert function
            if (date2.getTime() < date.getTime()) {
                showAlert('', 'Tanggal jatuh tempo tidak boleh kurang dari tanggal pengajuan dana', 'error');
                element2.val('');
            }
        }
    </script>
@endpush
