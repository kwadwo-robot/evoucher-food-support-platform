<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display all donations
     */
    public function index(Request $request)
    {
        $query = Donation::query();

        // Search by email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('donor_email', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $donations = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => Donation::count(),
            'completed' => Donation::where('status', 'completed')->count(),
            'processing' => Donation::where('status', 'processing')->count(),
            'failed' => Donation::where('status', 'failed')->count(),
            'total_amount' => Donation::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.donations.index', compact('donations', 'stats'));
    }

    /**
     * Show donation details
     */
    public function show(Donation $donation)
    {
        return view('admin.donations.show', compact('donation'));
    }
}
