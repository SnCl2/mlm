@extends('layout.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold text-orange-600 mb-4">Assign Activation Keys</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('activation-keys.assign') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Referral ID (Username or Code)</label>
            <input type="text" name="referral_code" value="{{ old('referral_code') }}" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input type="number" name="quantity" min="1" required value="{{ old('quantity') }}" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Generate & Assign</button>
        </div>
    </form>
</div>
@endsection
