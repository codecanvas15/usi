@include('components.validate-error')
<form action='{{ $model ? route("$routeName.update", $model) : route("$routeName.store") }}' method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="form-group">
        <x-input name="name" placeholder="Masukkan nama" label="name" :value="$model->name ?? (old('name') ?? '')" required autofocus />
    </div>

    <x-button type="submit" color="primary" icon="save" label="Simpan" />
</form>
