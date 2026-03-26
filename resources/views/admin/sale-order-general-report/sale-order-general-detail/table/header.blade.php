<tr>
    <th class="font-small-1"><b>{{ Str::headline('#') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="5%" @endif><b>{{ Str::headline('Tanggal') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="15%" @endif><b>{{ Str::headline('Customer') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="10%" @endif><b>{{ Str::headline('No. Faktur') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="10%" @endif><b>{{ Str::headline('Invoice') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="5%" @endif><b>{{ Str::headline('Target Pengiriman') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="10%" @endif><b>{{ Str::headline('No SO') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="10%" @endif><b>{{ Str::headline('No SJ') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="10%" @endif><b>{{ Str::headline('No PO') }}</b></th>
    <th class="font-small-1" @if (!$is_excel) width="10%" @endif><b>{{ Str::headline('Nama Barang') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Jumlah') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Satuan ') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Harga ') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Sub total ') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Total Pajak') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Kurs ') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Sub total IDR') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('Total Pajak IDR') }}</b></th>
    <th class="font-small-1"><b>{{ Str::headline('total IDR') }}</b></th>
</tr>
