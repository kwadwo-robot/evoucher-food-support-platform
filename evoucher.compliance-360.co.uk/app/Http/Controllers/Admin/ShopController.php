<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of shops.
     */
    public function index()
    {
        $shops = ShopProfile::with('user')->paginate(15);
        return view('admin.shops.index', compact('shops'));
    }

    /**
     * Display the specified shop.
     */
    public function show(ShopProfile $shop)
    {
        return view('admin.shops.show', compact('shop'));
    }

    /**
     * Show the form for editing the specified shop.
     */
    public function edit(ShopProfile $shop)
    {
        return view('admin.shops.edit', compact('shop'));
    }

    /**
     * Update the specified shop in storage.
     */
    public function update(Request $request, ShopProfile $shop)
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'town' => ['required', 'string', 'max:100'],
            'postcode' => ['required', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $shop->update([
            'shop_name' => $validated['shop_name'],
            'category' => $validated['category'],
            'address' => $validated['address'],
            'town' => $validated['town'],
            'postcode' => $validated['postcode'],
            'phone' => $validated['phone'],
            'opening_hours' => $validated['opening_hours'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.shops.show', $shop)
            ->with('success', 'Shop updated successfully!');
    }

    /**
     * Suspend/Deactivate a shop.
     */
    public function suspend(ShopProfile $shop)
    {
        $shop->user->update(['is_active' => false]);
        
        return redirect()->route('admin.shops.show', $shop)
            ->with('success', 'Shop suspended successfully!');
    }

    /**
     * Reactivate a suspended shop.
     */
    public function reactivate(ShopProfile $shop)
    {
        $shop->user->update(['is_active' => true]);
        
        return redirect()->route('admin.shops.show', $shop)
            ->with('success', 'Shop reactivated successfully!');
    }

    /**
     * Delete a shop completely.
     */
    public function destroy(ShopProfile $shop)
    {
        $userId = $shop->user_id;
        $shopName = $shop->shop_name;
        
        // Delete shop profile
        $shop->delete();
        
        // Delete associated user
        User::find($userId)->delete();
        
        return redirect()->route('admin.shops.index')
            ->with('success', "Shop '{$shopName}' has been deleted successfully!");
    }
}
