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
                <p class="font-small-2 text-uppercase my-0">periode : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped mt-10">
            <thead>
                <tr>
                    <th class="font-small-1 text-center">NO.</th>
                    <th class="font-small-1 text-center">SUPPLIER</th>
                    <th class="font-small-1 text-center">SALDO AWAL</th>
                    <th class="font-small-1 text-center">PEMBELIAN</th>
                    <th class="font-small-1 text-center">PELUNASAN</th>
                    <th class="font-small-1 text-center">SALDO AKHIR HUTANG</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $d)
                    <tr>
                        <td class="font-small-1 text-center">{{ $key + 1 }}.</td>
                        <td class="font-small-1">{{ $d->code }} - {{ $d->nama }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->beginning) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->current_in) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->current_out) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->final_balance) }}</td>
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
                    <th></th>
                    <th>TOTAL</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('beginning')) }}</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('current_in')) }}</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('current_out')) }}</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('final_balance')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
