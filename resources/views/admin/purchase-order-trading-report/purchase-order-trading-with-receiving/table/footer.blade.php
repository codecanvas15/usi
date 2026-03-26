<tr>
    <td colspan="9" class="font-small-1" align="center"><b>Total</b></td>
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->quantity }}
            @else
                {{ formatNumber($total_all->quantity) }}
            @endif
        </b>
    </td>
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->sub_total }}
            @else
                {{ formatNumber($total_all->sub_total) }}
            @endif
        </b>
    </td>
    @foreach ($taxes as $tax)
        <td class="font-small-1" align="right">
            <b>
                @if ($format == 'excel')
                    {{ $total_all->$tax ?? 0 }}
                @else
                    {{ formatNumber($total_all->$tax ?? 0) }}
                @endif
            </b>
        </td>
    @endforeach
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->total_additional }}
            @else
                {{ formatNumber($total_all->total_additional) }}
            @endif
        </b>
    </td>
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->total }}
            @else
                {{ formatNumber($total_all->total) }}
            @endif
        </b>
    </td>
    <td class="font-small-1" align="right"></td>
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->sub_total_idr }}
            @else
                {{ formatNumber($total_all->sub_total_idr) }}
            @endif
        </b>
    </td>
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->total_additional }}
            @else
                {{ formatNumber($total_all->total_additional) }}
            @endif
        </b>
    </td>
    <td class="font-small-1" align="right">
        <b>
            @if ($format == 'excel')
                {{ $total_all->total_idr }}
            @else
                {{ formatNumber($total_all->total_idr) }}
            @endif
        </b>
    </td>
    <td class="font-small-1" align="right"></td>
</tr>
