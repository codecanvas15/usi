<x-card-data-table title="Minat">
    <x-slot name="table_content">
        @foreach ($model->employeeInterests as $item)
            <div class="form-group">
                <label for="">{{ $item->interest }}</label>
                <p>{{ $item->rank }}</p>
            </div>
        @endforeach
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit.step4', [
                'employee_id' => $model->id,
            ])" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
