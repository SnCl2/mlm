@extends('layout.app')

@section('title', 'Notifications Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notifications Management</h1>
        <a href="{{ route('admin.notifications.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Send New Notification
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Type</label>
                <select name="recipient_type" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Types</option>
                    <option value="user" {{ request('recipient_type') == 'user' ? 'selected' : '' }}>Users</option>
                    <option value="shop" {{ request('recipient_type') == 'shop' ? 'selected' : '' }}>Shops</option>
                    <option value="all" {{ request('recipient_type') == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notification Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Types</option>
                    <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Error</option>
                    <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Status</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md transition duration-200">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($notifications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notification</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notifications as $notification)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-{{ $notification->icon }} text-{{ $notification->type === 'info' ? 'blue' : ($notification->type === 'success' ? 'green' : ($notification->type === 'warning' ? 'yellow' : ($notification->type === 'error' ? 'red' : 'purple'))) }}-500"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $notification->title }}</div>
                                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($notification->message, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($notification->recipient_type === 'all')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            All Users & Shops
                                        </span>
                                    @elseif($notification->user)
                                        <div class="text-sm text-gray-900">{{ $notification->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $notification->user->referral_code }}</div>
                                    @elseif($notification->shop)
                                        <div class="text-sm text-gray-900">{{ $notification->shop->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $notification->shop->owner_name }}</div>
                                    @else
                                        <span class="text-sm text-gray-500">Unknown</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notification->type_badge_class }}">
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($notification->is_read)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Read
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Unread
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $notification->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.notifications.show', $notification) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $notification) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications found</h3>
                <p class="text-gray-500">No notifications match your current filters.</p>
            </div>
        @endif
    </div>
</div>
@endsection
