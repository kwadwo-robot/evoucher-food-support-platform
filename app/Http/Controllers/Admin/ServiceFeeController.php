<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceFeeTransaction;
use App\Services\ServiceFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceFeeController extends Controller
{
    /**
     * Show service fee dashboard with statistics and transactions
     */
    public function index(Request $request)
    {
        $stats = ServiceFeeService::getServiceFeeStats();
        
        // Get transactions with pagination
        $transactions = ServiceFeeTransaction::with(['shop', 'payoutRequest'])
            ->orderByDesc('created_at')
            ->paginate(20);

        // Get filter options
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $transactions = ServiceFeeTransaction::with(['shop', 'payoutRequest'])
                ->where('status', $status)
                ->orderByDesc('created_at')
                ->paginate(20);
        }

        return view('admin.service-fees.index', compact('stats', 'transactions', 'status'));
    }

    /**
     * Show service fee settings page
     */
    public function settings()
    {
        $currentPercentage = ServiceFeeService::getServiceFeePercentage();
        return view('admin.service-fees.settings', compact('currentPercentage'));
    }

    /**
     * Update service fee percentage
     */
    public function updatePercentage(Request $request)
    {
        $request->validate([
            'service_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        try {
            ServiceFeeService::setServiceFeePercentage((float) $request->service_fee_percentage);
            return back()->with('success', 'Service fee percentage updated to ' . $request->service_fee_percentage . '%');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating service fee: ' . $e->getMessage());
        }
    }

    /**
     * Show detailed view of a single service fee transaction
     */
    public function show($id)
    {
        $transaction = ServiceFeeTransaction::with(['shop', 'payoutRequest.redemptions'])
            ->findOrFail($id);

        return view('admin.service-fees.show', compact('transaction'));
    }

    /**
     * Export service fee report
     */
    public function export(Request $request)
    {
        $query = ServiceFeeTransaction::with(['shop', 'payoutRequest']);

        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->get();

        $csv = "ID,Shop Name,Payout Request ID,Total Amount,Service Fee %,Service Fee Amount,Amount After Fee,Status,Created At\n";
        
        foreach ($transactions as $transaction) {
            $csv .= sprintf(
                "%d,%s,%d,%.2f,%.2f,%.2f,%.2f,%s,%s\n",
                $transaction->id,
                $transaction->shop->name ?? 'N/A',
                $transaction->payout_request_id,
                $transaction->total_amount,
                $transaction->service_fee_percentage,
                $transaction->service_fee_amount,
                $transaction->amount_after_fee,
                $transaction->status,
                $transaction->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="service-fees-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
