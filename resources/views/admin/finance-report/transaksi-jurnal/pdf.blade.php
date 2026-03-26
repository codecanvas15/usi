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
            padding: 3px 4px !important
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
                <h5 class="text-uppercase my-0">laporan {{ Str::headline($type) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
            </div>
        </div>
        <br>
        <table class="table table-bordered mt-10">
            <thead>
                <tr>
                    <th class="font-small-1 text-center">TANGGAL</th>
                    <th class="font-small-1 text-center">TRANSAKSI</th>
                    <th class="font-small-1 text-center">NO DOKUMEN</th>
                    <th class="font-small-1 text-center">NO REFERENSI</th>
                    <th class="font-small-1 text-center">ITEM</th>
                    <th class="font-small-1 text-center" colspan="2">ACCOUNT</th>
                    <th class="font-small-1 text-center">DEBIT</th>
                    <th class="font-small-1 text-center">KREDIT</th>
                    <th class="font-small-1 text-center">KURS</th>
                    <th class="font-small-1 text-center">DEBIT ({{ get_local_currency()->kode }})</th>
                    <th class="font-small-1 text-center">KREDIT ({{ get_local_currency()->kode }})</th>
                    <th class="font-small-1 text-center">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @forelse ($data as $key => $d)
                    @if ($d->journal_id != ($data[$key - 1]->journal_id ?? '') || $key == 0)
                        @php
                            $i++;
                        @endphp
                    @endif
                    <tr @if ($i % 2 != 0) class="bg-grey" @endif>
                        @if ($d->journal_id != ($data[$key - 1]->journal_id ?? '') || $key == 0)
                            <td class="font-small-1 text-center"><b>{{ localDate($d->journal_date) }}</b></td>
                            <td class="font-small-1 text-left"><b>{{ $d->journal_type }}</b></td>
                            <td class="font-small-1 text-center">
                                @if ($d->document_reference)
                                    @if ($d->document_reference->link ?? null)
                                        <a href="{{ toLocalLink($d->document_reference->link) }}" target="_blank">{{ $d->document_reference->code }}</a>
                                    @endif
                                @endif
                            </td>
                            <td class="font-small-1 text-center">
                                @if ($d->reference)
                                    @if ($d->reference->link ?? null)
                                        <a href="{{ toLocalLink($d->reference->link) }}" target="_blank">{{ $d->reference->code }}</a>
                                    @endif
                                @endif
                            </td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                        <td class="font-small-1 text-left">{{ $d->remark }}</td>
                        <td class="font-small-1 text-left">{{ $d->coa_code }}</td>
                        <td class="font-small-1 text-left">{{ $d->coa_name }}</td>
                        <td class="font-small-1 text-right">{{ $d->currency_symbol }} {{ formatNumber($d->debit) }}</td>
                        <td class="font-small-1 text-right">{{ $d->currency_symbol }} {{ formatNumber($d->credit) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->journal_exchange_rate) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->debit_exchanged) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->credit_exchanged) }}</td>
                        <td class="font-small-1 text-left">{{ $d->journal_remark }}</td>
                    </tr>
                @empty
                    <tr>
                        <td align="center" colspan="13" class="font-small-1">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="font-small-1 text-center" colspan="10">TOTAL</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('debit_exchanged')) }}</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('credit_exchanged')) }}</th>
                    <th class="font-small-1 text-right"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
