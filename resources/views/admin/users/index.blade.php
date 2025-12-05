@extends('layout.app')

@section('title', 'Users')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center py-6">
        <h1 class="text-2xl font-bold text-gray-800">All Users</h1>

    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Phone</th>
                    <th class="px-6 py-3 text-left">Referred By</th>
                    <th class="px-6 py-3 text-left">Left Downline</th> <!-- New Column -->
                    <th class="px-6 py-3 text-left">Right Downline</th> <!-- New Column -->
                    <th class="px-6 py-3 text-left">KYC</th>
                    <th class="px-6 py-3 text-left">Active</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr data-user-id="{{ $user->id }}">
                        <td class="px-6 py-4">{{ $users->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4 font-medium">
                            {{ $user->name }}<br>
                            {{ $user->product?->name ?? 'No Product' }}
                        </td>

                        <td class="px-6 py-4">{{ $user->phone }}</td>
                        <td class="px-6 py-4">
                            {{ optional($user->referredBy)->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 left-downline">
                            <span class="text-gray-500">Loading...</span>
                        </td>
                        <td class="px-6 py-4 right-downline">
                            <span class="text-gray-500">Loading...</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full {{ $user->is_kyc_verified ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $user->is_kyc_verified ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-wrap gap-1 justify-end">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 hover:text-green-800 hover:underline">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @if($user->kyc)
                                    <button onclick="toggleKycStatus({{ $user->kyc->id }})" 
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium {{ $user->is_kyc_verified ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline">
                                        <i class="fas fa-{{ $user->is_kyc_verified ? 'times' : 'check' }} mr-1"></i>
                                        {{ $user->is_kyc_verified ? 'Reject KYC' : 'Approve KYC' }}
                                    </button>
                                @endif
                                <button onclick="toggleUserStatus({{ $user->id }})" 
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium {{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:underline">
                                    <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }} mr-1"></i>
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-gray-500 py-6">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Styled Pagination Links -->
    <div class="mt-6 flex justify-center">
        {{ $users->links('pagination::tailwind') }} <!-- Assuming Tailwind pagination; customize as needed -->
    </div>
</div>
@endsection

@section('styles')
    <style>
        /* Custom Pagination Styles (unchanged from previous) */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .pagination .flex {
            display: flex;
            gap: 0.25rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            background-color: #ffffff;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background-color: #f97316;
            color: #ffffff;
            border-color: #f97316;
        }

        .pagination .active a {
            background-color: #f97316;
            color: #ffffff;
            border-color: #f97316;
            font-weight: bold;
        }

        .pagination .disabled a {
            cursor: not-allowed;
            opacity: 0.5;
        }

        @media (max-width: 640px) {
            .pagination {
                flex-wrap: wrap;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tr[data-user-id]');
            
            rows.forEach(row => {
                const userId = row.dataset.userId;
                const leftCell = row.querySelector('.left-downline');
                const rightCell = row.querySelector('.right-downline');
                
                // Make AJAX request immediately
                fetch(`{{ url('/admin/users') }}/${userId}/downline`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Fill left downline
                        leftCell.innerHTML = `
                            Total: ${data.left.total}<br>
                            <span class="text-green-600">Active: ${data.left.active}</span><br>
                            <span class="text-red-600">Inactive: ${data.left.inactive}</span>
                        `;
                        
                        // Fill right downline
                        rightCell.innerHTML = `
                            Total: ${data.right.total}<br>
                            <span class="text-green-600">Active: ${data.right.active}</span><br>
                            <span class="text-red-600">Inactive: ${data.right.inactive}</span>
                        `;
                    })
                    .catch(error => {
                        console.error('Error fetching downline info for user ' + userId + ':', error);
                        leftCell.innerHTML = '<span class="text-red-500">Error loading</span>';
                        rightCell.innerHTML = '<span class="text-red-500">Error loading</span>';
                    });
            });
        });

        function toggleUserStatus(userId) {
            if (confirm('Are you sure you want to toggle this user\'s status?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = `{{ url('/admin/toggle-user') }}/${userId}`;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function toggleKycStatus(kycId) {
            if (confirm('Are you sure you want to toggle this user\'s KYC status?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = `{{ url('/admin/toggle-kyc') }}/${kycId}`;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
