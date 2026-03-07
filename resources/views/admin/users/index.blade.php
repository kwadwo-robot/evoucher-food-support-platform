@extends('layouts.dashboard')
@section('page-title', 'Manage Users')
@section('title', 'Manage Users')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Users</h1>
        <p class="text-gray-500 text-sm mt-1">Manage all platform users</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-1"></i> Back</a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
            class="flex-1 min-w-48 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500">
        <select name="role" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500">
            <option value="">All Roles</option>
            <option value="recipient" {{ request('role') === 'recipient' ? 'selected' : '' }}>Recipient</option>
            <option value="local_shop" {{ request('role') === 'local_shop' ? 'selected' : '' }}>Local Shop</option>
            <option value="vcfse" {{ request('role') === 'vcfse' ? 'selected' : '' }}>VCFSE</option>
            <option value="school_care" {{ request('role') === 'school_care' ? 'selected' : '' }}>School/Care</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
        </select>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Clear</a>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Role</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Joined</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-green-700 font-semibold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <span class="px-2 py-1 text-xs rounded-full capitalize
                            {{ $user->role === 'recipient' ? 'bg-green-100 text-green-700' :
                               ($user->role === 'local_shop' ? 'bg-blue-100 text-blue-700' :
                               (in_array($user->role, ['admin','super_admin']) ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700')) }}">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        @if(!$user->is_active)
                            <span class="badge-expired">Inactive</span>
                        @elseif(!$user->is_approved && !$user->isAdmin() && $user->role !== 'recipient')
                            <span class="badge-pending">Pending</span>
                        @else
                            <span class="badge-active">Active</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if(!$user->is_approved && !$user->isAdmin() && $user->role !== 'recipient')
                            <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded">Approve</button>
                            </form>
                            @endif
                            <a href="{{ route('admin.users.show', $user) }}" class="text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded">View</a>
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} px-2 py-1 rounded">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection
