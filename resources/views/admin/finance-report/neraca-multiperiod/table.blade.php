<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <td class="font-xsmall-1" colspan="14" align="center"><b>AKTIVA</b></td>
        </tr>
        <tr>
            <td class="font-xsmall-1" align="center"><b>KODE REK.</b></td>
            <td class="font-xsmall-1" align="center"><b>KETERANGAN</b></td>
            @foreach (months() as $month)
                <td class="font-xsmall-1" align="center"><b>{{ $month }}</b></td>
            @endforeach
        </tr>
        @foreach ($aktiva as $key => $item)
            <tr>
                <td class="font-xsmall-1" align="center">
                    @if ($item['is_parent'] || $item['is_total'])
                        <b>
                    @endif
                    {{ $item['code'] ?? '' }}
                    @if ($item['is_parent'] || $item['is_total'])
                        </b>
                    @endif
                </td>
                <td class="font-xsmall-1" align="{{ $item['is_total'] ? 'right' : '' }}">
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
                @foreach ($item['balance'] as $key => $balance)
                    <td class="font-xsmall-1" align="right">
                        @if ($item['is_parent'] || $item['is_total'])
                            <b>
                        @endif
                        @if ($balance != 0)
                            {{ $format_number ? formatNumber($balance) : $balance }}
                        @endif
                        @if (($item['total_balance'][$key] ?? null != 0) && $item['is_total'])
                            {{ $format_number ? formatNumber($item['total_balance'][$key]) : $item['total_balance'][$key] }}
                        @endif
                        @if ($item['is_parent'] || $item['is_total'])
                            </b>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td class="font-xsmall-1" align="right">
                <b>TOTAL AKTIVA</b>
            </td>
            @php
                $total_activa_balance = [];
            @endphp
            @for ($i = 1; $i <= 12; $i++)
                @php
                    $total_activa_balance[$i] = 0;
                @endphp
            @endfor

            @foreach ($aktiva as $item)
                @foreach ($item['balance'] as $key => $balance)
                    @php
                        $total_activa_balance[$key] += $balance;
                    @endphp
                @endforeach
            @endforeach
            @for ($i = 1; $i <= 12; $i++)
                <td class="font-xsmall-1" align="right">
                    @if ($total_activa_balance[$i] != 0)
                        <b>{{ $format_number ? formatNumber($total_activa_balance[$i]) : $total_activa_balance[$i] }}</b>
                    @endif
                </td>
            @endfor
        </tr>
        <tr>
            <td class="font-xsmall-1" colspan="14" align="center"><b>PASIVA</b></td>
        </tr>
        <tr>
            <td class="font-xsmall-1" align="center"><b>KODE REK.</b></td>
            <td class="font-xsmall-1" align="center"><b>KETERANGAN</b></td>
            @foreach (months() as $month)
                <td class="font-xsmall-1" align="center"><b>{{ $month }}</b></td>
            @endforeach
        </tr>
        @foreach ($pasiva as $key => $item_pasiva)
            <tr>
                <td class="font-xsmall-1" align="center">
                    @if ($item_pasiva['is_parent'] || $item_pasiva['is_total'])
                        <b>
                    @endif
                    {{ $item_pasiva['code'] ?? '' }}
                    @if ($item_pasiva['is_parent'] || $item_pasiva['is_total'])
                        </b>
                    @endif
                </td>
                <td class="font-xsmall-1" align="{{ $item_pasiva['is_total'] ? 'right' : '' }}">
                    @for ($i = 0; $i < $item_pasiva['indent']; $i++)
                        &nbsp;
                    @endfor
                    @if ($item_pasiva['is_parent'] || $item_pasiva['is_total'])
                        <b>
                    @endif
                    {{ $item_pasiva['name'] ?? '' }}
                    @if ($item_pasiva['is_parent'] || $item_pasiva['is_total'])
                        </b>
                    @endif
                </td>
                @foreach ($item_pasiva['balance'] as $key => $balance)
                    <td class="font-xsmall-1" align="right">
                        @if ($item_pasiva['is_parent'] || $item_pasiva['is_total'])
                            <b>
                        @endif
                        @if ($balance != 0)
                            {{ $format_number ? formatNumber($balance) : $balance }}
                        @endif
                        @if (($item['total_balance'][$key] ?? null != 0) && $item['is_total'])
                            {{ $format_number ? formatNumber($item_pasiva['total_balance'][$key]) : $item_pasiva['total_balance'][$key] }}
                        @endif
                        @if ($item_pasiva['is_parent'] || $item_pasiva['is_total'])
                            </b>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td class="font-xsmall-1" align="right">
                <b>TOTAL PASIVA</b>
            </td>
            @php
                $total_pasiva_balance = [];
            @endphp
            @for ($i = 1; $i <= 12; $i++)
                @php
                    $total_pasiva_balance[$i] = 0;
                @endphp
            @endfor

            @foreach ($pasiva as $item)
                @foreach ($item['balance'] as $key => $balance)
                    @php
                        $total_pasiva_balance[$key] += $balance;
                    @endphp
                @endforeach
            @endforeach
            @for ($i = 1; $i <= 12; $i++)
                <td class="font-xsmall-1" align="right">
                    @if ($total_pasiva_balance[$i] != 0)
                        <b>{{ $format_number ? formatNumber($total_pasiva_balance[$i]) : $total_pasiva_balance[$i] }}</b>
                    @endif
                </td>
            @endfor
        </tr>
        <tr>
            <td></td>
            <td class="font-xsmall-1" align="right">
                <b>BALANCE (AKTIVA - PASIVA)</b>
            </td>
            @for ($i = 1; $i <= 12; $i++)
                @php
                    $difference = $total_activa_balance[$i] - $total_pasiva_balance[$i];
                @endphp
                <td class="font-xsmall-1" align="right">
                    @if ($difference != 0)
                        <b>{{ $format_number ? formatNumber($difference) : $difference }}</b>
                    @endif
                </td>
            @endfor
        </tr>
    </tbody>
</table>
