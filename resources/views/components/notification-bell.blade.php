@props(['guard' => 'web'])

<div class="relative" x-data="notificationBell()" x-init="init()">
    <!-- Notification Bell Button -->
    <button @click="toggleDropdown()" 
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full">
        <i class="fas fa-bell text-xl"></i>
        
        <!-- Unread Count Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
        </span>
    </button>

    <!-- Notification Dropdown -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="isOpen = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <div class="flex space-x-2">
                    <button @click="markAllAsRead()" 
                            x-show="unreadCount > 0"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Mark all read
                    </button>
                    <button @click="refreshNotifications()" 
                            class="text-sm text-gray-600 hover:text-gray-800">
                        <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <div x-show="loading" class="p-4 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>Loading notifications...
            </div>
            
            <div x-show="!loading && notifications.length === 0" class="p-4 text-center text-gray-500">
                <i class="fas fa-bell-slash text-2xl mb-2"></i>
                <p>No notifications yet</p>
            </div>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="p-4 hover:bg-gray-50 cursor-pointer" 
                         :class="{ 'bg-blue-50': !notification.is_read }"
                         @click="markAsRead(notification.id)">
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full flex items-center justify-center"
                                     :class="getIconClass(notification.type)">
                                    <i :class="getIcon(notification.type)" class="text-sm"></i>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900" x-text="notification.title"></h4>
                                    <div class="flex items-center space-x-2">
                                        <span x-show="!notification.is_read" 
                                              class="h-2 w-2 bg-blue-500 rounded-full"></span>
                                        <span class="text-xs text-gray-500" x-text="formatDate(notification.created_at)"></span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        loading: false,
        guard: '{{ $guard }}',

        init() {
            this.loadNotifications();
            // Refresh notifications every 30 seconds
            setInterval(() => {
                this.loadNotifications();
            }, 30000);
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },

        async loadNotifications() {
            this.loading = true;
            try {
                // Don't pass guard parameter, let the controller auto-detect
                const response = await fetch('/notifications');
                console.log('Notification API Response:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Notification Data:', data);
                
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            } catch (error) {
                console.error('Error loading notifications:', error);
                this.notifications = [];
                this.unreadCount = 0;
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });
                
                if (response.ok) {
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });
                
                if (response.ok) {
                    this.notifications.forEach(notification => {
                        notification.is_read = true;
                    });
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        },

        async refreshNotifications() {
            await this.loadNotifications();
        },

        getIcon(type) {
            const icons = {
                'info': 'fas fa-info-circle',
                'success': 'fas fa-check-circle',
                'warning': 'fas fa-exclamation-triangle',
                'error': 'fas fa-times-circle',
                'announcement': 'fas fa-bullhorn'
            };
            return icons[type] || 'fas fa-bell';
        },

        getIconClass(type) {
            const classes = {
                'info': 'bg-blue-100 text-blue-600',
                'success': 'bg-green-100 text-green-600',
                'warning': 'bg-yellow-100 text-yellow-600',
                'error': 'bg-red-100 text-red-600',
                'announcement': 'bg-purple-100 text-purple-600'
            };
            return classes[type] || 'bg-gray-100 text-gray-600';
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
            if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
            return date.toLocaleDateString();
        }
    }
}
</script>
