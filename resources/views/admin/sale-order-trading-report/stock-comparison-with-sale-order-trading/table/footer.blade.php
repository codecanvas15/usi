<tr>
    <td colspan="3" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('jumlah')) : $data->sum('jumlah') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('sudah_dikirim')) : $data->sum('sudah_dikirim') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding')) : $data->sum('outstanding') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('stock')) : $data->sum('stock') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('gap')) : $data->sum('gap') }}</td>
</tr>
