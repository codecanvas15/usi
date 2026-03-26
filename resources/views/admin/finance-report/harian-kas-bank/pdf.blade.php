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
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="valign-middle font-xsmall-3 text-center" rowspan="2">NO.</th>
                    <th class="valign-middle font-xsmall-3 text-center" rowspan="2">BANK</th>
                    <th class="valign-middle font-xsmall-3 text-center" rowspan="2">SALDO AWAL</th>
                    <th class="valign-middle font-xsmall-3 text-center" colspan="2">MUTASI</th>
                    <th class="valign-middle font-xsmall-3 text-center" rowspan="2">SALDO AKHIR</th>
                </tr>
                <tr>
                    <th class="valign-middle font-xsmall-3 text-center">PENERIMAAN</th>
                    <th class="valign-middle font-xsmall-3 text-center">PENGELUARAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $d)
                    <tr>
                        <td class="font-xsmall-3 text-center">{{ $key + 1 }}.</td>
                        <td class="font-xsmall-3">{{ $d->account_code }} - {{ $d->name }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->balance_amount_before, true) }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->mutation_debit, true) }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->mutation_credit, true) }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->balance_final, true) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td align="center" colspan="6">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="font-xsmall-3 text-center"></th>
                    <th class="font-xsmall-3">TOTAL</th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right">{{ formatNumber($data->sum('mutation_debit')) }}</th>
                    <th class="font-xsmall-3 text-right">{{ formatNumber($data->sum('mutation_credit')) }}</th>
                    <th class="font-xsmall-3 text-right"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
