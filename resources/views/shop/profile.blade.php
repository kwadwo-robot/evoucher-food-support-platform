@extends('layouts.dashboard')
@section('title','Shop Profile')
@section('page-title','Shop Profile')
@section('content')
<div class="page-hd">
  <h1>Shop Profile</h1>
  <p>Update your shop details shown to recipients</p>
</div>

@if(session('success'))
<div class="alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:640px">
  <div class="card-body">
  <form method="POST" action="{{ route('shop.profile.update') }}">
    @csrf @method('PUT')
    <div class="mb-4">
      <label class="form-label">Shop Name</label>
      <input type="text" name="shop_name" class="form-input" value="{{ old('shop_name', $profile->shop_name ?? '') }}" required>
      @error('shop_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="mb-4">
      <label class="form-label">Shop Category</label>
      <select name="category" class="form-input">
        <option value="">-- Select a category --</option>
        @foreach(['african' => 'African', 'caribbean' => 'Caribbean', 'mixed_african_caribbean' => 'Mixed African & Caribbean', 'indian_south_asian' => 'Indian / South Asian', 'eastern_european' => 'Eastern European', 'middle_eastern' => 'Middle Eastern'] as $val => $label)
          <option value="{{ $val }}" {{ old('category', $profile->category ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-4">
      <label class="form-label">Phone Number</label>
      <input type="text" name="phone" class="form-input" value="{{ old('phone', $profile->phone ?? '') }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Address</label>
      <input type="text" name="address" class="form-input" value="{{ old('address', $profile->address ?? '') }}" required>
      @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="form-label">Town</label>
        <select name="town" class="form-input">
          <option value="">-- Select your town --</option>
          <optgroup label="North Northamptonshire">
            <option value="Wellingborough" {{ old('town', $profile->town ?? '') === 'Wellingborough' ? 'selected' : '' }}>Wellingborough</option>
            <option value="Kettering" {{ old('town', $profile->town ?? '') === 'Kettering' ? 'selected' : '' }}>Kettering</option>
            <option value="Corby" {{ old('town', $profile->town ?? '') === 'Corby' ? 'selected' : '' }}>Corby</option>
          </optgroup>
          <optgroup label="East Northamptonshire">
            <option value="Rushden" {{ old('town', $profile->town ?? '') === 'Rushden' ? 'selected' : '' }}>Rushden</option>
            <option value="Higham Ferrers" {{ old('town', $profile->town ?? '') === 'Higham Ferrers' ? 'selected' : '' }}>Higham Ferrers</option>
            <option value="Raunds" {{ old('town', $profile->town ?? '') === 'Raunds' ? 'selected' : '' }}>Raunds</option>
            <option value="Irthlingborough" {{ old('town', $profile->town ?? '') === 'Irthlingborough' ? 'selected' : '' }}>Irthlingborough</option>
            <option value="Oundle" {{ old('town', $profile->town ?? '') === 'Oundle' ? 'selected' : '' }}>Oundle</option>
            <option value="Thrapston" {{ old('town', $profile->town ?? '') === 'Thrapston' ? 'selected' : '' }}>Thrapston</option>
          </optgroup>
          <optgroup label="West Northamptonshire">
            <option value="Northampton" {{ old('town', $profile->town ?? '') === 'Northampton' ? 'selected' : '' }}>Northampton</option>
            <option value="Daventry" {{ old('town', $profile->town ?? '') === 'Daventry' ? 'selected' : '' }}>Daventry</option>
            <option value="Brackley" {{ old('town', $profile->town ?? '') === 'Brackley' ? 'selected' : '' }}>Brackley</option>
            <option value="Towcester" {{ old('town', $profile->town ?? '') === 'Towcester' ? 'selected' : '' }}>Towcester</option>
          </optgroup>
        </select>
      </div>
      <div>
        <label class="form-label">Postcode</label>
        <input type="text" name="postcode" class="form-input" value="{{ old('postcode', $profile->postcode ?? '') }}">
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label">Opening Hours</label>
      <input type="text" name="opening_hours" class="form-input" placeholder="e.g. Mon–Sat 8am–6pm" value="{{ old('opening_hours', $profile->opening_hours ?? '') }}">
    </div>
    <div class="mb-6">
      <label class="form-label">Shop Description</label>
      <textarea name="description" class="form-input" rows="3" placeholder="Brief description of your shop...">{{ old('description', $profile->description ?? '') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
  </form>
  </div>
</div>
@endsection
