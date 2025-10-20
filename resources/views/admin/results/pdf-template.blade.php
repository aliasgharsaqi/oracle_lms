<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Card</title>
    <style>
        /* * IMPORTANT: mPDF works best with non-flex/grid CSS. 
         * Use tables for layout. All CSS must be in <style> tags.
         * Tailwind classes will NOT work here unless you embed the full Tailwind CSS, which is not recommended.
        */
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10pt;
        }
        .text-center { text-align: center; }
        .font-semibold { font-weight: 600; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-5 { margin-bottom: 1.25rem; }

        /* Copied from your @push('styles') for table consistency */
        #resultCardTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        #resultCardTable th,
        #resultCardTable td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #resultCardTable th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .pass-status {
            color: green;
            font-weight: bold;
        }

        .fail-status {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="text-center mb-5">
        <h3 class="mb-1">{{ $student_name }}</h3>
        <span style="margin-right: 15px;">
            Class: <span class="font-semibold">{{ $class_name }}</span>
        </span>
        <span>
            Semester: <span class="font-semibold">{{ $semester_name }}</span>
        </span>
    </div>

    <table id="resultCardTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $res)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $res['subject_name'] }}</td>
                    <td>{{ $res['total_marks'] }}</td>
                    <td>{{ $res['obtained_marks'] }}</td>
                    <td>
                        <span class="{{ $res['status'] === 'Pass' ? 'pass-status' : 'fail-status' }}">
                            {{ $res['status'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="2" style="text-align: right;">Total</td>
                <td>{{ $total_possible }}</td>
                <td>{{ $total_obtained }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; text-align: center;">
        <span class="font-semibold">Percentage: {{ $percentage }}%</span> |
        <span class="font-semibold">Overall Status:
            <span class="{{ $status === 'Pass' ? 'pass-status' : 'fail-status' }}">{{ $status }}</span>
        </span>
    </div>

</body>
</html>