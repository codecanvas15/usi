<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline('Laporan Hutang Dagang') }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    <div class="row">
        <table>
            <tr>
                <td>
                    <h4 class="text-danger text-uppercase my-0">{{ Str::upper(getCompany()->name) }}</h4>
                    <p class="font-small-2 my-0">{{ getCompany()->address }}</p>
                    <p class="font-small-2 my-0">Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</p>
                </td>
                <td style="width: 25%">
                    {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" style="width: 136px"></center> --}}
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-2">
        <div class="row">
            <div class="text-center">
                <h5 class="text-uppercase my-0">{{ Str::headline('Laporan Hutang Dagang') }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
                @if ($vendor)
                    <p class="font-small-2 text-uppercase my-0">VENDOR : {{ $vendor->nama }}</p>
                @endif
                @if ($currency)
                    <p class="font-small-2 text-uppercase my-0">CURRENCY : {{ $currency->nama }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center font-small-1">NO.</th>
                    <th class="text-center font-small-1">TGL LPB</th>
                    <th class="text-center font-small-1">NO. PO</th>
                    <th class="text-center font-small-1">PROJECT</th>
                    <th class="text-center font-small-1">VENDOR</th>
                    <th class="text-center font-small-1">NO LPB</th>
                    <th class="text-center font-small-1">CUR.</th>
                    <th class="text-center font-small-1">TOTAL LPB</th>
                    <th class="text-center font-small-1">RATE</th>
                    <th class="text-center font-small-1">TOTAL LPB IDR</th>
                    <th class="text-center font-small-1">TGL BAYAR</th>
                    <th class="text-center font-small-1">BANK</th>
                    <th class="text-center font-small-1">NOMINAL</th>
                    <th class="text-center font-small-1">KODE PEMBAYARAN</th>
                    <th class="text-center font-small-1">SISA HUTANG</th>
                    <th class="text-center font-small-1">KET.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $total = 0;
                    $total_all = 0;
                @endphp
                @forelse ($data as $key => $d)
                    @if ($key == 0 || $d->kode != ($data[$key - 1]->kode ?? ''))
                        @php
                            $total = $d->total;
                            $total -= $d->amount_payment;
                            $total_all += $d->total_rp;
                        @endphp
                        <tr>
                            <td class="font-small-1 text-center">{{ $no++ }}.</td>
                            <td class="font-small-1 text-center">{{ localDate($d->date_receive) }}</td>
                            <td class="font-small-1 text-center">{{ $d->po_code }}</td>
                            <td class="text-center">
                                <a href="{{ $d->po_project_link}}">
                                    {{ $d->po_project }}
                                </a>
                            </td>
                            <td class="font-small-1 text-center">{{ $d->vendor_name }}</td>
                            <td class="font-small-1 text-center">{{ $d->kode }}</td>
                            <td class="font-small-1 text-center">{{ $d->currency_kode }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($d->total) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($d->exchange_rate) }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($d->total_rp) }}</td>
                            <td class="font-small-1 text-center">{{ $d->date_payment ? localDate($d->date_payment) : '' }}</td>
                            <td class="font-small-1 text-center">{{ $d->bank }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($d->amount_payment) }}</td>
                            <td class="font-small-1 text-center">{{ $d->bank_code }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($d->outstanding) }}</td>
                            <td class="font-small-1 text-left">{{ $d->note }}</td>
                        </tr>
                    @else
                        @php
                            $total -= $d->amount_payment;
                        @endphp
                        <tr>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1"></td>
                            <td class="font-small-1 text-center">{{ $d->date_payment ? localDate($d->date_payment) : '' }}</td>
                            <td class="font-small-1 text-center">{{ $d->bank }}</td>
                            <td class="font-small-1 text-right">{{ formatNumber($d->amount_payment) }}</td>
                            <td class="font-small-1 text-center">{{ $d->bank_code }}</td>
                            <td class="font-small-1 text-right"></td>
                            <td class="font-small-1 text-left">{{ $d->note }}</td>
                        </tr>
                    @endif
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
                    <th colspan="6" class="font-small-1 text-right">TOTAL</th>
                    <td></td>
                    <td></td>
                    <th class="font-small-1 text-right">{{ formatNumber($total_all) }}</th>
                    <td></td>
                    <td></td>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('amount_payment')) }}</th>
                    <td></td>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('outstanding')) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
