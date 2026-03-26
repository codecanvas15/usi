<tr>
    <th>Tanggal</th>
    <th>Kode PO</th>
    <th>Kode LPB</th>
    <th>Vendor</th>
    <th>Customer</th>
    <th>Item</th>

    <th>Harga</th>
    <th>Dikirim</th>
    <th>Diterima</th>
    <th>Sub Total</th>
    @foreach ($taxNames as $item)
        <th>{{ $item }}</th>
    @endforeach
    <th>Total</th>
    <th>Kurs</th>
    <th>Sub Total Idr</th>
    @foreach ($taxNames as $item)
        <th>{{ $item }} Idr</th>
    @endforeach
    <th>Total Idr</th>
    <th>Status</th>
</tr>
