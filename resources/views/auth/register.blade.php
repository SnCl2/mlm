@extends('layout.app')

@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-orange-500 mb-6">Dream Life Management</h2>

    @if($errors->any())
      <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-800 font-semibold mb-2">Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm text-red-700">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('error'))
      <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-800 font-semibold">{{ session('error') }}</p>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf

      {{-- Full Name --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Full Name</label>
        <input name="name" type="text" placeholder="John Doe" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
      </div>

      {{-- Phone --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Phone Number</label>
        <input name="phone" type="text" placeholder="9876543210" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
      </div>

      {{-- Email --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Email Address</label>
        <input name="email" type="email" placeholder="example@mail.com" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
      </div>

      {{-- Confirm Email --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Confirm Email Address</label>
        <input name="email_confirmation" type="email" placeholder="example@mail.com" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('email_confirmation') border-red-500 @enderror">
        @error('email_confirmation')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Referral --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Referral ID <span class="text-red-500">*</span></label>
        <input 
          name="referred_by" 
          id="referred_by"
          type="text" 
          placeholder="Referral Code" 
          value="{{ old('referred_by', Auth::check() ? Auth::user()->referral_code : request('ref')) }}" 
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('referred_by') border-red-500 @enderror"
        >
        <div id="referred-name" class="mt-1 text-sm"></div>
        @error('referred_by')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Placement --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Placement ID <span class="text-red-500">*</span></label>
        <input 
          name="place_under" 
          id="place_under"
          type="text" 
          placeholder="Referral Code" 
          value="{{ old('place_under', request('place_under')) }}" 
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('place_under') border-red-500 @enderror"
        >
        <div id="placement-name" class="mt-1 text-sm"></div>
        @error('place_under')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Placement Side --}}
      <div>
        <label class="block mb-1 text-sm font-semibold text-gray-700">Placement Side <span class="text-red-500">*</span></label>
        <select name="side" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
          <option value="">Select Side</option>
          <option value="left" {{ old('side', request('side', request('position'))) == 'left' ? 'selected' : '' }}>Left</option>
          <option value="right" {{ old('side', request('side', request('position'))) == 'right' ? 'selected' : '' }}>Right</option>
        </select>
        @error('side')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Product Selection --}}
      <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Select a Product</label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($products as $product)
              <label class="cursor-pointer">
                <input type="radio" name="product_id" value="{{ $product->id }}" class="hidden peer" required>
                <div class="p-4 border rounded-xl shadow-sm hover:shadow-md transition 
                            peer-checked:border-orange-500 peer-checked:bg-orange-50 flex items-center space-x-3">
                  @if($product->image)
                    <img src="{{ asset('public/storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover">
                  @endif
                  <div>
                    <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600">₹3,000</p>
                  </div>
                </div>
              </label>
            @endforeach
          </div>
        </div>


      <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transition">
        Register
      </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
      Already have an account?
      <a href="{{ route('login') }}" class="text-orange-500 font-semibold hover:underline">Login here</a>
    </p>
  </div>
</div>

{{-- Referral check JS --}}
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
      displayEl.classList.add('text-green-600');
    })
    .catch(() => {
      displayEl.textContent = 'User not found.';
      displayEl.classList.remove('text-green-600');
      displayEl.classList.add('text-red-500');
    });
}

document.addEventListener('DOMContentLoaded', function () {
  const referredInput = document.getElementById('referred_by');
  const placementInput = document.getElementById('place_under');
  const referredNameDisplay = document.getElementById('referred-name');
  const placementNameDisplay = document.getElementById('placement-name');

  if (referredInput) {
    referredInput.addEventListener('blur', () => {
      fetchReferralUser(referredInput.value, referredNameDisplay);
    });
  }

  if (placementInput) {
    placementInput.addEventListener('blur', () => {
      fetchReferralUser(placementInput.value, placementNameDisplay);
    });
  }
});
</script>
@endsection
