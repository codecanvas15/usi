@php
    $no = 1;
@endphp

@forelse ($data->groupBy('coa_name') as $key => $items)
    <tr>
        @if ($items->first()?->coa_code)
            <td class="font-small-1"><b>{{ Str::upper(Str::headline($items->first()?->coa_code)) }}</b></td>
        @endif
        <td colspan="13" class="font-small-1"><b>{{ Str::upper(Str::headline(!$key || $key == '' ? 'ASSET LAIN LAIN' : $key)) }}</b></td>
    </tr>
    @php
        $no++;
    @endphp
    @foreach ($items as $item)
        <tr>
            {{-- A --}}
            <td class="font-small-1">{{ $item->coa_code }}</td>
            {{-- B --}}
            <td class="font-small-1">{{ $item->asset_name }}</td>
            {{-- C --}}
            <td class="font-small-1" align="right">1</td>
            {{-- D --}}
            <td class="font-small-1">{{ localDate($item->purchase_date) }}</td>
            {{-- E --}}
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->value) : $item->value }}</td>
            {{-- F --}}
            <td class="font-small-1">{{ $item->estimated_life ?? 0 }}</td>
            {{-- G --}}
            <td class="font-small-1">{{ $item->depreciation_percentage ?? 0 }}%</td>
            {{-- H --}}
            <td class="font-small-1">{{ 'STRAIGHT LINE' }}</td>
            {{-- I --}}
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->depreciation_value) : $item->depreciation_value }}</td>
            {{-- J --}}
            <td class="font-small-1">{{ $item->depreciation_count }}</td>
            {{-- K --}}
            <td class="font-small-1" align="right">
                {{ $formatNumber ? formatNumber($item->total_depreciation_this_month) : $item->total_depreciation_this_month }}
            </td>
            {{-- L --}}
            <td class="font-small-1" align="right">
                {{ $formatNumber ? formatNumber($item->acumulated_depreciation) : $item->acumulated_depreciation }}
            </td>
            {{-- M --}}
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->final_book_value) : $item->final_book_value }}</td>
            {{-- N --}}
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
