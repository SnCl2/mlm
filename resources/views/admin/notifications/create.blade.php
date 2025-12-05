@extends('layout.app')

@section('title', 'Send New Notification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Send New Notification</h1>
            <p class="text-gray-600 mt-2">Send notifications to users, shops, or all members</p>
        </div>

        <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notification Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                               placeholder="Enter notification title"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" 
                                name="type" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Select notification type</option>
                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="error" {{ old('type') == 'error' ? 'selected' : '' }}>Error</option>
                            <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" 
                              name="message" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('message') border-red-500 @enderror"
                              placeholder="Enter notification message"
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Recipient Selection</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Send To <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="recipient_type" 
                                       value="all" 
                                       {{ old('recipient_type') == 'all' ? 'checked' : '' }}
                                       class="mr-3 text-blue-600 focus:ring-blue-500"
                                       onchange="toggleRecipientSelection()">
                                <span class="text-sm text-gray-700">All Users and Shops</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="recipient_type" 
                                       value="user" 
                                       {{ old('recipient_type') == 'user' ? 'checked' : '' }}
                                       class="mr-3 text-blue-600 focus:ring-blue-500"
                                       onchange="toggleRecipientSelection()">
                                <span class="text-sm text-gray-700">Specific User</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="recipient_type" 
                                       value="shop" 
                                       {{ old('recipient_type') == 'shop' ? 'checked' : '' }}
                                       class="mr-3 text-blue-600 focus:ring-blue-500"
                                       onchange="toggleRecipientSelection()">
                                <span class="text-sm text-gray-700">Specific Shop</span>
                            </label>
                        </div>
                        @error('recipient_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="recipient-selection" class="hidden">
                        <label for="recipient_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Recipient
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="recipient_search" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Search by name, email, or code..."
                                   autocomplete="off">
                            <input type="hidden" 
                                   id="recipient_id" 
                                   name="recipient_id" 
                                   value="{{ old('recipient_id') }}">
                            <div id="recipient_dropdown" class="hidden absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                        </div>
                        @error('recipient_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Options</h2>
                
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="send_email" 
                               value="1"
                               {{ old('send_email') ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Also send as email notification</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.notifications.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRecipientSelection() {
    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
    const recipientSelection = document.getElementById('recipient-selection');
    const recipientId = document.getElementById('recipient_id');
    
    if (recipientType === 'all') {
        recipientSelection.classList.add('hidden');
        recipientId.value = '';
    } else {
        recipientSelection.classList.remove('hidden');
        recipientId.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRecipientSelection();
    
    // Recipient search functionality
    const recipientSearch = document.getElementById('recipient_search');
    const recipientDropdown = document.getElementById('recipient_dropdown');
    const recipientId = document.getElementById('recipient_id');
    let searchTimeout;

    recipientSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;
        
        if (query.length < 2) {
            recipientDropdown.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
            
            fetch(`/notifications/recipients?type=${recipientType}&search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    recipientDropdown.innerHTML = '';
                    
                    if (data.length === 0) {
                        recipientDropdown.innerHTML = '<div class="p-3 text-gray-500">No results found</div>';
                    } else {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
                            div.innerHTML = `
                                <div class="font-medium">${item.name || item.owner_name}</div>
                                <div class="text-sm text-gray-500">${item.email}</div>
                                ${item.referral_code ? `<div class="text-xs text-blue-600">${item.referral_code}</div>` : ''}
                            `;
                            
                            div.addEventListener('click', function() {
                                recipientId.value = item.id;
                                recipientSearch.value = item.name || item.owner_name;
                                recipientDropdown.classList.add('hidden');
                            });
                            
                            recipientDropdown.appendChild(div);
                        });
                    }
                    
                    recipientDropdown.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300);
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!recipientSearch.contains(e.target) && !recipientDropdown.contains(e.target)) {
            recipientDropdown.classList.add('hidden');
        }
    });
});
</script>
@endsection
