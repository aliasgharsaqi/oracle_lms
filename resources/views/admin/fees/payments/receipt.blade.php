<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $voucher->student->user->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2ff;
            font-family: sans-serif;
        }

        .receipt-container {
            width: 100%;
            max-width: 420px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #dee2e6;
            box-shadow: 0 0.3rem 0.6rem rgba(0, 0, 0, .08);
            border-radius: .5rem;
        }

        .receipt-header {
            background: #4a5568;
            color: #fff;
            padding: 1rem;
            border-top-left-radius: .5rem;
            border-top-right-radius: .5rem;
        }

        .receipt-header h2 {
            margin: 0;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.2rem;
        }

        .receipt-body {
            padding: 1.2rem;
        }

        .receipt-footer {
            text-align: center;
            padding: 1rem;
            font-size: 0.85em;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .school-logo {
            max-height: 55px;
            margin-bottom: 0.5rem;
        }

        .table thead th {
            font-weight: 600;
            background-color: #f8f9fa;
            font-size: 0.9rem;
        }

        .table td, .table th {
            padding: 6px 8px !important;
        }

        .total-row td,
        .total-row th {
            font-weight: bold;
            font-size: 1rem;
        }

        .row p {
            margin-bottom: 0.2rem;
        }

        .receipt-body h5 {
            margin-bottom: 0.3rem;
            font-size: 1rem;
        }

        @media print {
            body {
                background-color: #fff;
            }

            .receipt-container {
                margin: 0;
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container no-print my-3">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-secondary rounded-pill px-3 py-1"><i class="bi bi-arrow-left"></i> Back</a>
            <button class="btn btn-primary rounded-pill px-3 py-1" onclick="window.print()"><i class="bi bi-printer-fill"></i> Print</button>
        </div>
    </div>

    <div class="receipt-container">
        <div class="receipt-header text-center">
            @if($voucher->student->user->school->logo)
            <img src="{{ asset('storage/' . $voucher->student->user->school->logo) }}" alt="School Logo" class="school-logo">
            @endif
            <h2>Fee Receipt</h2>
            <p class="mb-0 fs-6">{{ $voucher->student->user->school->name ?? 'School Management System' }}</p>
        </div>

        <div class="receipt-body">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h5>Billed To:</h5>
                    <p><strong>{{ $voucher->student->user->name }}</strong></p>
                    <p>Class: {{ $voucher->student->schoolClass->name }}</p>
                    <p>ID: {{ str_pad($voucher->student->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h5>Details:</h5>
                    <p><strong>Receipt #:</strong> R-{{ str_pad($voucher->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p><strong>Date:</strong> {{ $voucher->paid_at->format('M d, Y') }}</p>
                    <p><strong>Month:</strong> {{ $voucher->voucher_month->format('F Y') }}</p>
                </div>
            </div>

            <table class="table table-bordered mb-2">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Due (PKR)</th>
                        <th class="text-end">Paid (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($voucher->tuition_fee > 0 || $voucher->paid_tuition > 0)
                    <tr>
                        <td>Tuition Fee</td>
                        <td class="text-end">{{ number_format($voucher->tuition_fee, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->paid_tuition, 2) }}</td>
                    </tr>
                    @endif
                    @if($voucher->admission_fee > 0 || $voucher->paid_admission > 0)
                    <tr>
                        <td>Admission Fee</td>
                        <td class="text-end">{{ number_format($voucher->admission_fee, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->paid_admission, 2) }}</td>
                    </tr>
                    @endif
                    @if($voucher->examination_fee > 0 || $voucher->paid_examination > 0)
                    <tr>
                        <td>Examination Fee</td>
                        <td class="text-end">{{ number_format($voucher->examination_fee, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->paid_examination, 2) }}</td>
                    </tr>
                    @endif
                    @if($voucher->other_fees > 0 || $voucher->paid_other > 0)
                    <tr>
                        <td>Other Charges</td>
                        <td class="text-end">{{ number_format($voucher->other_fees, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->paid_other, 2) }}</td>
                    </tr>
                    @endif
                    @if($voucher->arrears > 0 || $voucher->paid_arrears > 0)
                    <tr>
                        <td class="text-danger">Previous Dues</td>
                        <td class="text-end text-danger">{{ number_format($voucher->arrears, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->paid_arrears, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="2" class="text-end">Total Due:</td>
                        <td class="text-end fw-bold">PKR {{ number_format($voucher->amount_due, 2) }}</td>
                    </tr>
                    <tr class="table-success">
                        <td colspan="2" class="text-end">Total Paid:</td>
                        <td class="text-end fw-bold">PKR {{ number_format($voucher->amount_paid, 2) }}</td>
                    </tr>
                    @php $balance = $voucher->amount_due - $voucher->amount_paid; @endphp
                    @if($balance > 0.01)
                    <tr class="table-warning">
                        <td colspan="2" class="text-end">Balance:</td>
                        <td class="text-end fw-bold">PKR {{ number_format($balance, 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>

            <p class="mb-0"><strong>Method:</strong> {{ $voucher->payment_method }}</p>
            <p class="mb-0"><strong>Status:</strong> <span class="fw-bold text-success">{{ strtoupper($voucher->status) }}</span></p>
            @if($voucher->notes)
            <p class="mb-0"><strong>Notes:</strong> {{ $voucher->notes }}</p>
            @endif
        </div>

        <div class="receipt-footer">
            <p class="mb-0">Thank you for your payment!</p>
        </div>
    </div>
</body>

</html>
