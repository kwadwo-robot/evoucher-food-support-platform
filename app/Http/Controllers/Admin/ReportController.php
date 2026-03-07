<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\FoodListing;
use App\Models\Redemption;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
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
        ];
        $monthly_donations = Donation::where('status','completed')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year','month')->orderBy('year','desc')->orderBy('month','desc')->take(12)->get();
        return view('admin.reports.index', compact('data','monthly_donations'));
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
