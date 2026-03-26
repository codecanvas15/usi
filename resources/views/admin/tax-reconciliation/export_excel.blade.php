<table>
    <tr>
        <td colspan="3">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="9" align="center">
            <p><b>REKONSILIASI PAJAK</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="9" align="center">
            <p><b>BULAN/MASA : {{ Carbon\Carbon::parse($model->tax_period)->translatedFormat('F Y') }}</b></p>
        </td>
    </tr>
</table>
<tr>
    <td></td>
</tr>
<table>
    <tbody>
        <tr>
            <th colspan="2">
                <b>LIST PPN MASUKAN DILAPORKAN</b>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid #000" align="center" rowspan="2"><b>NO</b></th>
            <th style="border: 1px solid #000" align="center" colspan="5"><b>LPB</b></th>
            <th style="border: 1px solid #000" align="center" colspan="2"><b>INVOICE</b></th>
            <th style="border: 1px solid #000" align="center" colspan="2"><b>FAKTUR PAJAK</b></th>
        </tr>
        <tr>
            <th style="border: 1px solid #000" align="center"><b>NOMOR</b></th>
            <th style="border: 1px solid #000" align="center"><b>TGL</b></th>
            <th style="border: 1px solid #000" align="center"><b>DPP</b></th>
            <th style="border: 1px solid #000" align="center"><b>PPN</b></th>
            <th style="border: 1px solid #000" align="center"><b>TOTAL</b></th>
            <th style="border: 1px solid #000" align="center"><b>NOMOR</b></th>
            <th style="border: 1px solid #000" align="center"><b>TGL</b></th>
            <th style="border: 1px solid #000" align="center"><b>NOMOR</b></th>
            <th style="border: 1px solid #000" align="center"><b>TGL</b></th>
        </tr>
        @forelse ($purchase_taxes as $key => $purchase_tax)
            <tr>
                <td style="border: 1px solid #000" align="center">{{ $key + 1 }}.</td>
                <td style="border: 1px solid #000" align="center">{{ $purchase_tax->reference_parent->kode ?? $purchase_tax->note }}</td>
                <td style="border: 1px solid #000" align="center">{{ localDate($purchase_tax->reference_parent->date) }}</td>
                <td style="border: 1px solid #000" align="center">{{ $purchase_tax->dpp }}</td>
                <td style="border: 1px solid #000" align="center">{{ $purchase_tax->amount }}</td>
                <td style="border: 1px solid #000" align="center">{{ $purchase_tax->dpp + $purchase_tax->amount }}</td>
                @if ($purchase_tax->vendor)
                    <td style="border: 1px solid #000" align="center">{{ $purchase_tax->reference_parent->code }}</td>
                    <td style="border: 1px solid #000" align="center">{{ localDate($purchase_tax->reference_parent->date) }}</td>
                    <td style="border: 1px solid #000" align="center">{{ $purchase_tax->tax_number }}</td>
                    <td style="border: 1px solid #000" align="center">{{ localDate($purchase_tax->reference_parent->date) }}</td>
                @else
                    <td style="border: 1px solid #000" align="center"></td>
                    <td style="border: 1px solid #000" align="center"></td>
                    <td style="border: 1px solid #000" align="center"></td>
                    <td style="border: 1px solid #000" align="center"></td>
                @endif
            </tr>
        @empty
            <tr>
                <td style="border: 1px solid #000" align="center" colspan="10">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
        <tr>
            <th style="border: 1px solid #000" align="center"><b>TOTAL</b></th>
            <th style="border: 1px solid #000" align="center"><b></b></th>
            <th style="border: 1px solid #000" align="center"></th>
            <th style="border: 1px solid #000" align="center"><b>=SUM(D9:D{{ count($purchase_taxes) + 9 }})</b></th>
            <th style="border: 1px solid #000" align="center"><b>=SUM(E9:E{{ count($purchase_taxes) + 9 }})</b></th>
            <th style="border: 1px solid #000" align="center"><b>=SUM(F9:F{{ count($purchase_taxes) + 9 }})</b></th>
            <th style="border: 1px solid #000" align="center"></th>
            <th style="border: 1px solid #000" align="center"></th>
            <th style="border: 1px solid #000" align="center"></th>
            <th style="border: 1px solid #000" align="center"></th>
        </tr>
        <tr>
            <td></td>
        </tr>
        @php
            $invoice_tax_start = count($purchase_taxes) + 14;
        @endphp
        <tr>
            <th colspan="2">
                <b>LIST PPN KELUARAN DILAPORKAN</b>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid #000" align="center" rowspan="2"><b>NO</b></th>
            <th style="border: 1px solid #000" align="center" colspan="5"><b>INVOICE</b></th>
            <th style="border: 1px solid #000" align="center" colspan="2"><b>FAKTUR PAJAK</b></th>
        </tr>
        <tr>
            <th style="border: 1px solid #000" align="center"><b>NOMOR</b></th>
            <th style="border: 1px solid #000" align="center"><b>TGL</b></th>
            <th style="border: 1px solid #000" align="center"><b>DPP</b></th>
            <th style="border: 1px solid #000" align="center"><b>PPN</b></th>
            <th style="border: 1px solid #000" align="center"><b>TOTAL</b></th>
            <th style="border: 1px solid #000" align="center"><b>NOMOR</b></th>
            <th style="border: 1px solid #000" align="center"><b>TGL</b></th>
        </tr>
        @forelse ($invoice_taxes as $key => $invoice_tax)
            <tr>
                <td style="border: 1px solid #000" align="center">{{ $key + 1 }}.</td>
                <td style="border: 1px solid #000" align="center">{{ $invoice_tax->reference_parent->code }}</td>
                <td style="border: 1px solid #000" align="center">{{ localDate($invoice_tax->reference_parent->date) }}</td>
                <td style="border: 1px solid #000" align="center">{{ $invoice_tax->dpp }}</td>
                <td style="border: 1px solid #000" align="center">{{ $invoice_tax->amount }}</td>
                <td style="border: 1px solid #000" align="center">{{ $invoice_tax->dpp + $invoice_tax->amount }}</td>
                <td style="border: 1px solid #000" align="center">{{ $invoice_tax->tax_number }}</td>
                <td style="border: 1px solid #000" align="center">{{ localDate($invoice_tax->reference_parent->date) }}</td>
            </tr>
        @empty
            <tr>
                <td style="border: 1px solid #000" align="center" colspan="8">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
        <tr>
            <th style="border: 1px solid #000" align="center"><b>TOTAL</b></th>
            <th style="border: 1px solid #000" align="center"><b></b></th>
            <th style="border: 1px solid #000" align="center"></th>
            <th style="border: 1px solid #000" align="center"><b>=SUM(D{{ $invoice_tax_start }}:D{{ $invoice_tax_start + count($invoice_taxes) }})</b></th>
            <th style="border: 1px solid #000" align="center"><b>=SUM(E{{ $invoice_tax_start }}:E{{ $invoice_tax_start + count($invoice_taxes) }})</b></th>
            <th style="border: 1px solid #000" align="center"><b>=SUM(F{{ $invoice_tax_start }}:F{{ $invoice_tax_start + count($invoice_taxes) }})</b></th>
            <th style="border: 1px solid #000" align="center"></th>
            <th style="border: 1px solid #000" align="center"></th>
        </tr>
    </tbody>
</table>
