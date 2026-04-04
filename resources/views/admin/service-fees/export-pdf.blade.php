<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Fees Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #3b82f6;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background-color: #3b82f6;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tbody tr:hover {
            background-color: #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .status-collected {
            background-color: #dcfce7;
            color: #15803d;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-refunded {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 12px;
        }
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        .summary-value {
            color: #3b82f6;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Service Fees Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
        <p>eVoucher Platform</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Shop Name</th>
                <th class="text-right">Total Amount</th>
                <th class="text-right">Fee %</th>
                <th class="text-right">Fee Amount</th>
                <th class="text-right">After Fee</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>#{{ $transaction->id }}</td>
                    <td>{{ $transaction->shop ? $transaction->shop->name : 'N/A' }}</td>
                    <td class="text-right">£{{ number_format($transaction->total_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($transaction->service_fee_percentage, 2) }}%</td>
                    <td class="text-right">£{{ number_format($transaction->service_fee_amount, 2) }}</td>
                    <td class="text-right">£{{ number_format($transaction->amount_after_fee, 2) }}</td>
                    <td>
                        @if($transaction->status === 'collected')
                            <span class="status-collected">Collected</span>
                        @elseif($transaction->status === 'pending')
                            <span class="status-pending">Pending</span>
                        @else
                            <span class="status-refunded">Refunded</span>
                        @endif
                    </td>
                    <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        No service fee transactions found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Total Transactions:</span>
            <span class="summary-value">{{ $transactions->count() }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Amount:</span>
            <span class="summary-value">£{{ number_format($transactions->sum('total_amount'), 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Fees Collected:</span>
            <span class="summary-value">£{{ number_format($transactions->sum('service_fee_amount'), 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Paid to Shops:</span>
            <span class="summary-value">£{{ number_format($transactions->sum('amount_after_fee'), 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>This is an automatically generated report. For more information, please contact the administrator.</p>
        <p>© {{ now()->year }} eVoucher Platform. All rights reserved.</p>
    </div>
</body>
</html>
