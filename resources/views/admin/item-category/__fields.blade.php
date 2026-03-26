<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" name="nama" label="nama" value="{{ $model->nama ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="remark" name="remark" label="remark" value="{{ $model->remark ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <x-select name="item_type_id" id="item_type_id" label="item_type" value="{{ $model->item_type_id ?? '' }}" required onchange="getItemTypeCoa()">
                @if ($model->item_type ?? null)
                    <option value="{{ $model->item_type_id }}">{{ $model->item_type->nama }}</option>
                @endif
            </x-select>
        </div>
    </div>
    <div class="row" id="item-type-coa-data">
        @if (count($model->item_category_coas ?? []) > 0)
            @include('admin.item-category._item_type_coa', ['item_type_coas' => $model->item_category_coas, 'edit' => $edit, 'default_item_type_coa' => $model->item_type->item_type_coas])
        @endif
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
        initSelect2Search('item_type_id', "{{ route('admin.select.item-type') }}", {
            id: "id",
            text: "nama",
        }, 0);
    </script>
@endpush
