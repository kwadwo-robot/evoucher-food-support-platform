<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vouchers Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4CAF50;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .summary {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .summary-item {
            padding: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            color: #4CAF50;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background: #4CAF50;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        tbody tr:hover {
            background: #f0f0f0;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-active {
            background: #c8e6c9;
            color: #2e7d32;
        }
        .status-redeemed {
            background: #bbdefb;
            color: #1565c0;
        }
        .status-expired {
            background: #ffccbc;
            color: #d84315;
        }
        .status-cancelled {
            background: #e0e0e0;
            color: #424242;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 11px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .amount {
            text-align: right;
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Vouchers Report</h1>
            <p>{{ $orgName }} - Generated on {{ now()->format('d M Y H:i') }}</p>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total Vouchers</div>
                <div class="summary-value">{{ $totalVouchers }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Value</div>
                <div class="summary-value">£{{ number_format($totalValue, 2) }}</div>
            </div>
        </div>

        @if($vouchers->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Voucher Code</th>
                    <th>Recipient</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Issued</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                <tr>
                    <td><strong>{{ $voucher->code }}</strong></td>
                    <td>{{ $voucher->recipient ? $voucher->recipient->name : 'N/A' }}</td>
                    <td class="amount">£{{ number_format($voucher->amount, 2) }}</td>
                    <td>
                        <span class="status status-{{ $voucher->status }}">
                            {{ ucfirst(str_replace('_', ' ', $voucher->status)) }}
                        </span>
                    </td>
                    <td>{{ $voucher->created_at->format('d M Y') }}</td>
                    <td>{{ $voucher->expires_at ? $voucher->expires_at->format('d M Y') : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>No vouchers found.</p>
        </div>
        @endif

        <div class="footer">
            <p>This is an official report from the eVoucher Platform</p>
            <p style="margin-top: 10px;">For questions or support, please contact the platform administrator</p>
        </div>
    </div>
</body>
</html>
