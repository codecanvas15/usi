@foreach ($data as $item)
    <tr>
        <td>{{ $item->code }}</td>
        <td>{{ $item->employee_name }}</td>
        <td>{{ $item->employee_nik }}</td>
        <td>{{ $item->employee_division }}</td>
        <td>{{ localDate($item->from_date) }}</td>
        <td>{{ localDate($item->to_date) }}</td>
        <td>{{ $item->employee_leave }}</td>
        <td>{{ $formatNumber ? formatNumber($item->day) : $item->day }}</td>
        <td>{{ $item->leave_remaining }}</td>
        <td>{{ $item->note }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@endforeach
