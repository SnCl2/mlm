@extends('layout.app')

@section('content')

<div class="min-h-screen bg-slate-100 py-10 px-4">

    <div class="max-w-4xl mx-auto">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 mb-8 text-white">
            <h2 class="text-2xl font-semibold">Edit KYC Details</h2>
            <p class="text-sm opacity-90 mt-1">
                Update your documents and bank information
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-100 border border-emerald-300 text-emerald-800 px-5 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('kyc.update') }}"
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
                        <div class="w-36 h-36 rounded-full border-4 border-dashed border-emerald-300 flex items-center justify-center overflow-hidden bg-emerald-50">
                            <img id="profilePreview"
                                 src="{{ asset('public/storage/'.$kyc->profile_image) }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <input type="file"
                               name="profile_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(event,'profilePreview')">
                    </label>
                </div>
            </div>

            {{-- DOCUMENTS --}}
            <div class="grid md:grid-cols-2 gap-6">

                {{-- PAN --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">
                        PAN Card
                    </h3>

                    <label class="block h-36 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center cursor-pointer relative">
                        <img id="panPreview"
                             src="{{ asset('public/storage/'.$kyc->pan_card_image) }}"
                             class="absolute inset-0 w-full h-full object-contain rounded-xl">
                        <input type="file"
                               name="pan_card_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(event,'panPreview')">
                    </label>
                </div>

                {{-- PASSBOOK --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">
                        Bank Passbook
                    </h3>

                    <label class="block h-36 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center cursor-pointer relative">
                        <img id="aadharPreview"
                             src="{{ asset('public/storage/'.$kyc->aadhar_card_image) }}"
                             class="absolute inset-0 w-full h-full object-contain rounded-xl">
                        <input type="file"
                               name="aadhar_card_image"
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(event,'aadharPreview')">
                    </label>
                </div>
            </div>

            {{-- DETAILS --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="font-semibold text-slate-800 mb-6">
                    Personal & Bank Details
                </h3>

                <div class="grid md:grid-cols-2 gap-5">

                    <input name="alternate_phone" value="{{ $kyc->alternate_phone }}" placeholder="Alternate Phone" class="input" required>
                    <input name="upi_id" value="{{ $kyc->upi_id }}" placeholder="UPI ID (optional)" class="input">

                    <input name="aadhar_number" value="{{ $kyc->aadhar_number }}" placeholder="Aadhar Number" class="input" required>
                    <input name="pan_card" value="{{ $kyc->pan_card }}" placeholder="PAN Card Number" class="input" required>

                    <input name="bank_account_number" value="{{ $kyc->bank_account_number }}" placeholder="Bank Account Number" class="input" required>
                    <input name="confirm_bank_account_number" placeholder="Confirm Account Number" class="input" required>

                    <input name="ifsc_code" value="{{ $kyc->ifsc_code }}" placeholder="IFSC Code" class="input" required>
                    <input name="bank_name" value="{{ $kyc->bank_name }}" placeholder="Bank Name" class="input" required>

                    <input name="country" value="{{ $kyc->country }}" placeholder="Country" class="input" required>
                    <input name="state" value="{{ $kyc->state }}" placeholder="State" class="input" required>

                    <input name="city" value="{{ $kyc->city }}" placeholder="City" class="input" required>
                    <input name="pincode" value="{{ $kyc->pincode }}" placeholder="Pincode" class="input" required>

                    <input name="address" value="{{ $kyc->address }}" placeholder="Full Address" class="input md:col-span-2" required>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="text-center">
                <button type="submit"
                        class="px-12 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition">
                    Update KYC
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
    border:1px solid #cbd5e1;
    background:#f8fafc;
    font-size:14px;
    outline:none;
}
.input:focus{
    border-color:#10b981;
    background:#ffffff;
}
</style>

<script>
function previewImage(event, id){
    const file = event.target.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById(id).src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>

@endsection
