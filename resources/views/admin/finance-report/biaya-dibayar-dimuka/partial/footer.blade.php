<tr>
    <th class="font-small-1" colspan="3"><b>GRAND TOTAL</b></th>
    <th class="font-small-1"></th>
    <th class="font-small-1" colspan="2"></th>
    <th class="font-small-1"></th>
    <th class="font-small-1"></th>
    <th class="font-small-1">{{ $formatNumber ? formatNumber($data->sum('total_depreciation_this_month')) : $data->sum('total_depreciation_this_month') }}</th>
    <th class="font-small-1">{{ $formatNumber ? formatNumber($data->sum('acumulated_depreciation')) : $data->sum('acumulated_depreciation') }}</th>
    <th class="font-small-1">{{ $formatNumber ? formatNumber($data->sum('final_book_value')) : $data->sum('final_book_value') }}</th>
    <th class="font-small-1"></th>
</tr>
