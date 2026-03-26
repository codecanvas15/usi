<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">

    <style>
        tr td,
        tr th {
            padding: 5px !important;
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
                @if ($vendor)
                    <p class="font-small-2 text-uppercase my-0">VENDOR : {{ $vendor->nama }} - {{ $vendor->code }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered">
            <tbody>
                @php
                    $total_balance = 0;
                    $total_balance_exchanged = 0;

                    $total_debit = 0;
                    $total_credit = 0;

                    $total_debit_exchanged = 0;
                    $total_credit_exchanged = 0;
                @endphp
                @forelse ($data as $d)
                    <tr>
                        <th class="font-small-1 text-left" colspan="14">{{ $d->nama }} - {{ $d->code }}</th>
                    </tr>
                    <tr>
                        <th class="font-small-1 text-center">TANGGAL</th>
                        <th class="font-small-1 text-center">TRANSAKSI</th>
                        <th class="font-small-1 text-center">NO TRANSAKSI</th>
                        <th class="font-small-1 text-center">NO BANK</th>
                        <th class="font-small-1 text-center">KETERANGAN</th>
                        <th class="font-small-1 text-center">LPB</th>
                        <th class="font-small-1 text-center">REF.</th>
                        <th class="font-small-1 text-center">DEBIT</th>
                        <th class="font-small-1 text-center">KREDIT</th>
                        <th class="font-small-1 text-center">SALDO</th>
                        <th class="font-small-1 text-center">KURS</th>
                        <th class="font-small-1 text-center">DEBIT {{ get_local_currency()->kode }}</th>
                        <th class="font-small-1 text-center">KREDIT {{ get_local_currency()->kode }}</th>
                        <th class="font-small-1 text-center">SALDO {{ get_local_currency()->kode }}</th>
                    </tr>
                    @php
                        $balance = $d->beginning_balance;
                        $total_balance += $balance;
                        $total_balance += $d->current_data->sum('credit') - $d->current_data->sum('debit');

                        $balance_exchanged = $d->beginning_balance_exchanged;
                        $total_balance_exchanged += $balance_exchanged;
                        $total_balance_exchanged += $d->current_data->sum('credit_exchanged') - $d->current_data->sum('debit_exchanged');

                        $total_debit += $d->current_data->sum('debit');
                        $total_credit += $d->current_data->sum('credit');

                        $total_debit_exchanged += $d->current_data->sum('debit_exchanged');
                        $total_credit_exchanged += $d->current_data->sum('credit_exchanged');
                    @endphp
                    <tr>
                        <td class="font-small-1 text-center">SALDO</td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-center"></td>
                        <td class="font-small-1 text-right"></td>
                        <td class="font-small-1 text-right"></td>
                        <td class="font-small-1 text-right">{{ formatNumber($balance) }}</td>
                        <td class="font-small-1 text-right"></td>
                        <td class="font-small-1 text-right"></td>
                        <td class="font-small-1 text-right"></td>
                        <td class="font-small-1 text-right">{{ formatNumber($balance_exchanged) }}</td>
                    </tr>
                    @forelse ($d->current_data as $key => $current)
                        @php
                            $balance -= $current->debit;
                            $balance += $current->credit;
                            $balance_exchanged -= $current->debit_exchanged;
                            $balance_exchanged += $current->credit_exchanged;
                        @endphp
                        <tr>
                            <td class="font-small-1 text-center">{{ localDate($current->date) }}</td>
                            <td class="font-small-1">{{ $current->transaction }}</td>
                            <td class="font-small-1">
                                <a href="{{ $current->link }}" target="_blank">
                                    {{ $current->transaction_code }}
                                </a>
                            </td>
                            <td class="font-small-1 text-center">{{ $current->bank_code ?? '' }}</td>
                            <td class="font-small-1 ">{{ $current->note }}</td>
                            <td class="font-small-1 ">{!! $current->lpb_number !!}</td>
                            <td class="font-small-1 ">{!! $current->po_number !!}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($current->debit) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($current->credit) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($balance) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($current->exchange_rate) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($current->debit_exchanged) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($current->credit_exchanged) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($balance_exchanged) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td align="center" colspan="14" class="font-small-1">
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
                        <th class="font-small-1 text-center"></th>
                        <th class="font-small-1 text-right"></th>
                        <th class="font-small-1 text-right"></th>
                        <th class="font-small-1 text-right"></th>
                        <th class="font-small-1 text-right"></th>
                        <th class="font-small-1 text-right">{{ formatNumber($d->current_data->sum('debit_exchanged')) }}</th>
                        <th class="font-small-1 text-right">{{ formatNumber($d->current_data->sum('credit_exchanged')) }}</th>
                        <th class="font-small-1 text-right"></th>
                    </tr>
                @empty
                    <tr>
                        <th class="font-small-1 text-center" colspan="14">Tidak ada data</th>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                    <td style="background-color: #000"></td>
                </tr>
                <tr>
                    <td class="font-small-1 text-center"><b>TOTAL</b></td>
                    <td class="font-small-1 text-center"></td>
                    <td class="font-small-1 text-center"></td>
                    <td class="font-small-1 text-center"></td>
                    <td class="font-small-1 text-center"></td>
                    <td class="font-small-1 text-right"></td>
                    <td class="font-small-1 text-right"></td>
                    <td class="font-small-1 text-right"></td>
                    <td class="font-small-1 text-right"></td>
                    <td class="font-small-1 text-right"></td>
                    <td class="font-small-1 text-right"></td>
                    <td class="font-small-1 text-right"><b>{{ formatNumber($total_debit) }}</b></td>
                    <td class="font-small-1 text-right"><b>{{ formatNumber($total_credit) }}</b></td>
                    <td class="font-small-1 text-right"><b>{{ formatNumber($total_balance_exchanged) }}</b></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
