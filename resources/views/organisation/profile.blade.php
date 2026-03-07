@extends('layouts.dashboard')
@section('title','Organisation Profile')
@section('page-title','Organisation Profile')
@section('content')
<div class="page-hd">
  <h1>Organisation Profile</h1>
  <p>Update your organisation details</p>
</div>

@if(session('success'))
<div class="alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:640px">
  <div class="card-body">
  @php
    $updateRoute = auth()->user()->role === 'vcfse' ? 'vcfse.profile.update' : 'school.profile.update';
  @endphp
  <form method="POST" action="{{ route($updateRoute) }}">
    @csrf @method('PUT')
    <div class="mb-4">
      <label class="form-label">Organisation Name</label>
      <input type="text" name="org_name" class="form-input" value="{{ old('org_name', $profile->org_name ?? '') }}" required>
      @error('org_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="mb-4">
      <label class="form-label">Contact Person</label>
      <input type="text" name="contact_person" class="form-input" value="{{ old('contact_person', $profile->contact_person ?? '') }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Phone Number</label>
      <input type="text" name="phone" class="form-input" value="{{ old('phone', $profile->phone ?? '') }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Address</label>
      <textarea name="address" class="form-input" rows="2">{{ old('address', $profile->address ?? '') }}</textarea>
    </div>
    <div class="mb-4">
      <label class="form-label">Postcode</label>
      <input type="text" name="postcode" class="form-input" value="{{ old('postcode', $profile->postcode ?? '') }}" style="max-width:160px">
    </div>
    <div class="mb-6">
      <label class="form-label">Website</label>
      <input type="url" name="website" class="form-input" placeholder="https://..." value="{{ old('website', $profile->website ?? '') }}">
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
  </div>
</div>
@endsection
