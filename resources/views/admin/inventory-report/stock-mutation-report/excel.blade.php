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
        <td></td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

<table theadColor="white" class="table-bordered mt-20">
    @foreach ($data as $item)
        <tbody>
            @include("admin.inventory-report.$type.table.header")
            @include("admin.inventory-report.$type.table.body", [
                'formatNumber' => false,
                'data' => $item->data,
            ])
            @include("admin.inventory-report.$type.table.footer", [
                'formatNumber' => false,
                'data' => $item->data,
            ])
            <tr>
                <td colspan="8"></td>
            </tr>
        <tbody>
    @endforeach
</table>
