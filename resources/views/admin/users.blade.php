@extends('layouts.dashboard')
@section('title','Manage Users')
@section('page-title','User Management')
@section('content')
<div class="page-hd">
  <h1>User Management</h1>
  <p>Manage all platform users, approve accounts, and assign roles</p>
</div>
<!-- Filter Bar -->
<div class="card mb-5">
  <div class="card-body" style="padding:14px 20px">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div style="flex:1;min-width:200px">
        <label class="form-label">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="form-input">
      </div>
      <div style="min-width:150px">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="">All Roles</option>
          <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
          <option value="super_admin" {{ request('role')=='super_admin'?'selected':'' }}>Super Admin</option>
          <option value="shop" {{ request('role')=='shop'?'selected':'' }}>Local Shop</option>
          <option value="recipient" {{ request('role')=='recipient'?'selected':'' }}>Recipient</option>
          <option value="vcfse" {{ request('role')=='vcfse'?'selected':'' }}>VCFSE</option>
          <option value="school" {{ request('role')=='school'?'selected':'' }}>School/Care</option>
        </select>
      </div>
      <div style="min-width:150px">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
          <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
          <option value="suspended" {{ request('status')=='suspended'?'selected':'' }}>Suspended</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-search"></i> Filter
      </button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-users text-green-600"></i> All Users <span class="badge badge-gray ml-2">{{ $users->total() ?? count($users) }}</span></div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>User</th><th>Role</th><th>Joined</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div class="flex items-center gap-3">
              <div style="width:34px;height:34px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#16a34a;flex-shrink:0">
                {{ strtoupper(substr($user->name, 0, 2)) }}
              </div>
              <div>
                <div style="font-weight:600;color:#0f172a;font-size:13px">{{ $user->name }}</div>
                <div style="font-size:11.5px;color:#94a3b8">{{ $user->email }}</div>
              </div>
            </div>
          </td>
          <td>
            @if($user->role === 'super_admin')<span class="badge badge-purple">Super Admin</span>
            @elseif($user->role === 'admin')<span class="badge badge-blue">Admin</span>
            @elseif($user->role === 'shop')<span class="badge badge-orange">Local Shop</span>
            @elseif($user->role === 'recipient')<span class="badge badge-green">Recipient</span>
            @elseif($user->role === 'vcfse')<span class="badge badge-blue">VCFSE</span>
            @elseif($user->role === 'school')<span class="badge badge-yellow">School/Care</span>
            @else<span class="badge badge-gray">{{ ucfirst($user->role) }}</span>@endif
          </td>
          <td style="font-size:12px;color:#64748b">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            @if($user->is_approved)<span class="badge badge-green"><i class="fas fa-check-circle"></i> Approved</span>
            @else<span class="badge badge-yellow"><i class="fas fa-clock"></i> Pending</span>@endif
          </td>
          <td>
            <div class="flex items-center gap-2">
              @if(!$user->is_approved)
              <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fas fa-check"></i> Approve
                </button>
              </form>
              @endif
              @if($user->is_active ?? true)
              <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deactivate this user?')">
                  <i class="fas fa-ban"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fas fa-users"></i></div><h3>No users found</h3><p>Try adjusting your filters</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if(method_exists($users, 'links'))
  <div style="padding:16px 20px;border-top:1px solid #f8fafc">{{ $users->links() }}</div>
  @endif
</div>
@endsection
