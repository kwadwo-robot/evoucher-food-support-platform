<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceFeeTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ServiceFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceFeeTransaction::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->with('shop')->paginate(15);

        // Calculate statistics
        $stats = [
            'total_collected' => ServiceFeeTransaction::where('status', 'collected')->sum('service_fee_amount'),
            'total_pending' => ServiceFeeTransaction::where('status', 'pending')->sum('service_fee_amount'),
            'collected_transactions' => ServiceFeeTransaction::where('status', 'collected')->count(),
            'pending_transactions' => ServiceFeeTransaction::where('status', 'pending')->count(),
            'total_transactions' => ServiceFeeTransaction::count(),
            'current_percentage' => DB::table('settings')->where('key', 'service_fee_percentage')->value('value') ?? 10,
        ];

        return view('admin.service-fees.index', compact('transactions', 'stats'));
    }

    public function settings()
    {
        $currentPercentage = DB::table('settings')->where('key', 'service_fee_percentage')->value('value') ?? 10;
        return view('admin.service-fees.settings', compact('currentPercentage'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'service_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'service_fee_percentage'],
            ['value' => $request->service_fee_percentage, 'updated_at' => now()]
        );

        return redirect()->route('admin.service-fees.settings')->with('success', 'Service fee percentage updated to ' . $request->service_fee_percentage . '%');
    }

    public function updatePercentage(Request $request)
    {
        $request->validate([
            'service_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'service_fee_percentage'],
            ['value' => $request->service_fee_percentage, 'updated_at' => now()]
        );

        return redirect()->route('admin.service-fees.settings')->with('success', 'Service fee percentage updated to ' . $request->service_fee_percentage . '%');
    }

    public function show($id)
    {
        $transaction = ServiceFeeTransaction::with('shop')->findOrFail($id);
        return view('admin.service-fees.show', compact('transaction'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $query = ServiceFeeTransaction::with('shop');

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->get();

        switch ($format) {
            case 'excel':
                return $this->exportExcel($transactions);
            case 'pdf':
                return $this->exportPdf($transactions);
            case 'csv':
            default:
                return $this->exportCsv($transactions);
        }
    }

    private function exportCsv($transactions)
    {
        $filename = 'service-fees-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID',
                'Shop Name',
                'Total Amount',
                'Fee Percentage',
                'Fee Amount',
                'Amount After Fee',
                'Status',
                'Date',
            ]);

            // Data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->shop ? $transaction->shop->name : 'N/A',
                    '£' . number_format($transaction->total_amount, 2),
                    $transaction->service_fee_percentage . '%',
                    '£' . number_format($transaction->service_fee_amount, 2),
                    '£' . number_format($transaction->amount_after_fee, 2),
                    ucfirst($transaction->status),
                    $transaction->created_at->format('M d, Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($transactions)
    {
        $filename = 'service-fees-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        // For now, just return CSV if Excel export is not configured
        return $this->exportCsv($transactions);
    }

    private function exportPdf($transactions)
    {
        $filename = 'service-fees-' . now()->format('Y-m-d-H-i-s') . '.pdf';

        $html = view('admin.service-fees.export-pdf', compact('transactions'))->render();
        
        // For now, just return CSV if PDF export is not configured
        return $this->exportCsv($transactions);
    }
}
