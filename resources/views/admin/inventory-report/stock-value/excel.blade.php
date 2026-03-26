<table>
    <tr>
        <td colspan="10">
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
        <td colspan="14" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($title)) }}</b></p>
        </td>
    </tr>
</table>

@foreach ($data as $item)
    <table theadColor="white" class="table-bordered mt-20">
        <thead name="table_head">
            @include("admin.inventory-report.$type.table.header")
        </thead>
        <tbody name="table_body">
            <tr>
                <td class="font-small-2"><b>Saldo Awal {{ $item['item']->nama }}</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                <td></td>
                <td></td>

                <td></td>
                <td></td>

                <td class="font-small-2" align="right">{{ $item['beginning_balance'] }}</td>
                <td class="font-small-2" align="right">{{ $item['last_mutation']?->total }}</td>
            </tr>
            @include("admin.inventory-report.$type.table.body", [
                'formatNumber' => false,
            ])
        </tbody>
        {{-- <tfoot name="table_foot">
            @include("admin.inventory-report.$type.table.footer", [
                'formatNumber' => false,
            ])
        </tfoot> --}}
    </table>
@endforeach
