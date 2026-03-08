<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\BankDeposit;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class BankDepositNotificationController extends Controller
{
    public function create()
    {
        $role = auth()->user()->role;
        return view('organisation.bank-deposit-notification.create', compact('role'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'required|string|unique:bank_deposits,reference',
            'bank_name' => 'required|string',
            'bank_account_holder' => 'required|string',
            'sort_code' => 'required|string|regex:/^\d{2}-\d{2}-\d{2}$/',
            'account_number' => 'required|string|regex:/^\d{8}$/',
            'notes' => 'nullable|string|max:500',
            'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('bank-deposits', 'public');
        }

        $bankDeposit = BankDeposit::create([
            'organisation_id' => auth()->user()->organisation_id,
            'organisation_user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'reference' => $validated['reference'],
            'bank_name' => $validated['bank_name'],
            'bank_account_holder' => $validated['bank_account_holder'],
            'sort_code' => $validated['sort_code'],
            'account_number' => $validated['account_number'],
            'notes' => $validated['notes'] ?? null,
            'receipt_path' => $receiptPath,
            'status' => 'pending',
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'bank_deposit_submitted',
                'title' => 'New Bank Deposit Submitted',
                'message' => auth()->user()->name . ' submitted a bank deposit of £' . number_format($validated['amount'], 2),
                'data' => json_encode([
                    'bank_deposit_id' => $bankDeposit->id,
                    'organisation_name' => auth()->user()->name,
                    'amount' => $validated['amount'],
                    'reference' => $validated['reference'],
                ]),
                'is_read' => false,
            ]);
        }

        return redirect()->route(auth()->user()->role === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard')
            ->with('success', 'Bank deposit notification submitted successfully. Admins will verify and load your funds shortly.');
    }

    public function index()
    {
        $role = auth()->user()->role;
        $bankDeposits = BankDeposit::where('organisation_user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('organisation.bank-deposit-notification.index', compact('bankDeposits', 'role'));
    }

    public function show(BankDeposit $bankDeposit)
    {
        $this->authorize('view', $bankDeposit);
        $role = auth()->user()->role;
        return view('organisation.bank-deposit-notification.show', compact('bankDeposit', 'role'));
    }
}
