@extends('layout.app')

@section('title', 'Notification Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Notification Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.notifications.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                    <form action="{{ route('admin.notifications.destroy', $notification) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this notification?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Notification Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-{{ $notification->icon }} text-2xl text-{{ $notification->type === 'info' ? 'blue' : ($notification->type === 'success' ? 'green' : ($notification->type === 'warning' ? 'yellow' : ($notification->type === 'error' ? 'red' : 'purple'))) }}-500"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $notification->title }}</h2>
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $notification->type_badge_class }}">
                                {{ ucfirst($notification->type) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $notification->created_at->format('M d, Y \a\t H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Content -->
            <div class="px-6 py-6">
                <div class="prose max-w-none">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Message</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $notification->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Recipient Information -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Recipient Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Type</label>
                        <p class="text-sm text-gray-900">
                            @if($notification->recipient_type === 'all')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-users mr-1"></i>All Users & Shops
                                </span>
                            @elseif($notification->recipient_type === 'user')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user mr-1"></i>User
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-store mr-1"></i>Shop
                                </span>
                            @endif
                        </p>
                    </div>

                    @if($notification->recipient_type !== 'all')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Details</label>
                            @if($notification->user)
                                <div class="text-sm text-gray-900">
                                    <div class="font-medium">{{ $notification->user->name }}</div>
                                    <div class="text-gray-500">{{ $notification->user->email }}</div>
                                    <div class="text-blue-600">{{ $notification->user->referral_code }}</div>
                                </div>
                            @elseif($notification->shop)
                                <div class="text-sm text-gray-900">
                                    <div class="font-medium">{{ $notification->shop->name }}</div>
                                    <div class="text-gray-500">{{ $notification->shop->owner_name }}</div>
                                    <div class="text-gray-500">{{ $notification->shop->email }}</div>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">Recipient not found</div>
                            @endif
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Created By</label>
                        <div class="text-sm text-gray-900">
                            <div class="font-medium">{{ $notification->creator->name ?? 'Unknown' }}</div>
                            <div class="text-gray-500">{{ $notification->creator->email ?? '' }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <p class="text-sm">
                            @if($notification->is_read)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Read
                                </span>
                                @if($notification->read_at)
                                    <div class="text-gray-500 mt-1">
                                        Read on {{ $notification->read_at->format('M d, Y \a\t H:i') }}
                                    </div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Unread
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Metadata (if exists) -->
            @if($notification->metadata)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Additional Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-700 overflow-x-auto">{{ json_encode($notification->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
