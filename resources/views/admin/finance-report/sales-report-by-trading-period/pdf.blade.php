<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
    <style>
        tr th,
        tr td {
            padding: 3px 2px !important;
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
                <h5 class="text-uppercase my-0">{{ Str::headline("laporan perbandingan penjualan trading dengan hpp") }}</h5>
                <h5 class="text-uppercase my-0">periode : {{ $from_date }} - {{ $to_date }}</h5>
                {{-- <p class="font-small-2 text-uppercase my-0">periode : {{ $period }}</p> --}}
                @if ($branch)
                    <p class="font-small-2 text-uppercase my-0">Branch : {{ $branch->name }}</p>
                @endif
            </div>
        </div>
        <br>
        @include('admin.finance-report.sales-report-by-trading-period.body', ['format_number' => true])
    </div>
</body>

</html>
