@extends('layout.app')

@section('title', 'All Shops')

@section('content')
<div class="flex justify-between items-center mb-6">
  <h1 class="text-2xl font-bold text-gray-800">Registered Shops</h1>

  <a href="{{ route('shop.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">
    <i class="fas fa-plus mr-2"></i> Add Shop
  </a>
</div>

@if(session('success'))
  <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
    {{ session('success') }}
  </div>
@endif

@if($shops->count() > 0)
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($shops as $shop)
      <div class="bg-white rounded-xl shadow-md p-5 relative hover:shadow-lg transition">
        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ $shop->name }}</h2>
                <p class="text-gray-700 text-sm font-medium">
                <span>Total Earned Commission: </span>
                ₹{{ number_format($shop->commission->total_commission ?? 0, 2) }}
              </p>
            <p class="text-sm text-gray-500">{{ $shop->category ?? 'Uncategorized' }}</p>
            <p class="mt-1 text-gray-600"><strong>Owner:</strong> {{ $shop->owner_name }}</p>
            <p class="text-gray-600"><strong>Phone:</strong> {{ $shop->phone }}</p>
            <p class="text-gray-600"><strong>Email:</strong> {{ $shop->email }}</p>
            <!-- Deduct Commission Form (in dropdown) -->
            <button onclick="togglePayForm({{ $shop->id }})" type="button" class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100">Pay Commission</button>
            
            <div id="pay-form-{{ $shop->id }}" class="hidden mt-2 px-4 py-2">
              <form method="POST" action="{{ route('shops.deductCommission', $shop->id) }}">
                @csrf
                <input type="number" step="0.01" name="amount" placeholder="Enter Amount" class="w-full border px-2 py-1 text-sm rounded mb-2" required>
                <input type="number" step="0.01" name="confirm_amount" placeholder="Confirm Amount" class="w-full border px-2 py-1 text-sm rounded mb-2" required>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm w-full">Pay</button>
              </form>
            </div>

          </div>
            <div class="flex flex-col items-center space-y-1">
              <span class="text-sm font-medium {{ $shop->is_active ? 'text-green-600' : 'text-red-600' }}">
                {{ $shop->is_active ? 'Active' : 'Inactive' }}
              </span>
            
              <form action="{{ route('shop.toggle-status', $shop->id) }}" method="POST" 
                    title="Click to {{ $shop->is_active ? 'Deactivate' : 'Activate' }}" 
                    onsubmit="return confirm('Are you sure you want to {{ $shop->is_active ? 'deactivate' : 'activate' }} this shop?');">
                @csrf
                @method('PATCH')
                <button type="submit"
                  class="relative inline-flex h-6 w-11 items-center rounded-full
                         transition-colors duration-300 focus:outline-none
                         {{ $shop->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                  <span class="sr-only">Toggle Status</span>
                  <span class="inline-block h-4 w-4 transform rounded-full bg-white transition
                        {{ $shop->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                  </span>
                </button>
              </form>
            </div>





        </div>

        <div class="mt-3 text-sm text-gray-500 line-clamp-2">
          {{ Str::limit($shop->address, 80) }}
        </div>

        <div class="flex items-center gap-2 mt-4">
          @if ($shop->aadhar_image_path)
            <img src="{{ asset('public/storage/' . $shop->aadhar_image_path) }}" alt="Aadhar" class="w-10 h-10 object-cover rounded-full border">
          @endif
          @if ($shop->pan_image_path)
            <img src="{{ asset('public/storage/' . $shop->pan_image_path) }}" alt="PAN" class="w-10 h-10 object-cover rounded-full border">
          @endif
        </div>

        <div class="mt-4 flex justify-between items-center">
          <span class="text-sm text-gray-700">Commission: {{ $shop->commission_rate }}%</span>

          <div class="relative inline-block text-left">
              <button onclick="toggleDropdown({{ $shop->id }})" class="text-gray-700 hover:text-orange-600 focus:outline-none">
                <i class="fas fa-ellipsis-v"></i>
              </button>
            
              <div id="dropdown-{{ $shop->id }}" class="hidden absolute right-0 mt-2 w-44 bg-white border rounded-md shadow-lg z-50">
                <a href="{{ route('shop.edit', $shop->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Info</a>
                <button onclick="togglePasswordForm({{ $shop->id }})" type="button" class="w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-gray-100">Edit Password</button>
            
                <form action="{{ route('shop.destroy', $shop->id) }}" method="POST" onsubmit="return confirm('Delete this shop?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</button>
                </form>
              </div>
            </div>
        </div>
      </div>
    @endforeach
    <div id="password-form-{{ $shop->id }}" class="hidden mt-3 bg-gray-50 border rounded-lg p-3">
      <form action="{{ route('shop.changePassword', $shop->id) }}" method="POST">
        @csrf
        <div class="mb-2">
          <input type="password" name="password" class="w-full border rounded px-3 py-1" placeholder="New Password" required>
        </div>
        <div class="mb-2">
          <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-1" placeholder="Confirm Password" required>
        </div>
        <div class="text-right">
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded text-sm">Update</button>
        </div>
      </form>
    </div>

  </div>
@else
  <div class="text-center text-gray-600">No shops registered yet.</div>
@endif
@endsection

@push('scripts')
<script>
  function toggleDropdown(id) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
      if (el.id === 'dropdown-' + id) {
        el.classList.toggle('hidden');
      } else {
        el.classList.add('hidden');
      }
    });
  }

  window.addEventListener('click', function (e) {
    if (!e.target.closest('.relative.inline-block')) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
    }
  });
</script>
<script>
  function toggleDropdown(id) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
      if (el.id === 'dropdown-' + id) {
        el.classList.toggle('hidden');
      } else {
        el.classList.add('hidden');
      }
    });
  }

  function togglePasswordForm(id) {
    document.getElementById('password-form-' + id).classList.toggle('hidden');
    document.getElementById('dropdown-' + id).classList.add('hidden');
  }

  window.addEventListener('click', function (e) {
    if (!e.target.closest('.relative.inline-block')) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
    }
  });
</script>
<script>
  function togglePayForm(id) {
    document.getElementById('pay-form-' + id).classList.toggle('hidden');
    document.getElementById('dropdown-' + id).classList.add('hidden');
  }
</script>

@endpush
