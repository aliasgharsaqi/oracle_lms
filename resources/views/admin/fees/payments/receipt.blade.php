<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $voucher->student->user->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eee; }
        .receipt-container { max-width: 800px; margin: 40px auto; background: #fff; border: 1px solid #ddd; }
        .receipt-header { background: #343a40; color: #fff; padding: 20px; text-align: center; }
        .receipt-header h2 { margin: 0; font-size: 2rem; }
        .receipt-body { padding: 30px; }
        .receipt-footer { text-align: center; padding: 20px; font-size: 0.8em; color: #777; }
        @media print {
            body { background-color: #fff; }
            .receipt-container { margin: 0; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>FEE RECEIPT</h2>
            <p>School Management System</p>
        </div>
        <div class="receipt-body">
            <div class="row mb-4">
                <div class="col-6">
                    <strong>Receipt #:</strong> {{ str_pad($voucher->id, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($voucher->paid_at)->format('M d, Y') }}
                </div>
                <div class="col-6 text-end">
                    <strong>Student Name:</strong> {{ $voucher->student->user->name }}<br>
                    <strong>Class:</strong> {{ $voucher->student->schoolClass->name }}
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tuition Fee for {{ \Carbon\Carbon::parse($voucher->voucher_month)->format('F Y') }}</td>
                        <td class="text-end">${{ number_format($voucher->amount_due, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td class="text-end">Total Amount Paid:</td>
                        <td class="text-end">${{ number_format($voucher->amount_paid, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            <div class="row mt-4">
                <div class="col-12">
                    <p><strong>Payment Method:</strong> {{ $voucher->payment_method }}</p>
                    <p class="text-success fw-bold">Status: PAID</p>
                </div>
            </div>
        </div>
        <div class="receipt-footer">
            <p>Thank you for your payment!</p>
            <button class="btn btn-primary no-print" onclick="window.print()">Print Receipt</button>
        </div>
    </div>
</body>
</html>
