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
        <x-input name="note" placeholder="Masukkan keterangan" label="note" :value="$model->note ?? (old('note') ?? '')" required autofocus />
    </div>
    <div class="form-group">
        <x-input name="amount" placeholder="Masukkan jumlah" label="jumlah" :value="formatNumber($model->amount ?? 0) ?? (old('amount') ?? '')" required autofocus class="commas-form" />
    </div>

    <x-button type="submit" color="primary" icon="save" label="Simpan" />
</form>
