<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
    <style>
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
                <h5 class="text-uppercase my-0">{{ Str::headline($type) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
            </div>
        </div>
        <br>
        <table theadColor="white" class="table table-bordered mt-20">
            <tbody>
                @include('admin.sale-order-trading-report.compare-so-po.table.header')
                @include('admin.sale-order-trading-report.compare-so-po.table.body', [
                    'formatNumber' => true,
                ])
                @include('admin.sale-order-trading-report.compare-so-po.table.footer', [
                    'formatNumber' => true,
                ])
            </tbody>
        </table>
        <br>
    </div>
</body>

</html>
