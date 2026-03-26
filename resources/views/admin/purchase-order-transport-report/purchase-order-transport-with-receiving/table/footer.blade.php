<tr>
    <td class="text-end" colspan="6">Total</td>
    <td class="text-end"></td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sended')) : $data->sum('sended') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('received')) : $data->sum('received') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total')) : $data->sum('sub_total') }}</td>
    @foreach ($taxNames as $taxName)
        <td class="text-end">{{ $formatNumber ? formatNumber($taxNameValues->where('tax_name', $taxName)->first()?->total) : $taxNameValues->where('tax_name', $taxName)->first()?->total }}</td>
    @endforeach
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td class="text-end"></td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total_local')) : $data->sum('sub_total_local') }}</td>
    @foreach ($taxNames as $taxName)
        <td class="text-end">{{ $formatNumber ? formatNumber($taxNameValues->where('tax_name', $taxName)->first()?->total_local) : $taxNameValues->where('tax_name', $taxName)->first()?->total_local }}</td>
    @endforeach
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('total_local')) : $data->sum('total_local') }}</td>
    <td></td>
</tr>
{{-- @if ($formatNumber)
@else
    <tr>
        <td class="text-end" colspan="7">Total</td>
        <td class="text-end"></td>
        <td class="text-end"> =SUM(H{{ 8 }}:H{{ 8 + count($data) - 1 }})</td>
        <td class="text-end"> =SUM(I{{ 8 }}:I{{ 8 + count($data) - 1 }})</td>
        <td class="text-end"> =SUM(J{{ 8 }}:J{{ 8 + count($data) - 1 }})</td>
        <td class="text-end"></td>
        <td class="text-end"> =SUM(L{{ 8 }}:L{{ 8 + count($data) - 1 }})</td>
        <td class="text-end"></td>
        <td></td>
    </tr>
@endif --}}
