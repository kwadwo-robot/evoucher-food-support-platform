@extends('layouts.dashboard')
@section('title','Create Broadcast')
@section('page-title','Create Broadcast Message')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-3xl font-bold mb-8">Create Broadcast Message</h1>

    <form action="{{ route('admin.broadcasts.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Title Field -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-2">Title <span class="text-red-600">*</span></label>
            <input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Broadcast title">
            @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Message Field -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-2">Message <span class="text-red-600">*</span></label>
            <textarea name="message" required rows="6" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Your message here..."></textarea>
            @error('message') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Recipients Selection - Searchable Multi-Select -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-2">Select Recipients <span class="text-red-600">*</span></label>
            
            <!-- Search Input -->
            <input 
                type="text" 
                id="search-recipients" 
                placeholder="Search users by name or email..." 
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 mb-3"
            >
            
            <!-- Multi-Select Checkboxes -->
            <div id="recipients-list" class="border rounded-lg max-h-64 overflow-y-auto bg-gray-50 p-3 space-y-2">
                @foreach($users as $user)
                    <label class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer rounded">
                        <input 
                            type="checkbox" 
                            name="recipients[]" 
                            value="{{ $user->id }}"
                            class="mr-3 w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                        >
                        <div class="flex-1">
                            <div class="font-medium text-sm">{{ $user->name }}</div>
                            <div class="text-xs text-gray-600">{{ $user->email }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            
            @error('recipients') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
                <span>✓</span> Send Broadcast
            </button>
            <a href="{{ route('admin.broadcasts.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const searchInput = document.getElementById('search-recipients');
    const recipientsList = document.getElementById('recipients-list');
    const labels = recipientsList.querySelectorAll('label');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        labels.forEach(label => {
            const checkbox = label.querySelector('input[type="checkbox"]');
            const name = checkbox.dataset.name.toLowerCase();
            const email = checkbox.dataset.email.toLowerCase();
            
            if (name.includes(searchTerm) || email.includes(searchTerm) || searchTerm === '') {
                label.style.display = '';
            } else {
                label.style.display = 'none';
            }
        });
    });
</script>
@endsection
