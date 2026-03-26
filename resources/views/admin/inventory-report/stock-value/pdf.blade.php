<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($title) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
    <style>
        tr th,
        tr td {
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
                <h3 class="text-uppercase">laporan {{ Str::headline($title) }}</h3>
            </div>
        </div>
        <br>

        <div class="mt-10">
            @foreach ($data as $item)
                <x-table theadColor="white" class="table-bordered mt-20">
                    <x-slot name="table_head">
                        @include("admin.inventory-report.$type.table.header")
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <td class="font-small-2"><b>Saldo Awal {{ $item['item']->nama }}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>

                            <td class="font-small-2" align="right">{{ formatNumber($item['beginning_balance']) }}</td>
                            <td class="font-small-2" align="right">{{ formatNumber($item['last_mutation']?->total) }}</td>
                        </tr>
                        @include("admin.inventory-report.$type.table.body", [
                            'formatNumber' => true,
                        ])
                    </x-slot>
                    {{-- <x-slot name="table_foot">
                        @include("admin.inventory-report.$type.table.footer", [
                            'formatNumber' => true,
                        ])
                    </x-slot> --}}
                </x-table>
            @endforeach
        </div>
    </div>
</body>

</html>
