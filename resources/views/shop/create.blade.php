@extends('layout.app')

@section('title', isset($shop) ? 'Edit Shop' : 'Create Shop')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
  <h1 class="text-2xl font-bold text-gray-800 mb-6">
    {{ isset($shop) ? 'Edit Shop' : 'Create Shop' }}
  </h1>

  <form action="{{ isset($shop) ? route('shop.update', $shop->id) : route('shop.store') }}" 
        method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if(isset($shop))
      @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium text-gray-700">Shop Name</label>
        <input type="text" name="name" value="{{ old('name', $shop->name ?? '') }}" class="w-full border rounded-lg px-3 py-2" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Category</label>
        <input type="text" name="category" value="{{ old('category', $shop->category ?? '') }}" class="w-full border rounded-lg px-3 py-2">
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Owner Name</label>
        <input type="text" name="owner_name" value="{{ old('owner_name', $shop->owner_name ?? '') }}" class="w-full border rounded-lg px-3 py-2" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $shop->phone ?? '') }}" class="w-full border rounded-lg px-3 py-2" required>
      </div>

      <div class="md:col-span-2">
        <label class="block mb-1 font-medium text-gray-700">Address</label>
        <textarea name="address" class="w-full border rounded-lg px-3 py-2" required>{{ old('address', $shop->address ?? '') }}</textarea>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Aadhar Number</label>
        <input type="text" name="aadhar_number" maxlength="12" value="{{ old('aadhar_number', $shop->aadhar_number ?? '') }}" class="w-full border rounded-lg px-3 py-2" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">PAN Number</label>
        <input type="text" name="pan_number" maxlength="10" value="{{ old('pan_number', $shop->pan_number ?? '') }}" class="w-full border rounded-lg px-3 py-2" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $shop->email ?? '') }}" class="w-full border rounded-lg px-3 py-2" {{ isset($shop) ? 'readonly' : 'required' }}>
      </div>

      @if(!isset($shop))
        <div class="md:col-span-2">
          <div class="text-sm text-blue-600 italic">A secure password will be generated automatically and sent to the email address provided.</div>
        </div>
      @endif

      <div>
        <label class="block mb-1 font-medium text-gray-700">Commission Rate (%)</label>
        <input type="number" name="commission_rate" step="0.01" value="{{ old('commission_rate', $shop->commission_rate ?? '10.00') }}" class="w-full border rounded-lg px-3 py-2">
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Aadhar Image</label>
        <input type="file" name="aadhar_image" accept="image/*" class="w-full border rounded-lg px-3 py-2">
        @if(isset($shop) && $shop->aadhar_image_path)
          <img src="{{ asset('public/storage/' . $shop->aadhar_image_path) }}" class="mt-2 h-24 w-auto rounded border">
        @endif
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">PAN Image</label>
        <input type="file" name="pan_image" accept="image/*" class="w-full border rounded-lg px-3 py-2">
        @if(isset($shop) && $shop->pan_image_path)
          <img src="{{ asset('public/storage/' . $shop->pan_image_path) }}" class="mt-2 h-24 w-auto rounded border">
        @endif
      </div>
    </div>

    <div class="pt-4">
      <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2 rounded-lg">
        {{ isset($shop) ? 'Update Shop' : 'Create Shop' }}
      </button>
    </div>
  </form>
</div>
@endsection
