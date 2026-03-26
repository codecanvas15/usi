<table>
    <thead>
        <tr>
            <th>ID (Jangan dirubah)</th>
            <th>Nomor Akun (Jangan dirubah)</th>
            <th>Nama Akun</th>
            <th>Credit (1.000.000,00)</th>
            <th>Debit (1.000.000,00)</th>
            <th>Normal Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($model as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->account_code }}</td>
                <td>{{ $item->name }}</td>
                <td></td>
                <td></td>
                <th>{{ $item->normal_balance }}</th>
            </tr>
        @endforeach
    </tbody>
</table>
