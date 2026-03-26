<table>
    <tr>
        <td colspan="10">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="5">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="15" align="center">
            <p><b>{{ Str::upper(Str::headline('histori dokumen purchase invoice')) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="15" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered">
    <thead>
        @include('admin.finance-report.document-history-purchase-invoice.table.header')
    </thead>
    <tbody>
        @include('admin.finance-report.document-history-purchase-invoice.table.body', [
            'formatNumber' => false,
        ])
        @include('admin.finance-report.document-history-purchase-invoice.table.footer', [
            'formatNumber' => false,
        ])
    </tbody>
</table>
