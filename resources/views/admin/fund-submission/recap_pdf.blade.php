<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pengajuan Dana</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
    <style>
        table tr th,
        table tr td {
            padding: 2px 3px !important;
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
                <h5 class="text-uppercase my-0">laporan pengajuan dana</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="valign-middle font-xsmall-2 text-center">TANGGAL</th>
                    <th class="valign-middle font-xsmall-2 text-center">NO</th>
                    <th class="valign-middle font-xsmall-2 text-center">KEPADA</th>
                    <th class="valign-middle font-xsmall-2 text-center">JENIS</th>
                    <th class="valign-middle font-xsmall-2 text-center">TOTAL</th>
                    <th class="valign-middle font-xsmall-2 text-center">KURS</th>
                    <th class="valign-middle font-xsmall-2 text-center">RATE</th>
                    <th class="valign-middle font-xsmall-2 text-center">TOTAL {{ get_local_currency()->kode }}</th>
                    <th class="valign-middle font-xsmall-2 text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @forelse ($data as $key => $d)
                    <tr>
                        <td class="font-xsmall-2 text-center">{{ localDate($d->date) }}</td>
                        <td class="font-xsmall-2 text-center">{{ $d->code }}</td>
                        <td class="font-xsmall-2 text-center">{{ $d->to_name }}</td>
                        <td class="font-xsmall-2 text-center text-uppercase">{{ $d->item }}</td>
                        <td class="font-xsmall-2 text-right">{{ formatNumber($d->total) }}</td>
                        <td class="font-xsmall-2 text-center text-uppercase">{{ $d->currency->nama }}</td>
                        <td class="font-xsmall-2 text-right">{{ formatNumber($d->exchange_rate) }}</td>
                        <td class="font-xsmall-2 text-right">{{ formatNumber($d->total * $d->exchange_rate) }}</td>
                        <td class="font-xsmall-2 text-center">{{ Str::upper($d->status) }} / {{ $d->is_used ? 'CAIR' : 'BELUM CAIR' }}</td>
                    </tr>
                    @php
                        $total += $d->total * $d->exchange_rate;
                    @endphp
                @empty
                    <tr>
                        <td align="center" colspan="9" class="font-xsmall-2">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="font-xsmall-2 text-end">TOTAL</th>
                    <th class="font-xsmall-2 text-right">{{ formatNumber($total) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
