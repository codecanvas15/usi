<table>
    <tr>
        <td colspan="5">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="3">
            {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline('DAFTAR AKTIVA TETAP')) }}</b></p>
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
        @include('admin.finance-report.daftar-aktifa-tetap.partial.head', ['formatNumber' => false])
    </thead>
    <tbody>
        @include('admin.finance-report.daftar-aktifa-tetap.partial.body', ['formatNumber' => false])
    </tbody>
    <tfoot>
        @include('admin.finance-report.daftar-aktifa-tetap.partial.footer', ['formatNumber' => false])
    </tfoot>
</table>
