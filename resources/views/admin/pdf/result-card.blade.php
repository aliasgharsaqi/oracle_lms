<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
            padding: 4px;
            height: 98%;
        }

        /* The inner container with the thin line border */
        .content-wrapper {
            border: 1px solid #000;
            padding: 20px;
            height: 100%;
        }

        /* Header Section */


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
        .info-line {
            margin-bottom: 12px;
            width: 100%;
        }

        .info-label {
            font-weight: bold;
            padding-right: 50px;
            display: inline-block;
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

        .results-table th,
        .results-table td {
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
            <div class="school-details" style="margin-bottom: 10px;">
                <div class="school-name-box">{{ $school->name }}</div>
            </div>
            <!-- HEADER -->
            <div style="width: 100%; overflow: hidden; border-bottom: 1px solid #000;">
                <div style="width: 20%; float: left;">
                    @if($school->logo)
                        <img src="{{ $school->logo }}" alt="School Logo" class="logo">
                    @else
                        <img src="https://placehold.co/90x90/000/FFF?text=Logo" alt="School Logo" class="logo">
                    @endif
                </div>
                <div style="width: 80%; float: left; text-align: center; margin-top: 20px;">
                    <p style="font-size: 17px; margin: 0; padding: 0;">{{ $school->decription }}</p>
                    <p style="font-size: 15px; margin: 0; padding: 0;">{{ $school->phone }} , {{ $school->email }}</p>
                    <p style="font-size: 15px; margin: 0; padding: 0;">{{ $school->address }}</p>
                </div>
            </div>


            <!-- STUDENT INFO -->
            <div style="margin-top: 10px;">
                <h3 style="text-align: center">Annual Result Session : 2025</h3>
                <div class="info-line" style="margin-top: 20px; font-weight: bold; text-align: center;">
                    Exam: <span
                        style=" display: inline-block; min-width: 200px; margin-left:10px; text-align:center;">{{ $semester_name }}</span>
                </div>
                <div style="width: 100%; overflow: hidden;">

                    <!-- Left Side: Student Info -->
                    <div style="width: 85%; float: left;">
                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">Section:</span>
                            <span style="display: inline-block; min-width: 250px; margin-left: 50px;">
                                {{ $student->section }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">Roll Number:</span>
                            <span style="display: inline-block; min-width: 250px; padding-left: 10px;">
                                {{ $student->school_class_id . $student->id }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">Student's Name:</span>
                            <span style="display: inline-block; min-width: 250px; padding-left: 10px;">
                                {{ $student->user->name }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">Student's Phone:</span>
                            <span style="display: inline-block; min-width: 250px; padding-left: 10px;">
                                {{ $student->user->phone }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">Father Name:</span>
                            <span style="display: inline-block; min-width: 250px; padding-left: 10px;">
                                {{ $student->father_name }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">Father Phone:</span>
                            <span style="display: inline-block; min-width: 250px; padding-left: 10px;">
                                {{ $student->father_phone }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label">Class:</span>
                            <span class="info-value short-field">
                                {{ $student->schoolClass->name }}
                            </span>
                        </div>

                        <div class="info-line">
                            <span class="info-label" style="margin-right: 30px;">ID Card Number:</span>
                            <span style="display: inline-block; min-width: 250px; padding-left: 10px;">
                                {{ $student->id_card_number }}
                            </span>
                        </div>
                    </div>

                    <!-- Right Side: Student Image -->
                    <div style="width: 15%; float: left; text-align: center;">
                        <img src="{{ $student->user->user_pic ?? 'https://placehold.co/100x100' }}" alt="user_image"
                            loading="lazy"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-top: 10px;">
                    </div>

                </div>

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
                            <td colspan="2" class="text-left" style="text-align: right; padding-right: 20px;">
                                <strong>Total</strong>
                            </td>
                            <td><strong>{{ $total_possible }}</strong></td>
                            <td><strong>{{ $total_obtained }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- SUMMARY -->
            <!-- SUMMARY SECTION -->
            <div
                style="width: 100%; margin-top: 30px; border-top: 1px solid #000; padding-top: 10px; font-size: 16px; line-height: 22px;">
                <div style="width: 100%; overflow: hidden; margin-bottom: 8px;">
                    <div style="float: left; width: 33%;">
                        Attendance: <span
                            style="display: inline-block; min-width: 80px; border-bottom: 1px solid #000;">&nbsp;</span>
                    </div>
                    <div style="float: left; width: 33%;">
                        Marks Percentage: <span
                            style="display: inline-block; min-width: 60px; border-bottom: 1px solid #000;">{{ $percentage }}%</span>
                    </div>
                    <div style="float: left; width: 33%;">
                        Position in Class: <span
                            style="display: inline-block; min-width: 80px; border-bottom: 1px solid #000;">&nbsp;</span>
                    </div>
                </div>
            </div>

            <!-- SIGNATURE SECTION -->
            <div style="width: 100%; margin-top: 50px; overflow: hidden; font-size: 15px; text-align: center;">
                <div
                    style="float: left; width: 28%; border-top: 1px solid #000; padding-top: 6px; display: inline-block;">
                    Sign of Class Incharge
                </div>

                <div
                    style="float: left; width: 28%; margin-left: 60px; border-top: 1px solid #000; padding-top: 6px; display: inline-block;">
                    Sign of Principal
                </div>

                <div
                    style="float: right; width: 28%; border-top: 1px solid #000; padding-top: 6px; display: inline-block;">
                    Result Declaration Date
                </div>
            </div>


        </div>
    </div>
</body>

</html>