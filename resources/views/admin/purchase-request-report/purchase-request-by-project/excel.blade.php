@php
    $main = 'purchase-request-report';
@endphp
<table>
    <tr>
        <td colspan="10">
            <p><b>{{ Str::upper(getCompany()->name) }}</b></p>
            <p><b>{{ getCompany()->address }}</b></p>
            <p><b>Telp. {{ getCompany()->phone }}</b></p>
        </td>
        <td colspan="4">
            {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" width="136px"></center> --}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>LAPORAN {{ Str::upper(Str::headline($title)) }}</b></p>
        </td>
    </tr>
    <tr>
        <td colspan="12" align="center">
            <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
        </td>
    </tr>
</table>
@foreach ($data as $item)
    <p>Nama Project : {{ $item['project_name'] }} / {{ $item['project_code'] }}</p>

    <table theadColor="white" class="table-bordered mt-20">
        <thead>
            @include("admin.$main.$type.table.head")
        </thead>
        <tbody>
            @foreach ($item['data'] as $itemReport)
                @include("admin.$main.$type.table.body", ['formatNumber' => false])
            @endforeach
        </tbody>
        <tfoot>
            @include("admin.$main.$type.table.footer", [
                'formatNumber' => false,
            ])
        </tfoot>
    </table>
@endforeach
