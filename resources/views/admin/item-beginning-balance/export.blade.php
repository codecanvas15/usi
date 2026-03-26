<table>
    <thead>
        <tr>
            <th>ID Gudang</th>
            <th>Nama Gudang</th>
            <th>Nama Item</th>
            <th>Kode Item</th>
            <th>Deskripsi Item</th>
            <th>Kategory Item</th>
            <th>Unit / Satuan Item</th>
            <th>Tipe Item</th>
            <th>Stock (1.000,00)</th>
            <th>Harga Jual (1.000,00)</th>
            <th>Harga Beli (1.000,00)</th>
        </tr>
    </thead>
    <thead>
        @foreach ($warehouses as $warehouse)
            @foreach ($models as $item)
                <tr>
                    <td>{{ $warehouse->id }}</td>
                    <td>{{ $warehouse->nama }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td>{{ $item->item_category?->nama }}</td>
                    <td>{{ $item->unit?->name }}</td>
                    <td>{{ $item->type }}</td>
                    <td>0,00</td>
                    <td>0,00</td>
                    <td>0,00</td>
                </tr>
            @endforeach
        @endforeach
    </thead>
</table>
