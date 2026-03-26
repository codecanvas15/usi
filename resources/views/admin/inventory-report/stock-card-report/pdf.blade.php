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
                <h3 class="text-uppercase">laporan {{ Str::headline($type) }}</h3>
                <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
            </div>
        </div>
        <br>
        @foreach ($data as $warehouse)
            @foreach ($warehouse['data'] ?? [] as $item)
                <div class="mb-3 border-bottom border-primary">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Item : {{ $item['item_name'] }}</label>
                            </div>
                            <div class="form-group">
                                <label for="">Gudang : {{ $warehouse['ware_house_name'] }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="">
                        <x-table theadColor="white" class="table-bordered mt-2">
                            <x-slot name="table_head">
                                @include("admin.inventory-report.$type.table.header")
                            </x-slot>
                            <x-slot name="table_body">
                                @include("admin.inventory-report.$type.table.body", [
                                    'formatNumber' => true,
                                ])
                            </x-slot>

                        </x-table>
                    </div>

                </div>
            @endforeach
        @endforeach
    </div>
</body>

</html>
