<x-card-data-table title="Kelebihan">
    <x-slot name="table_content">
        <ul>
            @foreach ($model->employeeStrengthAndWeaknesses->where('type', 'strength') as $item)
                <li>{{ $item->description }}</li>
            @endforeach
        </ul>
    </x-slot>
</x-card-data-table>
<x-card-data-table title="kekurangan">
    <x-slot name="table_content">
        <ul>
            @foreach ($model->employeeStrengthAndWeaknesses->where('type', 'weakness') as $item)
                <li>{{ $item->description }}</li>
            @endforeach
        </ul>
    </x-slot>
</x-card-data-table>

<x-card-data-table>
    <x-slot name="table_content">
        <div class="d-flex justify-content-end gap-3">
            <x-button color="warning" :link="route('admin.employee.edit.step6', [
                'employee_id' => $model->id,
            ])" label="edit" />
        </div>
    </x-slot>
</x-card-data-table>
