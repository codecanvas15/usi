<table>
    <tr>
        <td colspan="10">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="6">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="16" align="center">
            <p><b>Laporan {{ Str::headline($type) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="16" align="center">
            <p><b>PERIODE : {{ \Carbon\Carbon::parse("01-$period")->format('m-Y') }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include('admin.sale-order-trading-report.per-periode-sale-order-trading.table.header')
    </thead>
    <tbody>
        @include('admin.sale-order-trading-report.per-periode-sale-order-trading.table.body', [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot name="table_foot">
        @include('admin.sale-order-trading-report.per-periode-sale-order-trading.table.footer', [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
