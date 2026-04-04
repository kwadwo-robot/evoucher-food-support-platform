<?php

namespace App\Http\Controllers\Recipient;

use App\Http\Controllers\Controller;
use App\Models\Redemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Export redemption history as PDF
     */
    public function exportPDF()
    {
        $user = Auth::user();
        $redemptions = Redemption::where('recipient_user_id', $user->id)
            ->with(['foodListing.shop.shopProfile', 'voucher'])
            ->latest()
            ->get();

        // Create HTML content for PDF
        $html = $this->generateReportHTML($user, $redemptions);

        // Generate PDF
        $pdf = Pdf::loadHTML($html);
        
        return $pdf->download('redemption-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export redemption history as Excel (CSV format)
     */
    public function exportExcel()
    {
        $user = Auth::user();
        $redemptions = Redemption::where('recipient_user_id', $user->id)
            ->with(['foodListing.shop.shopProfile', 'voucher'])
            ->latest()
            ->get();

        // Create CSV content
        $csv = "Redemption Report\n";
        $csv .= "Recipient: " . $user->name . "\n";
        $csv .= "Generated: " . now()->format('d/m/Y H:i') . "\n\n";
        
        // Column headers
        $csv .= "Date,Item Name,Shop,Quantity,Voucher Used,Amount Redeemed,Status\n";
        
        // Add data rows
        $totalAmount = 0;
        foreach ($redemptions as $redemption) {
            $amount = $redemption->amount_used ?? 0;
            $totalAmount += $amount;
            
            $itemName = $redemption->foodListing->item_name ?? 'N/A';
            $shopName = $redemption->foodListing->shop->shopProfile->shop_name ?? $redemption->foodListing->shop->name ?? 'N/A';
            $voucherCode = $redemption->voucher->voucher_code ?? 'N/A';
            $status = ucfirst($redemption->status ?? 'completed');
            
            $csv .= $redemption->created_at->format('d/m/Y') . ",";
            $csv .= '"' . $itemName . '",';
            $csv .= '"' . $shopName . '",';
            $csv .= ($redemption->quantity ?? 1) . ",";
            $csv .= '"' . $voucherCode . '",';
            $csv .= '"£' . number_format($amount, 2) . '",';
            $csv .= '"' . $status . "\"\n";
        }
        
        $csv .= "\nTotal Amount Redeemed,,,,,\"£" . number_format($totalAmount, 2) . "\"\n";
        
        $filename = 'redemption-report-' . now()->format('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        echo $csv;
        exit;
    }

    /**
     * Generate HTML for PDF report
     */
    private function generateReportHTML($user, $redemptions)
    {
        $totalAmount = 0;
        $rows = '';
        
        foreach ($redemptions as $redemption) {
            $amount = $redemption->amount_used ?? 0;
            $totalAmount += $amount;
            
            $rows .= '
                    <tr>
                        <td>' . $redemption->created_at->format('d/m/Y') . '</td>
                        <td>' . htmlspecialchars($redemption->foodListing->item_name ?? 'N/A') . '</td>
                        <td>' . htmlspecialchars($redemption->foodListing->shop->shopProfile->shop_name ?? $redemption->foodListing->shop->name ?? 'N/A') . '</td>
                        <td>' . ($redemption->quantity ?? 1) . '</td>
                        <td>' . htmlspecialchars($redemption->voucher->voucher_code ?? 'N/A') . '</td>
                        <td>£' . number_format($amount, 2) . '</td>
                        <td>' . ucfirst($redemption->status ?? 'completed') . '</td>
                    </tr>';
        }

        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #0f172a; }
                .header { margin-bottom: 20px; }
                .header p { margin: 5px 0; color: #64748b; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #0f172a; color: white; padding: 10px; text-align: left; }
                td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
                tr:nth-child(even) { background: #f1f5f9; }
                .total { font-weight: bold; background: #f0fdf4; }
                .footer { margin-top: 30px; font-size: 12px; color: #94a3b8; }
            </style>
        </head>
        <body>
            <h1>Redemption Report</h1>
            <div class="header">
                <p><strong>Recipient Name:</strong> ' . htmlspecialchars($user->name) . '</p>
                <p><strong>Report Generated:</strong> ' . now()->format('d/m/Y H:i') . '</p>
                <p><strong>Total Redemptions:</strong> ' . count($redemptions) . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item Name</th>
                        <th>Shop</th>
                        <th>Quantity</th>
                        <th>Voucher Code</th>
                        <th>Amount Redeemed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $rows . '
                    <tr class="total">
                        <td colspan="5">Total Amount Redeemed:</td>
                        <td>£' . number_format($totalAmount, 2) . '</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="footer">
                <p>This is an official report from eVoucher Food Support Platform.</p>
                <p>© 2026 eVoucher Food Support Platform. All rights reserved.</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
