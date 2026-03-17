<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminShopController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'local_shop');

        // Search by name or email
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'active') {
                $query->where('is_approved', true);
            }
        }

        // Sort
        $sortBy = $request->sort ?? 'newest';
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest();
        }

        $shops = $query->paginate(20);
        
        return view('admin.shops.index', compact('shops', 'sortBy'));
    }

    public function show(User $shop)
    {
        if ($shop->role !== 'local_shop') {
            abort(404);
        }
        
        return view('admin.shops.show', compact('shop'));
    }

    public function approve(User $shop)
    {
        if ($shop->role !== 'local_shop') {
            abort(404);
        }
        
        $shop->update(['is_approved' => true]);
        return back()->with('success', 'Shop approved successfully.');
    }

    public function reject(User $shop)
    {
        if ($shop->role !== 'local_shop') {
            abort(404);
        }
        
        $shop->update(['is_approved' => false]);
        return back()->with('success', 'Shop rejected successfully.');
    }

    public function toggleActive(User $shop)
    {
        if ($shop->role !== 'local_shop') {
            abort(404);
        }
        
        $shop->update(['is_active' => !$shop->is_active]);
        return back()->with('success', 'Shop status updated.');
    }

    public function destroy(User $shop)
    {
        if ($shop->role !== 'local_shop') {
            abort(404);
        }
        
        $shop->delete();
        return back()->with('success', 'Shop deleted successfully.');
    }
}
