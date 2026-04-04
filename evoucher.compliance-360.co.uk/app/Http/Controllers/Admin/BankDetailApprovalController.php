<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankDetailChangeRequest;
use App\Models\ShopBankDetail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankDetailApprovalController extends Controller
{
    /**
     * Show all pending bank detail change requests.
     */
    public function index()
    {
        $changeRequests = BankDetailChangeRequest::with(['shop', 'bankDetail'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.bank-details.index', compact('changeRequests'));
    }

    /**
     * Show a specific change request for review.
     */
    public function show($id)
    {
        $changeRequest = BankDetailChangeRequest::with(['shop', 'bankDetail'])
            ->findOrFail($id);

        return view('admin.bank-details.show', compact('changeRequest'));
    }

    /**
     * Approve a bank detail change request.
     */
    public function approve(Request $request, $id)
    {
        $changeRequest = BankDetailChangeRequest::findOrFail($id);

        if (!$changeRequest->isPending()) {
            return redirect()->route('admin.bank-details.index')
                ->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($changeRequest) {
            // Update the bank detail with new information
            $bankDetail = $changeRequest->bankDetail;
            $bankDetail->update([
                'account_holder_name' => $changeRequest->account_holder_name,
                'bank_name' => $changeRequest->bank_name,
                'sort_code' => $changeRequest->sort_code,
                'account_number' => $changeRequest->account_number,
                'bank_reference' => $changeRequest->bank_reference,
            ]);

            // Mark the change request as approved
            $changeRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Create notification for the shop user
            Notification::create([
                'user_id' => $changeRequest->shop_user_id,
                'title' => 'Bank Details Approved',
                'message' => 'Your bank detail change request has been approved. Your new bank details are now active.',
                'type' => 'bank_detail_approved',
                'icon' => 'fa-check-circle',
                'read_at' => null,
            ]);
        });

        return redirect()->route('admin.bank-details.index')
            ->with('success', 'Bank detail change request approved successfully.');
    }

    /**
     * Reject a bank detail change request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $changeRequest = BankDetailChangeRequest::findOrFail($id);

        if (!$changeRequest->isPending()) {
            return redirect()->route('admin.bank-details.index')
                ->with('error', 'This request has already been processed.');
        }

        // Mark the change request as rejected
        $changeRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Create notification for the shop user
        Notification::create([
            'user_id' => $changeRequest->shop_user_id,
            'title' => 'Bank Details Change Rejected',
            'message' => 'Your bank detail change request has been rejected. Reason: ' . $request->rejection_reason,
            'type' => 'bank_detail_rejected',
            'icon' => 'fa-times-circle',
            'read_at' => null,
        ]);

        return redirect()->route('admin.bank-details.index')
            ->with('success', 'Bank detail change request rejected successfully.');
    }
}
