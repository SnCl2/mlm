@extends('layout.app')

@section('title', 'Shop Owner Dashboard')

@section('content')
<div style="max-width:1400px; margin:0 auto; padding:24px; font-family:system-ui,-apple-system,sans-serif;">

  {{-- HEADER BAR --}}
  <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:linear-gradient(135deg,#0f172a,#1e293b); border-radius:16px; margin-bottom:24px; color:#ffffff; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
      <div>
          <div style="font-size:22px; font-weight:600;">Shop Dashboard</div>
          <div style="font-size:13px; opacity:0.85;">Manage your sales and commissions</div>
      </div>
      <div>
        <span class="bg-orange-500 text-white text-xs px-3 py-1 rounded-full uppercase font-bold tracking-wide">
            {{ Auth::guard('shop')->user()->name }}
        </span>
      </div>
  </div>

  {{-- Success Message --}}
  @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
      <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
  @endif

  <!-- Dashboard Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all">
      <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
      <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Today’s Sales</h3>
      <div class="flex items-baseline">
        <span class="text-2xl font-bold text-slate-800">₹{{ number_format($todayTotal, 2) }}</span>
      </div>
      <div class="absolute bottom-4 right-4 text-orange-100 group-hover:text-orange-50 transition-colors">
          <i class="fas fa-calendar-day text-4xl"></i>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute right-0 top-0 h-full w-1 bg-green-500"></div>
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Commission Earned</h3>
        <div class="flex items-baseline">
          <span class="text-2xl font-bold text-slate-800">₹{{ number_format($commissionEarned, 2) }}</span>
        </div>
        <div class="absolute bottom-4 right-4 text-green-100 group-hover:text-green-50 transition-colors">
            <i class="fas fa-hand-holding-usd text-4xl"></i>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Total Sales</h3>
        <div class="flex items-baseline">
          <span class="text-2xl font-bold text-slate-800">₹{{ number_format($totalSubmitted, 2) }}</span>
        </div>
        <div class="absolute bottom-4 right-4 text-blue-100 group-hover:text-blue-50 transition-colors">
            <i class="fas fa-chart-line text-4xl"></i>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute right-0 top-0 h-full w-1 bg-purple-500"></div>
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Commission Payable</h3>
        <div class="flex items-baseline">
          <span class="text-2xl font-bold text-slate-800">₹{{ number_format($commission[0]->total_commission ?? 0, 2) }}</span>
        </div>
        <div class="absolute bottom-4 right-4 text-purple-100 group-hover:text-purple-50 transition-colors">
            <i class="fas fa-file-invoice-dollar text-4xl"></i>
        </div>
    </div>
  </div>

  <!-- Content Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
    <!-- Add Purchase Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-semibold text-slate-700">New Transaction</h2>
                <i class="fas fa-plus-circle text-orange-500"></i>
            </div>
            <div class="p-6">
                <form id="purchaseForm" method="POST" action="{{ route('shop.transaction.store') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Customer ID / Code</label>
                        <div class="relative">
                            <input name="referral_code" id="referral_code" type="text" placeholder="Scanning..." 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors" required>
                            <div class="absolute left-3 top-3.5 text-slate-400">
                                <i class="fas fa-qrcode"></i>
                            </div>
                        </div>
                        
                        <!-- Scanner Controls -->
                        <div class="mt-3">
                             <button type="button" id="open-scanner-btn" 
                                class="w-full py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                <i class="fas fa-camera"></i> <span>Open Scanner</span>
                            </button>
                            <div id="qr-reader" class="hidden mt-3 rounded-lg overflow-hidden border border-slate-300"></div>
                            <div id="qr-reader-results" class="hidden text-sm text-green-600 mt-2 font-medium bg-green-50 p-2 rounded border border-green-100 text-center"></div>
                            <div id="scanner-error" class="hidden text-sm text-red-600 mt-2 bg-red-50 p-2 rounded border border-red-100 text-center"></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Purchase Amount (₹)</label>
                        <div class="relative">
                            <input name="amount" id="amount" type="number" step="0.01" placeholder="0.00" 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-colors" required>
                            <div class="absolute left-3 top-3.5 text-slate-400">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-3 rounded-xl font-semibold shadow-lg shadow-orange-500/30 transition-all transform hover:-translate-y-0.5">
                        Submit Transaction
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-semibold text-slate-700">Recent Transactions</h2>
                <span class="text-xs bg-white border border-slate-200 px-2 py-1 rounded text-slate-500">Last {{ count($transactions) }}</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Commission</th>
                            <th class="px-6 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                      @forelse($transactions as $txn)
                        <tr class="hover:bg-slate-50 transition-colors">
                          <td class="px-6 py-4">
                              <div class="flex items-center gap-3">
                                  <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs">
                                      {{ substr($txn->user->name ?? '?', 0, 1) }}
                                  </div>
                                  <span class="font-medium text-slate-700">{{ $txn->user->name ?? 'Unknown' }}</span>
                              </div>
                          </td>
                          <td class="px-6 py-4 font-bold text-slate-700">₹{{ number_format($txn->purchase_amount, 2) }}</td>
                          <td class="px-6 py-4 text-green-600 font-medium">+ ₹{{ number_format($txn->commission_amount, 2) }}</td>
                          <td class="px-6 py-4 text-slate-500 text-xs">{{ $txn->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">
                              <i class="fas fa-inbox text-2xl mb-2 block opacity-20"></i>
                              No recent transactions found.
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

  </div> <!-- End Grid -->
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const openScannerBtn = document.getElementById('open-scanner-btn');
        const qrReaderElement = document.getElementById('qr-reader');
        const qrReaderResults = document.getElementById('qr-reader-results');
        const scannerError = document.getElementById('scanner-error');
        const customerIdInput = document.getElementById('referral_code');

        let qrScanner = null;

        if (!customerIdInput || !openScannerBtn) return;

        openScannerBtn.addEventListener('click', () => {
             if (qrScanner && qrScanner.isScanning) {
                 stopScanner();
             } else {
                 startScanner();
             }
        });

        function startScanner() {
            scannerError.classList.add('hidden');
            qrReaderResults.classList.add('hidden');
            qrReaderElement.classList.remove('hidden');
            openScannerBtn.innerHTML = '<i class="fas fa-stop-circle"></i> <span>Stop Scanner</span>';
            openScannerBtn.classList.add('text-red-600', 'bg-red-50');

            qrScanner = new Html5Qrcode("qr-reader");

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    qrScanner.start(cameraId, config, onScanSuccess, onScanError)
                        .catch(err => {
                            showError("Failed to start: " + err);
                            stopScanner();
                        });
                } else {
                    showError("No cameras found.");
                }
            }).catch(err => {
                showError("Camera error: " + err);
            });
        }

        function stopScanner() {
            if (qrScanner) {
                qrScanner.stop().then(() => {
                    qrScanner.clear();
                    qrScanner = null;
                    qrReaderElement.classList.add('hidden');
                    openScannerBtn.innerHTML = '<i class="fas fa-camera"></i> <span>Open Scanner</span>';
                    openScannerBtn.classList.remove('text-red-600', 'bg-red-50');
                }).catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            customerIdInput.value = decodedText;
            qrReaderResults.textContent = "Scanned: " + decodedText;
            qrReaderResults.classList.remove('hidden');
            stopScanner();
        }

        function onScanError(errorMessage) {
            // Quietly ignore scan errors during seeking
        }

        function showError(msg) {
            scannerError.textContent = msg;
            scannerError.classList.remove('hidden');
        }
    });
</script>
@endpush
