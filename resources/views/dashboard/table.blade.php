@extends('layout.app')

@section('title', 'Downline Structure')

@section('content')

<div style="
    max-width:1400px;
    margin:0 auto;
    padding:24px;
    font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto;
    background:#f8fafc;
    color:#0f172a;
">

    {{-- Header --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:600;margin:0;">
            Downline Structure
        </h1>
        <p style="margin:6px 0 0;color:#64748b;">
            Network hierarchy overview
        </p>
        <a href="{{ url()->previous() }}" style="
            display:inline-block;
            margin-top:10px;
            font-size:14px;
            color:#2563eb;
            text-decoration:none;
        ">← Back</a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div style="
            background:#dcfce7;
            border:1px solid #86efac;
            color:#166534;
            padding:12px 16px;
            border-radius:8px;
            margin-bottom:20px;
            font-size:14px;
        ">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div style="
        display:flex;
        gap:12px;
        flex-wrap:wrap;
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:10px;
        padding:16px;
        margin-bottom:16px;
    ">
        <input id="searchInput" type="text" placeholder="Search name / code"
            style="padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;min-width:220px;">

        <select id="statusFilter"
            style="padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <select id="levelFilter"
            style="padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
            <option value="">All Levels</option>
            @foreach(range(1,20) as $lvl)
                <option value="{{ $lvl }}">Level {{ $lvl }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div style="
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:12px;
        overflow:auto;
    ">
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f1f5f9;">
                <tr>
                    @foreach([
                        'Level','User','Referred By','Parent',
                        'Status','Left Points','Right Points',
                        'Left Users','Right Users'
                    ] as $head)
                        <th style="
                            padding:14px;
                            text-align:left;
                            font-size:12px;
                            text-transform:uppercase;
                            color:#64748b;
                            border-bottom:1px solid #e5e7eb;
                        ">{{ $head }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody id="tableBody">
            @forelse($tableData as $row)
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td style="padding:14px;font-weight:600;">
                        {{ $row['level'] }}
                    </td>

                    <td style="padding:14px;">
                        <div style="display:flex;gap:10px;align-items:center;">
                            <img
                                src="{{ $row['image'] ? asset('storage/'.$row['image']) : 'https://ui-avatars.com/api/?name='.urlencode($row['name']) }}"
                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}'"
                                style="width:40px;height:40px;border-radius:10px;"
                            >
                            <div>
                                <div style="font-weight:500;">{{ $row['name'] }}</div>
                                <div style="font-size:12px;color:#64748b;">
                                    {{ $row['referral_code'] }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td style="padding:14px;">{{ $row['referred_by'] ?? '—' }}</td>
                    <td style="padding:14px;">{{ $row['parent'] ?? '—' }}</td>

                    <td style="padding:14px;">
                        @if(strtolower($row['status']) === 'active')
                            <span style="background:#dcfce7;color:#166534;padding:4px 10px;border-radius:999px;font-size:12px;">
                                Active
                            </span>
                        @else
                            <span style="background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:999px;font-size:12px;">
                                Inactive
                            </span>
                        @endif
                    </td>

                    <td style="padding:14px;font-weight:600;color:#2563eb;">
                        {{ number_format($row['leftPoints']) }}
                    </td>

                    <td style="padding:14px;font-weight:600;color:#7c3aed;">
                        {{ number_format($row['rightPoints']) }}
                    </td>

                    <td style="padding:14px;font-size:13px;">
                        @if(is_array($row['leftUsers']))
                            A: {{ $row['leftUsers']['active'] ?? 0 }},
                            I: {{ $row['leftUsers']['inactive'] ?? 0 }}
                        @else
                            {{ $row['leftUsers'] }}
                        @endif
                    </td>

                    <td style="padding:14px;font-size:13px;">
                        @if(is_array($row['rightUsers']))
                            A: {{ $row['rightUsers']['active'] ?? 0 }},
                            I: {{ $row['rightUsers']['inactive'] ?? 0 }}
                        @else
                            {{ $row['rightUsers'] }}
                        @endif
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('searchInput');
    const status = document.getElementById('statusFilter');
    const level  = document.getElementById('levelFilter');
    const rows   = document.querySelectorAll('#tableBody tr');

    function filter() {
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            const s = status.value.toLowerCase();
            const l = level.value;

            let show = true;
            if (search.value && !text.includes(search.value.toLowerCase())) show = false;
            if (s && !text.includes(s)) show = false;
            if (l && !text.includes(' ' + l)) show = false;

            r.style.display = show ? '' : 'none';
        });
    }

    [search, status, level].forEach(el => el.addEventListener('input', filter));
});
</script>

@endsection
