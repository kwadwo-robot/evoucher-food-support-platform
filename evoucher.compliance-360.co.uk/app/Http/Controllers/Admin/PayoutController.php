<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopPayoutRequest;
use App\Models\ShopBankDetail;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * List all payout requests.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = ShopPayoutRequest::with(['shop.shopProfile'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payouts = $query->paginate(20);

        $stats = [
            'pending'  => ShopPayoutRequest::where('status', 'pending')->count(),
            'approved' => ShopPayoutRequest::where('status', 'approved')->count(),
            'paid'     => ShopPayoutRequest::where('status', 'paid')->count(),
            'rejected' => ShopPayoutRequest::where('status', 'rejected')->count(),
            'total_paid_amount' => ShopPayoutRequest::where('status', 'paid')->sum('total_amount'),
            'total_pending_amount' => ShopPayoutRequest::whereIn('status', ['pending', 'approved'])->sum('total_amount'),
        ];

        return view('admin.payouts.index', compact('payouts', 'stats', 'status'));
    }

    /**
     * Show a single payout request with bank details and redemptions.
     */
    public function show($id)
    {
        $payout = ShopPayoutRequest::with([
            'shop.shopProfile',
            'redemptions.foodListing',
            'processedBy',
        ])->findOrFail($id);

        $bankDetails = ShopBankDetail::where('shop_user_id', $payout->shop_user_id)->first();

        return view('admin.payouts.show', compact('payout', 'bankDetails'));
    }

    /**
     * Approve a payout request.
     */
    public function approve($id)
    {
        $payout = ShopPayoutRequest::findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $payout->update([
            'status'       => 'approved',
            'processed_by' => Auth::id(),
        ]);

        // Notify shop about approval
        NotificationService::notifyShopPayoutApproved($payout);

        return back()->with('success', 'Payout request approved. Please process the bank transfer.');
    }

    /**
     * Mark a payout as paid (bank transfer completed).
     */
    public function markPaid(Request $request, $id)
    {
        $request->validate([
            'payment_reference' => 'required|string|max:100',
            'admin_notes'       => 'nullable|string|max:500',
        ]);

        $payout = ShopPayoutRequest::findOrFail($id);

        if (!in_array($payout->status, ['pending', 'approved'])) {
            return back()->with('error', 'This payout cannot be marked as paid.');
        }

        $payout->update([
            'status'             => 'paid',
            'payment_reference'  => $request->payment_reference,
            'admin_notes'        => $request->admin_notes,
            'processed_by'       => Auth::id(),
            'paid_at'            => now(),
        ]);

        // Notify shop about payment
        NotificationService::notifyShopPayoutProcessed($payout);

        return back()->with('success', 'Payout marked as paid. Reference: ' . $request->payment_reference);
    }

    /**
     * Reject a payout request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $payout = ShopPayoutRequest::findOrFail($id);

        if (!in_array($payout->status, ['pending', 'approved'])) {
            return back()->with('error', 'This payout cannot be rejected.');
        }

        // Unlink redemptions so they can be re-requested
        DB::transaction(function () use ($payout, $request) {
            $payout->redemptions()->update(['payout_request_id' => null]);
            $payout->update([
                'status'       => 'rejected',
                'admin_notes'  => $request->admin_notes,
                'processed_by' => Auth::id(),
            ]);
        });

        // Notify shop about rejection
        NotificationService::notifyShopPayoutRejected($payout);

        return back()->with('success', 'Payout request rejected. Redemptions are now available for a new request.');
    }
}
