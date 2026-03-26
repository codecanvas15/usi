@foreach ($data as $item)
    <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->NIK }}</td>
        <td>{{ $item->employee_division }}</td>
        <td>{{ $item->employee_position }}</td>
        <td>{{ $item->join_date }}</td>
        <td>{{ $item->end_date }}</td>
        <td>{{ $item->work_period }} Bulan</td>
        <td>{{ $item->employee_status }}</td>
    </tr>
@endforeach
