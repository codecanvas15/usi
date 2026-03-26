<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="from_date" name="from_date" value="{{ localDate($model->from_date) ?? '' }}" required class="datepicker-input" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="to_date" name="to_date" value="{{ localDate($model->to_date) ?? '' }}" required class="datepicker-input" />
            </div>
        </div>
        <div class="col-md-4">
            <x-select name="status" label="status" id="status" required>
                <option value="open" {{ $model->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="close" {{ $model->status == 'close' ? 'selected' : '' }}>Close</option>
            </x-select>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="note" name="note" value="{{ $model->note ?? '' }}" required />
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
