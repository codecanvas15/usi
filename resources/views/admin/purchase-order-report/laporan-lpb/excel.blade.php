<table>
    <tr>
        <td colspan="3">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</b></p>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="11" align="center">
            <p><b>{{ Str::upper(Str::headline('Laporan Hutang Dagang')) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="11" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
    @if ($vendor)
        <tr>
            <td colspan="11" align="center">
                <p><b>VENDOR : {{ $vendor->nama }}</b></p>
            </td>
        </tr>
    @endif
    @if ($currency)
        <tr>
            <td colspan="11" align="center">
                <p><b>CURRENCY : {{ $currency->nama }}</b></p>
            </td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>
            <th align="center"><b>NO.</b></th>
            <th align="center"><b>TGL LPB</b></th>
            <th align="center"><b>NO. PO</b></th>
            <th align="center"><b>PROJECT</b></th>
            <th align="center"><b>VENDOR</b></th>
            <th align="center"><b>NO LPB</b></th>
            <th align="center"><b>CUR.</b></th>
            <th align="center"><b>TOTAL LPB</b></th>
            <th align="center"><b>RATE</b></th>
            <th align="center"><b>TOTAL LPB IDR</b></th>
            <th align="center"><b>TGL BAYAR</b></th>
            <th align="center"><b>BANK</b></th>
            <th align="center"><b>NOMINAL</b></th>
            <th align="center"><b>KODE PEMBAYARAN</b></th>
            <th align="center"><b>SISA HUTANG</b></th>
            <th align="center"><b>KET.</b></th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $total = 0;
            $total_all = 0;
            $background_color = '';
        @endphp
        @forelse ($data as $key => $d)
            @if ($key == 0 || $d->kode != ($data[$key - 1]->kode ?? ''))
                @php
                    if ($background_color == '') {
                        $background_color = 'background-color: #e3e3e3';
                    } else {
                        $background_color = '';
                    }
                    $total = $d->total;
                    $total -= $d->amount_payment;
                    $total_all += $d->total_rp;
                @endphp
                <tr>
                    <td style="{{ $background_color }}" align="center">{{ $no++ }}.</td>
                    <td style="{{ $background_color }}" align="center">{{ localDate($d->date_receive) }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->po_code }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->po_project }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->vendor_name }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->kode }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->currency_kode }}</td>
                    <td style="{{ $background_color }}" align="right">{{ $d->total }}</td>
                    <td style="{{ $background_color }}" align="right">{{ $d->exchange_rate }}</td>
                    <td style="{{ $background_color }}" align="right">{{ $d->total_rp }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->date_payment ? localDate($d->date_payment) : '' }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->bank }}</td>
                    <td style="{{ $background_color }}" align="right">{{ $d->amount_payment }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->bank_code }}</td>
                    <td style="{{ $background_color }}" align="right">{{ $d->outstanding }}</td>
                    <td style="{{ $background_color }}">{{ $d->note }}</td>
                </tr>
            @else
                @php
                    $total -= $d->amount_payment;
                @endphp
                <tr>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}"></td>
                    <td style="{{ $background_color }}" align="center">{{ $d->date_payment ? localDate($d->date_payment) : '' }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->bank }}</td>
                    <td style="{{ $background_color }}" align="right">{{ $d->amount_payment }}</td>
                    <td style="{{ $background_color }}" align="center">{{ $d->bank_code }}</td>
                    <td style="{{ $background_color }}" align="right"></td>
                    <td style="{{ $background_color }}">{{ $d->note }}</td>
                </tr>
            @endif
        @empty
            <tr>
                <td align="center" colspan="13">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" align="right"><b>TOTAL</b></th>
            <td></td>
            <td></td>
            <th align="right"><b>{{ $total_all }}</b></th>
            <td></td>
            <td></td>
            <th align="right"><b>{{ $data->sum('amount_payment') }}</b></th>
            <td></td>
            <th align="right"><b>{{ $data->sum('outstanding') }}</b></th>
            <th></th>
        </tr>
    </tfoot>
</table>
