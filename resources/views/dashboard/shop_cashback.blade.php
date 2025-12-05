@extends('layout.app')

@section('title', 'Shop Cashback')


@section('content')

  <div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-[var(--primary)] mb-6 flex items-center">
      <i class="fas fa-store mr-3"></i> Shop Cashback
    </h1>

    <!-- Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Cashback Collected</h2>
        <p class="text-2xl font-bold text-[var(--primary)]">₹ {{ number_format($totalCashback, 2) }}</p>
      </div>
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Transactions</h2>
        <p class="text-2xl font-bold text-[var(--primary)]">{{ $totalTransactions }}</p>
      </div>
      <div class="bg-white shadow rounded-lg p-6 text-center">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Your QR Code</h2>
        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="mx-auto">
        <p class="mt-2 text-sm text-gray-500">Scan to earn cashback</p>
      </div>
    </div>

    <!-- Purchase History -->
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-receipt mr-2 text-[var(--primary)]"></i> Purchase History
      </h2>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead>
            <tr class="bg-[var(--primary-light)] text-gray-700">
              <th class="px-4 py-2 text-left">Date</th>
              <th class="px-4 py-2 text-left">Store</th>
              <th class="px-4 py-2 text-right">Amount (₹)</th>

            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach ($transactions as $txn)
            <tr>
              <td class="px-4 py-2">{{ $txn->created_at->format('Y-m-d') }}</td>
              <td class="px-4 py-2">{{ $txn->shop->name ?? 'N/A' }}</td>
              <td class="px-4 py-2 text-right">{{ $txn->purchase_amount }}</td>

            </tr>
            @endforeach

            <!-- Add more rows as needed -->
          </tbody>
        </table>
      </div>
    </div>
  </div>


@endsection