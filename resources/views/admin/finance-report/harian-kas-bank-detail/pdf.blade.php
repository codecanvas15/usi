<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
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
                @if ($coa)
                    <p class="font-small-2 text-uppercase my-0">KAS/BANK : {{ $coa->account_code }} - {{ $coa->name }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped">
            <tbody>
                @foreach ($data as $d)
                    <tr class="bg-light">
                        <th class="font-xsmall-3 text-left" colspan="14">{{ $d->name }}</th>
                    </tr>
                    <tr>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">TANGGAL</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">NO BUKTI</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">NO DOKUMEN</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">NO CHECK/GIRO</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">NAMA</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">URAIAN</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">NO DETAIL/KODE ACCT</th>
                        <th class="valign-middle font-xsmall-3 text-center" colspan="2">MUTASI</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">SALDO AKHIR</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">KURS</th>
                        <th class="valign-middle font-xsmall-3 text-center" colspan="2">MUTASI ({{ get_local_currency()->simbol }})</th>
                        <th class="valign-middle font-xsmall-3 text-center" rowspan="2">SALDO AKHIR</th>
                    </tr>
                    <tr>
                        <th class="valign-middle font-xsmall-3 text-center">PENERIMAAN</th>
                        <th class="valign-middle font-xsmall-3 text-center">PENGELUARAN</th>
                        <th class="valign-middle font-xsmall-3 text-center">PENERIMAAN</th>
                        <th class="valign-middle font-xsmall-3 text-center">PENGELUARAN</th>
                    </tr>
                    <tr>
                        <td class="font-xsmall-3">SALDO AWAL</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="font-xsmall-3 text-end">{{ formatNumber($d->foreign_beginning_balance, true) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="font-xsmall-3 text-end">{{ formatNumber($d->beginning_balance, true) }}</td>
                    </tr>
                    @forelse ($d->transactions as $key => $transaction)
                        <tr>
                            <td class="font-xsmall-3 text-center">{{ localDate($transaction->date) }}</td>
                            <td class="font-xsmall-3 text-center">{{ $transaction->bank_code_mutation }}</td>
                            <td class="font-xsmall-3">{{ $transaction->document_reference->code ?? '' }}</td>
                            <td class="font-xsmall-3 text-center">{{ $transaction->giro_in ?? $transaction->giro_out }}</td>
                            <td class="font-xsmall-3">{{ $transaction->vendor_customer->nama ?? '' }}</td>
                            <td class="font-xsmall-3">{{ $transaction->remark }}</td>
                            <td class="font-xsmall-3">{{ $transaction->opponent_account_code ?? '' }} {{ $transaction->opponent_name ?? '' }}</td>
                            <td class="font-xsmall-3 text-right">{{ $transaction->simbol }} {{ formatNumber($transaction->debit, true) }}</td>
                            <td class="font-xsmall-3 text-right">{{ $transaction->simbol }} {{ formatNumber($transaction->credit, true) }}</td>
                            <td class="font-xsmall-3 text-right">{{ formatNumber($transaction->foreign_balance_after, true) }}</td>
                            <td class="font-xsmall-3 text-right">{{ formatNumber($transaction->exchange_rate, true) }}</td>
                            <td class="font-xsmall-3 text-right">{{ formatNumber($transaction->debit_exchanged, true) }}</td>
                            <td class="font-xsmall-3 text-right">{{ formatNumber($transaction->credit_exchanged, true) }}</td>
                            <td class="font-xsmall-3 text-right">{{ formatNumber($transaction->balance_after, true) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td align="center" colspan="14" class="font-xsmall-3">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                    <tr class="bg-light">
                        <th colspan="7" class="font-xsmall-3 text-end">TOTAL</th>
                        <th class="font-xsmall-3 text-end">{{ formatNumber($d->transactions->sum('debit'), true) }}</th>
                        <th class="font-xsmall-3 text-end">{{ formatNumber($d->transactions->sum('credit'), true) }}</th>
                        <th class="font-xsmall-3 text-end"></th>
                        <th class="font-xsmall-3 text-end"></th>
                        <th class="font-xsmall-3 text-end">{{ formatNumber($d->transactions->sum('debit_exchanged'), true) }}</th>
                        <th class="font-xsmall-3 text-end">{{ formatNumber($d->transactions->sum('credit_exchanged'), true) }}</th>
                        <th class="font-xsmall-3 text-end">{{ formatNumber($d->final_balance, true) }}</th>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
