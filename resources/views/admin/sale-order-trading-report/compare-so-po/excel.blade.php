<table>
    <tr>
        <td colspan="8">
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
        <td colspan="12" align="center">
            <p><b>{{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <tbody>
        @include('admin.sale-order-trading-report.compare-so-po.table.header')
        @include('admin.sale-order-trading-report.compare-so-po.table.body', [
            'formatNumber' => false,
        ])
        @include('admin.sale-order-trading-report.compare-so-po.table.footer', [
            'formatNumber' => false,
        ])
    </tbody>
</table>
