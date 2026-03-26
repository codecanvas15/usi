<table>
    <tr>
        <td colspan="8">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="4">
            {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
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
            <p><b>Tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include("admin.finance-report.$type.table.header")
    </thead>
    <tbody>
        @include("admin.finance-report.$type.table.body", [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot>
        @include("admin.finance-report.$type.table.footer", [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
