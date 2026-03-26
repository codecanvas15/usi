<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model)}}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="name" value="{{ $model->name ?? '' }}" required autofucus/>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}"/>
            <x-button type="submit" color="primary" label="Save data"/>
        </div>
    </div>
</form>