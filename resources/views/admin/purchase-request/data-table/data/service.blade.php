<div class="mb-20">
    <x-table>
        <x-slot name="table_head">
            <th>#</th>
            <th>Item</th>
            <th>Jumlah</th>
            <th>Jumlah di approve</th>
            <th>Unit</th>
            <th>Status</th>
            <th>Alasan Reject</th>
            <th>Attachment</th>
        </x-slot>
        <x-slot name="table_body">
            @foreach ($data->purchase_request_details as $item)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $item->item_data->nama }}</td>
                    <td>{{ formatNUmber($item->jumlah) }}</td>
                    <td>{{ formatNUmber($item->jumlah_diapprove) }}</td>
                    <td>{{ $item->unit?->name ?? '-' }}</td>
                    <td>
                        <div class="badge badge-lg badge-{{ purchase_request_status()[$item->status]['color'] }}">
                            {{ purchase_request_status()[$item->status]['label'] }} - {{ purchase_request_status()[$item->status]['text'] }}
                        </div>
                    </td>
                    <td>{{ $item->reject_reason ?? '-' }}</td>
                    <td>
                        @if ($item->file)
                            <x-button color="primary" icon="eye" fontawesome link="{{ asset('storage/' . $item->file) }}" size="sm" />
                        @else
                            <x-button color="danger" badge icon="eye-slash" fontawesome size="sm" label="not available" />
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>
</div>

<div>
    <h4>Puchase Order</h4>
    <x-table>
        <x-slot name="table_head">
            <th>#</th>
            <td>{{ Str::headline('kode') }}</td>
            <td>{{ Str::headline('tanggal') }}</td>
            <td>{{ Str::headline('nama item') }}</td>
            <td>{{ Str::headline('kode item') }}</td>
            <td>{{ Str::headline('unit') }}</td>
            <td>{{ Str::headline('jumlah') }}</td>
            <td>{{ Str::headline('jumlah diterima') }}</td>
            <td>{{ Str::headline('status') }}</td>
        </x-slot>
        <x-slot name="table_body">
            @foreach ($data->purchase_order_and_item_receiving_report as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item?->code ?? 'Undefined' }}</td>
                    <td>{{ $item?->date ? localDate($item?->date) : 'Undefined' }}</td>
                    <td>{{ $item?->item_name ?? 'Undefined' }}</td>
                    <td>{{ $item?->item_code ?? 'Undefined' }}</td>
                    <td>{{ $item?->unit_name ?? 'Undefined' }}</td>
                    <td>{{ $item?->quantity ?? 'Undefined' }}</td>
                    <td>{{ $item?->quantity_received ?? 'Undefined' }}</td>
                    <td>{{ $item?->status ?? 'Undefined' }}</td>
                </tr>
            @endforeach
        </x-slot>
    </x-table>
</div>
