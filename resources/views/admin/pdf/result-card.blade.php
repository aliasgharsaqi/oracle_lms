<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Result Card - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: 'dejavu sans', sans-serif;
            font-size: 13px;
            color: #333;
        }
        .container {
            padding: 5px;
            border: 2px solid #002D62;
            border-radius: 10px;
            height: 98%;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #002D62;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #002D62;
            margin: 0;
        }
        .header .school-address {
            font-size: 12px;
            margin: 5px 0;
        }
        .header .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            text-decoration: underline;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 6px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th, .results-table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        .results-table th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .text-left { text-align: left; }
        .pass-status { color: #008000; font-weight: bold; }
        .fail-status { color: #FF0000; font-weight: bold; }

        .summary-section {
            width: 100%;
        }
        .summary-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
        }
        .summary-table td {
            border: 1px solid #999;
            padding: 8px;
        }
        .summary-table .label {
            font-weight: bold;
            background-color: #e0e0e0;
        }
        .signature-section {
            margin-top: 60px;
            width: 100%;
        }
        .signature-box {
            width: 30%;
            float: left;
            text-align: center;
            margin: 0 1.5%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
            font-weight: bold;
        }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p class="school-name">Global Knowledge High School</p>
            <p class="school-address">123 Education Lane, Lahore, Punjab, Pakistan</p>
            <p class="title">ACADEMIC RESULT CARD</p>
        </div>

        <div class="student-info">
            <table>
                <tr>
                    <td class="info-label">Student Name:</td>
                    <td>{{ $student->user->name }}</td>
                    <td class="info-label">Roll Number:</td>
                    <td>{{ $student->roll_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Class:</td>
                    <td>{{ $student->schoolClass->name }}</td>
                    <td class="info-label">Semester:</td>
                    <td>{{ $semester_name }}</td>
                </tr>
            </table>
        </div>

        <table class="results-table">
            <thead>
                <tr>
                    <th class="text-left">Subject</th>
                    <th>Total Marks</th>
                    <th>Obtained Marks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr>
                    <td class="text-left">{{ $result['subject_name'] }}</td>
                    <td>{{ $result['total_marks'] }}</td>
                    <td><strong>{{ $result['obtained_marks'] }}</strong></td>
                    <td>
                        <span class="{{ $result['status'] === 'Pass' ? 'pass-status' : 'fail-status' }}">
                            {{ $result['status'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="label">Total Marks</td>
                    <td><strong>{{ $total_possible }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Marks Obtained</td>
                    <td><strong>{{ $total_obtained }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Percentage</td>
                    <td><strong>{{ $percentage }}%</strong></td>
                </tr>
                <tr>
                    <td class="label">Overall Result</td>
                    <td>
                        <strong class="{{ $status === 'Pass' ? 'pass-status' : 'fail-status' }}">
                            {{ $status }}
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
        <div class="clearfix"></div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Class Teacher</div>
            </div>
            <div class="signature-box" style="float:right;">
                <div class="signature-line">Principal</div>
            </div>
        </div>

    </div>
</body>
</html>