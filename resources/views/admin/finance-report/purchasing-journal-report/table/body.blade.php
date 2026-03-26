@foreach ($data as $item)
    <tr>
        <td>{{ $item->journal_date }}</td>
        <td>{{ $item->journal_type }}</td>
        <td>{{ $item->code }}</td>
        <td>
            @if ($item->document_reference)
                <a href="{{ toLocalLink($item->document_reference->link) }}" target="_blank">{{ $item->document_reference->code ?? '' }}</a>
            @endif
        </td>
        <td>{{ $item->remark }}</td>
        <td>{{ $item->coa_code }}</td>
        <td>{{ $item->coa_name }}</td>
        <td>{{ $formatNumber ? formatNumber($item->debit) : $item->debit }}</td>
        <td>{{ $formatNumber ? formatNumber($item->credit) : $item->credit }}</td>
        <td>{{ $formatNumber ? formatNumber($item->journal_exchange_rate) : $item->journal_exchange_rate }}</td>
        <td>{{ $formatNumber ? formatNumber($item->debit_exchanged) : $item->debit_exchanged }}</td>
        <td>{{ $formatNumber ? formatNumber($item->credit_exchanged) : $item->credit_exchanged }}</td>
    </tr>
@endforeach
