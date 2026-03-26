<tr class="text-center">
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('tanggal') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('kode po') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('kode lpb') }}</b></td>
    <td class="font-small-1" colspan="3"><b>{{ Str::headline('customer') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('vendor') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('nama item') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('qty (l)') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('price') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('sub_total') }}</b></td>
    @foreach ($taxes as $tax)
        <td class="font-small-1" rowspan="2"><b>{{ Str::upper(Str::headline($tax)) }}</b></td>
    @endforeach
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('total additional') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('total') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('kurs') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('sub_total idr') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('total additional idr') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('total idr') }}</b></td>
    <td class="font-small-1" rowspan="2"><b>{{ Str::headline('status') }}</b></td>
</tr>

<tr class="text-center">
    <td class="font-small-1"><b>{{ Str::headline('nama') }}</b></td>
    <td class="font-small-1"><b>{{ Str::headline('sh no.') }}</b></td>
    <td class="font-small-1"><b>{{ Str::headline('lokasi') }}</b></td>
</tr>
