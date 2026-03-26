<table>
    <tr>
        <td colspan="6">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="3">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="9" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="9" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include('admin.purchase-order-trading-report.detail-purchase-order-trading.table.header')
    </thead>
    <tbody>
        @include('admin.purchase-order-trading-report.detail-purchase-order-trading.table.body', [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot>
        @include('admin.purchase-order-trading-report.detail-purchase-order-trading.table.footer', [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
