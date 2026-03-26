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
                    <th class="font-small-1 text-center">TANGGAL</th>
                    <th class="font-small-1 text-center">NO TRANSAKSI</th>
                    <th class="font-small-1 text-center">JATUH TEMPO</th>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <th class="font-small-1 text-center">TERBAYAR</th>
                    <th class="font-small-1 text-center">SISA</th>
                    <th class="font-small-1 text-center">KURS</th>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <th class="font-small-1 text-center">TERBAYAR</th>
                    <th class="font-small-1 text-center">SISA</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $d)
                    <tr>
                        <td></td>
                        <td class="text-left font-small-1"><b>{{ $d->vendor_nama }}</b></td>
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
                    @forelse ($d->data as $key2 => $data)
                        <tr>
                            <td class="font-small-1 text-center">{{ $loop->iteration }}.</td>
                            <td class="font-small-1">{{ localDate($data->date) }}</td>
                            <td class="font-small-1">{{ $data->code }}</td>
                            <td class="font-small-1">{{ localDate($data->due_date ?? '') }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->total, true) }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->paid_amount, true) }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->outstanding_amount, true) }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->exchange_rate, true) }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->total_exchanged, true) }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->paid_amount_exchanged, true) }}</td>
                            <td class="font-small-1 text-end">{{ formatNumber($data->outstanding_amount_exchanged, true) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td align="center">
                                Tidak ada data
                            </td>
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
                    @endforelse
                    <tr>
                        <td></td>
                        <th class="font-small-1 text-center">TOTAL</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th class="font-small-1 text-end">{{ formatNumber($d->data->sum('total_exchanged'), true) }}</th>
                        <th class="font-small-1 text-end">{{ formatNumber($d->data->sum('paid_amount_exchanged'), true) }}</th>
                        <th class="font-small-1 text-end">{{ formatNumber($d->data->sum('outstanding_amount_exchanged'), true) }}</th>
                    </tr>
                @empty
                    <tr>
                        <td align="center" class="font-small-1">
                            Tidak ada data
                        </td>
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
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th class="font-small-1 text-end">{{ formatNumber($total_exchanged, true) }}</th>
                    <th class="font-small-1 text-end">{{ formatNumber($paid_amount_exchanged, true) }}</th>
                    <th class="font-small-1 text-end">{{ formatNumber($outstanding_amount_exchanged, true) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
