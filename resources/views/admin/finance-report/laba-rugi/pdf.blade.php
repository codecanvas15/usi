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
                @if ($branch)
                    <p class="font-small-2 text-uppercase my-0">Branch : {{ $branch->name }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="font-xsmall-3 text-center">KODE REK.</th>
                    <th class="font-xsmall-3 text-center">KETERANGAN</th>
                    <th class="font-xsmall-3 text-center">BULAN INI</th>
                    <th class="font-xsmall-3 text-center">S/D BULAN INI</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_parent = 0;
                    $total_parent_prev = 0;
                @endphp
                @foreach ($data as $key => $item)
                    @foreach ($data[$key] as $key_subcategory => $subcategory)
                        @php
                            $total_subcategory = 0;
                            $total_subcategory_prev = 0;
                        @endphp
                        <tr>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3"><b>{{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3"></td>
                        </tr>
                        @foreach ($item[$key_subcategory]['data'] as $detail)
                            <tr>
                                <td class="font-xsmall-3 text-center">{{ Str::upper(Str::headline($detail['code'])) }}</td>
                                <td class="font-xsmall-3">{{ Str::upper(Str::headline($detail['coa'])) }}</td>
                                <td class="font-xsmall-3 text-right">{{ formatNumber($detail['current_period']) }}</td>
                                <td class="font-xsmall-3 text-right">{{ formatNumber($detail['prev_period']) }}</td>
                            </tr>
                            @php
                                if ($item[$key_subcategory]['type'] == 'plus') {
                                    $total_subcategory += $detail['current_period'];
                                    $total_subcategory_prev += $detail['prev_period'];
                                } else {
                                    $total_subcategory -= $detail['current_period'];
                                    $total_subcategory_prev -= $detail['prev_period'];
                                }
                            @endphp
                        @endforeach
                        <tr>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3 text-right"><b>TOTAL {{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                            <td class="font-xsmall-3 text-right"><b>{{ formatNumber($total_subcategory) }}</b></td>
                            <td class="font-xsmall-3 text-right"><b>{{ formatNumber($total_subcategory_prev) }}</b></td>
                        </tr>
                        @php
                            $total_parent += $total_subcategory;
                            $total_parent_prev += $total_subcategory_prev;
                        @endphp
                    @endforeach
                    <tr>
                        <td class="font-xsmall-3" colspan="2"><b>{{ Str::upper(Str::headline($key)) }}</b></td>
                        <td class="font-xsmall-3 text-right"><b>{{ formatNumber($total_parent) }}</b></td>
                        <td class="font-xsmall-3 text-right"><b>{{ formatNumber($total_parent_prev) }}</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</body>

</html>
