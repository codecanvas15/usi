<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="font-small-1" align="center">KODE REK.</th>
            <th class="font-small-1" align="center">KETERANGAN</th>
            @for ($i = 1; $i <= 12; $i++)
                <th class="font-small-1" align="center">{{ months()[$i] }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @php
            $total_parent[1] = 0;
            $total_parent[2] = 0;
            $total_parent[3] = 0;
            $total_parent[4] = 0;
            $total_parent[5] = 0;
            $total_parent[6] = 0;
            $total_parent[7] = 0;
            $total_parent[8] = 0;
            $total_parent[9] = 0;
            $total_parent[10] = 0;
            $total_parent[11] = 0;
            $total_parent[12] = 0;
        @endphp
        @foreach ($data as $key => $item)
            @foreach ($data[$key] as $key_subcategory => $subcategory)
                @php
                    $total_subcategory[1] = 0;
                    $total_subcategory[2] = 0;
                    $total_subcategory[3] = 0;
                    $total_subcategory[4] = 0;
                    $total_subcategory[5] = 0;
                    $total_subcategory[6] = 0;
                    $total_subcategory[7] = 0;
                    $total_subcategory[8] = 0;
                    $total_subcategory[9] = 0;
                    $total_subcategory[10] = 0;
                    $total_subcategory[11] = 0;
                    $total_subcategory[12] = 0;
                @endphp
                <tr>
                    <td></td>
                    <td class="font-small-1"><b>{{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td></td>
                    @endfor
                </tr>
                @foreach ($item[$key_subcategory]['data'] as $detail)
                    <tr>
                        <td class="font-small-1" align="center">{{ Str::upper(Str::headline($detail['code'])) }}</td>
                        <td class="font-small-1">{{ Str::upper(Str::headline($detail['coa'])) }}</td>
                        @foreach ($detail['data'] as $detail_period)
                            <td class="font-small-1" align="right">{{ $format_number ? formatNumber($detail_period) : $detail_period }}</td>
                        @endforeach
                    </tr>
                    @foreach ($detail['data'] as $key_period => $detail_period)
                        @php
                            if ($item[$key_subcategory]['type'] == 'plus') {
                                $total_subcategory[$key_period] += $detail_period;
                            } else {
                                $total_subcategory[$key_period] -= $detail_period;
                            }
                        @endphp
                    @endforeach
                @endforeach
                <tr>
                    <td></td>
                    <td class="font-small-1" align="right"><b>TOTAL {{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td class="font-small-1" align="right"><b>{{ $format_number ? formatNumber($total_subcategory[$i]) : $total_subcategory[$i] }}</b></td>
                    @endfor
                </tr>
                @php
                    for ($i = 1; $i < 12; $i++) {
                        $total_parent[$i] += $total_subcategory[$i];
                    }
                @endphp
            @endforeach
            <tr>
                <td class="font-small-1" colspan="2"><b>{{ Str::upper(Str::headline($key)) }}</b></td>
                @for ($i = 1; $i <= 12; $i++)
                    <td class="font-small-1" align="right"><b>{{ $format_number ? formatNumber($total_parent[$i]) : $total_parent[$i] }}</b></td>
                @endfor
            </tr>
            <tr>
                <td>
                    <br>
                </td>
                <td></td>
                @for ($i = 1; $i <= 12; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach

    </tbody>
</table>
