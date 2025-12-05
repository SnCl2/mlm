@extends('layout.app')

@section('title', 'Income Settings')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary)] mb-2">💰 Income Settings</h1>
                <p class="text-gray-600">Configure referral income, binary matching income, and related parameters</p>
            </div>
            <form method="POST" action="{{ route('income-settings.reset') }}" onsubmit="return confirm('Are you sure you want to reset all settings to default values?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                    <i class="fas fa-undo mr-2"></i>Reset to Defaults
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Settings Form -->
    <form method="POST" action="{{ route('income-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Referral Income Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user-plus text-cyan-500 mr-3"></i>
                Direct Referral Income
            </h2>
            
            @php
                $referralSetting = $settings->where('key', 'referral_income_amount')->first();
            @endphp
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $referralSetting->label }}
                    </label>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                                <input 
                                    type="number" 
                                    name="settings[referral_income_amount]" 
                                    value="{{ old('settings.referral_income_amount', $referralSetting->value) }}" 
                                    step="0.01" 
                                    min="0" 
                                    required
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                >
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $referralSetting->description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Binary Matching Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-code-branch text-blue-500 mr-3"></i>
                Binary Matching Income
            </h2>
            
            @php
                $matchingIncome = $settings->where('key', 'binary_matching_income')->first();
                $pointsPerMatch = $settings->where('key', 'points_per_match')->first();
            @endphp
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $matchingIncome->label }}
                    </label>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                                <input 
                                    type="number" 
                                    name="settings[binary_matching_income]" 
                                    value="{{ old('settings.binary_matching_income', $matchingIncome->value) }}" 
                                    step="0.01" 
                                    min="0" 
                                    required
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $matchingIncome->description }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $pointsPerMatch->label }}
                    </label>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <input 
                                type="number" 
                                name="settings[points_per_match]" 
                                value="{{ old('settings.points_per_match', $pointsPerMatch->value) }}" 
                                step="1" 
                                min="1" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>
                        <span class="text-gray-600 font-medium">points</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $pointsPerMatch->description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Binary Points Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-green-500 mr-3"></i>
                Binary Points Distribution
            </h2>
            
            @php
                $pointsPerActivation = $settings->where('key', 'points_per_activation')->first();
                $uplineLevels = $settings->where('key', 'upline_chain_levels')->first();
            @endphp
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $pointsPerActivation->label }}
                    </label>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <input 
                                type="number" 
                                name="settings[points_per_activation]" 
                                value="{{ old('settings.points_per_activation', $pointsPerActivation->value) }}" 
                                step="1" 
                                min="1" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                        </div>
                        <span class="text-gray-600 font-medium">points</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $pointsPerActivation->description }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $uplineLevels->label }}
                    </label>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <input 
                                type="number" 
                                name="settings[upline_chain_levels]" 
                                value="{{ old('settings.upline_chain_levels', $uplineLevels->value) }}" 
                                step="1" 
                                min="1" 
                                max="50"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                        </div>
                        <span class="text-gray-600 font-medium">levels</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $uplineLevels->description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl shadow-md p-6 border-l-4 border-cyan-500">
            <h3 class="text-lg font-bold text-gray-800 mb-3">📊 Current Configuration Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Referral Income:</span>
                    <span class="font-bold text-cyan-600 ml-2">₹{{ number_format($referralSetting->value, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Binary Match Income:</span>
                    <span class="font-bold text-blue-600 ml-2">₹{{ number_format($matchingIncome->value, 2) }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Points per Activation:</span>
                    <span class="font-bold text-green-600 ml-2">{{ number_format($pointsPerActivation->value, 0) }} points</span>
                </div>
                <div>
                    <span class="text-gray-600">Upline Chain Levels:</span>
                    <span class="font-bold text-purple-600 ml-2">{{ number_format($uplineLevels->value, 0) }} levels</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('management.dashboard') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-white rounded-lg font-medium shadow-md">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

