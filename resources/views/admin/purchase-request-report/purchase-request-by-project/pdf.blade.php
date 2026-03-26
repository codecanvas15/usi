<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>laporan {{ Str::headline($title) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    @php
        $main = 'purchase-request-report';
    @endphp
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
                <h5 class="text-uppercase my-0">laporan {{ Str::headline($title) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
            </div>
        </div>
        <br>
        @foreach ($data as $item)
            <div class="border-top border-primary py-30">
                <p>Nama Project : {{ $item['project_name'] }} / {{ $item['project_code'] }}</p>

                <x-table theadColor="white" class="table-bordered mt-20">
                    <x-slot name="table_head">
                        @include("admin.$main.$type.table.head")
                    </x-slot>
                    <x-slot name="table_body">
                        @foreach ($item['data'] as $itemReport)
                            @include("admin.$main.$type.table.body", ['formatNumber' => true])
                        @endforeach
                    </x-slot>
                    <x-slot name="table_footer">
                        @include("admin.$main.$type.table.footer")
                    </x-slot>
                </x-table>
            </div>
        @endforeach
    </div>
</body>

</html>
