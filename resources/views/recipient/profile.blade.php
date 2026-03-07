@extends('layouts.dashboard')
@section('title','My Profile')
@section('page-title','My Profile')
@section('content')
<div class="page-hd">
  <h1>My Profile</h1>
  <p>Update your personal details</p>
</div>

@if(session('success'))
<div class="alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:600px">
  <div class="card-body">
  <form method="POST" action="{{ route('recipient.profile.update') }}">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <div>
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-input" value="{{ old('first_name', $profile->first_name ?? '') }}" required>
        @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-input" value="{{ old('last_name', $profile->last_name ?? '') }}" required>
        @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label">Email</label>
      <input type="email" class="form-input bg-gray-50" value="{{ auth()->user()->email }}" disabled>
    </div>
    <div class="mb-4">
      <label class="form-label">Phone Number</label>
      <input type="text" name="phone" class="form-input" value="{{ old('phone', $profile->phone ?? '') }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Address</label>
      <textarea name="address" class="form-input" rows="2">{{ old('address', $profile->address ?? '') }}</textarea>
    </div>
    <div class="mb-6">
      <label class="form-label">Postcode</label>
      <input type="text" name="postcode" class="form-input" value="{{ old('postcode', $profile->postcode ?? '') }}" style="max-width:160px">
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
  </div>
</div>
@endsection
