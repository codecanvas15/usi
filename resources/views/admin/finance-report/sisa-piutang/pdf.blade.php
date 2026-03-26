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
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($to_date) }}</p>
                @if ($customer)
                    <p class="font-small-2 text-uppercase my-0">CUSTOMER : {{ $customer->nama }} - {{ $customer->code }}</p>
                @endif
                @if ($currency)
                    <p class="font-small-2 text-uppercase my-0">MATA UANG : {{ $currency->kode }} - {{ $currency->nama }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped mt-10">
            <thead>
                <tr>
                    <th class="font-small-1 text-center">NO.</th>
                    <th class="font-small-1 text-center">NO TRANSAKSI</th>
                    <th class="font-small-1 text-center">KODE CUSTOMER</th>
                    <th class="font-small-1 text-center">NAMA CUSTOMER</th>
                    <th class="font-small-1 text-center">TANGGAL</th>
                    <th class="font-small-1 text-center">JATUH TEMPO</th>
                    <th class="font-small-1 text-center">OVERDUE</th>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <th class="font-small-1 text-center">TERBAYAR</th>
                    <th class="font-small-1 text-center">SISA</th>
                    <th class="font-small-1 text-center">KURS</th>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <th class="font-small-1 text-center">TERBAYAR</th>
                    <th class="font-small-1 text-center">AKUMULASI <br> SELISIH KURS</th>
                    <th class="font-small-1 text-center">SISA</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $d)
                    <tr>
                        <td class="font-small-1 text-center">{{ $key + 1 }}.</td>
                        <td class="font-small-1 text-center">{{ $d->code }}</td>
                        <td class="font-small-1">{{ $d->customer_code }}</td>
                        <td class="font-small-1">{{ $d->customer_nama }}</td>
                        <td class="font-small-1 text-center">{{ localDate($d->date) }}</td>
                        <td class="font-small-1 text-center">{{ localDate($d->due_date) }}</td>
                        <td class="font-small-1 text-center">{{ $d->overdue ? formatNumber($d->overdue) : '-' }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->total) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->paid_amount) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->outstanding_amount) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->exchange_rate) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->total_exchanged) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->paid_amount_exchanged) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->acumulated_exchange_rate_gap) }}</td>
                        <td class="font-small-1 text-end">{{ formatNumber($d->outstanding_amount_exchanged) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td align="center" colspan="13" class="font-small-1">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="font-small-1 text-center"></th>
                    <th class="font-small-1 text-center" colspan="10">TOTAL</th>
                    <th class="font-small-1 text-end">{{ formatNumber($data->sum('total_exchanged')) }}</th>
                    <th class="font-small-1 text-end">{{ formatNumber($data->sum('paid_amount_exchanged')) }}</th>
                    <th class="font-small-1 text-end"></th>
                    <th class="font-small-1 text-end">{{ formatNumber($data->sum('outstanding_amount_exchanged')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
