<table>
    <tr>
        <td colspan="7">
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
        <td colspan="10" align="center">
            <h3 class="text-uppercase">laporan {{ Str::headline($type) }}</h3>
        </td>
    </tr>
    <tr>
        <td colspan="10" align="center">
            <h5 class="text-uppercase my-0">periode : {{ \Carbon\Carbon::parse("01-$period")->format('m-Y') }}</h5>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include('admin.purchase-order-service-report.per-periode-purchase-order-service.table.header')
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @include('admin.purchase-order-service-report.per-periode-purchase-order-service.table.body', [
            'formatNumber' => false,
        ])
    </tbody>
    <tfoot>
        @include('admin.purchase-order-service-report.per-periode-purchase-order-service.table.footer', [
            'formatNumber' => false,
        ])
    </tfoot>
</table>
