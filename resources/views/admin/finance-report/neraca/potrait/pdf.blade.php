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
                <p class="font-small-2 text-uppercase my-0">periode : {{ $period }}</p>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th colspan="4" class="font-small-1 text-center">AKTIVA</th>
                </tr>
                <tr>
                    <th class="font-small-1 text-center">KODE REK.</th>
                    <th class="font-small-1 text-center">KETERANGAN</th>
                    <th class="font-small-1 text-center">BULAN INI</th>
                    <th class="font-small-1 text-center">BULAN LALU</th>
                </tr>
                @foreach ($aktiva as $key => $item)
                    <tr>
                        <td class="font-small-1 text-center">
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            {{ $item['code'] ?? '' }}
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                        <td class="font-small-1 {{ $item['is_total'] ? 'text-right' : '' }}">
                            @for ($i = 0; $i < $item['indent']; $i++)
                                &nbsp;
                            @endfor
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            {{ $item['name'] ?? '' }}
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                        <td class="font-small-1 text-right">
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            @if ($item['balance'] != 0)
                                {{ formatNumber($item['balance']) }}
                            @endif
                            @if ($item['total_balance'] != 0)
                                {{ formatNumber($item['total_balance']) }}
                            @endif
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                        <td class="font-small-1 text-right">
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            @if ($item['prev_balance'] != 0)
                                {{ formatNumber($item['prev_balance']) }}
                            @endif
                            @if ($item['total_prev_balance'] != 0)
                                {{ formatNumber($item['total_prev_balance']) }}
                            @endif
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th></th>
                    <th class="font-small-1 text-right">
                        <b>TOTAL AKTIVA</b>
                    </th>
                    <th class="font-small-1 text-right">
                        <b>{{ formatNumber(array_sum(array_column($aktiva, 'balance'))) }}</b>
                    </th>
                    <th class="font-small-1 text-right">
                        <b>{{ formatNumber(array_sum(array_column($aktiva, 'prev_balance'))) }}</b>
                    </th>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered table-striped mt-2">
            <tbody>
                <tr>
                    <th colspan="4" class="font-small-1 text-center">PASIVA (KEWAJIBAN & EKUITAS)</th>
                </tr>
                <tr>
                    <th class="font-small-1 text-center">KODE REK.</th>
                    <th class="font-small-1 text-center">KETERANGAN</th>
                    <th class="font-small-1 text-center">BULAN INI</th>
                    <th class="font-small-1 text-center">BULAN LALU</th>
                </tr>
                @foreach ($pasiva as $key => $item)
                    <tr>
                        <td class="font-small-1 text-center">
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            {{ $item['code'] ?? '' }}
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                        <td class="font-small-1 {{ $item['is_total'] ? 'text-end' : '' }}">
                            @for ($i = 0; $i < $item['indent']; $i++)
                                &nbsp;
                            @endfor
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            {{ $item['name'] ?? '' }}
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                        <td class="font-small-1 text-end">
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            @if ($item['balance'] != 0)
                                {{ formatNumber($item['balance']) }}
                            @endif
                            @if ($item['total_balance'] != 0)
                                {{ formatNumber($item['total_balance']) }}
                            @endif
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                        <td class="font-small-1 text-end">
                            @if ($item['is_parent'] || $item['is_total'])
                                <b>
                            @endif
                            @if ($item['prev_balance'] != 0)
                                {{ formatNumber($item['prev_balance']) }}
                            @endif
                            @if ($item['total_prev_balance'] != 0)
                                {{ formatNumber($item['total_prev_balance']) }}
                            @endif
                            @if ($item['is_parent'] || $item['is_total'])
                                </b>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th></th>
                    <th class="font-small-1 text-right">
                        <b>TOTAL PASIVA</b>
                    </th>
                    <th class="font-small-1 text-right">
                        <b>{{ formatNumber(array_sum(array_column($pasiva, 'balance'))) }}</b>
                    </th>
                    <th class="font-small-1 text-right">
                        <b>{{ formatNumber(array_sum(array_column($pasiva, 'prev_balance'))) }}</b>
                    </th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <th class="font-small-1 text-right">
                        <b>BALANCE (AKTIVA - PASIVA)</b>
                    </th>
                    <th class="font-small-1 text-right">
                        <b>{{ formatNumber(array_sum(array_column($aktiva, 'balance')) - array_sum(array_column($pasiva, 'balance'))) }}</b>
                    </th>
                    <th class="font-small-1 text-right">
                        <b>{{ formatNumber(array_sum(array_column($aktiva, 'prev_balance')) - array_sum(array_column($pasiva, 'prev_balance'))) }}</b>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
