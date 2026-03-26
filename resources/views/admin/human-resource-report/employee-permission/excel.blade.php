<table>
    <tr>
        <td colspan="8">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="2">
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="10" align="center">
            <p><b>{{ Str::upper(Str::headline($title)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="10" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include("admin.human-resource-report.$type.table.header")
    </thead>
    <tbody>
        @include("admin.human-resource-report.$type.table.body", [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot>
        @include("admin.human-resource-report.$type.table.footer", [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
