@include('components.validate-error')
<form action='{{ $model ? route("$routeName.update", $model) : route("$routeName.store") }}' method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="form-group">
        <x-input name="min" placeholder="Minimal Penghasilan/tahun" label="minimal" :value="formatNumber($model->min ?? 0) ?? (old('min') ?? '')" required autofocus class="commas-form" />
    </div>
    <div class="form-group">
        <x-input name="max" placeholder="Maksimal Penghasilan/tahun" label="maksimal" :value="formatNumber($model->max ?? 0) ?? (old('max') ?? '')" required autofocus class="commas-form" />
    </div>
    <div class="form-group">
        <x-input name="percentage" placeholder="Masukkan persentase" label="persentase" :value="formatNumber($model->percentage ?? 0) ?? (old('percentage') ?? '')" required autofocus class="commas-form" />
    </div>

    <x-button type="submit" color="primary" icon="save" label="Simpan" />
</form>
