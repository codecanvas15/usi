@include('components.validate-error')
<form action='{{ $model ? route("$routeName.update", $model) : route("$routeName.store") }}' method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="form-group">
        <x-input name="name" placeholder="Masukkan nama" label="name" :value="$model->name ?? (old('name') ?? '')" required autofocus />
    </div>
    <div class="form-group">
        <x-select name="type" id="type" label="Jenis" required autofocus>
            <option value="tunjangan" {{ $model ? ($model->type == 'tunjangan' ? 'selected' : '') : '' }}>Tunjangan</option>
            <option value="potongan" {{ $model ? ($model->type == 'potongan' ? 'selected' : '') : '' }}>Potongan</option>
        </x-select>
    </div>
    <div class="form-group">
        <x-input name="percentage" placeholder="Masukkan persentase" label="persentase" :value="formatNumber($model->percentage ?? 0) ?? (old('percentage') ?? '')" required autofocus class="commas-form" />
    </div>

    <x-button type="submit" color="primary" icon="save" label="Simpan" />
</form>
