@php
    use Carbon\Carbon;
    $row_start = 7;
    $row_end = $row_start + $data->groupBy('coa_name')->count() + $data->count() - 1;
    $no = 0;
@endphp

<table>
    <tr>
        <td colspan="5">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="3">
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline('DAFTAR BIAYA DIBAYAR DI MUKA')) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>TANGGAL : {{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>
<table>
    <thead>
        @include('admin.finance-report.biaya-dibayar-dimuka.partial.head', ['formatNumber' => false])
    </thead>
    <tbody>
        @include('admin.finance-report.biaya-dibayar-dimuka.partial.body', ['formatNumber' => false])
    </tbody>
    <tfoot>
        @include('admin.finance-report.biaya-dibayar-dimuka.partial.footer', ['formatNumber' => false])
    </tfoot>
</table>
