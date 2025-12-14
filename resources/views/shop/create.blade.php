@extends('layout.app')

@section('title', isset($shop) ? 'Edit Shop' : 'Create Shop')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- HEADER BAR --}}
    <div class="flex items-center justify-between bg-slate-800 text-white px-6 py-4 rounded-2xl mb-6">
        <div>
            <h1 class="text-xl font-semibold">
                {{ isset($shop) ? 'Edit Shop Details' : 'Create New Shop' }}
            </h1>
            <p class="text-sm opacity-80">
                Shop & owner verification details
            </p>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ isset($shop) ? route('shop.update', $shop->id) : route('shop.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf
        @if(isset($shop))
            @method('PUT')
        @endif

        {{-- BASIC INFO --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase">
                Shop Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="name"
                       value="{{ old('name', $shop->name ?? '') }}"
                       placeholder="Shop Name"
                       class="input" required>

                <input type="text" name="category"
                       value="{{ old('category', $shop->category ?? '') }}"
                       placeholder="Category"
                       class="input">

                <input type="text" name="owner_name"
                       value="{{ old('owner_name', $shop->owner_name ?? '') }}"
                       placeholder="Owner Name"
                       class="input" required>
            </div>
        </div>

        {{-- CONTACT --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase">
                Contact & Address
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="phone"
                       value="{{ old('phone', $shop->phone ?? '') }}"
                       placeholder="Phone Number"
                       class="input" required>

                <input type="email" name="email"
                       value="{{ old('email', $shop->email ?? '') }}"
                       placeholder="Email Address"
                       class="input"
                       {{ isset($shop) ? 'readonly' : 'required' }}>

                <input type="number" name="commission_rate" step="0.01"
                       value="{{ old('commission_rate', $shop->commission_rate ?? '10.00') }}"
                       placeholder="Commission (%)"
                       class="input">
            </div>

            <textarea name="address"
                      placeholder="Full Address"
                      class="input mt-4 h-20 resize-none"
                      required>{{ old('address', $shop->address ?? '') }}</textarea>

            @if(!isset($shop))
                <p class="text-xs text-indigo-600 italic mt-2">
                    Password will be auto-generated and sent to the email.
                </p>
            @endif
        </div>

        {{-- KYC --}}
        <div>
            <h2 class="text-sm font-semibold text-slate-700 mb-3 uppercase">
                KYC Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="aadhar_number" maxlength="12"
                       value="{{ old('aadhar_number', $shop->aadhar_number ?? '') }}"
                       placeholder="Aadhar Number"
                       class="input" required>

                <input type="text" name="pan_number" maxlength="10"
                       value="{{ old('pan_number', $shop->pan_number ?? '') }}"
                       placeholder="PAN Number"
                       class="input" required>

                <div>
                    <label class="file-box">
                        <span>Aadhar Image</span>
                        <input type="file" name="aadhar_image" accept="image/*" class="hidden">
                    </label>
                    @if(isset($shop) && $shop->aadhar_image_path)
                        <img src="{{ asset('public/storage/'.$shop->aadhar_image_path) }}"
                             class="mt-2 h-20 rounded-lg border">
                    @endif
                </div>

                <div>
                    <label class="file-box">
                        <span>PAN Image</span>
                        <input type="file" name="pan_image" accept="image/*" class="hidden">
                    </label>
                    @if(isset($shop) && $shop->pan_image_path)
                        <img src="{{ asset('public/storage/'.$shop->pan_image_path) }}"
                             class="mt-2 h-20 rounded-lg border">
                    @endif
                </div>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end pt-4">
            <button type="submit"
                    class="px-8 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                {{ isset($shop) ? 'Update Shop' : 'Create Shop' }}
            </button>
        </div>
    </form>
</div>

{{-- INPUT STYLES --}}
<style>
.input{
    width:100%;
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #cbd5e1;
    background:#f8fafc;
    font-size:14px;
}
.input:focus{
    outline:none;
    border-color:#6366f1;
    background:#ffffff;
}
.file-box{
    display:flex;
    align-items:center;
    justify-content:center;
    height:48px;
    border:2px dashed #cbd5e1;
    border-radius:12px;
    font-size:13px;
    color:#475569;
    cursor:pointer;
    background:#f8fafc;
}
.file-box:hover{
    border-color:#6366f1;
    color:#6366f1;
}
</style>

@endsection
