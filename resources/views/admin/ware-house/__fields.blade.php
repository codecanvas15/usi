<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif

    <div class="row">
        @if (get_current_branch()->is_primary)
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="branch_id" id="branch_id" label="cabang" required="required">
                        @if ($model && $model->branch)
                            <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                        @endif
                    </x-select>
                </div>
            </div>
        @endif

        @if (!$model)
            <div class="col-md-4">
                <div class="form-group">
                    <x-select name="type" label="tipe" required="required">
                        <option value="general" @if ($model && $model->type == 'general') selected @endif>General</option>
                        <option value="trading" @if ($model && $model->type == 'trading') selected @endif>Trading</option>
                        <option value="" disabled selected>-- pilih warehouse type --</option>
                    </x-select>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="form-group">
                    <x-input type="text" label="tipe" value="{{ $model->type }}" disabled="disabled" required />
                </div>
            </div>
        @endif

    </div>

    <div class="row">
        @if (strtolower($model->nama ?? '') == 'gudang reject')
            <div class="col-md-4">
                <div class="form-group">
                    <x-input type="text" id="nama" label="nama" name="nama" value="{{ $model->nama ?? '' }}" required autofucus readonly />
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="form-group">
                    <x-input type="text" id="nama" label="nama" name="nama" value="{{ $model->nama ?? '' }}" required autofucus />
                </div>
            </div>
        @endif
        <div class="col-md-8">
            <div class="form-group">
                <label for="deskripsi">Deskripsi
                    <span class="text-primary">* </span>
                </label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror mt-2" type="text" id="deskripsi" name="deskripsi" rows="5" placeholder="Deskripsi">{!! $model->deskripsi ?? '' !!}</textarea>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="jalan" label="jalan" name="jalan" value="{{ $model->jalan ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="kota" label="kota" name="kota" value="{{ $model->kota ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="provinsi" label="provinsi" name="provinsi" value="{{ $model->provinsi ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="longitude" label="longitude" name="longitude" value="{{ $model->longitude ?? '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="zip_code" label="zip_code" name="zip_code" value="{{ $model->zip_code ?? '' }}" />
            </div>
        </div>

        {{-- <div class="col-md-4">
            <div class="form-group">
                <label for="type">Type
                    <span class="text-primary">* </span>
                </label>
                <select class="form-control mt-2" name="type" id="type">
                    <option value="general" @if ($model && $model->type == 'general') selected @endif>General</option>
                    <option value="trading" @if ($model && $model->type == 'trading') selected @endif>Trading</option>
                    <option value="" disabled selected>-- pilih warehouse type --</option>
                </select>
            </div>
        </div> --}}

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
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>

    <script>
        $(document).ready(function() {
            initBranchSelect('#branch_id');
        });
    </script>
@endpush
