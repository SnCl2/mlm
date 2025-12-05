@extends('layout.app')

@section('title', 'My Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Notifications</h1>
            <p class="text-gray-600 mt-2">Stay updated with the latest information</p>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <button onclick="markAllAsRead()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-check-double mr-2"></i>Mark All as Read
                    </button>
                    <button onclick="refreshNotifications()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="unread-count">0</span> unread notifications
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div id="notifications-container">
                <!-- Loading state -->
                <div id="loading-state" class="p-8 text-center">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Loading notifications...</p>
                </div>

                <!-- Empty state -->
                <div id="empty-state" class="hidden p-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-500">You'll see notifications here when they arrive.</p>
                </div>

                <!-- Notifications list -->
                <div id="notifications-list" class="hidden">
                    <!-- Notifications will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let notifications = [];
let unreadCount = 0;

// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});

async function loadNotifications() {
    try {
        const response = await fetch('/notifications');
        const data = await response.json();
        
        notifications = data.notifications || [];
        unreadCount = data.unread_count || 0;
        
        updateUI();
    } catch (error) {
        console.error('Error loading notifications:', error);
        showError('Failed to load notifications');
    }
}

function updateUI() {
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    const notificationsList = document.getElementById('notifications-list');
    const unreadCountElement = document.getElementById('unread-count');
    
    // Update unread count
    unreadCountElement.textContent = unreadCount;
    
    // Hide loading state
    loadingState.classList.add('hidden');
    
    if (notifications.length === 0) {
        emptyState.classList.remove('hidden');
        notificationsList.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        notificationsList.classList.remove('hidden');
        renderNotifications();
    }
}

function renderNotifications() {
    const container = document.getElementById('notifications-list');
    
    if (notifications.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    const html = notifications.map(notification => `
        <div class="border-b border-gray-200 last:border-b-0 ${!notification.is_read ? 'bg-blue-50' : ''}">
            <div class="p-6 hover:bg-gray-50 cursor-pointer" onclick="markAsRead(${notification.id})">
                <div class="flex items-start space-x-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center ${getIconClass(notification.type)}">
                            <i class="${getIcon(notification.type)} text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">${notification.title}</h3>
                            <div class="flex items-center space-x-2">
                                ${!notification.is_read ? '<span class="h-2 w-2 bg-blue-500 rounded-full"></span>' : ''}
                                <span class="text-sm text-gray-500">${formatDate(notification.created_at)}</span>
                            </div>
                        </div>
                        <p class="text-gray-600 mt-1 whitespace-pre-wrap">${notification.message}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeBadgeClass(notification.type)}">
                                ${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            const notification = notifications.find(n => n.id === notificationId);
            if (notification && !notification.is_read) {
                notification.is_read = true;
                unreadCount = Math.max(0, unreadCount - 1);
                updateUI();
            }
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
        showError('Failed to mark notification as read');
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            notifications.forEach(notification => {
                notification.is_read = true;
            });
            unreadCount = 0;
            updateUI();
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
        showError('Failed to mark all notifications as read');
    }
}

function refreshNotifications() {
    loadNotifications();
}

function getIcon(type) {
    const icons = {
        'info': 'fas fa-info-circle',
        'success': 'fas fa-check-circle',
        'warning': 'fas fa-exclamation-triangle',
        'error': 'fas fa-times-circle',
        'announcement': 'fas fa-bullhorn'
    };
    return icons[type] || 'fas fa-bell';
}

function getIconClass(type) {
    const classes = {
        'info': 'bg-blue-100 text-blue-600',
        'success': 'bg-green-100 text-green-600',
        'warning': 'bg-yellow-100 text-yellow-600',
        'error': 'bg-red-100 text-red-600',
        'announcement': 'bg-purple-100 text-purple-600'
    };
    return classes[type] || 'bg-gray-100 text-gray-600';
}

function getTypeBadgeClass(type) {
    const classes = {
        'info': 'bg-blue-100 text-blue-800',
        'success': 'bg-green-100 text-green-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'error': 'bg-red-100 text-red-800',
        'announcement': 'bg-purple-100 text-purple-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return date.toLocaleDateString();
}

function showError(message) {
    // You can implement a toast notification here
    alert(message);
}
</script>
@endsection
