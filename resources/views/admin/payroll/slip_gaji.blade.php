<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style type="text/css">
        body {
            color: #000;
        }

        table {
            border-spacing: 0px;
        }

        span {
            font-size: 12px;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        #footer .page:after {
            content: counter(page, upper-roman);
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%">
            <table style="width: 100%">
                <tr>
                    <td style="width: 65%; vertical-align: top">
                        <h1 class="font-medium-1 text-uppercase mb-0 text-danger">{{ getCompany()->name }}</h1>
                        <p class="font-small-1 text-uppercase mt-0">{{ getCompany()->address }}</p>
                    </td>
                    <td style="width: 25%">
                        {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
                    </td>
                </tr>
            </table>
            <div class="text-center">
                <h1 class="font-medium-1">SLIP GAJI</h1>
            </div>
        </div>
        <table width="100%" class="mt-2">
            <tbody>
                <tr>
                    <td class="font-small-2 " width="2%">
                        BULAN
                    </td>
                    <td class="font-small-2  text-uppercase" width="20%">: {{ \Carbon\Carbon::parse($period->date)->translatedformat('F Y') }}</td>
                </tr>
                <tr>
                    <td class="font-small-2 " width="2%">
                        NAMA
                    </td>
                    <td class="font-small-2 " width="20%">: {{ $salary->user->name }}</td>
                </tr>
                <tr>
                    <td class="font-small-2 " width="2%">
                        JABATAN
                    </td>
                    <td class="font-small-2 " width="20%">: {{ $salary->user->position?->nama }}</td>
                </tr>
            </tbody>
        </table>
        <table width="100%" class="table table-bordered nowrap mt-1 mb-5 " collapsing="0">
            <tbody>
                <tr>
                    <th class="font-small-2">Ket</th>
                    <th class="font-small-2">Nominal</th>
                    <th class="font-small-2">Jumlah</th>
                    <th class="font-small-2">Total</th>
                </tr>
                @foreach ($salary->feeSalaries as $item)
                    <tr>
                        <td class="font-small-2  ">{{ $item->name }}</td>
                        <td class="font-small-2  text-right">
                            {{ $item->amount > 0 ? floatDotFormat($item->amount) : '' }}</td>
                        <td class="font-small-2  text-center">{{ $item->qty > 0 ? $item->qty : '' }}</td>
                        <td class="font-small-2  text-right">
                            {{ $item->total > 0 ? floatDotFormat($item->total) : '' }}</td>
                    </tr>
                @endforeach
                @foreach ($salary->allowanceSalaries as $item)
                    <tr>
                        <td class="font-small-2  ">{{ $item->name }}</td>
                        <td class="font-small-2  text-right">
                            {{ $item->amount > 0 ? floatDotFormat($item->amount) : '' }}</td>
                        <td class="font-small-2  text-center">{{ $item->qty > 0 ? $item->qty : '' }}</td>
                        <td class="font-small-2  text-right">
                            {{ $item->total > 0 ? floatDotFormat($item->total) : '' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="font-small-2 text-left" colspan="3">Total Penerimaan</th>
                    <th class="font-small-2 text-right">
                        {{ $salary->brutto_salary > 0 ? floatDotFormat($salary->brutto_salary) : '' }}</th>
                </tr>
                @foreach ($salary->deductionSalaries as $item)
                    <tr>
                        <td class="font-small-2  ">{{ $item->name }}</td>
                        <td class="font-small-2  text-right">
                            {{ $item->amount > 0 ? floatDotFormat($item->amount) : '' }}</td>
                        <td class="font-small-2  text-center">{{ $item->qty }}</td>
                        <td class="font-small-2  text-right">
                            {{ $item->total > 0 ? floatDotFormat($item->total) : '' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="font-small-2 text-left" colspan="3">Total Pengurangan</th>
                    <th class="font-small-2 text-right">
                        {{ $salary->deduction_total > 0 ? floatDotFormat($salary->deduction_total) : '' }}</th>
                </tr>
                <tr>
                    <th class="font-small-2 text-left" colspan="3">Gaji Bersih</th>
                    <th class="font-small-2 text-right">
                        {{ $salary->netto_salary > 0 ? floatDotFormat($salary->netto_salary) : '' }}</th>
                </tr>
            </tbody>
        </table>

        <div id="footer">
            <div class="row">
                <table style="width: 100%;margin-top:20px;margin-left: 10px;">
                    <tr>
                        <td style="width: 25%">
                            <div>
                                <img src="data:image/png;base64, {{ $qr }}" width="70px">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
