@extends('layout.app')

@section('title', 'All Shops')

@section('content')
<div style="max-width:1400px; margin:0 auto; padding:24px; font-family:system-ui,-apple-system,sans-serif;">

  {{-- HEADER BAR --}}
  <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:linear-gradient(135deg,#0f172a,#1e293b); border-radius:16px; margin-bottom:24px; color:#ffffff; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
      <div>
          <div style="font-size:22px; font-weight:600;">Registered Shops</div>
          <div style="font-size:13px; opacity:0.85;">Manage shop partners</div>
      </div>
      <div>
        <a href="{{ route('shop.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg shadow-md transition transform hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Add Shop
        </a>
      </div>
  </div>

  @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
      <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
  @endif

  @if($shops->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($shops as $shop)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative hover:shadow-md transition-all group">
          <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
              <h2 class="text-lg font-bold text-slate-800 group-hover:text-orange-600 transition-colors">{{ $shop->name }}</h2>
              <span class="inline-block bg-slate-100 text-slate-500 text-xs px-2 py-1 rounded mt-1">{{ $shop->category ?? 'Uncategorized' }}</span>
            </div>
            
            <div class="flex flex-col items-end space-y-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $shop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                  {{ $shop->is_active ? 'Active' : 'Inactive' }}
                </span>

                <form action="{{ route('shop.toggle-status', $shop->id) }}" method="POST" 
                      title="Click to {{ $shop->is_active ? 'Deactivate' : 'Activate' }}" 
                      onsubmit="return confirm('Are you sure you want to {{ $shop->is_active ? 'deactivate' : 'activate' }} this shop?');">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-300 focus:outline-none {{ $shop->is_active ? 'bg-green-500' : 'bg-slate-300' }}">
                    <span class="sr-only">Toggle Status</span>
                    <span class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $shop->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                  </button>
                </form>
            </div>
          </div>

          <div class="space-y-2 text-sm text-slate-600 mb-4">
              <div class="flex items-center justify-between">
                  <span class="text-slate-400">Owner:</span>
                  <span class="font-medium">{{ $shop->owner_name }}</span>
              </div>
              <div class="flex items-center justify-between">
                  <span class="text-slate-400">Phone:</span>
                  <span>{{ $shop->phone }}</span>
              </div>
              <div class="flex items-center justify-between">
                  <span class="text-slate-400">Commission:</span>
                  <span class="font-bold text-orange-600">{{ $shop->commission_rate }}%</span>
              </div>
              
              <div class="pt-2 mt-2 border-t border-slate-100">
                  <p class="text-xs text-slate-500 mb-1">Total Earned Commission</p>
                  <p class="text-xl font-bold text-green-600">₹{{ number_format($shop->commission->total_commission ?? 0, 2) }}</p>
              </div>
          </div>

          <!-- Actions Area -->
          <div class="bg-slate-50 -mx-6 -mb-6 px-6 py-4 flex items-center justify-between border-t border-slate-100 mt-4 rounded-b-2xl">
              
              <!-- Pay Commission -->
              <div class="relative">
                  <button onclick="togglePayForm({{ $shop->id }})" class="text-sm font-medium text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors border border-green-200">
                      Pay Commission
                  </button>
                  
                  <!-- Pay Dropdown -->
                  <div id="pay-form-{{ $shop->id }}" class="hidden absolute top-full left-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-xl z-20 p-3">
                      <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">Deduct Commission</h4>
                      <form method="POST" action="{{ route('shops.deductCommission', $shop->id) }}">
                        @csrf
                        <input type="number" step="0.01" name="amount" placeholder="Amount" class="w-full border border-slate-300 px-3 py-2 text-sm rounded-lg mb-2 focus:ring-1 focus:ring-green-500 focus:outline-none" required>
                        <input type="number" step="0.01" name="confirm_amount" placeholder="Confirm" class="w-full border border-slate-300 px-3 py-2 text-sm rounded-lg mb-2 focus:ring-1 focus:ring-green-500 focus:outline-none" required>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm w-full font-medium transition">Confirm Pay</button>
                      </form>
                  </div>
              </div>

              <!-- More Options -->
              <div class="relative inline-block text-left">
                  <button onclick="toggleDropdown({{ $shop->id }})" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 text-slate-500 transition">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                
                  <div id="dropdown-{{ $shop->id }}" class="hidden absolute right-0 bottom-full mb-2 w-48 bg-white border border-slate-200 rounded-xl shadow-xl z-20 overflow-hidden">
                    <a href="{{ route('shop.edit', $shop->id) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition border-b border-slate-100">
                        <i class="fas fa-edit w-4 text-slate-400 mr-2"></i> Edit Info
                    </a>
                    <button onclick="togglePasswordForm({{ $shop->id }})" type="button" class="w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition border-b border-slate-100">
                        <i class="fas fa-key w-4 text-blue-400 mr-2"></i> Edit Password
                    </button>
                    <form action="{{ route('shop.destroy', $shop->id) }}" method="POST" onsubmit="return confirm('Delete this shop?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                          <i class="fas fa-trash w-4 text-red-400 mr-2"></i> Delete
                      </button>
                    </form>
                  </div>
              </div>
          </div>
          
          <!-- Hidden Password Form (Overlay or inline) -->
          <div id="password-form-{{ $shop->id }}" class="hidden absolute inset-0 bg-white/95 backdrop-blur-sm z-30 flex flex-col justify-center p-6 rounded-2xl">
              <h4 class="text-sm font-bold text-slate-800 mb-3 text-center">Change Password</h4>
              <form action="{{ route('shop.changePassword', $shop->id) }}" method="POST">
                @csrf
                <div class="mb-2">
                  <input type="password" name="password" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="New Password" required>
                </div>
                <div class="mb-3">
                  <input type="password" name="password_confirmation" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Confirm" required>
                </div>
                <div class="flex gap-2">
                  <button type="button" onclick="togglePasswordForm({{ $shop->id }})" class="flex-1 bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-sm hover:bg-slate-200">Cancel</button>
                  <button type="submit" class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 shadow-sm">Update</button>
                </div>
              </form>
          </div>

        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
            <i class="fas fa-store-slash text-slate-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-slate-900">No shops found</h3>
        <p class="text-slate-500 max-w-sm mx-auto mt-2">Get started by creating a new shop partner.</p>
        <a href="{{ route('shop.create') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg shadow-md transition">
            <i class="fas fa-plus mr-2"></i> Add First Shop
        </a>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  function toggleDropdown(id) {
    // Close others
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
      if (el.id !== 'dropdown-' + id) el.classList.add('hidden');
    });
    document.querySelectorAll('[id^="pay-form-"]').forEach(el => el.classList.add('hidden'));

    const el = document.getElementById('dropdown-' + id);
    if(el) el.classList.toggle('hidden');
  }

  function togglePayForm(id) {
    // Close others
    document.querySelectorAll('[id^="pay-form-"]').forEach(el => {
      if (el.id !== 'pay-form-' + id) el.classList.add('hidden');
    });
    document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));

    const el = document.getElementById('pay-form-' + id);
    if(el) el.classList.toggle('hidden');
  }

  function togglePasswordForm(id) {
    const el = document.getElementById('password-form-' + id);
    if(el) {
        el.classList.toggle('hidden');
        // Hide dropdown if showing
        document.getElementById('dropdown-' + id)?.classList.add('hidden');
    }
  }

  // Close when clicking outside
  window.addEventListener('click', function (e) {
    if (!e.target.closest('.relative') && !e.target.closest('[id^="password-form-"]')) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('[id^="pay-form-"]').forEach(el => {
           // Don't hide if clicking inside it
           if(!e.target.closest('#pay-form-' + el.id.replace('pay-form-',''))) {
               el.classList.add('hidden');
           }
      });
    }
  });
</script>
@endpush
