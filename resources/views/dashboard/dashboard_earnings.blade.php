@extends('layout.app')

@section('title', 'Earnings')

@section('content')

<!-- Page Header -->
<header class="bg-white shadow p-4 mb-6">
    <h1 class="text-xl font-bold text-cyan-500">Referral Dashboard</h1>
</header>

<!-- Wallet Overview -->
<section class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">💰 Earnings Overview</h2>
        <div class="flex space-x-2">
            <button class="p-2 bg-white shadow rounded-lg text-gray-600 hover:bg-gray-50"><i class="fas fa-filter"></i></button>
            <button class="p-2 bg-white shadow rounded-lg text-gray-600 hover:bg-gray-50"><i class="fas fa-download"></i></button>
        </div>
    </div>

    @php
        $wallets = [
            ['title' => 'Referral Wallet', 'amount' => $referral, 'icon' => 'fa-user-plus', 'color' => 'cyan-300', 'border' => 'var(--primary)', 'text' => 'var(--primary)'],
            ['title' => 'Binary Wallet', 'amount' => $binary, 'icon' => 'fa-code-branch', 'color' => 'blue-300', 'border' => 'blue-500', 'text' => 'blue-500'],
            ['title' => 'Cashback Wallet', 'amount' => $cashback, 'icon' => 'fa-percentage', 'color' => 'green-300', 'border' => 'green-500', 'text' => 'green-500'],
            ['title' => 'Left Points', 'amount' => $leftPoints, 'icon' => 'fa-arrow-left', 'color' => 'indigo-300', 'border' => 'indigo-500', 'text' => 'indigo-500'],
            ['title' => 'Right Points', 'amount' => $rightPoints, 'icon' => 'fa-arrow-right', 'color' => 'purple-300', 'border' => 'purple-500', 'text' => 'purple-500'],
            ['title' => 'Left Cash Points', 'amount' => $leftPoints_C, 'icon' => 'fa-arrow-left', 'color' => 'indigo-300', 'border' => 'indigo-500', 'text' => 'indigo-500'],
            ['title' => 'Right Cash Points', 'amount' => $rightPoints_C, 'icon' => 'fa-arrow-right', 'color' => 'purple-300', 'border' => 'purple-500', 'text' => 'purple-500'],
        ];
    @endphp
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($wallets as $wallet)
        <div class="bg-white p-6 rounded-xl shadow-md border-l-4" style="border-color: {{ $wallet['border'] }}">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">{{ $wallet['title'] }}</h3>
                <i class="fas {{ $wallet['icon'] }} text-{{ $wallet['color'] }}"></i>
            </div>
            <p class="text-2xl font-bold text-{{ $wallet['text'] }}">₹{{ number_format($wallet['amount'], 2) }}</p>
        </div>
        @endforeach
    </div>

</section>
<!-- Withdrawal Section -->
<section class="mb-6">
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-2xl font-bold text-cyan-500 mb-6">Withdrawal Request</h2>

        <!-- Alert Messages -->
        @foreach (['success', 'error'] as $msg)
            @if(session($msg))
                <div class="mb-4 px-4 py-3 rounded {{ $msg == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach
        <!-- Form -->
        <form method="POST" action="{{ route('withdrawal.request') }}" class="space-y-5">
            @csrf

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cashback Wallet</label>
                    <input type="number" name="cashback_amount" min="0" step="0.01"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                           placeholder="₹0.00">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referral Wallet</label>
                    <input type="number" name="referral_amount" min="0" step="0.01"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                           placeholder="₹0.00">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Binary Wallet</label>
                    <input type="number" name="binary_amount" min="0" step="0.01"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                           placeholder="₹0.00">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note (Optional)</label>
                <textarea name="note" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                          placeholder="Write a note..."></textarea>
            </div>

            <div>
                <button type="submit"
                        class="w-full sm:w-auto bg-cyan-500 text-white px-6 py-2 rounded hover:bg-cyan-600 transition">
                    Submit Withdrawal Request
                </button>
            </div>
        </form>
    </div>
</section>
<!-- Recent Transactions -->
<section class="mb-6 bg-white rounded-xl shadow-md">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Recent Transactions</h3>
        <a href="#" class="text-sm text-[var(--primary)] hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="py-3 px-4 text-left text-gray-600 font-medium">Type</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium">Description</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium">Amount</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium">Date</th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $txn)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-percentage text-green-500 text-xs"></i>
                            </div>
                            <span>Cashback</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-gray-500">Purchase from shop #{{ $txn->shop_id }}</td>
                    <td class="py-3 px-4 font-medium text-green-500">+₹{{ number_format($txn->commission_amount, 2) }}</td>
                    <td class="py-3 px-4 text-gray-500">{{ $txn->created_at->format('Y-m-d') }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Completed</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- Quick Stats -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
    @php
        $stats = [
            ['label' => 'Total Referrals', 'value' => 42, 'color' => 'var(--primary)', 'percent' => 70],
            ['label' => 'Active Team', 'value' => 128, 'color' => 'blue-500', 'percent' => 85],
            ['label' => 'Cashback Earned', 'value' => 1200, 'color' => 'green-500', 'percent' => 60],
        ];
    @endphp

    @foreach($stats as $stat)
    <!--<div class="bg-white p-4 rounded-xl shadow-md">-->
    <!--    <h4 class="text-sm font-medium text-gray-500 mb-1">{{ $stat['label'] }}</h4>-->
    <!--    <p class="text-xl font-bold">₹{{ $stat['value'] }}</p>-->
    <!--    <div class="h-2 bg-gray-100 rounded-full mt-2">-->
    <!--        <div class="h-2 rounded-full" style="width: {{ $stat['percent'] }}%; background-color: {{ $stat['color'] }}"></div>-->
    <!--    </div>-->
    <!--</div>-->
    @endforeach
</section>

<!-- Withdrawal Section -->



@endsection
