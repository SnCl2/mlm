@extends('layout.app')

@section('title', 'Shop Owner Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-orange-500 mb-6">Shop Owner Dashboard</h1>

  {{-- Success Message --}}
  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  <!-- Dashboard Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-500">
      <h3 class="text-sm text-gray-500 mb-1">Today’s Transactions</h3>
      <p class="text-2xl font-bold">₹{{ number_format($todayTotal, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
      <h3 class="text-sm text-gray-500 mb-1">Commission to give</h3>
      <p class="text-2xl font-bold text-green-600">₹{{ number_format($commissionEarned, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
      <h3 class="text-sm text-gray-500 mb-1">Total Transaction </h3>
      <p class="text-2xl font-bold text-blue-600">₹{{ number_format($totalSubmitted, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
      <h3 class="text-sm text-gray-500 mb-1">Total Commission to Pay</h3>
      <p class="text-2xl font-bold text-blue-600">₹{{ number_format($commission[0]->total_commission ?? 0, 2) }}</p>
    </div>
  </div>

  <!-- Add Purchase Form with QR Scanner -->
    <!-- Relevant HTML from your Blade template for the QR Scanner -->
    <div class="bg-white p-6 rounded-lg shadow mb-10">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Add Purchase</h2>
        <form id="purchaseForm" class="grid grid-cols-1 md:grid-cols-3 gap-4" method="POST" action="{{ route('shop.transaction.store') }}">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Customer ID or Scan QR</label>
                <input name="referral_code" id="referral_code" type="text" placeholder="Enter or scan..." class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                <button type="button" id="open-scanner-btn" class="mt-2 mb-2 bg-gray-100 border border-gray-300 text-sm px-3 py-1 rounded hover:bg-gray-200">
                    Open Scanner
                </button>
                <div id="qr-reader" class="hidden mt-2"></div>
                <div id="qr-reader-results" class="hidden text-sm text-green-600 mt-2"></div>
                <div id="scanner-error" class="hidden text-sm text-red-600 mt-2"></div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Purchase Amount</label>
                <input name="amount" id="amount" type="number" placeholder="₹" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class=" mt-2 w-full bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 transition">Submit</button>
            </div>
        </form>
    </div>

  <!-- Transaction History -->
  <div class="bg-white p-6 rounded-lg shadow mb-10">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Transaction History</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left py-2 px-4 font-medium text-gray-600">Customer</th>
            <th class="text-left py-2 px-4 font-medium text-gray-600">Amount</th>
            <th class="text-left py-2 px-4 font-medium text-gray-600">Commission</th>
            <th class="text-left py-2 px-4 font-medium text-gray-600">Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $txn)
            <tr class="border-b hover:bg-gray-50">
              <td class="py-2 px-4">{{ $txn->user->name ?? 'N/A' }}</td>
              <td class="py-2 px-4">₹{{ number_format($txn->purchase_amount, 2) }}</td>
              <td class="py-2 px-4 text-green-600">₹{{ number_format($txn->commission_amount, 2) }}</td>
              <td class="py-2 px-4">{{ $txn->created_at->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-gray-500">No transactions yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Commission Update -->
  
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM is fully loaded");
    });

    console.log("Script loaded");

    ['purchaseForm', 'referral_code', 'amount', 'open-scanner-btn', 'qr-reader', 'qr-reader-results', 'scanner-error'].forEach(id => {
        if (!document.getElementById(id)) {
            console.error(`Missing element: #${id}`);
        }
    });

    // Wrap your entire JavaScript code inside a DOMContentLoaded listener
    document.addEventListener('DOMContentLoaded', function() {
        // Get references to the DOM elements
        const form = document.getElementById('purchaseForm');
        const openScannerBtn = document.getElementById('open-scanner-btn');
        const qrReaderElement = document.getElementById('qr-reader');
        const qrReaderResults = document.getElementById('qr-reader-results');
        const scannerError = document.getElementById('scanner-error');
        const customerIdInput = document.getElementById('referral_code'); // Added for clarity

        let qrScanner = null; // Variable to hold the Html5Qrcode instance

        // Check if elements exist before attaching listeners or proceeding
        if (!form || !openScannerBtn || !qrReaderElement || !qrReaderResults || !scannerError || !customerIdInput) {
            console.error("One or more required DOM elements not found. Please check your HTML IDs.");
            return; // Exit if elements are missing
        }

        
        // Scanner button handler: Toggles the scanner on/off
        openScannerBtn.addEventListener('click', function() {
            if (qrScanner && qrScanner.isScanning) {
                stopScanner(); // If scanning, stop it
            } else {
                startScanner(); // Otherwise, start it
            }
        });

        /**
         * Initializes and starts the QR code scanner.
         * It requests camera access and begins scanning for QR codes.
         */
        function startScanner() {
            // Clear any previous error or result messages
            scannerError.classList.add('hidden');
            qrReaderResults.classList.add('hidden');

            // Show the scanner UI element
            qrReaderElement.classList.remove('hidden');
            openScannerBtn.textContent = 'Stop Scanner'; // Change button text

            // Create a new Html5Qrcode instance, targeting the 'qr-reader' div
            qrScanner = new Html5Qrcode("qr-reader");

            // Configuration for the scanner
            const config = {
                fps: 10, // Frames per second for scanning
                qrbox: { width: 250, height: 250 }, // Size of the QR scanning box
                rememberLastUsedCamera: true, // Remember user's camera preference
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA] // Only use camera for scanning
            };

            // Get available cameras and start the scanner
            Html5Qrcode.getCameras().then(devices => {
                if (devices.length > 0) {
                    // Try to find the back camera (usually has 'back' or 'rear' in the label)
                    const backCamera = devices.find(device => 
                        device.label.toLowerCase().includes('back') || 
                        device.label.toLowerCase().includes('rear')
                    );
            
                    const selectedCameraId = backCamera ? backCamera.id : devices[0].id;
            
                    qrScanner.start(
                        selectedCameraId,
                        config,
                        onScanSuccess,
                        onScanError
                    ).catch(err => {
                        showScannerError("Failed to start scanner: " + err);
                        stopScanner();
                    });
                } else {
                    showScannerError("No cameras found.");
                }
            }).catch(err => {
                showScannerError("Camera access error: " + err);
            });
        }

        /**
         * Stops the QR code scanner and cleans up the UI.
         */
        function stopScanner() {
            if (qrScanner) {
                qrScanner.stop() // Stop the scanner
                    .then(() => {
                        qrScanner = null; // Clear the scanner instance
                        qrReaderElement.classList.add('hidden'); // Hide scanner UI
                        openScannerBtn.textContent = 'Open Scanner'; // Reset button text
                    })
                    .catch(err => {
                        console.error("Failed to stop scanner:", err);
                        // Even if stopping fails, try to hide UI and reset button
                        qrReaderElement.classList.add('hidden');
                        openScannerBtn.textContent = 'Open Scanner';
                    });
            }
        }

        /**
         * Callback function executed when a QR code is successfully scanned.
         * @param {string} decodedText - The data decoded from the QR code.
         * @param {object} decodedResult - Additional details about the scan.
         */
        function onScanSuccess(decodedText, decodedResult) {
            // Populate the customer ID input field with the scanned data
            customerIdInput.value = decodedText;
            // Display the scanned text to the user
            qrReaderResults.textContent = `Scanned: ${decodedText}`;
            qrReaderResults.classList.remove('hidden');

            // Stop the scanner immediately after a successful scan
            stopScanner();
        }

        /**
         * Callback function executed when the scanner encounters an error (e.g., no QR code in view).
         * This is generally verbose, so it's often used for debugging.
         * @param {string} errorMessage - The error message.
         */
        function onScanError(errorMessage) {
            // You can uncomment the line below for debugging, but it can be noisy during live scanning.
            // console.warn(`QR Scan error: ${errorMessage}`);
        }

        /**
         * Displays an error message related to the scanner.
         * @param {string} message - The error message to display.
         */
        function showScannerError(message) {
            scannerError.textContent = message;
            scannerError.classList.remove('hidden');
        }

        // Event listener to clean up the scanner if the user navigates away from the page
        window.addEventListener('beforeunload', function() {
            if (qrScanner && qrScanner.isScanning) {
                qrScanner.stop().catch(err => {
                    console.error("Error stopping scanner on unload:", err);
                });
            }
        });
    }); // End of DOMContentLoaded listener
</script>
@endpush
