<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeController extends Controller
{
    /**
     * Generate QR code for a voucher
     * Returns PNG image of QR code
     */
    public function generateQR($voucherId)
    {
        $voucher = Voucher::findOrFail($voucherId);

        // Verify the voucher belongs to the authenticated user
        if (auth()->user()->id !== $voucher->recipient_id) {
            abort(403, 'Unauthorized');
        }

        // Prepare QR code data with voucher and recipient information
        $qrData = [
            'voucher_code' => $voucher->code,
            'recipient_name' => auth()->user()->name,
            'recipient_email' => auth()->user()->email,
            'recipient_phone' => auth()->user()->phone ?? 'N/A',
            'amount' => $voucher->amount,
            'expiry_date' => $voucher->expiry_date->format('Y-m-d'),
            'issued_date' => $voucher->created_at->format('Y-m-d'),
            'verification_url' => route('shop.verify')
        ];

        // Create QR code with JSON data
        $qrCode = new QrCode(json_encode($qrData));
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Write QR code to PNG format
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString(), 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Display QR code modal for a voucher
     */
    public function showQRModal($voucherId)
    {
        $voucher = Voucher::findOrFail($voucherId);

        // Verify the voucher belongs to the authenticated user
        if (auth()->user()->id !== $voucher->recipient_id) {
            abort(403, 'Unauthorized');
        }

        return view('recipient.vouchers.qr-modal', compact('voucher'));
    }

    /**
     * Verify QR code data from shop side
     * Scanned QR code contains JSON data that needs to be verified
     */
    public function verifyQR(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|json'
        ]);

        try {
            $qrData = json_decode($request->input('qr_data'), true);

            // Find voucher by code
            $voucher = Voucher::where('code', $qrData['voucher_code'])->first();

            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'error' => 'Voucher not found'
                ], 404);
            }

            // Verify voucher is valid
            if ($voucher->is_redeemed) {
                return response()->json([
                    'success' => false,
                    'error' => 'Voucher has already been redeemed'
                ], 400);
            }

            if ($voucher->expiry_date < now()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Voucher has expired'
                ], 400);
            }

            // Return voucher details for confirmation
            return response()->json([
                'success' => true,
                'voucher' => [
                    'code' => $voucher->code,
                    'recipient_name' => $qrData['recipient_name'],
                    'recipient_email' => $qrData['recipient_email'],
                    'recipient_phone' => $qrData['recipient_phone'],
                    'amount' => $qrData['amount'],
                    'expiry_date' => $qrData['expiry_date'],
                    'status' => 'valid'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid QR code data'
            ], 400);
        }
    }

    /**
     * Get voucher details by code (for manual entry fallback)
     */
    public function getVoucherByCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $voucher = Voucher::where('code', $request->input('code'))->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'error' => 'Voucher not found'
            ], 404);
        }

        // Verify voucher is valid
        if ($voucher->is_redeemed) {
            return response()->json([
                'success' => false,
                'error' => 'Voucher has already been redeemed'
            ], 400);
        }

        if ($voucher->expiry_date < now()) {
            return response()->json([
                'success' => false,
                'error' => 'Voucher has expired'
            ], 400);
        }

        // Get recipient details
        $recipient = $voucher->recipient;

        return response()->json([
            'success' => true,
            'voucher' => [
                'code' => $voucher->code,
                'recipient_name' => $recipient->name,
                'recipient_email' => $recipient->email,
                'recipient_phone' => $recipient->phone ?? 'N/A',
                'amount' => $voucher->amount,
                'expiry_date' => $voucher->expiry_date->format('Y-m-d'),
                'status' => 'valid'
            ]
        ]);
    }
}
