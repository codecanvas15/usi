<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
    <style>
        tr td,
        tr th {
            padding: 5px !important;
        }
    </style>
</head>

<body class="">
    <div class="row">
        <table>
            <tr>
                <td>
                    <h4 class="text-danger text-uppercase my-0">
                        {{ getCompany()->name }}</h4>
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
                <h5 class="text-uppercase my-0">{{ Str::headline($type) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($to_date) }}</p>
            </div>
        </div>

        <br>
        <x-table class="table-bordered">
            <x-slot name="table_head" class="text-white">
                @include('admin.finance-report.biaya-dibayar-dimuka.partial.head', ['ext' => 'pdf', 'formatNumber' => true])
            </x-slot>
            <x-slot name="table_body">
                @include('admin.finance-report.biaya-dibayar-dimuka.partial.body', ['formatNumber' => true])
            </x-slot>
            <x-slot name="table_foot">
                @include('admin.finance-report.biaya-dibayar-dimuka.partial.footer', ['formatNumber' => true])
            </x-slot>
        </x-table>
    </div>
</body>

</html>
