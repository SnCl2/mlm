@extends('layout.app')

@section('title','Downline Structure')

@section('content')

<div style="
    max-width:1400px;
    margin:0 auto;
    padding:24px;
    font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto;
    background:#f8fafc;
    color:#0f172a;
">

    {{-- HEADER BAR --}}
    <div style="
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:18px 22px;
        background:linear-gradient(135deg,#0f172a,#1e293b);
        border-radius:16px;
        margin-bottom:22px;
        color:#ffffff;
    ">
        <div>
            <div style="font-size:22px;font-weight:600;">
                Downline Structure
            </div>
            <div style="font-size:13px;opacity:.85;">
                Network hierarchy overview
            </div>
        </div>

        <a href="{{ url()->previous() }}" style="
            color:#38bdf8;
            font-size:14px;
            text-decoration:none;
            font-weight:500;
        ">← Back</a>
    </div>

    {{-- FILTER BAR --}}
    <div style="
        display:flex;
        gap:12px;
        flex-wrap:wrap;
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:16px;
        margin-bottom:18px;
    ">
        <input id="searchInput" type="text" placeholder="Search name / code"
            style="padding:10px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;min-width:220px;">

        <select id="statusFilter"
            style="padding:10px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <select id="levelFilter"
            style="padding:10px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;">
            <option value="">All Levels</option>
            @foreach(range(1,15) as $lvl)
                <option value="{{ $lvl }}">Level {{ $lvl }}</option>
            @endforeach
        </select>
    </div>

    {{-- TABLE --}}
    <div style="
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:16px;
        overflow:auto;
    ">
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f1f5f9;">
                <tr>
                    @foreach([
                        'Level','User','Referred By','Parent',
                        'Status','Left Points','Right Points',
                        'Left Users','Right Users'
                    ] as $h)
                        <th style="
                            padding:14px;
                            text-align:left;
                            font-size:12px;
                            text-transform:uppercase;
                            color:#475569;
                            border-bottom:1px solid #e5e7eb;
                        ">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody id="tableBody">
            @forelse($tableData as $row)

                {{-- OPTIONAL: hide root (level 0) --}}
                @continue($row['level'] === 0)

                <tr
                    data-level="{{ $row['level'] }}"
                    data-status="{{ strtolower($row['status']) }}"
                    style="border-bottom:1px solid #e5e7eb;"
                >
                    <td style="padding:14px;font-weight:600;">
                        {{ $row['level'] }}
                    </td>

                    {{-- USER --}}
                    <td style="padding:14px;">
                        <div style="display:flex;gap:12px;align-items:center;">
                            <img
                                src="{{ $row['image'] ? asset('storage/'.$row['image']) : 'https://ui-avatars.com/api/?name='.urlencode($row['name']) }}"
                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}'"
                                style="width:44px;height:44px;border-radius:12px;background:#e5e7eb;"
                            >
                            <div>
                                <div style="font-weight:600;">{{ $row['name'] }}</div>
                                <div style="font-size:12px;color:#64748b;">
                                    {{ $row['referral_code'] }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td style="padding:14px;">{{ $row['referred_by'] ?? '—' }}</td>
                    <td style="padding:14px;">{{ $row['parent'] ?? '—' }}</td>

                    {{-- STATUS --}}
                    <td style="padding:14px;">
                        @if(strtolower($row['status']) === 'active')
                            <span style="
                                background:#dcfce7;
                                color:#166534;
                                padding:6px 14px;
                                border-radius:999px;
                                font-size:12px;
                                font-weight:500;
                            ">Active</span>
                        @else
                            <span style="
                                background:#fee2e2;
                                color:#991b1b;
                                padding:6px 14px;
                                border-radius:999px;
                                font-size:12px;
                                font-weight:500;
                            ">Inactive</span>
                        @endif
                    </td>

                    {{-- POINTS --}}
                    <td style="padding:14px;font-weight:700;color:#2563eb;">
                        {{ number_format($row['leftPoints']) }}
                    </td>

                    <td style="padding:14px;font-weight:700;color:#7c3aed;">
                        {{ number_format($row['rightPoints']) }}
                    </td>

                    {{-- LEFT USERS --}}
                    <td style="padding:14px;">
                        <span style="background:#dcfce7;color:#166534;padding:5px 12px;border-radius:999px;font-size:12px;">
                            Active {{ $row['leftUsers']['active'] ?? 0 }}
                        </span>
                        <span style="background:#fee2e2;color:#991b1b;padding:5px 12px;border-radius:999px;font-size:12px;">
                            Inactive {{ $row['leftUsers']['inactive'] ?? 0 }}
                        </span>
                    </td>

                    {{-- RIGHT USERS --}}
                    <td style="padding:14px;">
                        <span style="background:#e0f2fe;color:#075985;padding:5px 12px;border-radius:999px;font-size:12px;">
                            Active {{ $row['rightUsers']['active'] ?? 0 }}
                        </span>
                        <span style="background:#ffe4e6;color:#9f1239;padding:5px 12px;border-radius:999px;font-size:12px;">
                            Inactive {{ $row['rightUsers']['inactive'] ?? 0 }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding:40px;text-align:center;color:#64748b;">
                        No data available
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- FIXED FILTER SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('searchInput');
    const status = document.getElementById('statusFilter');
    const level  = document.getElementById('levelFilter');
    const rows   = document.querySelectorAll('#tableBody tr');

    function filterTable() {
        rows.forEach(row => {
            let show = true;

            const text   = row.innerText.toLowerCase();
            const rLevel = row.dataset.level;
            const rStatus= row.dataset.status;

            if (search.value && !text.includes(search.value.toLowerCase())) show = false;
            if (status.value && rStatus !== status.value) show = false;
            if (level.value && rLevel !== level.value) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    [search, status, level].forEach(el =>
        el.addEventListener('input', filterTable)
    );
});
</script>

@endsection
