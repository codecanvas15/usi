<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="nama" name="nama" value="{{ $model->nama ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <x-select name="type" id="type" label="type" value="{{ $model->type ?? '' }}" required>
                <option value="">Pilih Item</option>
                @foreach (get_vechicle_types() as $key => $item)
                    <option value="{{ $key }}" {{ $model && $model->type == $key ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="col-md-4">
            <div class="form-group" id="kapasitas-form">
                {{-- <x-input type="text" label="kapasitas" id="kapasitas" name="kapasitas" value="{{ $model->kapasitas ?? '' }}" required /> --}}
            </div>
        </div>
        <div class="col-md-4">
            <x-select name="tahun_pembuatan" id="tahun_pembuatan" label="tahun_pembuatan" value="{{ $model->tahun_pembuatan ?? '' }}" required>
                <option value="">Pilih Item</option>
                @foreach (range(strftime('%Y', time()), 1900) as $key => $item)
                    <option value="{{ $item }}" {{ $model && $model->tahun_pembuatan == $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="nomor_lambung" name="nomor_lambung" value="{{ $model->nomor_lambung ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="nomor_stnk" name="nomor_stnk" value="{{ $model->nomor_stnk ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="nomor_rangka" name="nomor_rangka" value="{{ $model->nomor_rangka ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="nomor_mesin" name="nomor_mesin" value="{{ $model->nomor_mesin ?? '' }}" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="plat_nomor" name="plat_nomor" value="{{ $model->plat_nomor ?? '' }}" required />
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
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#type').change(function(e) {
                e.preventDefault();

                const input_kapasitas = `<x-input type="text" label="kapasitas" id="kapasitas" name="kapasitas" value="10000" required readonly />`;
                const select_kapasitas = `<x-select name="kapasitas" id="kapasitas" label="kapasitas" required>
                                                <option value="">Pilih Item</option>
                                                <option value="16000">16000</option>
                                                <option value="24000">24000</option>
                                            </x-select>`;

                let value = $(this).val();

                if (value == '4x2 truck') {
                    $('#kapasitas-form').html('');
                    $('#kapasitas-form').html(input_kapasitas);
                } else if (value == '6x4 truck') {
                    $('#kapasitas-form').html('');
                    $('#kapasitas-form').html(select_kapasitas);
                    $('.select2').select2();
                } else {
                    $('#kapasitas-form').html('');
                }

                $('#kapasitas').attr('readonly', true);
            });
        });
    </script>
@endpush
