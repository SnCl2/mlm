@extends('layout.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ tab: 'assigned' }">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">My Activation Keys</h1>
            <p class="text-sm text-slate-500">Manage, use or transfer your PINs</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- TABS --}}
    <div class="flex gap-2 mb-6">
        <button @click="tab='assigned'"
            class="tab-btn"
            :class="tab==='assigned' ? 'tab-active' : 'tab-inactive'">
            Assigned to Me
        </button>
        <button @click="tab='transferred'"
            class="tab-btn"
            :class="tab==='transferred' ? 'tab-active' : 'tab-inactive'">
            Transferred by Me
        </button>
    </div>

    {{-- ASSIGNED --}}
    <div x-show="tab==='assigned'" x-cloak>

        @if($activationKeys->count())

        {{-- BULK ACTION --}}
        <div class="flex justify-end mb-4">
            <button
                onclick="document.getElementById('bulk-transfer-modal').classList.remove('hidden')"
                class="btn-primary">
                Bulk Transfer PINs
            </button>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
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
                                <button onclick="document.getElementById('use-key-{{ $key->id }}').classList.remove('hidden')" class="btn-sm btn-indigo">Use</button>
                                <button onclick="document.getElementById('transfer-key-{{ $key->id }}').classList.remove('hidden')" class="btn-sm btn-amber">Transfer</button>
                            @endif
                        </td>
                    </tr>

                    {{-- USE MODAL --}}
                    <div id="use-key-{{ $key->id }}" class="modal hidden">
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
                                    <button type="button" onclick="document.getElementById('use-key-{{ $key->id }}').classList.add('hidden')" class="btn-cancel">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- TRANSFER MODAL --}}
                    <div id="transfer-key-{{ $key->id }}" class="modal hidden">
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
                                    <button type="button" onclick="document.getElementById('transfer-key-{{ $key->id }}').classList.add('hidden')" class="btn-cancel">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $activationKeys->links() }}</div>

        @else
            <p class="text-slate-500">No activation keys assigned.</p>
        @endif
    </div>

    {{-- TRANSFERRED --}}
    <div x-show="tab==='transferred'" x-cloak>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
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
                        <tr><td colspan="3" class="td text-center text-slate-500 py-6">No transfers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
.th{padding:12px 16px;text-align:left;font-weight:600}
.td{padding:12px 16px}
.tab-btn{padding:8px 16px;border-radius:999px;font-weight:600}
.tab-active{background:#4f46e5;color:white}
.tab-inactive{background:#e5e7eb;color:#475569}
.badge{padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600}
.badge-green{background:#dcfce7;color:#166534}
.badge-red{background:#fee2e2;color:#991b1b}
.btn-primary{background:#4f46e5;color:white;padding:10px 18px;border-radius:12px;font-weight:600}
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
