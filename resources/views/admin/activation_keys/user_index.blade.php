@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ tab: 'assigned' }">

    {{-- HEADER CARD --}}
    <div class="bg-white border border-slate-200 rounded-2xl px-6 py-5 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            {{-- TITLE --}}
            <div>
                <h1 class="text-xl font-semibold text-slate-800">
                    My Activation Keys
                </h1>
                <p class="text-sm text-slate-500">
                    Manage, use or transfer your PINs
                </p>
            </div>

            {{-- TABS + ACTION --}}
            <div class="flex flex-wrap items-center gap-3">

                {{-- TABS --}}
                <div class="flex bg-slate-100 rounded-xl p-1">
                    <button
                        @click="tab='assigned'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                        :class="tab==='assigned'
                            ? 'bg-indigo-600 text-white shadow'
                            : 'text-slate-600 hover:text-slate-800'">
                        Assigned to Me
                    </button>

                    <button
                        @click="tab='transferred'"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition"
                        :class="tab==='transferred'
                            ? 'bg-indigo-600 text-white shadow'
                            : 'text-slate-600 hover:text-slate-800'">
                        Transferred by Me
                    </button>
                </div>

                {{-- CTA --}}
                <button
                    onclick="document.getElementById('bulk-transfer-modal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
                    Bulk Transfer PINs
                </button>
            </div>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-xl text-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ASSIGNED TAB --}}
    <div x-show="tab==='assigned'" x-cloak>
        @if($activationKeys->count())

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="th">Key</th>
                        <th class="th">Status</th>
                        <th class="th">Used At</th>
                        <th class="th">Used For</th>
                        <th class="th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($activationKeys as $key)
                    <tr class="hover:bg-slate-50">
                        <td class="td font-mono">{{ $key->key }}</td>

                        <td class="td">
                            <span class="badge {{ $key->status === 'fresh' ? 'badge-green' : 'badge-red' }}">
                                {{ ucfirst($key->status) }}
                            </span>
                        </td>

                        <td class="td">{{ $key->used_at ?? '-' }}</td>
                        <td class="td">{{ optional($key->usedFor)->name ?? '-' }}</td>

                        <td class="td text-right space-x-2">
                            @if($key->status === 'fresh')
                                <button onclick="document.getElementById('use-key-{{ $key->id }}').classList.remove('hidden')" class="btn-sm btn-indigo">
                                    Use
                                </button>
                                <button onclick="document.getElementById('transfer-key-{{ $key->id }}').classList.remove('hidden')" class="btn-sm btn-amber">
                                    Transfer
                                </button>
                            @endif
                        </td>
                    </tr>

                    {{-- USE MODAL --}}
                    <div id="use-key-{{ $key->id }}" class="modal hidden flex">
                        <div class="modal-card">
                            <h2 class="modal-title">Use Activation Key</h2>
                            <form action="{{ route('activation-keys.use') }}" method="POST">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key->key }}">
                                <input type="text" name="referral_code" id="use-referral-{{ $key->id }}" placeholder="Referral ID" class="input" required>
                                <input type="text" name="confirm_referral_code" placeholder="Confirm Referral ID" class="input mt-3" required>
                                <div id="use-name-display-{{ $key->id }}" class="text-sm mt-1"></div>
                                <div class="modal-actions">
                                    <button class="btn-indigo">Confirm</button>
                                    <button type="button" onclick="document.getElementById('use-key-{{ $key->id }}').classList.add('hidden')" class="btn-cancel">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- TRANSFER MODAL --}}
                    <div id="transfer-key-{{ $key->id }}" class="modal hidden flex">
                        <div class="modal-card">
                            <h2 class="modal-title">Transfer Activation Key</h2>
                            <form action="{{ route('activation-keys.transfer') }}" method="POST" onsubmit="return validateTransferForm({{ $key->id }})">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key->key }}">
                                <input type="text" id="referral-{{ $key->id }}" name="to_referral_code" placeholder="Recipient Referral ID" class="input" required>
                                <input type="text" id="referral-confirm-{{ $key->id }}" placeholder="Confirm Referral ID" class="input mt-3" required>
                                <div id="transfer-name-display-{{ $key->id }}" class="text-sm mt-1"></div>
                                <div class="modal-actions">
                                    <button class="btn-amber">Transfer</button>
                                    <button type="button" onclick="document.getElementById('transfer-key-{{ $key->id }}').classList.add('hidden')" class="btn-cancel">
                                        Cancel
                                    </button>
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
            <p class="text-slate-500">You have no activation keys assigned.</p>
        @endif
    </div>

    {{-- TRANSFERRED TAB --}}
    <div x-show="tab==='transferred'" x-cloak>
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="th">Key</th>
                        <th class="th">Transferred To</th>
                        <th class="th">Transferred At</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($transfers as $transfer)
                        <tr>
                            <td class="td font-mono">{{ $transfer->activationKey->key ?? '-' }}</td>
                            <td class="td">{{ $transfer->toUser->name ?? '-' }}</td>
                            <td class="td">{{ \Carbon\Carbon::parse($transfer->transferred_at)->format('d M Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="td text-center text-slate-500 py-6">
                                You haven’t transferred any keys.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- BULK TRANSFER MODAL --}}
<div id="bulk-transfer-modal" class="modal hidden flex">
    <div class="modal-card">
        <h2 class="modal-title">Bulk Transfer PINs</h2>
        <form action="{{ route('activation-keys.bulk-transfer') }}" method="POST" onsubmit="return validateBulkTransferForm()">
            @csrf
            <input type="text" id="bulk-referral" name="to_referral_code" placeholder="Recipient Referral ID" class="input" required>
            <input type="text" id="bulk-referral-confirm" placeholder="Confirm Referral ID" class="input mt-3" required>
            <div id="bulk-name-display" class="text-sm mt-1"></div>
            <input type="number" id="bulk-count" name="count" min="1" class="input mt-3" placeholder="Number of PINs" required>
            <div id="available-pins-count" class="text-sm mt-2"></div>

            <div class="modal-actions">
                <button class="btn-indigo">Transfer</button>
                <button type="button" onclick="document.getElementById('bulk-transfer-modal').classList.add('hidden')" class="btn-cancel">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- STYLES --}}
<style>
.th{padding:12px 16px;text-align:left;font-weight:600}
.td{padding:12px 16px}
.badge{padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600}
.badge-green{background:#dcfce7;color:#166534}
.badge-red{background:#fee2e2;color:#991b1b}
.btn-sm{padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600}
.btn-indigo{background:#4f46e5;color:white}
.btn-amber{background:#f59e0b;color:white}
.input{width:100%;padding:10px 12px;border-radius:10px;border:1px solid #cbd5e1}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.5);align-items:center;justify-content:center;z-index:50}
.modal-card{background:white;padding:24px;border-radius:16px;width:100%;max-width:420px}
.modal-title{font-weight:700;margin-bottom:16px}
.modal-actions{display:flex;justify-content:space-between;margin-top:20px}
.btn-cancel{color:#64748b}
</style>

<script src="https://unpkg.com/alpinejs" defer></script>
@endsection
