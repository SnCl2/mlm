@extends('layout.app')

@section('title', 'Downline Structure')

@push('styles')
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #0f172a;
        color: #e2e8f0;
        margin: 0;
        padding: 0;
    }

    .table-page {
        min-height: 100vh;
        padding: 2rem;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Header */
    .page-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 24px;
        padding: 3rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
    }

    .page-header h1 {
        font-size: 3rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
    }

    .page-header p {
        font-size: 1.125rem;
        color: #94a3b8;
        margin: 0;
        font-weight: 400;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: rgba(96, 165, 250, 0.1);
        border: 1px solid rgba(96, 165, 250, 0.3);
        border-radius: 12px;
        color: #60a5fa;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        margin-top: 1.5rem;
    }

    .back-btn:hover {
        background: rgba(96, 165, 250, 0.2);
        border-color: rgba(96, 165, 250, 0.5);
        transform: translateX(-4px);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 20px;
        padding: 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #60a5fa, #a78bfa, #f472b6);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        border-color: rgba(148, 163, 184, 0.2);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.2), rgba(167, 139, 250, 0.2));
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #f8fafc;
        margin: 0.5rem 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Filters */
    .filters-section {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .filters-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f8fafc;
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .filter-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filter-input {
        width: 100%;
        padding: 0.875rem 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 12px;
        color: #f8fafc;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .filter-input:focus {
        outline: none;
        border-color: #60a5fa;
        background: rgba(15, 23, 42, 0.8);
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }

    .filter-input::placeholder {
        color: #64748b;
    }

    /* Table Container */
    .table-container {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }

    .table-header-bar {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(15, 23, 42, 0.5);
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f8fafc;
        margin: 0;
    }

    .table-count {
        font-size: 0.875rem;
        color: #94a3b8;
    }

    .table-count strong {
        color: #60a5fa;
        font-weight: 700;
    }

    /* Table */
    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: rgba(15, 23, 42, 0.8);
    }

    th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        cursor: pointer;
        user-select: none;
        transition: all 0.2s;
    }

    th:hover {
        color: #60a5fa;
        background: rgba(96, 165, 250, 0.05);
    }

    th.sort-icon {
        margin-left: 0.5rem;
        opacity: 0.5;
        font-size: 0.625rem;
    }

    tbody tr {
        border-bottom: 1px solid rgba(148, 163, 184, 0.05);
        transition: all 0.2s;
    }

    tbody tr:hover {
        background: rgba(96, 165, 250, 0.05);
        transform: scale(1.01);
    }

    td {
        padding: 1.25rem 1.5rem;
        color: #e2e8f0;
        font-size: 0.95rem;
    }

    /* Level Badge */
    .level-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #60a5fa, #a78bfa);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 6px rgba(96, 165, 250, 0.3);
    }

    /* User Info */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid rgba(96, 165, 250, 0.3);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .user-details h4 {
        font-weight: 600;
        color: #f8fafc;
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
    }

    .user-details p {
        font-size: 0.75rem;
        color: #94a3b8;
        margin: 0;
        font-family: 'Courier New', monospace;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-badge.active {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .status-badge.inactive {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* Points */
    .points {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.875rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .points-left {
        background: rgba(96, 165, 250, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(96, 165, 250, 0.3);
    }

    .points-right {
        background: rgba(167, 139, 250, 0.2);
        color: #a78bfa;
        border: 1px solid rgba(167, 139, 250, 0.3);
    }

    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: rgba(96, 165, 250, 0.1);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f8fafc;
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        color: #94a3b8;
        font-size: 1rem;
        margin: 0;
    }

    /* Success Message */
    .success-msg {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-left: 4px solid #22c55e;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        color: #22c55e;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table-page {
            padding: 1rem;
        }

        .page-header {
            padding: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        th, td {
            padding: 1rem;
            font-size: 0.875rem;
        }
    }

    /* Scrollbar */
    .table-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: rgba(96, 165, 250, 0.5);
        border-radius: 4px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(96, 165, 250, 0.7);
    }
</style>
@endpush

@section('content')
<div class="table-page">
    <!-- Header -->
    <div class="page-header">
        <h1>Downline Structure</h1>
        <p>Comprehensive view of your network hierarchy and performance metrics</p>
        <a href="{{ url()->previous() }}" class="back-btn">
            <span>←</span>
            <span>Back</span>
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="success-msg">
        ✓ {{ session('success') }}
    </div>
    @endif

    <!-- Stats -->
    @php
        $totalUsers = count($tableData);
        $activeUsers = collect($tableData)->where('status', 'active')->count();
        $totalLeftPoints = collect($tableData)->sum('leftPoints');
        $totalRightPoints = collect($tableData)->sum('rightPoints');
    @endphp
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✓</div>
            <div class="stat-value">{{ $activeUsers }}</div>
            <div class="stat-label">Active Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⬅️</div>
            <div class="stat-value">{{ number_format($totalLeftPoints) }}</div>
            <div class="stat-label">Left Points</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">➡️</div>
            <div class="stat-value">{{ number_format($totalRightPoints) }}</div>
            <div class="stat-label">Right Points</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <h3 class="filters-title">
            <span>🔍</span>
            <span>Filter & Search</span>
        </h3>
        <div class="filters-grid">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="searchInput" placeholder="Search by name or code..." class="filter-input">
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select id="statusFilter" class="filter-input">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Level</label>
                <select id="levelFilter" class="filter-input">
                    <option value="">All Levels</option>
                    @foreach(range(1, 20) as $level)
                        <option value="{{ $level }}">Level {{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-header-bar">
            <h3 class="table-title">Downline Structure</h3>
            <div class="table-count">
                Showing <strong id="visibleRows">0</strong> of <strong id="totalRows">0</strong> entries
            </div>
        </div>
        <div class="table-wrapper">
            <table id="binaryTreeTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="0">Level <span class="sort-icon">↓↑</span></th>
                        <th class="sortable" data-column="1">User <span class="sort-icon">↓↑</span></th>
                        <th class="sortable" data-column="2">Referred By <span class="sort-icon">↓↑</span></th>
                        <th class="sortable" data-column="3">Parent <span class="sort-icon">↓↑</span></th>
                        <th class="sortable" data-column="4">Status <span class="sort-icon">↓↑</span></th>
                        <th class="sortable text-center" data-column="5">Left Points <span class="sort-icon">↓↑</span></th>
                        <th class="sortable text-center" data-column="6">Right Points <span class="sort-icon">↓↑</span></th>
                        <th class="sortable" data-column="7">Left Users <span class="sort-icon">↓↑</span></th>
                        <th class="sortable" data-column="8">Right Users <span class="sort-icon">↓↑</span></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse ($tableData as $row)
                        <tr>
                            <td><span class="level-badge">{{ $row['level'] }}</span></td>
                            <td>
                                <div class="user-cell">
                                    @php
                                        $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($row['name']) . '&background=60a5fa&color=fff&size=128';
                                        if ($row['image']) {
                                            $imagePath = $row['image'];
                                            $imageUrl = asset('storage/' . $imagePath);
                                        } else {
                                            $imageUrl = $fallbackUrl;
                                        }
                                    @endphp
                                    <img class="user-avatar" 
                                         src="{{ $imageUrl }}" 
                                         alt="{{ $row['name'] }}"
                                         onerror="this.src='{{ $fallbackUrl }}';"
                                         loading="lazy">
                                    <div class="user-details">
                                        <h4>{{ $row['name'] }}</h4>
                                        <p>{{ $row['referral_code'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $row['referred_by'] ?? '—' }}</td>
                            <td>{{ $row['parent'] ?? '—' }}</td>
                            <td>
                                @if(strtolower($row['status']) == 'active')
                                    <span class="status-badge active">Active</span>
                                @else
                                    <span class="status-badge inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="points points-left">{{ number_format($row['leftPoints']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="points points-right">{{ number_format($row['rightPoints']) }}</span>
                            </td>
                            <td>
                                @if(is_array($row['leftUsers']))
                                    Active: {{ $row['leftUsers']['active'] ?? 0 }}, Inactive: {{ $row['leftUsers']['inactive'] ?? 0 }}
                                @else
                                    {{ $row['leftUsers'] }}
                                @endif
                            </td>
                            <td>
                                @if(is_array($row['rightUsers']))
                                    Active: {{ $row['rightUsers']['active'] ?? 0 }}, Inactive: {{ $row['rightUsers']['inactive'] ?? 0 }}
                                @else
                                    {{ $row['rightUsers'] }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                <div class="empty-icon">📊</div>
                                <h3>No Data Available</h3>
                                <p>There is no binary tree data to display at the moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('tableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const levelFilter = document.getElementById('levelFilter');
    const sortableHeaders = document.querySelectorAll('.sortable');
    const visibleRowsSpan = document.getElementById('visibleRows');
    const totalRowsSpan = document.getElementById('totalRows');
    
    let currentSortColumn = 0;
    let currentSortDirection = 'asc';
    
    const totalCount = rows.length;
    totalRowsSpan.textContent = totalCount;
    visibleRowsSpan.textContent = totalCount;
    
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const levelValue = levelFilter.value;
        let visibleCount = 0;
    
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length === 0) return;
            
            const level = cells[0].textContent.trim();
            const name = cells[1].querySelector('.user-details h4')?.textContent.toLowerCase() || '';
            const referralCode = cells[1].querySelector('.user-details p')?.textContent.toLowerCase() || '';
            const statusElement = cells[4].querySelector('.status-badge');
            const status = statusElement ? statusElement.textContent.trim().toLowerCase() : '';
    
            const matchesSearch = searchTerm === '' || name.includes(searchTerm) || referralCode.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            const matchesLevel = levelValue === '' || level === levelValue;
    
            if (matchesSearch && matchesStatus && matchesLevel) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    
        visibleRowsSpan.textContent = visibleCount;
    
        const existingEmpty = tbody.querySelector('.no-results');
        if (visibleCount === 0) {
            if (!existingEmpty) {
                const emptyRow = document.createElement('tr');
                emptyRow.classList.add('no-results');
                emptyRow.innerHTML = `
                    <td colspan="9" class="empty-state">
                        <div class="empty-icon">🔍</div>
                        <h3>No Matching Data Found</h3>
                        <p>Try adjusting your search or filter criteria.</p>
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
        } else {
            if (existingEmpty) existingEmpty.remove();
        }
    }
    
    function sortTable(columnIndex, direction) {
        const rowsArray = Array.from(tbody.querySelectorAll('tr:not(.no-results)'));
        
        rowsArray.sort((a, b) => {
            const aCell = a.querySelectorAll('td')[columnIndex];
            const bCell = b.querySelectorAll('td')[columnIndex];
            
            let aValue = aCell.textContent.trim();
            let bValue = bCell.textContent.trim();
            
            if (columnIndex === 0 || columnIndex === 5 || columnIndex === 6) {
                aValue = parseFloat(aValue.replace(/,/g, '')) || 0;
                bValue = parseFloat(bValue.replace(/,/g, '')) || 0;
                return direction === 'asc' ? aValue - bValue : bValue - aValue;
            }
            
            if (columnIndex === 4) {
                if (aValue === 'Active' && bValue !== 'Active') return direction === 'asc' ? -1 : 1;
                if (aValue !== 'Active' && bValue === 'Active') return direction === 'asc' ? 1 : -1;
            }
            
            return direction === 'asc' 
                ? aValue.localeCompare(bValue) 
                : bValue.localeCompare(aValue);
        });
        
        const existingEmpty = tbody.querySelector('.no-results');
        tbody.innerHTML = '';
        if (existingEmpty) tbody.appendChild(existingEmpty);
        rowsArray.forEach(row => tbody.appendChild(row));
    }
    
    searchInput.addEventListener('input', filterRows);
    statusFilter.addEventListener('change', filterRows);
    levelFilter.addEventListener('change', filterRows);
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const columnIndex = parseInt(header.dataset.column);
            
            if (currentSortColumn === columnIndex) {
                currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = columnIndex;
                currentSortDirection = 'asc';
            }
            
            sortableHeaders.forEach(h => {
                h.querySelector('.sort-icon').textContent = '↓↑';
            });
            
            const icon = header.querySelector('.sort-icon');
            icon.textContent = currentSortDirection === 'asc' ? '↑' : '↓';
            
            sortTable(columnIndex, currentSortDirection);
        });
    });
    
    filterRows();
});
</script>
@endsection
