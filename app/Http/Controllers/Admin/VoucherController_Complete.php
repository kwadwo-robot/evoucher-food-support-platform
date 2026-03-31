<?php

namespace App\Http\Controllers\Admin;

use App\Models\Voucher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::with('user')->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $recipients = User::where('role', 'recipient')->get();
        $counties = [
            'Northamptonshire',
            'Bedfordshire',
            'Cambridgeshire',
            'Leicestershire',
            'Lincolnshire',
            'Norfolk',
            'Suffolk',
            'Other'
        ];
        
        $categories = [
            'Family',
            'Individual',
            'Senior/Elderly',
            'Student',
            'Child/Young Person',
            'Vulnerable Person',
            'Other'
        ];
        
        $programs = [
            'Holiday Provision',
            'Emergency Support',
            'Community Fund',
            'School Program',
            'Care Organization',
            'VCFSE Initiative'
        ];
        
        return view('admin.vouchers.create', compact('recipients', 'counties', 'categories', 'programs'));
    }

    public function store(Request $request)
    {
        try {
            // Check if issuing to existing recipient or new recipient
            if (!empty($request->recipient_id)) {
                // Issue to existing recipient
                $this->issueToExistingRecipient($request);
            } else {
                // Issue to new recipient (manual entry)
                $this->issueToNewRecipient($request);
            }
            
            return redirect()->route('admin.vouchers.index')->with('success', 'Voucher issued successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error issuing voucher: ' . $e->getMessage())->withInput();
        }
    }

    private function issueToExistingRecipient(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'expiry_date' => 'nullable|date',
        ]);

        $recipient = User::findOrFail($request->recipient_id);
        
        $voucher = Voucher::create([
            'code' => $this->generateVoucherCode(),
            'user_id' => $recipient->id,
            'amount' => $request->amount,
            'expiry_date' => $request->expiry_date ?? now()->addDays(30),
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        // Send email
        try {
            Mail::to($recipient->email)->send(new \App\Mail\VoucherIssuedMail($voucher, $recipient));
        } catch (\Exception $e) {
            \Log::warning('Email sending failed: ' . $e->getMessage());
        }
    }

    private function issueToNewRecipient(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'county' => 'required|string',
            'post_code' => 'required|string|max:10',
            'recipient_category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'voucher_description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'funding_organization' => 'nullable|string',
            'funding_program' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Create temporary recipient user
        $tempPassword = Str::random(16);
        $recipient = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'county' => $request->county,
            'post_code' => $request->post_code,
            'role' => 'recipient',
            'password' => bcrypt($tempPassword),
            'status' => 'pending', // Pending registration
        ]);

        // Create voucher
        $voucher = Voucher::create([
            'code' => $this->generateVoucherCode(),
            'user_id' => $recipient->id,
            'amount' => $request->amount,
            'expiry_date' => $request->expiry_date ?? now()->addDays(30),
            'description' => $request->voucher_description,
            'category' => $request->recipient_category,
            'funding_source' => $request->funding_organization,
            'funding_program' => $request->funding_program,
            'special_conditions' => $request->special_conditions,
            'notes' => $request->internal_notes,
            'status' => 'active',
        ]);

        // Send email with registration link and voucher details
        $registrationLink = route('register', ['email' => $recipient->email]);
        
        // Send custom email with voucher details
        try {
            Mail::to($recipient->email)->send(new \App\Mail\VoucherIssuedMail($voucher, $recipient, [
                'registrationLink' => $registrationLink,
                'tempPassword' => $tempPassword,
            ]));
        } catch (\Exception $e) {
            \Log::warning('Email sending failed: ' . $e->getMessage());
        }
    }

    private function generateVoucherCode()
    {
        do {
            $code = 'EV-' . strtoupper(Str::random(10));
        } while (Voucher::where('code', $code)->exists());
        
        return $code;
    }

    public function show($id)
    {
        $voucher = Voucher::with('user')->findOrFail($id);
        return view('admin.vouchers.show', compact('voucher'));
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $voucher = Voucher::findOrFail($id);
        $voucher->update($request->only(['amount', 'expiry_date', 'notes']));

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher updated successfully!');
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted successfully!');
    }
}
