<table>
    <tr>
        <td colspan="9">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td colspan="4">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="13" align="center">
            <p><b>{{ Str::upper(Str::headline($title)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="13" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

@foreach ($data as $item)
    <table theadColor="white" class="table-bordered mt-20">
        <tbody>
            <tr>
                <th>Tanggal: {{ $item->date }}</th>
                <th>Gudang: {{ $item->ware_house_name }}</th>
            </tr>
            <tr>
                <th>Kode / Nomor Faktur: {{ $item->code }} / {{ $item->tax_number }}</th>
                <th>Customer : {{ $item->customer_name }}</th>
            </tr>
        </tbody>
    </table>
    <table theadColor="white" class="table-bordered mt-20">
        <tbody>
            @include('admin.sale-order-general-report.invoice-return-sale-order-general-detail.table.header')
        </tbody>
        <tbody>
            @include('admin.sale-order-general-report.invoice-return-sale-order-general-detail.table.body', [
                'formatNumber' => true,
            ])
        </tbody>
        <tfoot>
            @include('admin.sale-order-general-report.invoice-return-sale-order-general-detail.table.footer', [
                'formatNumber' => true,
            ])
        </tfoot>
    </table>
@endforeach
