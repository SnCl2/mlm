@extends('layout.app')

@section('content')

<div class="min-h-screen bg-gray-100 flex items-center justify-center py-10 px-4">

  <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-8">

    <h2 class="text-2xl font-semibold text-center text-indigo-600 mb-6">
      Dream Life Management
    </h2>

    <p class="text-center text-sm text-gray-600 mb-6">
      Register below to start your journey
    </p>

    {{-- ERRORS --}}
    @if($errors->any())
      <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-700 list-disc list-inside">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('error'))
      <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700">
        {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
      @csrf

      {{-- NAME & PHONE ON SAME ROW --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input name="name" type="text" placeholder="Full Name" required class="input">
        <input name="phone" type="text" placeholder="Phone Number" class="input">
      </div>

      {{-- EMAIL & CONFIRM EMAIL ON SAME ROW --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input name="email" type="email" placeholder="Email Address" required class="input">
        <input name="email_confirmation" type="email" placeholder="Confirm Email Address"
               required class="input @error('email_confirmation') border-red-500 @enderror">
      </div>

      {{-- REFERRAL + PLACEMENT + SIDE IN A GRID --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <input name="referred_by" id="referred_by" type="text"
                 placeholder="Referral ID"
                 value="{{ old('referred_by', Auth::check() ? Auth::user()->referral_code : request('ref')) }}"
                 required class="input">
          <div id="referred-name" class="mt-1 text-xs text-gray-500"></div>
        </div>

        <div>
          <input name="place_under" id="place_under" type="text"
                 placeholder="Placement ID"
                 value="{{ old('place_under', request('place_under')) }}"
                 required class="input">
          <div id="placement-name" class="mt-1 text-xs text-gray-500"></div>
        </div>

        <select name="side" required class="input">
          <option value="">Side</option>
          <option value="left" {{ old('side', request('side', request('position'))) == 'left' ? 'selected' : '' }}>Left</option>
          <option value="right" {{ old('side', request('side', request('position'))) == 'right' ? 'selected' : '' }}>Right</option>
        </select>
      </div>

      {{-- PRODUCT SELECTION --}}
      <div>
        <p class="text-sm font-semibold text-gray-700 mb-2">Choose a Product</p>
        <div class="space-y-3">
          @foreach($products as $product)
            <label class="block cursor-pointer">
              <input type="radio" name="product_id" value="{{ $product->id }}" class="hidden peer" required>
              <div class="flex items-center gap-3 p-4 rounded-xl border border-gray-300 
                          peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                @if($product->image)
                  <img src="{{ asset('public/storage/'.$product->image) }}" class="w-12 h-12 rounded-lg object-cover">
                @endif
                <div>
                  <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                  <p class="text-sm text-gray-600">₹4,000</p>
                </div>
              </div>
            </label>
          @endforeach
        </div>
      </div>

      <button type="submit"
              class="w-full py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
        Register Account
      </button>
    </form>

    <p class="mt-5 text-center text-sm text-gray-600">
      Already registered?
      <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">
        Login here
      </a>
    </p>

  </div>
</div>

{{-- INPUT STYLE --}}
<style>
.input {
  width: 100%;
  padding: 12px 14px;
  border-radius: 12px;
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  font-size: 14px;
}
.input:focus {
  outline: none;
  border-color: #6366f1;
  background: #ffffff;
}
</style>

{{-- REFERRAL CHECK JS --}}
<script>
function fetchReferralUser(code, displayEl) {
  if (!code.trim()) {
    displayEl.textContent = '';
    return;
  }

  fetch(`/referral-user/${code}`)
    .then(res => {
      if (!res.ok) throw new Error('User not found');
      return res.json();
    })
    .then(data => {
      displayEl.textContent = 'User: ' + data.name;
      displayEl.classList.remove('text-red-500');
      displayEl.classList.add('text-emerald-600');
    })
    .catch(() => {
      displayEl.textContent = 'User not found';
      displayEl.classList.remove('text-emerald-600');
      displayEl.classList.add('text-red-500');
    });
}

document.addEventListener('DOMContentLoaded', () => {
  const r = document.getElementById('referred_by');
  const p = document.getElementById('place_under');
  const rn = document.getElementById('referred-name');
  const pn = document.getElementById('placement-name');

  r?.addEventListener('blur', () => fetchReferralUser(r.value, rn));
  p?.addEventListener('blur', () => fetchReferralUser(p.value, pn));
});
</script>

@endsection
