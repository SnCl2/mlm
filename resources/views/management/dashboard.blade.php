@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<style>
  

  .shadow-card {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .text-primary {
    color: var(--primary);
  }

  .bg-primary {
    background-color: var(--primary);
  }

  .hover\:bg-primary-dark:hover {
    background-color: var(--primary-dark);
  }
</style>

<div class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="text-3xl font-bold text-primary mb-6">Welcome, {{ Auth::user()->name ?? 'Admin' }}</h1>

  {{-- KPI Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-lg shadow-card border-l-4 border-cyan-400">
      <p class="text-gray-500 text-sm">Total Users</p>
      <h2 class="text-2xl font-bold">1,234</h2>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-card border-l-4 border-blue-400">
      <p class="text-gray-500 text-sm">Registered Shops</p>
      <h2 class="text-2xl font-bold">145</h2>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-card border-l-4 border-green-400">
      <p class="text-gray-500 text-sm">Wallet Balance</p>
      <h2 class="text-2xl font-bold">₹45,230.00</h2>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-card border-l-4 border-pink-400">
      <p class="text-gray-500 text-sm">Total Transactions</p>
      <h2 class="text-2xl font-bold">₹1,23,456.78</h2>
    </div>
  </div>

  {{-- Chart + Recent Users --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <!-- Chart Placeholder -->
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Earnings Overview</h3>
      <div class="w-full h-64 bg-cyan-50 rounded flex items-center justify-center text-gray-400">
        <span>Chart (Coming Soon)</span>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Users</h3>
      <ul class="divide-y divide-gray-100">
        <li class="py-2 flex justify-between items-center">
          <span>Ravi Kumar</span>
          <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">Active</span>
        </li>
        <li class="py-2 flex justify-between items-center">
          <span>Neha Sharma</span>
          <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded">Pending</span>
        </li>
        <li class="py-2 flex justify-between items-center">
          <span>Akash Patel</span>
          <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Inactive</span>
        </li>
      </ul>
    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{-- route('users.index') --}}" class="block text-center bg-cyan-100 text-cyan-700 p-4 rounded-lg shadow hover:bg-cyan-200 transition">
      <i class="fas fa-users mr-2"></i> Manage Users
    </a>
    <a href="{{-- route('shops.index') --}}" class="block text-center bg-blue-100 text-blue-700 p-4 rounded-lg shadow hover:bg-blue-200 transition">
      <i class="fas fa-store mr-2"></i> Manage Shops
    </a>
    <a href="{{-- route('transactions.index') --}}" class="block text-center bg-green-100 text-green-700 p-4 rounded-lg shadow hover:bg-green-200 transition">
      <i class="fas fa-wallet mr-2"></i> View Transactions
    </a>
  </div>
</div>
@endsection
