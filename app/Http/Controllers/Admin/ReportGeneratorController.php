<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Redemption;
use App\Models\User;
use App\Models\FoodListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportGeneratorController extends Controller
{
    public function index()
    {
        $data = [
            'total_donations' => DB::table('donations')->sum('amount') ?? 0,
            'total_vouchers_issued' => Voucher::count(),
            'total_redemptions' => Redemption::count(),
            'food_redeemed' => Redemption::distinct('food_listing_id')->count('food_listing_id'),
            'total_recipients' => User::where('role', 'recipient')->count(),
            'total_shops' => User::where('role', 'local_shop')->count(),
            'total_donors' => User::whereIn('role', ['vcfse', 'school'])->count(),
            'total_food_listed' => FoodListing::count(),
            'total_funds_loaded' => 0,
            'total_bank_deposits' => 0,
        ];

        $monthly_donations = [];

        return view('admin.reports.index', compact('data', 'monthly_donations'));
    }

    public function vouchersReport(Request $request)
    {
        $query = Voucher::with('recipient', 'donor');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $vouchers = $query->latest()->get();

        if ($request->format === 'pdf') {
            return $this->exportVouchersPDF($vouchers);
        } elseif ($request->format === 'excel') {
            return $this->exportVouchersExcel($vouchers);
        }

        return view('admin.reports.vouchers', compact('vouchers'));
    }

    public function redemptionsReport(Request $request)
    {
        $query = Redemption::with('recipient', 'foodListing', 'voucher');

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $redemptions = $query->latest()->get();

        if ($request->format === 'pdf') {
            return $this->exportRedemptionsPDF($redemptions);
        } elseif ($request->format === 'excel') {
            return $this->exportRedemptionsExcel($redemptions);
        }

        return view('admin.reports.redemptions', compact('redemptions'));
    }

    public function usersReport(Request $request)
    {
        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $users = $query->latest()->get();

        if ($request->format === 'pdf') {
            return $this->exportUsersPDF($users);
        } elseif ($request->format === 'excel') {
            return $this->exportUsersExcel($users);
        }

        return view('admin.reports.users', compact('users'));
    }

    public function foodListingsReport(Request $request)
    {
        $query = FoodListing::with('shop');

        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $listings = $query->latest()->get();

        if ($request->format === 'pdf') {
            return $this->exportFoodListingsPDF($listings);
        } elseif ($request->format === 'excel') {
            return $this->exportFoodListingsExcel($listings);
        }

        return view('admin.reports.food-listings', compact('listings'));
    }

    private function exportVouchersPDF($vouchers)
    {
        // Using a simple HTML to PDF approach
        $html = view('admin.reports.vouchers-pdf', compact('vouchers'))->render();
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="vouchers-report.pdf"',
        ]);
    }

    private function exportVouchersExcel($vouchers)
    {
        $filename = 'vouchers-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($vouchers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Recipient', 'Donor', 'Amount', 'Status', 'Created At']);

            foreach ($vouchers as $voucher) {
                fputcsv($file, [
                    $voucher->id,
                    $voucher->recipient?->name ?? 'N/A',
                    $voucher->donor?->name ?? 'N/A',
                    '£' . number_format($voucher->initial_value, 2),
                    $voucher->status,
                    $voucher->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRedemptionsPDF($redemptions)
    {
        $html = view('admin.reports.redemptions-pdf', compact('redemptions'))->render();
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="redemptions-report.pdf"',
        ]);
    }

    private function exportRedemptionsExcel($redemptions)
    {
        $filename = 'redemptions-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($redemptions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Recipient', 'Food Item', 'Voucher', 'Amount', 'Status', 'Redeemed At']);

            foreach ($redemptions as $redemption) {
                fputcsv($file, [
                    $redemption->id,
                    $redemption->recipient?->name ?? 'N/A',
                    $redemption->foodListing?->item_name ?? 'N/A',
                    $redemption->voucher?->id ?? 'N/A',
                    '£' . number_format($redemption->amount_used, 2),
                    $redemption->status,
                    $redemption->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportUsersPDF($users)
    {
        $html = view('admin.reports.users-pdf', compact('users'))->render();
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="users-report.pdf"',
        ]);
    }

    private function exportUsersExcel($users)
    {
        $filename = 'users-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Status', 'Approved', 'Created At']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->is_approved ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportFoodListingsPDF($listings)
    {
        $html = view('admin.reports.food-listings-pdf', compact('listings'))->render();
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="food-listings-report.pdf"',
        ]);
    }

    private function exportFoodListingsExcel($listings)
    {
        $filename = 'food-listings-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($listings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Shop', 'Item Name', 'Type', 'Quantity', 'Value', 'Status', 'Expiry Date']);

            foreach ($listings as $listing) {
                fputcsv($file, [
                    $listing->id,
                    $listing->shop?->name ?? 'N/A',
                    $listing->item_name,
                    $listing->listing_type,
                    $listing->quantity,
                    '£' . number_format($listing->voucher_value, 2),
                    $listing->status,
                    $listing->expiry_date->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
