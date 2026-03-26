<x-card-data-table title="Bank">
    <x-slot name="table_content">
        @foreach ($model->employee_banks as $item)
            <div class="row border-top border-primary mt-10 pt-10">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nama Bank</label>
                        <p>{{ $item->bank_name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Atas Nama</label>
                        <p>{{ $item->behalf_of }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Nomor Rekening</label>
                        <p>{{ $item->account_number }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit.step7', [
                'employee_id' => $model->id,
            ])" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
