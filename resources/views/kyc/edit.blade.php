@extends('layout.app')

@section('content')
<div class="max-w-xl mx-auto p-4">
    <h2 class="text-2xl font-bold text-[var(--primary)] mb-6 text-center">Edit KYC</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('kyc.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Profile --}}
        <div class="flex justify-center">
            <label for="profileImage" class="cursor-pointer">
                <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                    <img id="profilePreview" src="{{ asset('public/storage/' . $kyc->profile_image) }}" class="h-full w-full object-cover" />
                </div>
            </label>
            <input id="profileImage" name="profile_image" type="file" accept="image/*" class="hidden" onchange="previewImage(event, 'profilePreview', null)">
        </div>

        {{-- PAN --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card</label>
            <label for="panUpload" class="relative block w-full h-32 bg-gray-100 border border-dashed border-gray-400 rounded-lg flex items-center justify-center cursor-pointer">
                <img id="panPreview" src="{{ asset('public/storage/' . $kyc->pan_card_image) }}" class="h-full object-contain w-full rounded-lg" />
            </label>
            <input id="panUpload" name="pan_card_image" type="file" accept="image/*" class="hidden" onchange="previewImage(event, 'panPreview', null)">
        </div>

        {{-- Aadhar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bank Pass Book</label>
            <label for="aadharUpload" class="relative block w-full h-32 bg-gray-100 border border-dashed border-gray-400 rounded-lg flex items-center justify-center cursor-pointer">
                <img id="aadharPreview" src="{{ asset('public/storage/' . $kyc->aadhar_card_image) }}" class="h-full object-contain w-full rounded-lg" />
            </label>
            <input id="aadharUpload" name="aadhar_card_image" type="file" accept="image/*" class="hidden" onchange="previewImage(event, 'aadharPreview', null)">
        </div>

        {{-- Other Inputs --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Alternative Phone Number</label>
            <input name="alternate_phone" type="text" value="{{ $kyc->alternate_phone }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bank Account Number</label>
            <input name="bank_account_number" type="text" value="{{ $kyc->bank_account_number }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Bank Account Number</label>
            <input name="confirm_bank_account_number" type="text" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">IFSC Code</label>
            <input name="ifsc_code" type="text" value="{{ $kyc->ifsc_code }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">UPI ID</label>
            <input name="upi_id" type="text" value="{{ $kyc->upi_id }}" class="w-full border border-gray-300 p-2 rounded">
        </div>

        {{-- NEW FIELDS --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Aadhar Number</label>
            <input name="aadhar_number" type="text" value="{{ $kyc->aadhar_number }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">PAN Card Number</label>
            <input name="pan_card" type="text" value="{{ $kyc->pan_card }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bank Name</label>
            <input name="bank_name" type="text" value="{{ $kyc->bank_name }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Country</label>
            <input name="country" type="text" value="{{ $kyc->country }}" class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">State</label>
                <input name="state" type="text" value="{{ $kyc->state }}" class="w-full border border-gray-300 p-2 rounded" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">City</label>
                <input name="city" type="text" value="{{ $kyc->city }}" class="w-full border border-gray-300 p-2 rounded" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pincode</label>
                <input name="pincode" type="text" value="{{ $kyc->pincode }}" class="w-full border border-gray-300 p-2 rounded" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                <input name="address" type="text" value="{{ $kyc->address }}" class="w-full border border-gray-300 p-2 rounded" required>
            </div>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">Update KYC</button>
    </form>
</div>

<script>
    function previewImage(event, previewId, placeholderId) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholderId) {
                    const placeholder = document.getElementById(placeholderId);
                    if (placeholder) placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
