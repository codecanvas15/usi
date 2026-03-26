<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                @if ($model)
                    <x-input type="text" id="nama" name="name" label="nama" value="{{ $model->name ?? '' }}" required />
                @else
                    <x-input type="text" id="nama" name="name" label="nama" value="{{ $model->name ?? '' }}" required autofucus />
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                @if ($model)
                    <x-input type="text" id="kategori" name="category" label="kategori" value="{{ $model->category ?? '' }}" />
                @else
                    <x-input type="text" id="kategori" name="category" label="kategori" value="{{ $model->category ?? '' }}" />
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="deskripsi" name="description" label="deskripsi" value="{{ $model->description ?? '' }}" required autofucus />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="value" name="value" value="{{ $model ? $model->value * 100 : '' }}" required helpers="percent" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-select name="coa_sale" id="coa_sale" label="coa sale" required>
                    @if ($model)
                        @if ($model->coa_sale)
                            <option value="{{ $model->coa_sale }}">{{ $model->coa_sale_data->account_code }} - {{ $model->coa_sale_data->name }}</option>
                        @endif
                    @endif
                </x-select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-select name="coa_purchase" id="coa_purchase" label="coa purchase" required>
                    @if ($model)
                        @if ($model->coa_purchase)
                            <option value="{{ $model->coa_purchase }}">{{ $model->coa_purchase_data->account_code }} - {{ $model->coa_purchase_data->name }}</option>
                        @endif
                    @endif
                </x-select>
            </div>
        </div>
        <div class="d-flex col-md-4 align-self-end">
            <div class="form-group">
                @if ($model && $model->is_discount)
                    <x-input-checkbox name="is_discount" label="Calculate After Discount" value="{{ $model && $model->is_discount ? 1 : 0 }}" checked />
                @else
                    <x-input-checkbox name="is_discount" label="Calculate After Discount" value="" />
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-select name="type" id="type" label="tipe" required>
                    <option value="non_ppn" @if (($model->type ?? '') == 'non_ppn') selected @endif>Non PPN</option>
                    <option value="ppn" @if (($model->type ?? '') == 'ppn') selected @endif>PPN</option>
                </x-select>
            </div>
        </div>
        <div class="d-flex col-md-4 align-self-end">
            <div class="form-group">
                @if ($model && $model->is_default)
                    <x-input-checkbox name="is_default" label="Default" value="{{ $model && $model->is_default ? 1 : 0 }}" checked />
                @else
                    <x-input-checkbox name="is_default" label="Default" value="" />
                @endif
            </div>
        </div>
        <div class="d-flex col-md-4 align-self-end">
            <div class="form-group">
                @if ($model && $model->is_show_percent)
                    <x-input-checkbox name="is_show_percent" label="Tampilkan Persentase" value="{{ $model && $model->is_show_percent ? 1 : 0 }}" checked />
                @else
                    <x-input-checkbox name="is_show_percent" label="Tampilkan Persentase" value="" />
                @endif
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
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {

            initCoaSelect('#coa_sale');
            initCoaSelect('#coa_purchase');
        });
    </script>
@endpush
