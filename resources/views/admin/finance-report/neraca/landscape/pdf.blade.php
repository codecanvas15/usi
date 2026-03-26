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
            <thead>
                <tr>
                    <th class="font-xsmall-2">KODE REK.</th>
                    <th class="font-xsmall-2">KETERANGAN</th>
                    <th class="font-xsmall-2">BULAN INI</th>
                    <th class="font-xsmall-2">BULAN LALU</th>
                    <th class="font-xsmall-2"></th>
                    <th class="font-xsmall-2">KODE REK.</th>
                    <th class="font-xsmall-2">KETERANGAN</th>
                    <th class="font-xsmall-2">BULAN INI</th>
                    <th class="font-xsmall-2">BULAN LALU</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $loop_data = $aktiva;
                    $second_loop_data = $pasiva;
                @endphp

                @if (count($aktiva) > count($pasiva))
                    @foreach ($loop_data as $key => $item)
                        <tr>
                            <td class="font-xsmall-2 text-center">
                                @if ($item['is_parent'] || $item['is_total'])
                                    <b>
                                @endif
                                {{ $item['code'] }}
                                @if ($item['is_parent'] || $item['is_total'])
                                    </b>
                                @endif
                            </td>
                            <td class="font-xsmall-2 {{ $item['is_total'] ? 'text-right' : '' }}">
                                @for ($i = 0; $i < $item['indent']; $i++)
                                    &nbsp;
                                @endfor
                                @if ($item['is_parent'] || $item['is_total'])
                                    <b>
                                @endif
                                {{ $item['name'] }}
                                @if ($item['is_parent'] || $item['is_total'])
                                    </b>
                                @endif
                            </td>
                            <td class="font-xsmall-2 text-right">
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
                            <td class="font-xsmall-2 text-right">
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
                            <td></td>
                            @if (isset($second_loop_data[$key]))
                                <td class="font-xsmall-2 text-center">
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    {{ $second_loop_data[$key]['code'] }}
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="font-xsmall-2 {{ $second_loop_data[$key]['is_total'] ? 'text-right' : '' }}">
                                    @for ($i = 0; $i < $second_loop_data[$key]['indent']; $i++)
                                        &nbsp;
                                    @endfor
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    {{ $second_loop_data[$key]['name'] }}
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="font-xsmall-2 text-right">
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    @if ($second_loop_data[$key]['balance'] != 0)
                                        {{ formatNumber($second_loop_data[$key]['balance']) }}
                                    @endif
                                    @if ($second_loop_data[$key]['total_balance'] != 0)
                                        {{ formatNumber($second_loop_data[$key]['total_balance']) }}
                                    @endif
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="font-xsmall-2 text-right">
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    @if ($second_loop_data[$key]['prev_balance'] != 0)
                                        {{ formatNumber($second_loop_data[$key]['prev_balance']) }}
                                    @endif
                                    @if ($second_loop_data[$key]['total_prev_balance'] != 0)
                                        {{ formatNumber($second_loop_data[$key]['total_prev_balance']) }}
                                    @endif
                                    @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    @foreach ($second_loop_data as $key => $item)
                        <tr>
                            @if (isset($loop_data[$key]))
                                <td class="font-xsmall-2 text-center">
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    {{ $loop_data[$key]['code'] }}
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="{{ $loop_data[$key]['is_total'] ? 'text-right' : '' }}">
                                    @for ($i = 0; $i < $loop_data[$key]['indent']; $i++)
                                        &nbsp;
                                    @endfor
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    {{ $loop_data[$key]['name'] }}
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="font-xsmall-2 text-right">
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    @if ($loop_data[$key]['balance'] != 0)
                                        {{ formatNumber($loop_data[$key]['balance']) }}
                                    @endif
                                    @if ($loop_data[$key]['total_balance'] != 0)
                                        {{ formatNumber($loop_data[$key]['total_balance']) }}
                                    @endif
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                                <td class="font-xsmall-2 text-right">
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        <b>
                                    @endif
                                    @if ($loop_data[$key]['prev_balance'] != 0)
                                        {{ formatNumber($loop_data[$key]['prev_balance']) }}
                                    @endif
                                    @if ($loop_data[$key]['total_prev_balance'] != 0)
                                        {{ formatNumber($loop_data[$key]['total_prev_balance']) }}
                                    @endif
                                    @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                        </b>
                                    @endif
                                </td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td></td>
                            <td class="font-xsmall-2 text-center">
                                @if ($item['is_parent'] || $item['is_total'])
                                    <b>
                                @endif
                                {{ $item['code'] }}
                                @if ($item['is_parent'] || $item['is_total'])
                                    </b>
                                @endif
                            </td>
                            <td class="font-xsmall-2 {{ $item['is_total'] ? 'text-right' : '' }}">
                                @for ($i = 0; $i < $item['indent']; $i++)
                                    &nbsp;
                                @endfor
                                @if ($item['is_parent'] || $item['is_total'])
                                    <b>
                                @endif
                                {{ $item['name'] }}
                                @if ($item['is_parent'] || $item['is_total'])
                                    </b>
                                @endif
                            </td>
                            <td class="font-xsmall-2 text-right">
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
                            <td class="font-xsmall-2 text-right">
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
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td class="font-xsmall-2"></td>
                    <td class="font-xsmall-2">
                        <b>TOTAL AKTIVA</b>
                    </td>
                    <td class="font-xsmall-2 text-right">
                        <b>{{ formatNumber(array_sum(array_column($loop_data, 'balance'))) }}</b>
                    </td>
                    <td class="font-xsmall-2 text-right">
                        <b>{{ formatNumber(array_sum(array_column($loop_data, 'prev_balance'))) }}</b>
                    </td>
                    <td></td>
                    <td class="font-xsmall-2"></td>
                    <td class="font-xsmall-2">
                        <b>TOTAL PASIVA</b>
                    </td>
                    <td class="font-xsmall-2 text-right">
                        <b>{{ formatNumber(array_sum(array_column($second_loop_data, 'balance'))) }}</b>
                    </td>
                    <td class="font-xsmall-2 text-right">
                        <b>{{ formatNumber(array_sum(array_column($second_loop_data, 'prev_balance'))) }}</b>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="font-xsmall-2">
                        <b>BALANCE (AKTIVA - PASIVA)</b>
                    </td>
                    <td class="font-xsmall-2" align="right">
                        <b>{{ formatNumber(array_sum(array_column($loop_data, 'balance')) - array_sum(array_column($second_loop_data, 'balance'))) }}</b>
                    </td>
                    <td class="font-xsmall-2" align="right">
                        <b>{{ formatNumber(array_sum(array_column($loop_data, 'prev_balance')) - array_sum(array_column($second_loop_data, 'prev_balance'))) }}</b>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
