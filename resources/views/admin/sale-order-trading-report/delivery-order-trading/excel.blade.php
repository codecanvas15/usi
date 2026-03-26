<table>
    <tr>
        <td colspan="11">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="4">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="15" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="15" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include('admin.sale-order-trading-report.delivery-order-trading.table.header')
    </thead>
    <tbody>
        @include('admin.sale-order-trading-report.delivery-order-trading.table.body', [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot name="table_foot">
        @include('admin.sale-order-trading-report.delivery-order-trading.table.footer', [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
