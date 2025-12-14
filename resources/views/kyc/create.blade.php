@extends('layout.app')

@section('content')

<div class="min-h-screen bg-slate-50 py-10 px-4">

    <div class="max-w-4xl mx-auto">

        {{-- TOP HEADER CARD --}}
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 mb-8 text-white">
            <h2 class="text-2xl font-semibold">KYC Verification</h2>
            <p class="text-sm opacity-90 mt-1">
                Complete your identity verification to activate withdrawals
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-100 border border-emerald-300 text-emerald-800 px-5 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('kyc.store') }}"
              enctype="multipart/form-data"
              class="space-y-8">
            @csrf

            {{-- PROFILE IMAGE --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-4">
                    Profile Photo
                </h3>

                <div class="flex justify-center">
                    <label class="cursor-pointer">
                        <div class="w-36 h-36 rounded-full border-4 border-dashed border-indigo-300 flex items-center justify-center overflow-hidden bg-indigo-50">
                            <img id="profilePreview" class="hidden w-full h-full object-cover">
                            <span id="profilePlaceholder" class="text-indigo-500 text-sm text-center">
                                Upload<br>Photo
                            </span>
                        </div>
                        <input type="file"
                               name="profile_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(event,'profilePreview','profilePlaceholder')">
                    </label>
                </div>
            </div>

            {{-- DOCUMENT UPLOADS --}}
            <div class="grid md:grid-cols-2 gap-6">

                {{-- PAN --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">PAN Card</h3>

                    <label class="block h-36 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center cursor-pointer relative">
                        <span id="panPlaceholder" class="text-slate-500">
                            Upload PAN Image
                        </span>
                        <img id="panPreview" class="hidden absolute inset-0 w-full h-full object-contain rounded-xl">
                        <input type="file"
                               name="pan_card_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(event,'panPreview','panPlaceholder')">
                    </label>
                </div>

                {{-- AADHAR / PASSBOOK --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">Bank Passbook</h3>

                    <label class="block h-36 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center cursor-pointer relative">
                        <span id="aadharPlaceholder" class="text-slate-500">
                            Upload Passbook Image
                        </span>
                        <img id="aadharPreview" class="hidden absolute inset-0 w-full h-full object-contain rounded-xl">
                        <input type="file"
                               name="aadhar_card_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(event,'aadharPreview','aadharPlaceholder')">
                    </label>
                </div>
            </div>

            {{-- PERSONAL & BANK DETAILS --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-6">
                    Personal & Bank Details
                </h3>

                <div class="grid md:grid-cols-2 gap-5">
                    <input name="alternate_phone" placeholder="Alternate Phone Number" class="input" required>
                    <input name="upi_id" placeholder="UPI ID (optional)" class="input">

                    <input name="aadhar_number" placeholder="Aadhar Number" class="input" required>
                    <input name="pan_card" placeholder="PAN Card Number" class="input" required>

                    <input name="bank_account_number" placeholder="Bank Account Number" class="input" required>
                    <input name="confirm_bank_account_number" placeholder="Confirm Account Number" class="input" required>

                    <input name="ifsc_code" placeholder="IFSC Code" class="input" required>
                    <input name="bank_name" placeholder="Bank Name" class="input" required>

                    <input name="country" placeholder="Country" class="input" required>
                    <input name="state" placeholder="State" class="input" required>

                    <input name="city" placeholder="City" class="input" required>
                    <input name="pincode" placeholder="Pincode" class="input" required>

                    <input name="address" placeholder="Full Address" class="input md:col-span-2" required>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="text-center">
                <button type="submit"
                        class="px-10 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                    Submit KYC
                </button>
            </div>

        </form>
    </div>
</div>

{{-- INPUT STYLE --}}
<style>
    .input{
        width:100%;
        padding:12px 14px;
        border-radius:12px;
        border:1px solid #cbd5f5;
        background:#f8fafc;
        font-size:14px;
        outline:none;
    }
    .input:focus{
        border-color:#6366f1;
        background:#ffffff;
    }
</style>

<script>
function previewImage(event, previewId, placeholderId){
    const file = event.target.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById(previewId);
        const ph  = document.getElementById(placeholderId);
        img.src = e.target.result;
        img.classList.remove('hidden');
        if(ph) ph.classList.add('hidden');
    };
    reader.readAsDataURL(file);
}
</script>

@endsection
