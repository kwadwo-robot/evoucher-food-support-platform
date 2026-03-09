<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BankDeposit;
use App\Models\Donation;
use App\Models\FoodListing;
use App\Models\FundLoad;
use App\Models\Redemption;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Calculate total donations (completed donations)
        $totalDonated = Donation::where('status','completed')->sum('amount');
        
        // Calculate total funds loaded
        $totalFundsLoaded = FundLoad::sum('amount');
        
        // Calculate total received (donations + funds loaded)
        $totalReceived = $totalDonated + $totalFundsLoaded;
        
        // Calculate total spent (voucher redemptions)
        $totalSpent = Redemption::sum('amount_used');
        $totalRedemptions = Redemption::count();
        
        // Calculate balance
        $totalBalance = $totalReceived - $totalSpent;
        
        // Get voucher statistics
        $voucherStats = [
            'active' => Voucher::where('status','active')->count(),
            'redeemed' => Voucher::where('status','redeemed')->count(),
            'expired' => Voucher::where('status','expired')->count(),
            'cancelled' => Voucher::where('status','cancelled')->count(),
        ];
        
        // Get platform participation metrics
        $totalRecipients = User::where('role','recipient')->count();
        $totalShops = User::where('role','local_shop')->count();
        $totalDonors = User::whereIn('role',['vcfse','school_care'])->count();
        $totalVouchers = Voucher::count();
        $totalListings = FoodListing::count();
        
        // Calculate redemption rate (percentage of vouchers that have been redeemed)
        $vouchersRedeemed = Voucher::whereIn('status', ['redeemed', 'partially_used'])->count();
        $redemptionRate = $totalVouchers > 0 ? round(($vouchersRedeemed / $totalVouchers) * 100) : 0;
        
        // Use database-agnostic date formatting (SQLite uses strftime, MySQL uses DATE_FORMAT)
        $isSqlite = config('database.default') === 'sqlite';
        
        if ($isSqlite) {
            $monthKeyExpr = "strftime('%Y-%m', created_at) as month_key";
            $monthLabelExpr = "strftime('%Y-%m', created_at) as month";
            $yearExpr = "CAST(strftime('%Y', created_at) AS INTEGER) as year";
            $monthExpr = "CAST(strftime('%m', created_at) AS INTEGER) as month";
        } else {
            $monthKeyExpr = 'DATE_FORMAT(created_at, "%Y-%m") as month_key';
            $monthLabelExpr = 'DATE_FORMAT(created_at, "%b %Y") as month';
            $yearExpr = 'YEAR(created_at) as year';
            $monthExpr = 'MONTH(created_at) as month';
        }
        
        // Get monthly data for the table
        $monthlyData = Donation::where('status','completed')
            ->selectRaw("{$monthKeyExpr}, {$monthLabelExpr}, COUNT(*) as donations, SUM(amount) as amount")
            ->groupBy('month_key', 'month')
            ->orderBy('month_key', 'desc')
            ->take(12)
            ->get()
            ->map(function($row) use ($isSqlite) {
                $monthKey = $row->month_key;
                // For SQLite the month label is also in Y-m format, convert to readable
                $monthLabel = $isSqlite
                    ? \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('M Y')
                    : $row->month;

                // Get vouchers issued in this month
                $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth();
                $monthEnd   = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->endOfMonth();
                
                $vouchers    = Voucher::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $redemptions = Redemption::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                
                return [
                    'month'       => $monthLabel,
                    'donations'   => $row->donations,
                    'amount'      => $row->amount,
                    'vouchers'    => $vouchers,
                    'redemptions' => $redemptions,
                ];
            })
            ->toArray();
        
        // Get spending breakdown by food category
        $spendingByCategory = Redemption::with('foodListing')
            ->get()
            ->groupBy(function($item) {
                return $item->foodListing->category ?? 'Uncategorized';
            })
            ->map(function($items) {
                return [
                    'count'  => $items->count(),
                    'amount' => $items->sum('amount_used')
                ];
            });
        
        $data = [
            'total_donations'       => Donation::where('status','completed')->sum('amount'),
            'total_vouchers_issued' => Voucher::count(),
            'active_vouchers'       => Voucher::where('status','active')->count(),
            'redeemed_vouchers'     => Voucher::whereIn('status',['redeemed','partially_used'])->count(),
            'total_redemptions'     => Redemption::count(),
            'confirmed_redemptions' => Redemption::where('status','confirmed')->count(),
            'total_food_listed'     => FoodListing::count(),
            'food_redeemed'         => FoodListing::where('status','redeemed')->count(),
            'total_recipients'      => User::where('role','recipient')->count(),
            'total_shops'           => User::where('role','local_shop')->count(),
            'total_donors'          => User::whereIn('role',['vcfse','school_care'])->count(),
            'total_funds_loaded'    => FundLoad::sum('amount'),
            'total_bank_deposits'   => BankDeposit::where('status','verified')->count(),
        ];
        
        if ($isSqlite) {
            $monthly_donations = Donation::where('status','completed')
                ->selectRaw("CAST(strftime('%Y', created_at) AS INTEGER) as year, CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(amount) as total")
                ->groupBy('year','month')->orderBy('year','desc')->orderBy('month','desc')->take(12)->get();
        } else {
            $monthly_donations = Donation::where('status','completed')
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('year','month')->orderBy('year','desc')->orderBy('month','desc')->take(12)->get();
        }
        
        return view('admin.reports.index', compact(
            'data',
            'monthly_donations',
            'totalDonated',
            'totalFundsLoaded',
            'totalReceived',
            'totalSpent',
            'totalRedemptions',
            'totalBalance',
            'voucherStats',
            'totalRecipients',
            'totalShops',
            'totalDonors',
            'totalVouchers',
            'totalListings',
            'monthlyData',
            'spendingByCategory',
            'redemptionRate'
        ));
    }

    public function export()
    {
        $redemptions = Redemption::with(['recipient.recipientProfile','foodListing.shop.shopProfile','voucher'])->latest()->get();
        $filename = 'evoucher-report-' . date('Y-m-d') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];
        $callback = function() use ($redemptions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Redemption ID','Voucher Code','Recipient Name','Food Item','Shop Name','Amount Used','Status','Date']);
            foreach ($redemptions as $r) {
                fputcsv($file, [
                    $r->id, $r->voucher->code ?? '',
                    $r->recipient->recipientProfile->full_name ?? $r->recipient->name,
                    $r->foodListing->item_name ?? '',
                    $r->foodListing->shop->shopProfile->shop_name ?? '',
                    '£'.$r->amount_used, $r->status,
                    $r->redeemed_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
