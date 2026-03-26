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
                    <h1 class="text-danger text-uppercase my-0">{{ getCompany()->name }}</h1>
                    <p class="font-medium-1 my-0">{{ getCompany()->address }}</p>
                    <p class="font-medium-1 my-0">Telp. {{ getCompany()->phone }}</p>
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
                <h2 class="text-uppercase mb-0">laporan {{ Str::headline($type) }}</h2>
                <h3 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h3>
            </div>
        </div>
        <br>
        <table class="table table-bordered mt-20">
            <tbody>
                @foreach ($data as $item)
                    @include("admin.inventory-report.$type.table.header")
                    @include("admin.inventory-report.$type.table.body", [
                        'formatNumber' => true,
                        'data' => $item->data,
                    ])
                    @include("admin.inventory-report.$type.table.footer", [
                        'formatNumber' => true,
                        'data' => $item->data,
                    ])
                    <tr>
                        <td colspan="8"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
