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
                <h5 class="text-uppercase my-0">laporan {{ Str::headline($type) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
                @if ($customer)
                    <p class="font-small-2 text-uppercase my-0">CUSTOMER : {{ $customer->nama }}</p>
                @endif
            </div>
        </div>
        <br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="font-small-1 text-center">NO.</th>
                    <th class="font-small-1 text-center">CUSTOMER</th>
                    <th class="font-small-1 text-center">BKK</th>
                    <th class="font-small-1 text-center">NO REF</th>
                    <th class="font-small-1 text-center">NO SO</th>
                    <th class="font-small-1 text-center">TANGGAL</th>
                    <th class="font-small-1 text-center">MATA UANG</th>
                    <th class="font-small-1 text-center">TOTAL</th>
                    <th class="font-small-1 text-center">DIGUNAKAN</th>
                    <th class="font-small-1 text-center">SALDO</th>
                    <th class="font-small-1 text-center">KURS</th>
                    <th class="font-small-1 text-center">TOTAL {{ get_local_currency()->kode }}</th>
                    <th class="font-small-1 text-center">DIGUNAKAN {{ get_local_currency()->kode }}</th>
                    <th class="font-small-1 text-center">SALDO {{ get_local_currency()->kode }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $d)
                    <tr>
                        <td class="font-small-1 text-center">{{ $loop->iteration }}</td>
                        <td class="font-small-1 text-center">{{ $d->customer_nama }}</td>
                        <td class="font-small-1 text-center">{{ $d->bank_code }}</td>
                        <td class="font-small-1 text-center">
                            @php
                                $link = '#';

                                if (isset($d->cash_advance_receive_id)) {
                                    $link = route('admin.cash-advance-receive.show', ['cash_advance_receive' => $d->cash_advance_receive_id]);
                                } elseif ($d->invoice_down_payment_id) {
                                    $link = route('admin.invoice-down-payment.show', ['invoice_down_payment' => $d->invoice_down_payment_id]);
                                }
                            @endphp
                            <a href="{{ $link }}" target="_blank">{{ $d->reference }}</a>
                        </td>
                        <td class="font-small-1 text-center">
                            @php
                                $link = '#';

                                if (isset($d->sale_order_model_id)) {
                                    if ($d->so_code) {
                                        $link = route('admin.sales-order-general.show', ['sales_order_general' => $d->sale_order_model_id]);
                                    } elseif ($d->so_trading_code) {
                                        $link = route('admin.sales-order.show', ['sales_order' => $d->sale_order_model_id]);
                                    }
                                }
                            @endphp
                            <a href="{{ $link }}" target="_blank">{{ $d->so_code ?? ($d->so_trading_code ?? '') }}</a>
                        </td>
                        <td class="font-small-1 text-center">{{ localDate($d->cash_advance_date) }}</td>
                        <td class="font-small-1 text-center">{{ $d->currency_nama }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->cash_advance_amount) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->returned_amount) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->cash_advance_remaining_amount) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->exchange_rate) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->cash_advance_amount_exchanged) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->returned_amount_exchanged) }}</td>
                        <td class="font-small-1 text-right">{{ formatNumber($d->cash_advance_remaining_amount_exchanged) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td align="center" colspan="14" class="font-small-1">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="font-small-1 text-center"></th>
                    <th class="font-small-1" colspan="10">TOTAL</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('cash_advance_amount_exchanged')) }}</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('returned_amount_exchanged')) }}</th>
                    <th class="font-small-1 text-right">{{ formatNumber($data->sum('cash_advance_remaining_amount_exchanged')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
