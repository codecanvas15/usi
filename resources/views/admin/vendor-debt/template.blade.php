<table>
    <thead>
        <tr>
            <th><b>{{ Str::headline('nama') }}</b></th>
            <th><b>{{ Str::headline('alamat') }}</b></th>
            <th><b>{{ Str::headline('npwp') }}</b></th>
            <th><b>{{ Str::headline('email') }}</b></th>
            <th><b>{{ Str::headline('mobile_phone') }}</b></th>
            <th><b>{{ Str::headline('business_phone') }}</b></th>
            <th><b>{{ Str::headline('whatsapp') }}</b></th>
            <th><b>{{ Str::headline('fax') }}</b></th>
            <th><b>{{ Str::headline('term_of_payment') }}</b>
                <br>
                cash/by days
            </th>
            <th><b>{{ Str::headline('top_days') }}</b></th>
            <th><b>{{ Str::headline('Account Payable COA') }}</b>
                <br>
                Diiisi nomor COA
            </th>
            <th><b>{{ Str::headline('Purchase Discounts COA') }}</b>
                <br>
                Diiisi nomor COA
            </th>
            <th><b>{{ Str::headline('Vendor Deposite COA') }}</b>
                <br>
                Diiisi nomor COA
            </th>
            <th><b>{{ Str::headline('nomor invoice') }}</b></th>
            <th><b>{{ Str::headline('currency') }}</b></th>
            <th><b>{{ Str::headline('exchange rate') }}</b></th>
            <th><b>{{ Str::headline('tanggal dokumen') }}</b></th>
            <th><b>{{ Str::headline('tanggal invoice') }}</b></th>
            <th><b>{{ Str::headline('tanggal jatuh tempo') }}</b></th>
            <th><b>{{ Str::headline('faktur pajak') }}</b></th>
            <th><b>{{ Str::headline('sisa invoice') }}</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vendors as $vendor)
            <tr>
                <td>{{ $vendor->nama }}</td>
                <td>{{ $vendor->alamat }}</td>
                <td>{{ $vendor->npwp }}</td>
                <td>{{ $vendor->email }}</td>
                <td>{{ $vendor->mobile_phone }}</td>
                <td>{{ $vendor->business_phone }}</td>
                <td>{{ $vendor->whatsapp }}</td>
                <td>{{ $vendor->fax }}</td>
                <td>{{ $vendor->term_of_payment }}</td>
                <td>{{ $vendor->top_days }}</td>
                <td>{{ $vendor->vendor_coas()->where('type', 'Account Payable COA')->first()->coa->account_code ?? '' }}</td>
                <td>{{ $vendor->vendor_coas()->where('type', 'Purchase Discounts COA')->first()->coa->account_code ?? '' }}</td>
                <td>{{ $vendor->vendor_coas()->where('type', 'Vendor Deposite COA')->first()->coa->account_code ?? '' }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
