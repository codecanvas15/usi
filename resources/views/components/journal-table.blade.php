<x-card-data-table title="{{ 'Journal' }}">
    <x-slot name="header_content">

    </x-slot>
    <x-slot name="table_content">
        <x-table theadColor="danger" id="table-journal">
            <x-slot name="table_head">
                <th>Account</th>
                <th>Debit</th>
                <th>Credit</th>
            </x-slot>
            <x-slot name="table_body">

            </x-slot>

            <x-slot name="table_foot">
            </x-slot>

        </x-table>

    </x-slot>
</x-card-data-table>
