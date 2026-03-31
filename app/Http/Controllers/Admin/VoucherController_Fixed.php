<?php

namespace App\Http\Controllers\Admin;

use App\Models\Voucher;
use App\Models\User;
use App\Mail\VoucherIssuedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
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
        // Check if issuing to existing recipient or new recipient
        if (!empty($request->recipient_id)) {
            // Issue to existing recipient
            $this->issueToExistingRecipient($request);
        } else {
            // Issue to new recipient (manual entry)
            $this->issueToNewRecipient($request);
        }
        
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher issued successfully!');
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
        Mail::to($recipient->email)->send(new VoucherIssuedMail($voucher, $recipient));
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
        $emailData = [
            'recipient' => $recipient,
            'voucher' => $voucher,
            'registrationLink' => $registrationLink,
            'tempPassword' => $tempPassword,
        ];
        
        Mail::to($recipient->email)->send(new VoucherIssuedMail($voucher, $recipient, $emailData));
    }

    private function generateVoucherCode()
    {
        do {
            $code = 'EV-' . strtoupper(Str::random(10));
        } while (Voucher::where('code', $code)->exists());
        
        return $code;
    }
}
