<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Result Card - {{ $student->user->name }}</title>
    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #000;
            font-size: 14px;
        }

        /* The main container with the outer decorative border */
        .page-container {
            border: 3px double #000;
            padding: 8px;
            height: 98%;
        }

        /* The inner container with the thin line border */
        .content-wrapper {
            border: 1px solid #000;
            padding: 20px;
            height: 100%;
        }

        /* Header Section */
        .header-section {
            display: -webkit; /* For wkhtmltopdf */
            -webkit-box-pack: justify;
            -webkit-box-align: start;
            text-align: center;
            margin-bottom: 15px;
        }

        .logo {
            max-width: 90px;
            max-height: 90px;
            margin-bottom: 20px
        }
        
        .school-details {
            width: 100%;
            text-align: center;
        }

        .title-box {
            background-color: #fff;
            color: #000;
            border: 1px solid #000;
            padding: 5px 10px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 5px;
        }

        .school-name-box {
            background-color: #000;
            color: #fff;
            padding: 8px 15px;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* Student Info Section */
        .info-section {
            margin-top: 20px;
            font-size: 15px;
        }
        
        .info-line {
            margin-bottom: 12px;
            width: 100%;
        }

        .info-line:after {
            content: "";
            display: table;
            clear: both;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
        }

        .info-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 250px;
            padding-left: 10px;
        }
        
        .info-line .short-field {
            min-width: 150px;
        }
        
        .info-line .pull-right {
            float: right;
        }

        /* Marks Table Section */
        .marks-section {
            margin-top: 25px;
            text-align: center;
        }

        .marks-title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        .results-table th, .results-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        .results-table th {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        
        .results-table .text-left {
            text-align: left;
        }

        .results-table .total-row td {
            font-weight: bold;
        }

        /* Summary & Footer Section */
        .summary-section {
            margin-top: 20px;
            width: 100%;
            font-weight: bold;
        }

        .summary-section .summary-field {
            display: inline-block;
            margin-right: 40px;
        }
        
        .summary-section .summary-value {
            border-bottom: 1px solid #000;
            min-width: 100px;
            display: inline-block;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 60px;
            width: 100%;
        }

        .signature-box {
            width: 200px;
            display: inline-block;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .signature-section .principal-sign {
            margin: 0 80px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="content-wrapper">

            <!-- HEADER -->
            <div class="header-section">
                @if($school->logo)
                    <img src="{{ $school->logo }}" alt="School Logo" class="logo">
                @else
                    <img src="https://placehold.co/90x90/000/FFF?text=Logo" alt="School Logo" class="logo">
                @endif
                <div class="school-details">
                    <div class="title-box">Student's Result Card</div>
                    <div class="school-name-box">{{ $school->name }}</div>
                </div>
            </div>

            <!-- STUDENT INFO -->
            <div class="info-section">
                <div class="info-line" style="margin-top: 20px; font-weight: bold;">
                    Exam: <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 200px; margin-left:10px; text-align:center;">{{ $semester_name }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Student's Name:</span>
                    <span class="info-value">{{ $student->user->name }}</span>
                </div>
               
                <div class="info-line" style="">
                    <span class="info-label">Class:</span>
                    <span class="info-value short-field">{{ $student->schoolClass->name }}</span>
                </div>
                <span class="pull-right">
                    <span class="info-label">Roll No.</span>
                    <span class="info-value short-field" style="min-width: 120px;">{{ $student->school_class_id . $student->id }}</span>
                </span>
            </div>

            <!-- MARKS DETAIL -->
            <div class="marks-section">
                <div class="marks-title">Marks Detail</div>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 5%;">Sr.#</th>
                            <th rowspan="2">Subjects</th>
                            <th colspan="2">Marks</th>
                        </tr>
                        <tr>
                            <th style="width: 20%;">Maximum</th>
                            <th style="width: 20%;">Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-left">{{ $result['subject_name'] }}</td>
                            <td>{{ $result['total_marks'] }}</td>
                            <td>{{ $result['obtained_marks'] }}</td>
                        </tr>
                        @endforeach
                        <!-- Total Row -->
                        <tr class="total-row">
                            <td colspan="2" class="text-left" style="text-align: right; padding-right: 20px;"><strong>Total</strong></td>
                            <td><strong>{{ $total_possible }}</strong></td>
                            <td><strong>{{ $total_obtained }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- SUMMARY -->
            <div class="summary-section">
                 <div class="summary-field">
                    Attendance: <span class="summary-value">&nbsp;</span>
                </div>
                <div class="summary-field">
                    Marks Percentage: <span class="summary-value">{{ $percentage }}%</span>
                </div>
                 <div class="summary-field">
                    Position in Class: <span class="summary-value">&nbsp;</span>
                </div>
            </div>

            <!-- SIGNATURES -->
            <div class="signature-section">
                <span class="signature-box">Sign of Class Incharge</span>
                <span class="signature-box principal-sign">Sign of Principal</span>
                <span class="signature-box" style="float: right;">Result Declaration Date</span>
            </div>
        </div>
    </div>
</body>
</html>

