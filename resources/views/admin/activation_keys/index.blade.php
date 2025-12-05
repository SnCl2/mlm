@extends('layout.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded shadow" x-data="{ tab: 'keys' }">
    <div class="flex border-b mb-4">
        <button
            class="px-4 py-2 font-semibold"
            :class="tab === 'keys' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-500'"
            @click="tab = 'keys'"
        >
            Activation Keys
        </button>
        <button
            class="px-4 py-2 font-semibold ml-4"
            :class="tab === 'transfers' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500'"
            @click="tab = 'transfers'"
        >
            Transfer History
        </button>
    </div>

    {{-- Activation Keys Tab --}}
    <div x-show="tab === 'keys'">
        <h2 class="text-xl font-semibold text-orange-600 mb-4">All Activation Keys</h2>
        <table class="w-full table-auto border border-collapse border-gray-300">
            <thead>
                <tr class="bg-orange-100">
                    <th class="border p-2 text-left">Key</th>
                    <th class="border p-2 text-left">Status</th>
                    <th class="border p-2 text-left">Assigned To</th>
                    <th class="border p-2 text-left">Assigned By</th>
                    <th class="border p-2 text-left">Used At</th>
                    <th class="border p-2 text-left">Used For</th>
                    <th class="border p-2 text-left">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($keys as $key)
                    <tr>
                        <td class="border p-2 font-mono">{{ $key->key }}</td>
                        <td class="border p-2 capitalize">{{ $key->status }}</td>
                        <td class="border p-2">{{ $key->assignedTo->name ?? '-' }}</td>
                        <td class="border p-2">{{ $key->assignedBy->name ?? '-' }}</td>
                        <td class="border p-2">{{ $key->used_at ?? '-' }}</td>
                        <td class="border p-2">{{ $key->usedFor->name ?? '-' }}</td>
                        <td class="border p-2">{{ $key->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-4 text-gray-500">No activation keys found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $keys->links() }}
        </div>
    </div>

    {{-- Transfer History Tab --}}
    <div x-show="tab === 'transfers'" x-cloak>
        <h2 class="text-xl font-semibold text-indigo-600 mb-4">Key Transfer History</h2>
        <table class="w-full table-auto border border-collapse border-gray-300">
            <thead>
                <tr class="bg-indigo-100">
                    <th class="border p-2 text-left">Key</th>
                    <th class="border p-2 text-left">From</th>
                    <th class="border p-2 text-left">To</th>
                    <th class="border p-2 text-left">Transferred At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transfers as $transfer)
                    <tr>
                        <td class="border p-2 font-mono">{{ $transfer->activationKey->key ?? '-' }}</td>
                        <td class="border p-2">{{ $transfer->fromUser->name ?? 'System' }}</td>
                        <td class="border p-2">{{ $transfer->toUser->name ?? '-' }}</td>
                        <td class="border p-2">{{ \Carbon\Carbon::parse($transfer->transferred_at)->format('d M Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center p-4 text-gray-500">No transfer records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
