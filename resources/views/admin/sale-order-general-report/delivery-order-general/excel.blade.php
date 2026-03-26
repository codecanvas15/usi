<table>
    <tr>
        <td colspan="9">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <p><b>{{ getCompany()->address }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="13" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="13" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include('admin.sale-order-general-report.delivery-order-general.table.header')
    </thead>
    <tbody>
        @include('admin.sale-order-general-report.delivery-order-general.table.body', [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot>
        @include('admin.sale-order-general-report.delivery-order-general.table.footer', [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
