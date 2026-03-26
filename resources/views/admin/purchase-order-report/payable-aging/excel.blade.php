@php
    $row_start = 8;
    $formatNumber = false;
@endphp
<table>
    <tr>
        <td colspan="9">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="3">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>{{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>SAMPAI TANGGAL : {{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>
<table class="table-bordered mt-20">
    <thead>
        <tr class="text-center">
            <th rowspan="2">{{ Str::headline('#') }}</th>
            <th colspan="2">{{ Str::headline('vendor') }}</th>
            <th rowspan="2">{{ Str::headline('total') }}</th>
            <th rowspan="2">{{ Str::headline('belum j.tempo') }}</th>
            <th rowspan="2">1 - 30 Hari</th>
            <th rowspan="2">31 - 60 Hari</th>
            <th rowspan="2">61 - 90 Hari</th>
            <th rowspan="2">{{ Str::headline('> 90 Hari') }}</th>
        </tr>
        <tr class="text-center">
            <th>{{ Str::headline('kode') }}</th>
            <th>{{ Str::headline('nama') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td class="font-small-1">{{ $loop->iteration }}</td>
                <td class="font-small-1">{{ $item->code }}</td>
                <td class="font-small-1">{{ $item->nama }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->not_overdue) : $item->not_overdue }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->first_group) : $item->first_group }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->second_group) : $item->second_group }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->third_group) : $item->third_group }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->fourth_group) : $item->fourth_group }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot name="table_foot">
        <tr>
            <td colspan="3" class="text-center">Total</td>
            <td class="font-small-1" align="right">
                {{-- {{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }} --}}
                <b>=SUM(D{{ $row_start }}:D{{ $row_start + count($data) - 1 }})</b>
            </td>
            <td class="font-small-1" align="right">
                {{-- {{ $formatNumber ? formatNumber($data->sum('not_overdue')) : $data->sum('not_overdue') }} --}}
                <b>=SUM(E{{ $row_start }}:E{{ $row_start + count($data) - 1 }})</b>
            </td>
            <td class="font-small-1" align="right">
                {{-- {{ $formatNumber ? formatNumber($data->sum('first_group')) : $data->sum('first_group') }} --}}
                <b>=SUM(F{{ $row_start }}:F{{ $row_start + count($data) - 1 }})</b>
            </td>
            <td class="font-small-1" align="right">
                {{-- {{ $formatNumber ? formatNumber($data->sum('second_group')) : $data->sum('second_group') }} --}}
                <b>=SUM(G{{ $row_start }}:G{{ $row_start + count($data) - 1 }})</b>
            </td>
            <td class="font-small-1" align="right">
                {{-- {{ $formatNumber ? formatNumber($data->sum('third_group')) : $data->sum('third_group') }} --}}
                <b>=SUM(H{{ $row_start }}:H{{ $row_start + count($data) - 1 }})</b>
            </td>
            <td class="font-small-1" align="right">
                {{-- {{ $formatNumber ? formatNumber($data->sum('fourth_group')) : $data->sum('fourth_group') }} --}}
                <b>=SUM(I{{ $row_start }}:I{{ $row_start + count($data) - 1 }})</b>
            </td>
        </tr>
    </tfoot>
</table>
