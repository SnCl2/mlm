@extends('layout.app')

@section('title', 'Income Report')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 8px 0;
    }
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    .progress-bar {
        height: 8px;
        border-radius: 4px;
        background: #e5e7eb;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:px-8">
    <!-- Page Header -->
    <header class="bg-white shadow p-4 mb-6 rounded-lg">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-cyan-500">📊 Income Report Analysis</h1>
            <form method="GET" action="{{ route('income.report') }}" class="flex gap-2">
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="px-3 py-2 border rounded-lg">
                <input type="date" name="date_to" value="{{ $dateTo }}" class="px-3 py-2 border rounded-lg">
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600">Filter</button>
            </form>
        </div>
        <p class="text-gray-600 mt-2">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</p>
    </header>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Income -->
        <div class="stat-card border-l-4 border-cyan-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Income</p>
                    <p class="stat-value text-cyan-600">₹{{ number_format($combinedStats['total_income'], 2) }}</p>
                </div>
                <div class="text-4xl text-cyan-200">💰</div>
            </div>
        </div>

        <!-- Referral Income -->
        <div class="stat-card border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Referral Income</p>
                    <p class="stat-value text-blue-600">₹{{ number_format($referralStats['total_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500">{{ $referralStats['total_transactions'] }} transactions</p>
                </div>
                <div class="text-4xl text-blue-200">👥</div>
            </div>
        </div>

        <!-- Binary Matching Income -->
        <div class="stat-card border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Binary Matching Income</p>
                    <p class="stat-value text-green-600">₹{{ number_format($binaryStats['total_matching_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500">{{ $binaryStats['potential_matches'] }} potential matches</p>
                </div>
                <div class="text-4xl text-green-200">⚡</div>
            </div>
        </div>

        <!-- Average per Transaction -->
        <div class="stat-card border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Avg. Referral Income</p>
                    <p class="stat-value text-purple-600">₹{{ number_format($referralStats['average_per_transaction'], 2) }}</p>
                    <p class="text-xs text-gray-500">per transaction</p>
                </div>
                <div class="text-4xl text-purple-200">📈</div>
            </div>
        </div>
    </div>

    <!-- Income Distribution -->
    <div class="chart-container mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Income Distribution</h2>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Referral Income</span>
                    <span class="text-sm font-bold text-blue-600">{{ number_format($combinedStats['referral_percentage'], 1) }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill bg-blue-500" style="width: {{ $combinedStats['referral_percentage'] }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Binary Matching Income</span>
                    <span class="text-sm font-bold text-green-600">{{ number_format($combinedStats['binary_percentage'], 1) }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill bg-green-500" style="width: {{ $combinedStats['binary_percentage'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Referral Income Details -->
        <div class="chart-container">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="mr-2">👥</span> Referral Income Analysis
            </h2>
            
            <div class="space-y-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $referralStats['total_transactions'] }}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Average per Transaction</p>
                    <p class="text-2xl font-bold text-blue-600">₹{{ number_format($referralStats['average_per_transaction'], 2) }}</p>
                </div>
            </div>

            @if($referralStats['daily_breakdown']->count() > 0)
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Daily Breakdown</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-right">Amount</th>
                            <th class="px-4 py-2 text-right">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referralStats['daily_breakdown']->take(10) as $day)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                            <td class="px-4 py-2 text-right font-medium text-blue-600">₹{{ number_format($day['amount'], 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ $day['count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">No referral income data for this period</p>
            @endif
        </div>

        <!-- Binary Matching Income Details -->
        <div class="chart-container">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="mr-2">⚡</span> Binary Matching Income Analysis
            </h2>
            
            <div class="space-y-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Total Matching Income</p>
                    <p class="text-2xl font-bold text-green-600">₹{{ number_format($binaryStats['total_matching_amount'], 2) }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">Potential Matches Available</p>
                    <p class="text-2xl font-bold text-green-600">{{ $binaryStats['potential_matches'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Potential Income: ₹{{ number_format($binaryStats['potential_income'], 2) }}</p>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-700 mb-3">Current Binary Points</h3>
            <div class="space-y-3">
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Left Points</span>
                        <span class="text-lg font-bold text-indigo-600">{{ number_format($binaryStats['current_left_points'], 0) }}</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        Matched: {{ number_format($binaryStats['current_left_points'] - $binaryStats['unmatched_left'], 0) }} | 
                        Unmatched: {{ number_format($binaryStats['unmatched_left'], 0) }}
                    </div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Right Points</span>
                        <span class="text-lg font-bold text-purple-600">{{ number_format($binaryStats['current_right_points'], 0) }}</span>
                    </div>
                    <div class="text-xs text-gray-500">
                        Matched: {{ number_format($binaryStats['current_right_points'] - $binaryStats['unmatched_right'], 0) }} | 
                        Unmatched: {{ number_format($binaryStats['unmatched_right'], 0) }}
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                <p class="text-sm font-medium text-yellow-800">💡 How Binary Matching Works:</p>
                <ul class="text-xs text-yellow-700 mt-2 space-y-1 list-disc list-inside">
                    <li>100 points on left + 100 points on right = 1 match</li>
                    <li>Each match earns ₹200</li>
                    <li>Matching runs automatically via cron job</li>
                    <li>Unmatched points carry forward for future matches</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    @if($referralStats['monthly_breakdown']->count() > 0)
    <div class="chart-container mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Income Breakdown</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">Month</th>
                        <th class="px-4 py-3 text-right">Referral Income</th>
                        <th class="px-4 py-3 text-right">Transactions</th>
                        <th class="px-4 py-3 text-right">Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referralStats['monthly_breakdown'] as $month)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $month['month'] }}</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600">₹{{ number_format($month['amount'], 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $month['count'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">₹{{ number_format($month['amount'] / max($month['count'], 1), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Referral Transactions -->
    @if($referralIncome->count() > 0)
    <div class="chart-container">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Referral Income Transactions</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">New User</th>
                        <th class="px-4 py-3 text-left">Parent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referralIncome->take(20) as $transaction)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 font-bold text-blue-600">₹{{ number_format($transaction->amount, 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($transaction->new_user_id)
                                User #{{ $transaction->new_user_id }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($transaction->parent_id)
                                User #{{ $transaction->parent_id }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Key Insights -->
    <div class="chart-container mt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Key Insights</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                <h3 class="font-semibold text-blue-800 mb-2">Referral Income</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Earned ₹{{ number_format($referralStats['total_amount'], 2) }} from {{ $referralStats['total_transactions'] }} referrals</li>
                    <li>• Average: ₹{{ number_format($referralStats['average_per_transaction'], 2) }} per referral</li>
                    <li>• Represents {{ number_format($combinedStats['referral_percentage'], 1) }}% of total income</li>
                </ul>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
                <h3 class="font-semibold text-green-800 mb-2">Binary Matching Income</h3>
                <ul class="text-sm text-green-700 space-y-1">
                    <li>• Earned ₹{{ number_format($binaryStats['total_matching_amount'], 2) }} from binary matches</li>
                    <li>• {{ $binaryStats['potential_matches'] }} potential matches available (₹{{ number_format($binaryStats['potential_income'], 2) }})</li>
                    <li>• Represents {{ number_format($combinedStats['binary_percentage'], 1) }}% of total income</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

