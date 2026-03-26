<table>
    <tbody>
        <tr>
            <th><b>NIK</b></th>
            <th><b>name</b></th>
            <th><b>date</b></th>
            <th><b>in_time</b></th>
            <th><b>out_time</b></th>
            <th><b>go_home_early</b></th>
            <th><b>late</b></th>
            <th><b>overtime</b></th>
            <th><b>work_hours</b></th>
            <th><b>attendance_hours</b></th>
            <th><b>description</b></th>
        </tr>
        @foreach ($employees as $employee)
            @foreach ($dates as $key => $date)
                @php
                    $attendance = $attendances
                        ->where('employee_id', $employee->id)
                        ->where('date', $date_strings[$key])
                        ->first();
                @endphp
                <tr>
                    <td>{{ $employee->NIK }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $date }}</td>
                    <td>{{ $attendance->in_time ?? '' }}</td>
                    <td>{{ $attendance->out_time ?? '' }}</td>
                    <td>{{ $attendance->go_home_early ?? '' }}</td>
                    <td>{{ $attendance->late ?? '' }}</td>
                    <td>{{ $attendance->overtime ?? '' }}</td>
                    <td>{{ $attendance->work_hours ?? '' }}</td>
                    <td>{{ $attendance->attendance_hours ?? '' }}</td>
                    <td>{{ $attendance->description ?? '' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
