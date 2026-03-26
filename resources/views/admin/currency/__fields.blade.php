<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="nama" value="{{ $model->nama ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="kode" name="kode" value="{{ $model->kode ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="simbol" name="simbol" value="{{ $model->simbol ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="remark" name="remark" value="{{ $model->remark ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="negara" name="negara" value="{{ $model->negara ?? '' }}" required />
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="kurs" name="exchange_rate" value="{{ $model->exchange_rate ?? '' }}" required />
            </div>
        </div> --}}
        <div class="col-md-4 align-self-end">
            <x-input-checkbox label="active" checked="{{ $model && $model->active ? 'checked' : '' }}" name="active" id="checkbox-1" />
        </div>
    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>
