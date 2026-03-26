@foreach ($data as $item)
    <tr>
        <td>{{ $item->letter_number }}</td>
        <td>{{ $item->letter_type_alias }}</td>
        <td>{{ $item->employee_name }}</td>
        <td>{{ $item->employee_nik }}</td>
        <td>{{ $item->employee_division }}</td>
        <td>{{ $item->letter_date_start ? localDate($item->letter_date_start) : '-' }}</td>
        <td>{{ $item->letter_date_end ? localDate($item->letter_date_end) : '-' }}</td>
        <td>{{ $item->letter_note }}</td>
        <td>{{ $item->letter_reason }}</td>
        <td>{{ $item->letter_status }}</td>
    </tr>
@endforeach
