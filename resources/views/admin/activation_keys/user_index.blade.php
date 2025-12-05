@extends('layout.app')

@section('content')
<div class="container mx-auto py-8 px-4" x-data="{ tab: 'assigned' }">
    <h1 class="text-2xl font-bold text-orange-500 mb-6">My Activation Keys</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <!-- Tabs -->
    <div class="flex border-b mb-6">
        <button @click="tab = 'assigned'"
            class="px-4 py-2 font-semibold"
            :class="tab === 'assigned' ? 'border-b-2 border-orange-600 text-orange-600' : 'text-gray-600'">
            Assigned to Me
        </button>
        <button @click="tab = 'transferred'"
            class="ml-4 px-4 py-2 font-semibold"
            :class="tab === 'transferred' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'">
            I Transferred
        </button>
    </div>

    <!-- ✅ Assigned Keys -->
    <div x-show="tab === 'assigned'" x-cloak>
        @if($activationKeys->count())
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Key</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Used At</th>
                        <th class="px-4 py-3">Used For</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @foreach($activationKeys as $key)
                    <tr>
                        <td class="px-4 py-2 font-mono">{{ $key->key }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $key->status === 'fresh' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($key->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $key->used_at ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($key->usedFor)->name ?? '-' }}</td>
                        <td class="px-4 py-2 space-x-2">
                            @if($key->status === 'fresh')
                            <button onclick="document.getElementById('use-key-{{ $key->id }}').classList.remove('hidden')" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">Use</button>
                            <button onclick="document.getElementById('transfer-key-{{ $key->id }}').classList.remove('hidden')" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded">Transfer</button>
                            @endif
                        </td>
                    </tr>

                    <!-- Use Key Modal -->
                    <div id="use-key-{{ $key->id }}" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md">
                            <h2 class="text-lg font-bold mb-4">Use Activation Key</h2>
                            <form action="{{ route('activation-keys.use') }}" method="POST">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key->key }}">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium">Referral ID</label>
                                    <input type="text" name="referral_code" id="use-referral-{{ $key->id }}" class="w-full border rounded px-3 py-2 mt-1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium">Confirm Referral ID</label>
                                    <input type="text" name="confirm_referral_code" class="w-full border rounded px-3 py-2 mt-1" required>
                                </div>
                                <div class="mb-1 text-sm" id="use-name-display-{{ $key->id }}"></div>
                                <div class="flex justify-between mt-4">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Confirm</button>
                                    <button type="button" onclick="document.getElementById('use-key-{{ $key->id }}').classList.add('hidden')" class="text-gray-600">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Transfer Key Modal -->
                    <div id="transfer-key-{{ $key->id }}" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md">
                            <h2 class="text-lg font-bold mb-4">Transfer Activation Key</h2>
                            <form action="{{ route('activation-keys.transfer') }}" method="POST" onsubmit="return validateTransferForm({{ $key->id }})">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key->key }}">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium">Recipient Referral ID</label>
                                    <input type="text" id="referral-{{ $key->id }}" name="to_referral_code" class="w-full border rounded px-3 py-2 mt-1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium">Confirm Referral ID</label>
                                    <input type="text" id="referral-confirm-{{ $key->id }}" class="w-full border rounded px-3 py-2 mt-1" required>
                                </div>
                                <div class="mb-1 text-sm" id="transfer-name-display-{{ $key->id }}"></div>
                                <div class="flex justify-between mt-4">
                                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded">Transfer</button>
                                    <button type="button" onclick="document.getElementById('transfer-key-{{ $key->id }}').classList.add('hidden')" class="text-gray-600">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $activationKeys->links() }}
        </div>
        @else
        <p class="text-gray-600">You have no activation keys assigned.</p>
        @endif
    </div>

    <!-- 🔁 Transferred Keys -->
    <div x-show="tab === 'transferred'" x-cloak>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-100 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Key</th>
                        <th class="px-4 py-3">Transferred To</th>
                        <th class="px-4 py-3">Transferred At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($transfers as $transfer)
                    <tr>
                        <td class="px-4 py-2 font-mono">{{ $transfer->activationKey->key ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $transfer->toUser->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($transfer->transferred_at)->format('d M Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center px-4 py-6 text-gray-500">You haven’t transferred any keys.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Alpine.js -->
<script src="https://unpkg.com/alpinejs" defer></script>

<!-- Script for Referral Name & Validation -->
<!--<script>-->
<!--function validateTransferForm(keyId) {-->
<!--    const original = document.getElementById(`referral-${keyId}`).value.trim();-->
<!--    const confirm = document.getElementById(`referral-confirm-${keyId}`).value.trim();-->
<!--    if (original !== confirm) {-->
<!--        alert("Referral IDs do not match!");-->
<!--        return false;-->
<!--    }-->
<!--    return true;-->
<!--}-->

<!--document.addEventListener('DOMContentLoaded', function () {-->
<!--    @foreach ($activationKeys as $key)-->
<!--        const transferInput = document.getElementById('referral-{{ $key->id }}');-->
<!--        const transferNameDisplay = document.getElementById('transfer-name-display-{{ $key->id }}');-->
<!--        if (transferInput) {-->
<!--            transferInput.addEventListener('blur', () => fetchUserName(transferInput, transferNameDisplay));-->
<!--        }-->

<!--        const useInput = document.getElementById('use-referral-{{ $key->id }}');-->
<!--        const useNameDisplay = document.getElementById('use-name-display-{{ $key->id }}');-->
<!--        if (useInput) {-->
<!--            useInput.addEventListener('blur', () => fetchUserName(useInput, useNameDisplay));-->
<!--        }-->
<!--    @endforeach-->
<!--});-->

<!--function fetchUserName(inputElement, displayElement) {-->
<!--    const code = inputElement.value.trim();-->
<!--    if (!code) {-->
<!--        displayElement.textContent = "";-->
<!--        return;-->
<!--    }-->
<!--    fetch(`/referral-user/${code}`)-->
<!--        .then(response => {-->
<!--            if (!response.ok) throw new Error();-->
<!--            return response.json();-->
<!--        })-->
<!--        .then(data => {-->
<!--            displayElement.textContent = "Referral belongs to: " + data.name;-->
<!--            displayElement.classList.remove('text-red-500');-->
<!--            displayElement.classList.add('text-green-600');-->
<!--        })-->
<!--        .catch(() => {-->
<!--            displayElement.textContent = "User not found.";-->
<!--            displayElement.classList.remove('text-green-600');-->
<!--            displayElement.classList.add('text-red-500');-->
<!--        });-->
<!--}-->
<!--</script>-->
<script>
function validateTransferForm(keyId) {
    const original = document.getElementById(`referral-${keyId}`).value.trim();
    const confirm = document.getElementById(`referral-confirm-${keyId}`).value.trim();

    if (original !== confirm) {
        alert("Referral IDs do not match!");
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    @foreach ($activationKeys as $key)
        // Transfer
        const transferInput{{ $key->id }} = document.getElementById('referral-{{ $key->id }}');
        const transferNameDisplay{{ $key->id }} = document.getElementById('transfer-name-display-{{ $key->id }}');

        if (transferInput{{ $key->id }}) {
            transferInput{{ $key->id }}.addEventListener('blur', function () {
                fetchUserName(transferInput{{ $key->id }}, transferNameDisplay{{ $key->id }});
            });
        }

        // Use
        const useInput{{ $key->id }} = document.getElementById('use-referral-{{ $key->id }}');
        const useNameDisplay{{ $key->id }} = document.getElementById('use-name-display-{{ $key->id }}');

        if (useInput{{ $key->id }}) {
            useInput{{ $key->id }}.addEventListener('blur', function () {
                fetchUserName(useInput{{ $key->id }}, useNameDisplay{{ $key->id }});
            });
        }
    @endforeach
});

function fetchUserName(inputElement, displayElement) {
    const code = inputElement.value.trim();
    if (!code) {
        displayElement.textContent = "";
        return;
    }

    fetch(`/referral-user/${code}`)
        .then(response => {
            if (!response.ok) throw new Error('Not found');
            return response.json();
        })
        .then(data => {
            displayElement.textContent = "Referral belongs to: " + data.name;
            displayElement.classList.remove('text-red-500');
            displayElement.classList.add('text-green-600');
        })
        .catch(() => {
            displayElement.textContent = "User not found.";
            displayElement.classList.remove('text-green-600');
            displayElement.classList.add('text-red-500');
        });
}
</script>
@endsection
