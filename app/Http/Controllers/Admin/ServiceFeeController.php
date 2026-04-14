<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceFee;
use App\Models\ServiceFeeSetting;
use App\Models\User;
use App\Services\ServiceFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceFeeController extends Controller
{
    /**
     * Display service fee dashboard with statistics and transactions
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $shopId = $request->get('shop_id');

        // Build query
        $query = ServiceFee::with('shopUser');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($shopId) {
            $query->where('shop_user_id', $shopId);
        }

        $serviceFees = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total_collected' => ServiceFee::where('status', 'collected')->sum('service_fee_amount'),
            'total_transactions' => ServiceFee::count(),
            'collected_transactions' => ServiceFee::where('status', 'collected')->count(),
            'average_fee_per_transaction' => $this->calculateAverageFee(),
            'current_percentage' => ServiceFeeSetting::getCurrentPercentage(),
        ];

        // Get shops for filter dropdown - get shops that have service fees
        $shopsWithFees = ServiceFee::distinct('shop_user_id')->pluck('shop_user_id');
        $shops = User::where('role', 'local_shop')
            ->whereIn('id', $shopsWithFees)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.service-fees.index', compact('serviceFees', 'stats', 'shops', 'startDate', 'endDate', 'shopId'));
    }

    /**
     * Show detailed view of a single service fee transaction
     */
    public function show($id)
    {
        $serviceFee = ServiceFee::with(['shopUser', 'payoutRequest'])->findOrFail($id);

        return view('admin.service-fees.show', compact('serviceFee'));
    }

    /**
     * Display service fee settings page
     */
    public function settings()
    {
        $currentSetting = ServiceFeeSetting::first();
        $stats = [
            'total_collected' => ServiceFee::where('status', 'collected')->sum('service_fee_amount'),
            'total_transactions' => ServiceFee::count(),
            'average_fee_per_transaction' => $this->calculateAverageFee(),
        ];

        return view('admin.service-fees.settings', compact('currentSetting', 'stats'));
    }

    /**
     * Update service fee percentage
     */
    public function updatePercentage(Request $request)
    {
        $request->validate([
            'service_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $percentage = (float) $request->input('service_fee_percentage');

        // Update or create the setting
        $setting = ServiceFeeSetting::first() ?? new ServiceFeeSetting();
        $setting->service_fee_percentage = $percentage;
        $setting->description = 'Service fee percentage updated to ' . $percentage . '% by ' . Auth::user()->name . ' on ' . now()->format('Y-m-d H:i:s');
        $setting->save();

        return back()->with('success', 'Service fee percentage updated to ' . $percentage . '%');
    }

    /**
     * Export service fee data to CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = ServiceFee::with('shopUser');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $serviceFees = $query->orderBy('created_at', 'desc')->get();

        $filename = 'service-fees-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($serviceFees) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Shop Name',
                'Shop Email',
                'Payout Amount',
                'Fee Percentage',
                'Fee Amount',
                'Amount After Fee',
                'Status',
                'Created At',
            ]);

            // Data rows
            foreach ($serviceFees as $fee) {
                fputcsv($file, [
                    $fee->id,
                    $fee->shopUser->name,
                    $fee->shopUser->email,
                    '£' . number_format($fee->payout_amount, 2),
                    $fee->service_fee_percentage . '%',
                    '£' . number_format($fee->service_fee_amount, 2),
                    '£' . number_format($fee->amount_after_fee, 2),
                    ucfirst($fee->status),
                    $fee->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate average fee per transaction
     */
    private function calculateAverageFee()
    {
        $totalFees = ServiceFee::sum('service_fee_amount');
        $totalTransactions = ServiceFee::count();

        return $totalTransactions > 0 ? round($totalFees / $totalTransactions, 2) : 0;
    }
}
