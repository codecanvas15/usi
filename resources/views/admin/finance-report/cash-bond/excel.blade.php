@php
    $beginning_row = 5;
@endphp
<table>
    <tr>
        <td colspan="10">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="10">
            <center><img src="{{ public_path('/images/icon.png') }}" style="width: 116px"></center>
        </td>
    </tr>
    <tr>
        <td colspan="16" align="center">
            <p><b>{{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="16" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>
<table>
    <thead>
        @include('admin.finance-report.cash-bond.table.header')
    </thead>
    <tbody>
        @include('admin.finance-report.cash-bond.table.body', [
            'formatNumber' => true,
        ])
    </tbody>
    <tfoot>
        @include('admin.finance-report.cash-bond.table.footer', [
            'formatNumber' => true,
        ])
    </tfoot>
</table>
