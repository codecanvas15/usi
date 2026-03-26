<table>
    <tr>
        <td colspan="2">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td></td>
        <td>
            {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="4" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="center">
            <p><b>PERIODE : {{ $period }}</b></p>
        </td>
    </tr>
    <tr>
        <th align="center"><b>KODE REK.</b></th>
        <th align="center"><b>KETERANGAN</b></th>
        <th align="center"><b>DEBIT</b></th>
        <th align="center"><b>KREDIT</b></th>
        <th align="center"><b>BALANCE</b></th>
    </tr>
    @foreach ($neraca as $key => $item)
        <tr>
            <td align="center">
                @if ($item['is_parent'] || $item['is_total'])
                    <b>
                @endif
                {{ $item['code'] ?? '' }}
                @if ($item['is_parent'] || $item['is_total'])
                    </b>
                @endif
            </td>
            <td align="{{ $item['is_total'] ? 'right' : 'left' }}">
                @for ($i = 0; $i < $item['indent']; $i++)
                    &nbsp;
                @endfor
                @if ($item['is_parent'] || $item['is_total'])
                    <b>
                @endif
                {{ $item['name'] ?? '' }}
                @if ($item['is_parent'] || $item['is_total'])
                    </b>
                @endif
            </td>
            <td align="right">
                @if ($item['is_parent'] || $item['is_total'])
                    <b>
                @endif
                @if ($item['debit'] != 0)
                    {{ $item['debit'] }}
                @endif
                @if ($item['total_debit'] != 0)
                    {{ $item['total_debit'] }}
                @endif
                @if ($item['is_parent'] || $item['is_total'])
                    </b>
                @endif
            </td>
            <td align="right">
                @if ($item['is_parent'] || $item['is_total'])
                    <b>
                @endif
                @if ($item['credit'] != 0)
                    {{ $item['credit'] }}
                @endif
                @if ($item['total_credit'] != 0)
                    {{ $item['total_credit'] }}
                @endif
                @if ($item['is_parent'] || $item['is_total'])
                    </b>
                @endif
            </td>
            <td align="right">
                @if ($item['is_parent'] || $item['is_total'])
                    <b>
                @endif
                @if ($item['balance'] != 0)
                    {{ $item['balance'] }}
                @endif
                @if ($item['total_balance'] != 0)
                    {{ $item['total_balance'] }}
                @endif
                @if ($item['is_parent'] || $item['is_total'])
                    </b>
                @endif
            </td>
        </tr>
    @endforeach
    <tr>
        <th></th>
        <th align="right">
            <b>TOTAL</b>
        </th>
        <th align="right">
            <b>{{ array_sum(array_column($neraca, 'debit')) }}</b>
        </th>
        <th align="right">
            <b>{{ array_sum(array_column($neraca, 'credit')) }}</b>
        </th>
        <th align="right">
            <b>{{ array_sum(array_column($neraca, 'balance')) }}</b>
        </th>
    </tr>
</table>
