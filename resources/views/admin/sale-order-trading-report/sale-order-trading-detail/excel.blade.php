<table>
    <tr>
        <td colspan="11">
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
        <td colspan="15" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="15" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>

@foreach ($data as $item)
    <div class="border-top border-primary mt-30 pt-10">
        <table theadColor="white" class="table-bordered">
            <tbody>
                <tr>
                    <th><b>Tanggal : {{ localDate($item->date) }}</b></th>
                    <th><b>Pelanggan : {{ $item->customer_name }}</b></th>
                </tr>
                <tr>
                    <th><b>No. Invoice : {{ $item->code }}</b></th>
                    <th><b>Lokasi : {{ $item->branch_name }}</b></th>
                </tr>
                <tr>
                    <th><b>Lost Tolerance : {{ Str::headline($item->lost_tolerance_type == 'percent' ? $item->lost_tolerance * 100 : $item->lost_tolerance) }}</b></th>
                    <th></th>
                </tr>
                <tr>
                    <th><b>Lost Tolerance Type : {{ Str::headline($item->lost_tolerance_type) }}</b></th>
                    <th></th>
                </tr>
                <tr>
                    <th><b>Calculate From : {{ Str::headline($item->calculate_from) }}</b></th>
                    <th></th>
                </tr>
            </tbody>
        </table>

        <table theadColor="white" class="table-bordered">
            <thead>
                <tr>
                    <th><b>Qty Total</b></th>
                    <th><b>Qty Losses</b></th>
                    <th><b>Losses Percentage</b></th>
                    <th><b>Qty Lost Tolerance</b></th>
                    <th><b>Qty Invoice</b></th>
                    <th><b>Harga</b></th>
                    <th><b>Sub Total</b></th>
                    <th><b>Total Pajak</b></th>
                    <th><b>Total</b></th>
                    <th><b>Kurs</b></th>
                    <th><b>Sub Total Idr</b></th>
                    <th><b>Total Pajak Idr</b></th>
                    <th><b>Total Idr</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ formatNumber($item->total_jumlah_dikirim) }}</td>
                    <td>{{ formatNumber($item->total_lost) }}</td>
                    <td>{{ formatNumber($item->losses_percentage) }}</td>
                    <td>{{ formatNumber($item->qty_losses_tolerance) }}</td>
                    <td>{{ formatNumber($item->jumlah) }}</td>
                    <td>{{ formatNumber($item->harga) }}</td>
                    <td>{{ formatNumber($item->subtotal) }}</td>
                    <td>{{ formatNumber($item->total_tax) }}</td>
                    <td>{{ formatNumber($item->total) }}</td>
                    <td>{{ formatNumber($item->exchange_rate) }}</td>
                    <td>{{ formatNumber($item->subtotal_local) }}</td>
                    <td>{{ formatNumber($item->total_tax_local) }}</td>
                    <td>{{ formatNumber($item->total_local) }}</td>
                </tr>
            </tbody>
        </table>

        <table theadColor="white" class="table-bordered">
            <thead>
                @include('admin.sale-order-trading-report.sale-order-trading-detail.table.header')
            </thead>
            <tbody>
                @include('admin.sale-order-trading-report.sale-order-trading-detail.table.body', [
                    'formatNumber' => false,
                ])
            </tbody>
            <tfoot name="table_foot">
                @include('admin.sale-order-trading-report.sale-order-trading-detail.table.footer', [
                    'formatNumber' => false,
                ])
            </tfoot>
        </table>
    </div>
@endforeach
