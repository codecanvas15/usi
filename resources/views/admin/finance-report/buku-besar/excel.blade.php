@php
    $beginning_row = 5;
@endphp
<table>
    <tr>
        <td colspan="5">
            <p><b>{{ getCompany()->name }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td></td>
        <td></td>
        <td colspan="2">
            <center><img src="{{ public_path('/images/icon.png') }}" style="width: 116px"></center>
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="10" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="10" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
    @if (is_object($coa))
        @php
            $beginning_row = 6;
        @endphp
        <tr>
            <td colspan="10" align="center">
                <p><b>COA : {{ $coa->nama ?? '-' }} - {{ $coa->account_code ?? '-' }}</b></p>
            </td>
        </tr>
    @endif
</table>
<table>
    <tbody>
        @forelse ($data as $d)
            @php
                $beginning_row += 3;
            @endphp
            <tr>
                <th align="left" colspan="10"><b>{{ Str::upper($d->name) }} - {{ Str::upper($d->account_code) }}</b></th>
            </tr>
            <tr>
                <th style="border: 1px solid #000" align="center"><b>TANGGAL</b></th>
                <th style="border: 1px solid #000" align="center"><b>KETERANGAN</b></th>
                <th style="border: 1px solid #000" align="center"><b>NO TRANSAKSI</b></th>
                <th style="border: 1px solid #000" align="center"><b>NO REFERENSI</b></th>
                <th style="border: 1px solid #000" align="center"><b>NILAI</b></th>
                <th style="border: 1px solid #000" align="center"><b>KURS</b></th>
                <th style="border: 1px solid #000" align="center"><b>DEBIT {{ get_local_currency()->kode }}</b></th>
                <th style="border: 1px solid #000" align="center"><b>KREDIT {{ get_local_currency()->kode }}</b></th>
                <th style="border: 1px solid #000" align="center"><b>SALDO {{ get_local_currency()->kode }}</b></th>
                <th style="border: 1px solid #000" align="center"><b>ITEM</b></th>
            </tr>
            <tr>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="left"><b>SALDO AWAL</b></td>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="center"></td>
                <td style="border: 1px solid #000" align="right">{{ $d->amount_before_exchanged }}</td>
                <td style="border: 1px solid #000" align="center"></td>
            </tr>
            @forelse ($d->details as $key => $detail)
                @php
                    $beginning_row += 1;
                @endphp
                <tr>
                    <td style="border: 1px solid #000" align="center">{{ localDate($detail->journal_date) }}</td>
                    <td style="border: 1px solid #000" align="left">{{ $detail->journal_remark }}</td>
                    <td style="border: 1px solid #000" align="center">
                        @if ($detail->document_reference)
                            <a href="{{ toLocalLink($detail->document_reference->link) }}" target="_blank">
                                {{ $detail->document_reference->code }}
                            </a>
                        @endif
                    </td>
                    <td style="border: 1px solid #000" align="center">
                        @if ($detail->reference)
                            @if ($detail->reference->link ?? null)
                                <a href="{{ toLocalLink($detail->reference->link) }}" target="_blank">
                                    {{ $detail->reference->code }}
                                </a>
                            @endif
                        @endif
                    </td>
                    <td style="border: 1px solid #000" align="right">{{ $detail->debit != 0 ? $detail->debit : $detail->credit }}</td>
                    <td style="border: 1px solid #000" align="right">{{ $detail->exchange_rate }}</td>
                    <td style="border: 1px solid #000" align="right">{{ $detail->debit_exchanged }}</td>
                    <td style="border: 1px solid #000" align="right">{{ $detail->credit_exchanged }}</td>
                    <td style="border: 1px solid #000" align="right">=I{{ $beginning_row - 1 }}+G{{ $beginning_row }}-H{{ $beginning_row }}</td>
                    <td style="border: 1px solid #000" align="left">{{ $detail->remark }}</td>
                </tr>
            @empty
                @php
                    $beginning_row += 1;
                @endphp
                <tr>
                    <td style="border: 1px solid #000" align="center" colspan="10" class="font-small-1">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
            <tr>
                <th style="border: 1px solid #000" align="center"><b>TOTAL</b></th>
                <th style="border: 1px solid #000" align="center"></th>
                <th style="border: 1px solid #000" align="center"></th>
                <th style="border: 1px solid #000" align="center"></th>
                <th style="border: 1px solid #000" align="center"></th>
                <th style="border: 1px solid #000" align="center"></th>
                <th style="border: 1px solid #000" align="right"><b>=SUM(G{{ $beginning_row - count($d->details) }}:G{{ $beginning_row }})</b></th>
                <th style="border: 1px solid #000" align="right"><b>=SUM(H{{ $beginning_row - count($d->details) }}:H{{ $beginning_row }})</b></th>
                <th style="border: 1px solid #000" align="right"></th>
                <th style="border: 1px solid #000" align="center"></th>
            </tr>
            <tr>
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
            </tr>
            @php
                $beginning_row += 2;
            @endphp
        @empty
            @php
                $beginning_row += 1;
            @endphp
            <tr>
                <th style="border: 1px solid #000" align="center" colspan="10">Tidak ada data</th>
            </tr>
        @endforelse
    </tbody>
</table>
