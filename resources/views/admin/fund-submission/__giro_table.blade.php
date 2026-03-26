<x-table>
    <x-slot name="table_head" class="bg-dark">
        <th colspan="2" class="text-center">INFORMASI GIRO</th>
    </x-slot>
    <x-slot name="table_body">
        @if ($send_payment ?? null)
            <tr>
                <td>Jatuh Tempo Giro</td>
                <td>{{ isset($send_payment) ? localDate($send_payment->due_date) : date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>No Cheque</td>
                <td>{{ isset($send_payment) ? $send_payment->cheque_no : '' }}</td>
            </tr>
            <tr>
                <td>BG Bank Mundur</td>
                <td>{{ isset($send_payment) ? $send_payment->from_bank : '' }}</td>
            </tr>
            <tr>
                <td>Bank Pencairan</td>
                <td>{{ isset($send_payment) ? $send_payment->realization_bank : '' }}</td>
            </tr>
        @else
            <tr>
                <td colspan="2">

                </td>
            </tr>
        @endif
    </x-slot>
</x-table>
