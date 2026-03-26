zA
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($title) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
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
                <h5 class="text-uppercase my-0">{{ Str::headline($title) }}</h5>
                <p class="font-small-2 text-uppercase my-0">Periode : {{ $month }}</p>
            </div>
        </div>
        <br>
        <x-table theadColor="white" class="table-bordered mt-20">
            <x-slot name="table_head">
                @include("admin.purchase-order-transport-report.$type.table.head")
            </x-slot>
            <x-slot name="table_body">
                @include("admin.purchase-order-transport-report.$type.table.body", [
                    'formatNumber' => true,
                ])
            </x-slot>
            <x-slot name="table_foot">
                @include("admin.purchase-order-transport-report.$type.table.footer", [
                    'formatNumber' => true,
                ])
            </x-slot>
        </x-table>
    </div>
</body>

</html>
