<tr class="text-center">
    <th>{{ Str::headline('tanggal') }}</th>
    <th>{{ Str::headline('kode PO') }}</th>
    <th>{{ Str::headline('kode LPB') }}</th>
    <th>{{ Str::headline('vendor') }}</th>
    <th>{{ Str::headline('Item') }}</th>
    <th>{{ Str::headline('qty') }}</th>
    <th>{{ Str::headline('unit') }}</th>
    <th>{{ Str::headline('harga') }}</th>
    <th>{{ Str::headline('subtotal') }}</th>
    @foreach ($unique_taxes as $tax)
        <th>{{ Str::upper(Str::headline($tax->tax->tax_name_with_percent)) }}</th>
    @endforeach
    <th>{{ Str::headline('total') }}</th>
    <th>{{ Str::headline('kurs') }}</th>
    <th>{{ Str::headline('total_idr') }}</th>
</tr>
