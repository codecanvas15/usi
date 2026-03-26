<x-button color="warning" icon="rotate" fontawesome label="generate jurnal" size="sm" dataToggle="modal" dataTarget="#generate-modal" />
<x-modal title="generate jurnal" id="generate-modal" headerColor="warning">
    <x-slot name="modal_body">
        <form action="{{ route('admin.generate-journal.store') }}" method="post" id="generate-form">
            @csrf
            <input type="hidden" name="model" value="{{ $model }}">
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="mt-10">
                <h3>{{ Str::headline('Apakah anda yakin ingin mengenerate jurnal') }}</h3>
            </div>
            <div class="mt-10 border-top pt-10">
                <x-button type="button" color="secondary" dataDismiss="modal" label="batal" size="sm" icon="times" fontawesome />
                <x-button type="submit" color="primary" label="Ya, saya yakin" size="sm" icon="save" fontawesome />
            </div>
        </form>
    </x-slot>
</x-modal>
