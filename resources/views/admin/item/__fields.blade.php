<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    @if (request()->get('type'))
        <div class="row">
            <div class="col-md-4">
                <x-select name="type" id="type" label="type" value="{{ $model->type ?? '' }}" required disabled>
                    <option value="">Pilih Item</option>
                    <option value="general" {{ request()->get('type') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="trading" {{ request()->get('type') == 'trading' ? 'selected' : '' }}>Trading</option>
                    <option value="service" {{ request()->get('type') == 'service' ? 'selected' : '' }}>Service</option>
                    <option value="transport" {{ request()->get('type') == 'transport' ? 'selected' : '' }}>Transport</option>
                </x-select>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-4">
                <x-select name="type" id="type" label="type" value="{{ $model->type ?? '' }}" required disabled>
                    <option value="">Pilih Item</option>
                    <option value="general" {{ $model && $model->type == 'general' ? 'selected' : '' }}>General</option>
                    <option value="trading" {{ $model && $model->type == 'trading' ? 'selected' : '' }}>Trading</option>
                    <option value="service" {{ $model && $model->type == 'service' ? 'selected' : '' }}>Service</option>
                    <option value="transport" {{ $model && $model->type == 'transport' ? 'selected' : '' }}>Transport</option>
                </x-select>
            </div>
        </div>
    @endif

    <div class="row mt-10">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="nama" label="nama" name="nama" value="{{ $model->nama ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="kode" label="kode" name="kode" value="{{ $model->kode ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="deskripsi" name="deskripsi" value="{{ $model->deskripsi ?? '' }}" id="deskripsi" required />
            </div>
        </div>
        <div class="col-md-4">
            <x-select name="item_category_id" id="item_category_id" label="Item Category" value="{{ $model->item_category_id ?? '' }}" required>
                @if ($model)
                    <option value="{{ $model->item_category_id }}">{{ $model->item_category->nama }}</option>
                @endif
            </x-select>
        </div>
        <div class="col-md-4">
            <x-select name="unit_id" id="unit_id" label="Satuan" value="{{ $model->unit_id ?? '' }}" required disabled>
                @if ($model)
                    <option value="{{ $model->unit_id }}">{{ $model->unit?->name }}</option>
                @endif
            </x-select>
        </div>
        <div class="col-md-4">
            <x-select name="status" id="status" label="Status" value="{{ $model->status ?? '' }}" required>
                @foreach (get_item_status() as $key => $item)
                    <option value="{{ $key }}">{{ Str::headline($item['label']) }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="col-md-4">
            @if ($model)
                <x-input type="file" id="file" name="file" label="File" accept="image/png, image/jpeg, image/jpg" />
            @else
                <x-input type="file" id="file" name="file" label="File" accept="image/png, image/jpeg, image/jpg" required />
            @endif

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
        initSelect2Search('item_category_id', `{{ route('admin.select.item-category') }}`, {
            id: "id",
            text: "nama"
        }, 0, {
            item_type_id: function () {
                return handleItemCategory($('#type').val());
            }
        });

        initSelect2Search('unit_id', `{{ route('admin.select.unit') }}`, {
            id: "id",
            text: "name"
        });

        $('#nama').change(function(e) {
            e.preventDefault();
            if (!$('#deskripsi').val()) {
                $('#deskripsi').val(this.value);
            }
        });

        function handleItemCategory(value) {
            console.log('tes: ', value);
            
            switch (value) {
                case 'general':
                    return "1,3";
                case 'trading':
                    return "1";
                case 'service':
                    return "2,4";
                default:
                    return "2";
            }
        }
    </script>
    @if (!$is_edit)
        <script>
            $('#unit_id').removeAttr('disabled');
            $('#type').removeAttr('disabled');
        </script>
    @endif
@endpush
