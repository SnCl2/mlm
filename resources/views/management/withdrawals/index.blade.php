@extends('layout.app')

@section('title', 'Withdrawal Requests')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-orange-500 mb-6">Withdrawal Requests</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form method="GET" action="{{ route('management.withdrawals') }}" class="mb-4 flex flex-wrap gap-3 items-end bg-gray-50 p-4 rounded-lg shadow">
    <div>
        <label class="block text-xs font-medium text-gray-700">Search User</label>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Name or Referral Code"
            class="border rounded px-2 py-1 w-40 focus:ring ring-blue-200" />
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">Status</label>
        <select name="status" class="border rounded px-2 py-1">
            <option value="">All</option>
            <option value="pending"   @selected(request('status')=='pending')>Pending</option>
            <option value="approved"  @selected(request('status')=='approved')>Approved</option>
            <option value="rejected"  @selected(request('status')=='rejected')>Rejected</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">From</label>
        <input type="date" name="from_date" value="{{ request('from_date') }}"
            class="border rounded px-2 py-1" />
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">To</label>
        <input type="date" name="to_date" value="{{ request('to_date') }}"
            class="border rounded px-2 py-1" />
    </div>
    <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded" type="submit">
        Filter
    </button>
    @if(request()->query())
        <a href="{{ route('management.withdrawals') }}"
            class="ml-2 text-sm underline text-gray-500">Reset</a>
    @endif
</form>

{{-- Analysis/Summary --}}
<div class="flex flex-wrap gap-6 mb-4 px-2">
    <div class="bg-green-50 text-green-700 px-4 py-2 rounded shadow text-sm">
        <span class="font-semibold">Total Amount:</span> ₹{{ number_format($totalAmount,2) }}
    </div>
    <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded shadow text-sm">
        <span class="font-semibold">Total Payable:</span> ₹{{ number_format($totalPayable,2) }}
    </div>
    <div class="bg-yellow-50 text-yellow-700 px-4 py-2 rounded shadow text-sm">
        <span class="font-semibold">Pending:</span> {{ $pendingCount }}
    </div>
    <div class="bg-green-100 text-green-900 px-4 py-2 rounded shadow text-sm">
        <span class="font-semibold">Approved:</span> {{ $approvedCount }}
    </div>
    <div class="bg-red-50 text-red-700 px-4 py-2 rounded shadow text-sm">
        <span class="font-semibold">Rejected:</span> {{ $rejectedCount }}
    </div>
</div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm bg-white shadow rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-6 py-3 text-left">User</th>
                <th class="px-6 py-3 text-left">Payment Details</th>
                <th class="px-6 py-3 text-left">Amounts</th>
                <th class="px-6 py-3 text-left">Status & Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr class="border-b hover:bg-gray-50 transition">
                    <!-- User Info grouped -->
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">
                            @if($request->user)
                                <a href="{{ route('admin.users.show', $request->user->id) }}" class="text-blue-600 hover:underline">
                                    {{ $request->user->name }}
                                </a>
                            @else
                                <span class="text-gray-400">Unknown</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">Referral: <span class="font-mono">{{ $request->user->referral_code ?? '-' }}</span></div>
                        <div class="text-xs text-gray-500 mt-0.5">Requested: {{ $request->created_at->format('Y-m-d H:i') }}</div>
                    </td>

                    <!-- Payment Details -->
                    <td class="px-6 py-4 text-xs">
                        @php $kyc = $request->user->kyc ?? null; @endphp
                        <div class="text-gray-600"><span class="font-medium">Phone:</span> {{ $kyc->alternate_phone ?? '-' }}</div>
                        <div class="text-gray-600"><span class="font-medium">Bank A/C:</span> {{ $kyc->bank_account_number ?? '-' }}</div>
                        <div class="text-gray-600"><span class="font-medium">IFSC:</span> {{ $kyc->ifsc_code ?? '-' }}</div>
                        <div class="text-gray-600"><span class="font-medium">UPI:</span> {{ $kyc->upi_id ?? '-' }}</div>
                    </td>
    
                    <!-- Amounts grouped -->
                    <td class="px-6 py-4">
                        <div>
                            <span class="font-medium text-green-700">Total:</span>
                            <span class="font-semibold">₹{{ number_format($request->total_amount, 2) }}</span>
                        </div>
                        <div class="mt-0.5">
                            <span class="font-medium text-blue-700">Payable (-18%):</span>
                            <span class="font-semibold text-green-800">
                                ₹{{ number_format($request->total_amount - ($request->total_amount * 0.18), 2) }}
                            </span>
                        </div>
                        <div class="flex gap-3 mt-0.5 text-xs">
                            <span class="text-green-600">C: ₹{{ number_format($request->cashback_amount,2) }}</span>
                            <span class="text-blue-600">R: ₹{{ number_format($request->referral_amount,2) }}</span>
                            <span class="text-purple-600">B: ₹{{ number_format($request->binary_amount,2) }}</span>
                        </div>
                    </td>
    
                    <!-- Status and Actions grouped -->
                    <td class="px-6 py-4 align-top">
                        <div class="mb-2">
                            @if($request->status === 'approved')
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Approved</span>
                            @elseif($request->status === 'rejected')
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">Rejected</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded">Pending</span>
                            @endif
                        </div>
                        <div>
                            @if($request->status === 'pending')
                                <form action="{{ route('management.withdrawals.approve', $request->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded mb-1">Approve</button>
                                </form>
                                <form action="{{ route('management.withdrawals.reject', $request->id) }}" method="POST" class="inline-block ml-1">
                                    @csrf
                                    <button class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded mb-1">Reject</button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">No action</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-gray-400">No requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>
@endsection
