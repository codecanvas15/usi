<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <x-select name="employee_id" id="employee_id" label="employee" value="{{ $model->employee_id ?? '' }}" required>
                @if ($model && $model->employee_id)
                    <option value="{{ $model->employee_id }}" selected>{{ $model->employee?->name }}</option>
                @endif
            </x-select>
            <small class="text-primary">nama, email, nomor induk karyawan</small>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="nama" value="{{ $model->nama ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="alamat" name="alamat" value="{{ $model->alamat ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="telpon" name="telpon" value="{{ $model->telpon ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="number" id="kapasitas" name="kapasitas" value="{{ $model->kapasitas ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="longitude" name="longitude" value="{{ $model->longitude ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="latitude" name="latitude" value="{{ $model->latitude ?? '' }}" />
            </div>
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
            initSelect2Search('employee_id', "{{ route('admin.select.employee') }}", {
                id: "id",
                text: "name"
            });
        });
    </script>
@endpush
