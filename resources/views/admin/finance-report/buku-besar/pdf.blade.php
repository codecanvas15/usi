<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">

    <style>
        table tr th,
        table tr td {
            padding: 4px 6px !important;
        }
    </style>
</head>

<body>
    <div class="row">
        <table>
            <tr>
                <td>
                    <h4 class="text-danger text-uppercase my-0">{{ getCompany()->name }}</h4>
                    <p class="font-small-2 my-0">{{ getCompany()->address }}</p>
                    <p class="font-small-2 my-0">Telp. {{ getCompany()->phone }}</p>
                </td>
                <td style="width: 25%">
                    {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-2">
        <div class="row">
            <div class="text-center">
                <h5 class="text-uppercase my-0">laporan {{ Str::headline($type) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
                @if (is_array($coa) && count($coa) > 0)
                    <p class="font-small-2 text-uppercase my-0">
                        COA :
                        {{ $coa[0]->name ?? '-' }} - {{ $coa[0]->account_code ?? '-' }}
                        @if (isset($coa[1]) && $coa[1])
                            to {{ $coa[1]->name ?? '-' }} - {{ $coa[1]->account_code ?? '-' }}
                        @endif
                    </p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered">
            <tbody>
                @forelse ($data as $d)
                    <tr>
                        <th class="font-small-1 text-left" colspan="10">{{ $d->name }} - {{ $d->account_code }}</th>
                    </tr>
                    <tr>
                        <th class="font-small-1 text-center" width="5%">TANGGAL</th>
                        <th class="font-small-1 text-center" width="15%">ITEM</th>
                        <th class="font-small-1 text-center" width="10%">NO TRANSAKSI</th>
                        <th class="font-small-1 text-center" width="10%">NO REFERENSI</th>
                        <th class="font-small-1 text-center" width="7%">NILAI</th>
                        <th class="font-small-1 text-center" width="7%">KURS</th>
                        <th class="font-small-1 text-center" width="7%">DEBIT {{ get_local_currency()->kode }}</th>
                        <th class="font-small-1 text-center" width="7%">KREDIT {{ get_local_currency()->kode }}</th>
                        <th class="font-small-1 text-center" width="7%">SALDO {{ get_local_currency()->kode }}</th>
                        <th class="font-small-1 text-center">KETERANGAN</th>
                    </tr>
                    <tr>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"><b>SALDO AWAL</b></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->amount_before_exchanged) }}</td>
                        <td class="font-small-1 text-center"></td>
                    </tr>
                    @php
                        $balance = $d->amount_before_exchanged;
                    @endphp
                    @forelse ($d->details as $key => $detail)
                        @php
                            $balance += $detail->debit_exchanged;
                            $balance -= $detail->credit_exchanged;

                        @endphp
                        <tr>
                            <td class="font-small-1 text-center">{{ localDate($detail->journal_date) }}</td>
                            <td class="font-small-1 text-left">{{ $detail->journal_remark }}</td>
                            <td class="font-small-1 text-center">
                                @if ($detail->document_reference)
                                    <a href="{{ toLocalLink($detail->document_reference->link) }}" target="_blank">
                                        {{ $detail->document_reference->code }}
                                    </a>
                                @endif
                            </td>
                            <td class="font-small-1 text-center">
                                @if ($detail->reference)
                                    @if ($detail->reference->link ?? null)
                                        <a href="{{ toLocalLink($detail->reference->link) }}" target="_blank">
                                            {{ $detail->reference->code }}
                                        </a>
                                    @endif
                                @endif
                            </td>
                            <td class="font-small-1 text-right">{{ formatNumber($detail->debit != 0 ? $detail->debit : $detail->credit) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($detail->exchange_rate) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($detail->debit_exchanged) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($detail->credit_exchanged) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($balance) }}</td>
                            <td class="font-small-1 text-left">{{ $detail->remark }}</td>
                        </tr>

                    @empty
                        <tr>
                            <td align="center" colspan="10" class="font-small-1">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                    <tr>
                        <th class="font-small-1 text-center">TOTAL</th>
                        <th class="font-small-1 text-center"></th>
                        <th class="font-small-1 text-center"></th>
                        <th class="font-small-1 text-center"></th>
                        <th class="font-small-1 text-center"></th>
                        <th class="font-small-1 text-center"></th>
                        <th class="font-small-1 text-right">{{ formatNumber($d->details->sum('debit_exchanged')) }}</th>
                        <th class="font-small-1 text-right">{{ formatNumber($d->details->sum('credit_exchanged')) }}</th>
                        <th class="font-small-1 text-right"></th>
                        <th class="font-small-1 text-center"></th>
                    </tr>
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                @empty
                    <tr>
                        <th class="font-small-1 text-center" colspan="10">Tidak ada data</th>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
