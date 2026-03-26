<tr>
    <td colspan="7" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('amount')) : $data->sum('amount') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('sended')) : $data->sum('sended') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding')) : $data->sum('outstanding') }}</td>
    <td></td>
    {{-- <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('price')) : $data->sum('price') }}</td> --}}
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding_idr')) : $data->sum('outstanding_idr') }}</td>
</tr>

