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
                @if ($coa)
                    <p class="font-small-2 text-uppercase my-0">KAS/BANK : {{ $coa->account_code }} - {{ $coa->name }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped mt-10">
            <thead>
                <tr>
                    <th class="font-small-1 text-center">NO.</th>
                    <th class="font-small-1 text-center">TANGGAL</th>
                    <th class="font-small-1 text-center">NO VOUCHER</th>
                    <th class="font-small-1 text-center">CUSTOMER</th>
                    <th class="font-small-1 text-center">KAS/BANK</th>
                    <th class="font-small-1 text-center">KETERANGAN</th>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <th class="font-small-1 text-center">RATE</th>
                    <th class="font-small-1 text-center">TOTAL ({{ get_local_currency()->kode }})</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $d)
                    @if ($d->code != ($data[$key - 1]->code ?? '') || $key == 0)
                        <tr class="bg-grey">
                            <td class="font-small-1 text-center">{{ $key + 1 }}.</td>
                            <td class="font-small-1 text-center">{{ localDate($d->date) }}</td>
                            <td class="font-small-1 text-left">
                                @if ($d->receivables_payment_id)
                                    <a href="{{ route('admin.receivables-payment.show', $d->receivables_payment_id) }}" target="_blank">
                                        {{ $d->bank_code_mutation ?? $d->code }}
                                    </a>
                                @else
                                    {{ $invoice->bank_code }}
                                @endif
                            </td>
                            <td class="font-small-1 text-left">{{ $d->nama }}</td>
                            <td class="font-small-1 ">{{ $d->coa_account_code }} - {{ $d->coa_name }}</td>
                            <td class="font-small-1 text-left"></td>
                            <td class="font-small-1 text-right"></td>
                            <td class="font-small-1 text-right"></td>
                            <td class="font-small-1 text-right"></td>
                        </tr>
                    @endif
                    <tr>
                        <td class="font-small-1 text-left" colspan="5">
                            @php
                                $modelClass = class_basename($d->model_reference);
                                $modelMap = [
                                    'InvoiceGeneral' => 'admin.invoice-general.show',
                                    'InvoiceDownPayment' => 'admin.invoice-down-payment.show',
                                    'InvoiceTrading' => 'admin.invoice-trading.show',
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
                        <td class="font-small-1 text-left">{{ $d->note }}</td>
                        <td class="font-small-1 text-right">{{ $d->currency_simbol }} {{ formatNumber($d->receive_amount) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->exchange_rate) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->receive_amount_local) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td align="center" colspan="9">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="font-small-1 " colspan="2">TOTAL</th>
                    <th class="font-small-1 text-right"></th>
                    <th class="font-small-1 text-right"></th>
                    <th class="font-small-1 text-right"></th>
                    <th class="font-small-1 text-right"></th>
                    <th class="font-small-1 text-right"></th>
                    <th class="font-small-1 text-right"></th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('receive_amount_local')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
