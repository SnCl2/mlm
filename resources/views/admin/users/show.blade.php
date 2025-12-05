@extends('layout.app')

@section('title', 'User Overview')

@section('content')
    <!-- Main Content -->
    <header class="bg-white shadow p-4">
        <h1 class="text-xl font-bold text-cyan-500">User Details & KPIs</h1>
    </header>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 mt-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
        <div class="flex space-x-2">
            @if($user->kyc)
            {{-- Toggle KYC --}}
            @if(optional($user->kyc)->status === 'approved')
                <a href="{{ route('admin.toggle.kyc', $user->kyc->id) }}" class="p-2 rounded-lg bg-red-500 text-white shadow hover:bg-red-600">
                    Unverify KYC
                </a>
            @else
                <a href="{{ route('admin.toggle.kyc', $user->kyc->id) }}" class="p-2 rounded-lg bg-green-500 text-white shadow hover:bg-green-600">
                    Verify KYC
                </a>
            @endif
            @endif

            {{-- Toggle Active --}}
            @if($user->is_active)
                <a href="{{ route('admin.toggle.user', $user->id) }}" class="p-2 rounded-lg bg-red-600 text-white shadow hover:bg-red-700">
                    Deactivate
                </a>
            @else
                <a href="{{ route('admin.toggle.user', $user->id) }}" class="p-2 rounded-lg bg-green-600 text-white shadow hover:bg-green-700">
                    Activate
                </a>
            @endif
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                <p class="text-lg">{{ $user->email }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Phone</h4>
                <p class="text-lg">{{ $user->phone }}</p>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">KYC Status</h4>
                <p class="text-lg text-blue-600">{{ $user->kyc->status ?? 'Not Submitted' }}</p>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Account Status</h4>
                <p class="text-lg {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </p>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Referrals</h3>
                <i class="fas fa-user-plus text-cyan-300"></i>
            </div>
            <p class="text-2xl font-bold text-[var(--primary)]">{{ $user->referrals->count() }}</p>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Transactions</h3>
                <i class="fas fa-exchange-alt text-blue-300"></i>
            </div>
            <p class="text-2xl font-bold text-blue-500">{{ $user->transactions->count() }}</p>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Shop Transactions</h3>
                <i class="fas fa-shopping-cart text-green-300"></i>
            </div>
            <p class="text-2xl font-bold text-green-500">{{ $user->shopTransactions->count() }}</p>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Payout Requests</h3>
                <i class="fas fa-money-bill-wave text-purple-300"></i>
            </div>
            <p class="text-2xl font-bold text-purple-500">{{ $user->payoutRequests->count() }}</p>
        </div>
    </div>

    <!-- Wallet Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="wallet-card bg-white p-6 rounded-xl shadow-md border-l-4 border-[var(--primary)]">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Referral Wallet</h3>
                <i class="fas fa-user-plus text-cyan-300"></i>
            </div>
            <p class="text-2xl font-bold text-[var(--primary)]">₹{{ number_format($user->referralWallets->sum('amount'), 2) }}</p>
        </div>
        
        <div class="wallet-card bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Binary Wallet</h3>
                <i class="fas fa-code-branch text-blue-300"></i>
            </div>
            <p class="text-2xl font-bold text-blue-500">₹{{ number_format(optional($user->binaryWallet)->matching_amount ?? 0, 2) }}</p>
        </div>
        
        <div class="wallet-card bg-white p-6 rounded-xl shadow-md border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-600">Cashback Wallet</h3>
                <i class="fas fa-percentage text-green-300"></i>
            </div>
            <p class="text-2xl font-bold text-green-500">₹{{ number_format($user->cashbackWallets->sum('cashback_amount'), 2) }}</p>
        </div>
    </div>
    <!-- KYC Details -->
    @if($user->kyc)
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">KYC Details</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Alternate Phone</h4>
                    <p class="text-lg text-gray-700">{{ $user->kyc->alternate_phone }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Bank Account Number</h4>
                    <p class="text-lg text-gray-700">{{ $user->kyc->bank_account_number }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">IFSC Code</h4>
                    <p class="text-lg text-gray-700">{{ $user->kyc->ifsc_code }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">UPI ID</h4>
                    <p class="text-lg text-gray-700">{{ $user->kyc->upi_id }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Profile Image</h4>
                    @if($user->kyc->profile_image)
                        <img src="{{ asset('public/storage/' . $user->kyc->profile_image) }}" alt="Profile" class="w-32 rounded-md border">
                    @else
                        <p class="text-gray-400">Not Uploaded</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">PAN Card Image</h4>
                    @if($user->kyc->pan_card_image)
                        <img src="{{ asset('public/storage/' . $user->kyc->pan_card_image) }}" alt="PAN" class="w-32 rounded-md border">
                    @else
                        <p class="text-gray-400">Not Uploaded</p>
                    @endif
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Aadhar Card Image</h4>
                    @if($user->kyc->aadhar_card_image)
                        <img src="{{ asset('public/storage/' . $user->kyc->aadhar_card_image) }}" alt="Aadhar" class="w-32 rounded-md border">
                    @else
                        <p class="text-gray-400">Not Uploaded</p>
                    @endif
                </div>
            </div>
        </div>
    @endif


    
@endsection