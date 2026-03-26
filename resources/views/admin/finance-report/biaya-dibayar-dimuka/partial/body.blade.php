@php
    $no = 0;
@endphp

@forelse ($data->groupBy('coa_name') as $key => $items)
    <tr>
        <td><b>{{ $items->first()?->coa_code }}</b></td>
        <td colspan="11"><b>{{ Str::upper(Str::headline($key)) }}</b></td>
    </tr>
    @php
        $no++;
    @endphp
    @foreach ($items as $item)
        <tr>
            {{-- A --}}
            <td class="font-small-1" align="center">{{ $item->coa_code }}</td>
            {{-- B --}}
            <td class="font-small-1">{{ $item->lease_name }}</td>
            {{-- C --}}
            <td class="font-small-1" align="center">{{ localDate($item->date) }}</td>
            {{-- D --}}
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->value) : $item->value }}</td>
            {{-- E --}}
            <td class="font-small-1" align="center">{{ $item->month_duration }}</td>
            {{-- F --}}
            <td class="font-small-1" align="center">{{ localDate($item->from_date) . ' - ' . localDate($item->to_date) }}</td>
            {{-- G --}}
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->depreciation_value) : $item->depreciation_value }}</td>
            {{-- H --}}
            <td class="font-small-1" align="center">{{ $item->depreciation_count }}</td>
            {{-- I --}}
            <td class="font-small-1" align="right">
                {{ $formatNumber ? formatNumber($item->total_depreciation_this_month) : $item->total_depreciation_this_month }}
            </td>
            {{-- J --}}
            <td class="font-small-1" align="right">
                {{ $formatNumber ? formatNumber($item->acumulated_depreciation) : $item->acumulated_depreciation }}
            </td>
            {{-- K --}}
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->final_book_value) : $item->final_book_value }}</td>
            <td class="font-small-1">{{ $item->note }}</td>
        </tr>

        @php
            $no++;
        @endphp
    @endforeach
@empty
    <tr>
        <td colspan="10" align="center">Data tidak ada!</td>
    </tr>
@endforelse
