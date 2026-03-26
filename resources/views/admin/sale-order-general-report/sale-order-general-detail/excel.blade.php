<table>
    <tr>
        <td colspan="10">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="9">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="19" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="19" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>
<table theadColor="white" class="table-bordered">
    <thead>
        @include('admin.sale-order-general-report.sale-order-general-detail.table.header', ['is_excel' => true])
    </thead>
    <tbody>
        @include('admin.sale-order-general-report.sale-order-general-detail.table.body', [
            'formatNumber' => false,
            'is_excel' => true,
        ])
    </tbody>
</table>

