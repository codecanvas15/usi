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
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="14" align="center">
            <p><b>PERIODE : {{ $period }}</b></p>
        </td>
    </tr>
</table>

@foreach ($data as $item)
    <div class="mt-20">
        <h4>{{ Str::headline($item->item_category_name) }}</h4>
        <table theadColor="white" class="table-bordered">
            <thead name="table_head">
                @include("admin.inventory-report.$type.table.header")
            </thead>
            <tbody name="table_body">
                @include("admin.inventory-report.$type.table.body", [
                    'formatNumber' => false,
                ])
            </tbody>
            <tfoot name="table_foot">
                @include("admin.inventory-report.$type.table.footer", [
                    'formatNumber' => false,
                ])
            </tfoot>
        </table>
    </div>
@endforeach
