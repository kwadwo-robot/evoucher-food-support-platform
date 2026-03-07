@extends('layouts.dashboard')
@section('title','Notifications')
@section('page-title','Notifications')
@section('content')
<div class="page-hd">
  <h1>Notifications</h1>
  <p>All your system notifications and alerts</p>
</div>

<div class="card">
  <div class="card-hd" style="display:flex;justify-content:space-between;align-items:center">
    <div class="card-title"><i class="fas fa-bell text-blue-500"></i> All Notifications</div>
    @if($notifications->count() > 0)
    <button onclick="markAllRead()" class="btn btn-secondary btn-sm">Mark all as read</button>
    @endif
  </div>
  
  @if($notifications->count() > 0)
  <div style="max-height:600px;overflow-y:auto">
    @foreach($notifications as $notif)
    <div class="notification-item" style="padding:16px;border-bottom:1px solid #f0f0f0;display:flex;gap:12px;align-items:flex-start;background:{{ $notif->isRead() ? '#fff' : '#f0fdf4' }}">
      <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
        @if($notif->type === 'success') background:#dcfce7;color:#16a34a
        @elseif($notif->type === 'warning') background:#fef9c3;color:#ca8a04
        @elseif($notif->type === 'error') background:#fee2e2;color:#dc2626
        @else background:#dbeafe;color:#2563eb
        @endif">
        <i class="{{ $notif->icon ?? 'fas fa-bell' }}"></i>
      </div>
      <div style="flex:1">
        <div style="font-weight:600;color:#0f172a;margin-bottom:4px">{{ $notif->title }}</div>
        <div style="font-size:13px;color:#64748b;line-height:1.5">{{ $notif->message }}</div>
        <div style="font-size:11px;color:#94a3b8;margin-top:6px">{{ $notif->created_at->diffForHumans() }}</div>
      </div>
      <div style="display:flex;gap:6px;flex-shrink:0">
        @if(!$notif->isRead())
        <button onclick="markRead({{ $notif->id }})" class="tb-btn" title="Mark as read">
          <i class="fas fa-check"></i>
        </button>
        @endif
        <button onclick="deleteNotif({{ $notif->id }})" class="tb-btn" title="Delete">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div class="empty-state" style="padding:64px 24px">
    <div class="empty-icon"><i class="fas fa-bell"></i></div>
    <h3>No notifications</h3>
    <p>You're all caught up!</p>
  </div>
  @endif
</div>

@if($notifications->hasPages())
<div style="margin-top:24px">{{ $notifications->links() }}</div>
@endif

<script>
function markRead(id) {
  fetch(`/notifications/${id}/read`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => location.reload());
}

function markAllRead() {
  fetch('/notifications/read-all', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => location.reload());
}

function deleteNotif(id) {
  if(confirm('Delete this notification?')) {
    fetch(`/notifications/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
  }
}
</script>
@endsection
