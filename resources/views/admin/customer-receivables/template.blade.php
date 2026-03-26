<table>
    <thead>
        <tr>
            <th><b>{{ Str::headline('nama') }}</b></th>
            <th><b>{{ Str::headline('alamat') }}</b></th>
            <th><b>{{ Str::headline('npwp') }}</b></th>
            <th><b>{{ Str::headline('email') }}</b></th>
            <th><b>{{ Str::headline('mobile_phone') }}</b></th>
            <th><b>{{ Str::headline('bussiness_phone') }}</b></th>
            <th><b>{{ Str::headline('whatsapp_number') }}</b></th>
            <th><b>{{ Str::headline('fax') }}</b></th>
            <th><b>{{ Str::headline('term_of_payment') }}</b>
                <br>
                cash/by days
            </th>
            <th><b>{{ Str::headline('top_days') }}</b></th>
            <th><b>{{ Str::headline('Account Receivable COA') }}</b>
                <br>
                Diiisi nomor COA
            </th>
            <th><b>{{ Str::headline('Sale Discounts COA') }}</b>
                <br>
                Diiisi nomor COA
            </th>
            <th><b>{{ Str::headline('Customer Deposite COA') }}</b>
                <br>
                Diiisi nomor COA
            </th>
            <th><b>{{ Str::headline('Lost Tolerance Type') }}</b>
                <br>
                percent/liter
            </th>
            <th><b>{{ Str::headline('lost tolerance') }}</b></th>
            <th><b>{{ Str::headline('website') }}</b></th>
            <th><b>{{ Str::headline('nomor invoice') }}</b></th>
            <th><b>{{ Str::headline('currency') }}</b></th>
            <th><b>{{ Str::headline('exchange rate') }}</b></th>
            <th><b>{{ Str::headline('tanggal invoice') }}</b></th>
            <th><b>{{ Str::headline('tanggal jatuh tempo') }}</b></th>
            <th><b>{{ Str::headline('faktur pajak') }}</b></th>
            <th><b>{{ Str::headline('sisa invoice') }}</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $customer)
            <tr>
                <td>{{ $customer->nama }}</td>
                <td>{{ $customer->alamat }}</td>
                <td>{{ $customer->npwp }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->mobile_phone }}</td>
                <td>{{ $customer->bussiness_phone }}</td>
                <td>{{ $customer->whatsapp_number }}</td>
                <td>{{ $customer->fax }}</td>
                <td>{{ $customer->term_of_payment }}</td>
                <td>{{ $customer->top_days }}</td>
                <td>{{ $customer->customer_coas()->where('tipe', 'Account Receivable COA')->first()->coa->account_code ?? '' }}</td>
                <td>{{ $customer->customer_coas()->where('tipe', 'Sale Discounts COA')->first()->coa->account_code ?? '' }}</td>
                <td>{{ $customer->customer_coas()->where('tipe', 'Customer Deposite COA')->first()->coa->account_code ?? '' }}</td>
                <td>{{ $customer->lost_tolerance_type }}</td>
                <td>{{ $customer->lost_tolerance_type == 'percent' ? $customer->lost_tolerance * 100 : $customer->lost_tolerance }}</td>
                <td>{{ $customer->website }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
