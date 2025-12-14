
@extends('layout.app')
@section('title','Downline Structure')

@push('styles')
<style>
body{
    background:#f8fafc;
    font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont;
    color:#0f172a;
}
.container-xl{
    max-width:1400px;
    margin:auto;
    padding:24px;
}

/* Header */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}
.page-header h1{
    font-size:26px;
    font-weight:600;
}
.page-header p{
    color:#64748b;
    margin-top:4px;
}

/* Stats */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;
    margin-bottom:24px;
}
.stat{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:16px;
}
.stat span{
    font-size:13px;
    color:#64748b;
}
.stat strong{
    display:block;
    font-size:24px;
    margin-top:4px;
}

/* Filters */
.filters{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    background:#fff;
    padding:16px;
    border-radius:12px;
    border:1px solid #e5e7eb;
    margin-bottom:16px;
}
.filters input,.filters select{
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #d1d5db;
    font-size:14px;
}

/* Table */
.table-box{
    background:#fff;
    border-radius:14px;
    border:1px solid #e5e7eb;
    overflow:hidden;
}

table{
    width:100%;
    border-collapse:collapse;
}
thead{
    background:#f1f5f9;
}
th{
    padding:14px;
    font-size:12px;
    text-transform:uppercase;
    color:#64748b;
    text-align:left;
}
td{
    padding:14px;
    border-top:1px solid #e5e7eb;
    font-size:14px;
}
tr:hover{
    background:#f8fafc;
}

/* User */
.user{
    display:flex;
    align-items:center;
    gap:10px;
}
.user img{
    width:40px;
    height:40px;
    border-radius:10px;
}
.user small{
    display:block;
    color:#64748b;
}

/* Badges */
.badge{
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:500;
}
.badge.active{background:#dcfce7;color:#166534;}
.badge.inactive{background:#fee2e2;color:#991b1b;}

.points{
    font-weight:600;
}
.points.left{color:#2563eb;}
.points.right{color:#7c3aed;}
</style>
@endpush

@section('content')
<div class="container-xl">

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1>Downline Structure</h1>
            <p>Binary network overview & performance</p>
        </div>
        <a href="{{ url()->previous() }}">← Back</a>
    </div>

    <!-- Stats -->


    <!-- Filters -->
    <div class="filters">
        <input type="text" placeholder="Search name / code" id="searchInput">
        <select id="statusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <select id="levelFilter">
            <option value="">All Levels</option>
            @foreach(range(0,20) as $l)
                <option value="{{ $l }}">Level {{ $l }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Level</th>
                    <th>User</th>
                    <th>Referred</th>
                    <th>Parent</th>
                    <th>Status</th>
                    <th>Left</th>
                    <th>Right</th>
                    <th>Left Users</th>
                    <th>Right Users</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach($tableData as $row)
                <tr>
                    <td>{{ $row['level'] }}</td>
                    <td>
                        <div class="user">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}">
                            <div>
                                {{ $row['name'] }}
                                <small>{{ $row['referral_code'] }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $row['referred_by'] ?? '—' }}</td>
                    <td>{{ $row['parent'] ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $row['status']=='active'?'active':'inactive' }}">
                            {{ ucfirst($row['status']) }}
                        </span>
                    </td>
                    <td class="points left">{{ $row['leftPoints'] }}</td>
                    <td class="points right">{{ $row['rightPoints'] }}</td>
                    <td>Active {{ $row['leftUsers']['active'] ?? 0 }}</td>
                    <td>Active {{ $row['rightUsers']['active'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
