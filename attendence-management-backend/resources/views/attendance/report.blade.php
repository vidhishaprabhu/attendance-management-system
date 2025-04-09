<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid black;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Monthly Attendance Report</h2>
    <table>
        <thead>
            <tr>
                <th>User-ID</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                <tr>
                    <td>{{$attendance->user_id}}</td>
                    <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->status ? 'Present' : 'Absent' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
