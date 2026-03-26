<tr>
    <td colspan="5" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('jumlah')) : $data->sum('jumlah') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('sudah_dikirim')) : $data->sum('sudah_dikirim') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding')) : $data->sum('outstanding') }}</td>
</tr>
