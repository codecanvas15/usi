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
            padding: 2px !important;
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
                @if ($coa)
                    <p class="font-small-2 text-uppercase my-0">KAS/BANK : {{ $coa->account_code }} - {{ $coa->name }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped mt-10">
            <thead>
                <tr>
                    <th class="font-xsmall-3 text-center" width="3%">NO.</th>
                    <th class="font-xsmall-3 text-center" width="6%">TANGGAL</th>
                    <th class="font-xsmall-3 text-center" width="10%">NO VOUCHER</th>
                    <th class="font-xsmall-3 text-center" width="15%">VENDOR</th>
                    <th class="font-xsmall-3 text-center" width="10%">KAS/BANK</th>
                    <th class="font-xsmall-3 text-center">KETERANGAN</th>
                    <th class="font-xsmall-3 text-center" width="3%">CURRENCY</th>
                    <th class="font-xsmall-3 text-center" width="7%">TOTAL</th>
                    <th class="font-xsmall-3 text-center" width="7%">RATE</th>
                    <th class="font-xsmall-3 text-center" width="7%">TOTAL ({{ get_local_currency()->kode }})</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @forelse ($data as $key => $d)
                    @if ($d->code != ($data[$key - 1]->code ?? '') || $key == 0)
                        <tr class="bg-grey">
                            <td class="font-xsmall-3 text-center">{{ $no }}.</td>
                            <td class="font-xsmall-3 text-center">{{ localDate($d->date) }}</td>
                            <td class="font-xsmall-3 text-center">
                                @php
                                    $modelClass = class_basename($d->bank_code_mutation_ref_model); // e.g. InvoiceGeneral
                                    $routeMap = [
                                        'OutgoingPayment' => 'admin.outgoing-payment.show',
                                        'AccountPayable' => 'admin.account-payable.show',
                                    ];
                                @endphp
                                @if (isset($routeMap[$modelClass]))
                                    <a href="{{ route($routeMap[$modelClass], $d->bank_code_mutation_ref_id) }}" target="_blank">
                                        {{ $d->bank_code_mutation ?? $d->code }}
                                    </a>
                                @else
                                    {{ $d->bank_code_mutation ?? $d->code }}
                                @endif
                            </td>
                            <td class="font-xsmall-3 text-center">{{ $d->nama }}</td>
                            <td class="font-xsmall-3 ">{{ $d->coa_account_code }} - {{ $d->coa_name }}</td>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3"></td>
                            <td class="font-xsmall-3"></td>
                        </tr>
                        @php
                            $no++;
                        @endphp
                    @endif
                    <tr>
                        <td class="font-xsmall-3" colspan="5">
                            @php
                                $modelClass = class_basename($d->model_reference);
                                $modelMap = [
                                    'SupplierInvoice' => 'admin.supplier-invoice.show',
                                    'SupplierInvoiceGeneral' => 'admin.supplier-invoice-general.show',
                                ];

                                $routeName = $modelMap[$modelClass] ?? null;
                            @endphp

                            @if ($routeName && Route::has($routeName))
                                <a href="{{ route($routeName, $d->reference_id) }}" target="_blank">
                                    {{ $d->invoice_code }}
                                </a>
                            @else
                                {{ $d->invoice_code }}
                            @endif
                        </td>
                        <td class="font-xsmall-3">{{ $d->note }}</td>
                        <td class="font-xsmall-3 text-center">{{ $d->currency_simbol }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->amount) }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->exchange_rate) }}</td>
                        <td class="font-xsmall-3 text-right">{{ formatNumber($d->amount_local) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td align="center" colspan="10">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="font-xsmall-3 " colspan="2">TOTAL</th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right"></th>
                    <th class="font-xsmall-3 text-right">{{ formatNumber($data->sum('amount_local')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
