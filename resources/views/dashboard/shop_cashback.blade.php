@extends('layout.app')

@section('title', 'Shop Cashback')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-store text-[var(--primary)]"></i>
                Shop Cashback
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Track your cashback earnings and purchase history
            </p>
        </div>

        <a href="{{ url()->previous() }}"
           class="text-sm text-[var(--primary)] hover:underline font-medium">
            ← Back
        </a>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        {{-- TOTAL CASHBACK --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Total Cashback</span>
                <i class="fas fa-wallet text-[var(--primary)]"></i>
            </div>
            <div class="mt-4 text-3xl font-bold text-gray-900">
                ₹ {{ number_format($totalCashback, 2) }}
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Lifetime cashback earned
            </p>
        </div>

        {{-- TOTAL TRANSACTIONS --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Total Transactions</span>
                <i class="fas fa-receipt text-[var(--primary)]"></i>
            </div>
            <div class="mt-4 text-3xl font-bold text-gray-900">
                {{ $totalTransactions }}
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Completed purchases
            </p>
        </div>

        {{-- QR CODE --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
            <div class="text-sm text-gray-500 mb-3">
                Your Cashback QR
            </div>
            <img src="{{ $qrCodeUrl }}"
                 alt="QR Code"
                 class="mx-auto w-32 h-32 rounded-lg border">
            <p class="text-xs text-gray-500 mt-3">
                Scan at shop to earn cashback
            </p>
        </div>

    </div>

    {{-- PURCHASE HISTORY --}}
    <div class="bg-white border border-gray-200 rounded-xl">

        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-receipt text-[var(--primary)]"></i>
                Purchase History
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Store</th>
                        <th class="px-6 py-3 text-right">Amount (₹)</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($transactions as $txn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-700">
                                {{ $txn->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $txn->shop->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                ₹ {{ number_format($txn->purchase_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection
