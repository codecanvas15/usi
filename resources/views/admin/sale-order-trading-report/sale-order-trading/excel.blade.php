<table>
    <tr>
        <td colspan="8">
            <b>{{ Str::upper(getCompany()->name) }}</b>
        </td>
    </tr>
    <tr>
        <td colspan="8">
            <b>{{ getCompany()->address }}</b>
        </td>
    </tr>
    <tr>
        <td colspan="8">
            <b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b>
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table class="mt-20">
    <thead>
        @include('admin.sale-order-trading-report.sale-order-trading.table.header')
    </thead>
    <tbody>
        @php
            $number = 1;
        @endphp
        @foreach ($data as $key => $item)
            @include('admin.sale-order-trading-report.sale-order-trading.table.body', [
                'formatNumber' => false,
            ])
            @include('admin.sale-order-trading-report.sale-order-trading.table.footer', [
                'formatNumber' => false,
            ])
            @php
                $number++;
            @endphp
        @endforeach
    </tbody>
</table>
