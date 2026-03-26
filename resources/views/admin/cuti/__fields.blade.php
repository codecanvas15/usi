<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        @if (!$model)
            <div class="col-md-3">
                <x-select name="employee_id" id="employee_id" label="Karyawan" value="{{ $model->employee_id ?? '' }}" required>
                    <option value="">Pilih Item</option>
                </x-select>
            </div>
        @endif

        @if ($model)
            <div class="col-md-3">
                <div class="form-group">
                    <x-input type="text" id="name_employee" label="Karyawan" value="{{ $model->name ?? '0' }}" disabled name="name_employee" required autofucus />
                </div>
            </div>
        @endif
        <div class="col-md-3">
            <div class="form-group">
                <x-input type="text" id="jatah_cuti" label="Sisa Cuti" value="{{ $model->jatah_cuti ?? '0' }}" disabled name="jatah_cuti" required autofucus />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" id="dari_tanggal" label="Dari Tanggal" name="dari_tanggal" value="{{ localDate($model->dari_tanggal) ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <x-input class="datepicker-input" id="sampai_tanggal" label="Sampai Tanggal" name="sampai_tanggal" value="{{ localDate($model->sampai_tanggal) ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-12">
            <x-text-area name="perihal" label="Perihal" id="perihal" cols="30" rows="20">{{ $model->perihal ?? '' }}</x-text-area>
        </div>

    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>

@push('script')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#form_end_date').hide();

            initSelect2Search('employee_id', "{{ route('admin.select.employee-with-user') }}", {
                id: "id",
                text: "name"
            });

            $('#employee_id').on('change', function() {
                console.log('sini ya')
                console.log(this.value)
                $.ajax({
                    type: "GET",
                    url: `{{ route('admin.select.employee-with-id') }}/${this.value}`,
                    success: function({
                        data
                    }) {
                        console.log(data)
                        $('#jatah_cuti').val(data[0].jatah_cuti)
                    }
                });
            })
        });
    </script>
@endpush
