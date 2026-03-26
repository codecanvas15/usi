<table>
    <tr>
        <td colspan="4">
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
        <td colspan="7" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="7" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

@foreach ($data as $warehouse)
    @foreach ($warehouse['data'] ?? [] as $item)
        <div class="my-5 border-bottom border-primary" style="page-break-after: always">

            {{-- <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Item : {{ $item['item_name'] }}</label>
                        </div>
                        <div class="form-group">
                            <label for="">Cabang : {{ $branch['branch_name'] }}</label>
                        </div>
                        <div class="form-group">
                            <label for="">Gudang : {{ $warehouse['ware_house_name'] }}</label>
                        </div>
                    </div>
                </div> --}}

            <table>
                <tr>
                    <td colspan="3">
                        <p><b>Item : {{ $item['item_name'] }}</b></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p><b>Gudang : {{ $warehouse['ware_house_name'] }}</b></p>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                </tr>
            </table>

            <div class="mt-3">
                <table theadColor="white" class="table-bordered mt-20">
                    <thead>
                        @include("admin.inventory-report.$type.table.header")
                    </thead>
                    <tbody>
                        @include("admin.inventory-report.$type.table.body", [
                            'formatNumber' => false,
                        ])
                    </tbody>
                    <tfoot>
                        @include("admin.inventory-report.$type.table.footer", [
                            'formatNumber' => false,
                        ])
                    </tfoot>
                </table>
            </div>

            <table>
                <tr>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                </tr>
            </table>

        </div>
    @endforeach
@endforeach

{{-- <table theadColor="white" class="table-bordered mt-20">
    <thead>
        @include("admin.inventory-report.$type.table.header")
    </thead>
    <tbody>
        @include("admin.inventory-report.$type.table.body", [
                    'formatNumber' => false,
                ])
    </tbody>
    <tfoot>
        @include("admin.inventory-report.$type.table.footer", [
                    'formatNumber' => false,
                ])
    </tfoot>
</table> --}}
