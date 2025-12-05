@extends('layout.app')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-200 text-red-800 p-4 rounded mb-6">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    </div>

                    <div>
                        <label for="referral_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Referral Code
                        </label>
                        <input type="text" 
                               id="referral_code" 
                               value="{{ $user->referral_code }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100"
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">Referral code cannot be changed</p>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                </div>

                <div class="mt-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- MLM Structure -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">MLM Structure</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="referred_by" class="block text-sm font-medium text-gray-700 mb-2">
                            Referred By
                        </label>
                        <select id="referred_by" 
                                name="referred_by" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('referred_by') border-red-500 @enderror">
                            <option value="">Select Referrer</option>
                            @foreach($users as $referrer)
                                <option value="{{ $referrer->id }}" 
                                        {{ old('referred_by', $user->referred_by) == $referrer->id ? 'selected' : '' }}>
                                    {{ $referrer->name }} ({{ $referrer->referral_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="place_under" class="block text-sm font-medium text-gray-700 mb-2">
                            Place Under
                        </label>
                        <select id="place_under" 
                                name="place_under" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('place_under') border-red-500 @enderror">
                            <option value="">Select Parent</option>
                            @foreach($users as $parent)
                                <option value="{{ $parent->id }}" 
                                        {{ old('place_under', $user->place_under) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }} ({{ $parent->referral_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="placement_leg" class="block text-sm font-medium text-gray-700 mb-2">
                            Placement Leg
                        </label>
                        <select id="placement_leg" 
                                name="placement_leg" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('placement_leg') border-red-500 @enderror">
                            <option value="">Select Position</option>
                            <option value="left" {{ old('placement_leg', $user->placement_leg) == 'left' ? 'selected' : '' }}>Left</option>
                            <option value="right" {{ old('placement_leg', $user->placement_leg) == 'right' ? 'selected' : '' }}>Right</option>
                        </select>
                    </div>

                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Product
                        </label>
                        <select id="product_id" 
                                name="product_id" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('product_id') border-red-500 @enderror">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        {{ old('product_id', $user->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Status Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Status Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <label for="is_active" class="text-sm text-gray-700">
                            Active User
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_kyc_verified" 
                               name="is_kyc_verified" 
                               value="1"
                               {{ old('is_kyc_verified', $user->is_kyc_verified) ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <label for="is_kyc_verified" class="text-sm text-gray-700">
                            KYC Verified
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Update User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate placement leg when place_under is selected
    const placeUnderSelect = document.getElementById('place_under');
    const placementLegSelect = document.getElementById('placement_leg');
    
    placeUnderSelect.addEventListener('change', function() {
        if (this.value) {
            placementLegSelect.disabled = false;
        } else {
            placementLegSelect.disabled = true;
            placementLegSelect.value = '';
        }
    });
    
    // Initialize state
    if (placeUnderSelect.value) {
        placementLegSelect.disabled = false;
    } else {
        placementLegSelect.disabled = true;
    }
});
</script>
@endsection
