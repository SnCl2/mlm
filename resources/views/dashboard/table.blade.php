@extends('layout.app')

@section('title', 'Downline Structure')

@push('styles')
<style>
    /* Modern Page Container */
    .table-page-container {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    /* Modern Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .page-header p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin: 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0.5rem 0;
    }

    .stat-card-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Filter Section */
    .filters-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border: 1px solid #e2e8f0;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .filter-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Modern Table */
    .table-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border: 1px solid #e2e8f0;
    }

    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .modern-table thead th {
        padding: 1rem 1.25rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border: none;
    }

    .modern-table thead th.sortable {
        cursor: pointer;
        user-select: none;
        transition: background 0.2s ease;
        position: relative;
    }

    .modern-table thead th.sortable:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e2e8f0;
    }

    .modern-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .modern-table tbody td {
        padding: 1rem 1.25rem;
        color: #475569;
        font-size: 0.95rem;
    }

    /* User Avatar */
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-details h4 {
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 0.25rem 0;
        font-size: 0.95rem;
    }

    .user-details p {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-badge.active {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 1px solid #10b981;
    }

    .status-badge.inactive {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    /* Points Display */
    .points-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .points-left {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }

    .points-right {
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #6b21a8;
    }

    /* Level Badge */
    .level-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }

    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #94a3b8;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        color: #64748b;
        font-size: 1rem;
    }

    /* Sort Icon */
    .sort-icon {
        margin-left: 0.5rem;
        font-size: 0.75rem;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .sortable:hover .sort-icon {
        opacity: 1;
    }

    /* Back Button */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .back-button:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    /* Success Message */
    .success-message {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-left: 4px solid #10b981;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
    }

    .success-message p {
        margin: 0;
        color: #065f46;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table-page-container {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="table-page-container">
    <!-- Modern Header -->
    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h1>📊 Downline Structure</h1>
                <p>Comprehensive view of your network hierarchy and performance metrics</p>
            </div>
            <a href="{{ url()->previous() }}" class="back-button">
                <span>←</span>
                <span>Back</span>
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="success-message">
        <p>✅ {{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats Cards -->
    @php
        $totalUsers = count($tableData);
        $activeUsers = collect($tableData)->where('status', 'active')->count();
        $totalLeftPoints = collect($tableData)->sum('leftPoints');
        $totalRightPoints = collect($tableData)->sum('rightPoints');
    @endphp
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af;">
                👥
            </div>
            <div class="stat-card-value">{{ $totalUsers }}</div>
            <div class="stat-card-label">Total Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;">
                ✓
            </div>
            <div class="stat-card-value">{{ $activeUsers }}</div>
            <div class="stat-card-label">Active Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af;">
                ⬅️
            </div>
            <div class="stat-card-value">{{ number_format($totalLeftPoints) }}</div>
            <div class="stat-card-label">Total Left Points</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); color: #6b21a8;">
                ➡️
            </div>
            <div class="stat-card-value">{{ number_format($totalRightPoints) }}</div>
            <div class="stat-card-label">Total Right Points</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filters-card">
        <h3 class="text-lg font-bold text-gray-800 mb-4">🔍 Filter & Search</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="searchInput" class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search by name or referral code..." 
                       class="filter-input">
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="filter-input">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label for="levelFilter" class="block text-sm font-semibold text-gray-700 mb-2">Level</label>
                <select id="levelFilter" class="filter-input">
                    <option value="">All Levels</option>
                    @foreach(range(1, 20) as $level)
                        <option value="{{ $level }}">Level {{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Modern Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>📋 Downline Structure</h3>
            <div id="tableStatus" class="text-sm font-medium text-gray-600">
                Showing <span id="visibleRows" class="font-bold text-indigo-600">0</span> of 
                <span id="totalRows" class="font-bold text-gray-800">0</span> entries
            </div>
        </div>
        <div class="table-wrapper">
            <table class="modern-table" id="binaryTreeTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="0">
                            Level <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable" data-column="1">
                            User <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable" data-column="2">
                            Referred By <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable" data-column="3">
                            Parent <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable" data-column="4">
                            Status <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable text-center" data-column="5">
                            Left Points <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable text-center" data-column="6">
                            Right Points <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable" data-column="7">
                            Left Users <span class="sort-icon">↓↑</span>
                        </th>
                        <th class="sortable" data-column="8">
                            Right Users <span class="sort-icon">↓↑</span>
                        </th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse ($tableData as $row)
                        <tr>
                            <td>
                                <span class="level-badge">{{ $row['level'] }}</span>
                            </td>
                            <td>
                                <div class="user-info">
                                    @php
                                        $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($row['name']) . '&background=667eea&color=fff&size=128';
                                        if ($row['image']) {
                                            // Try multiple path variations
                                            $imagePath = $row['image'];
                                            $possiblePaths = [
                                                asset('storage/' . $imagePath),
                                                asset('public/storage/' . $imagePath),
                                                url('storage/' . $imagePath),
                                            ];
                                            $imageUrl = $possiblePaths[0]; // Default to standard path
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
                            <td>
                                <span class="text-gray-600">{{ $row['referred_by'] ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="text-gray-600">{{ $row['parent'] ?? '—' }}</span>
                            </td>
                            <td>
                                @if(strtolower($row['status']) == 'active')
                                    <span class="status-badge active">Active</span>
                                @else
                                    <span class="status-badge inactive">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="points-display points-left">{{ number_format($row['leftPoints']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="points-display points-right">{{ number_format($row['rightPoints']) }}</span>
                            </td>
                            <td>
                                @if(is_array($row['leftUsers']))
                                    <span class="text-gray-600">
                                        Active: {{ $row['leftUsers']['active'] ?? 0 }}, 
                                        Inactive: {{ $row['leftUsers']['inactive'] ?? 0 }}
                                    </span>
                                @else
                                    <span class="text-gray-600">{{ $row['leftUsers'] }}</span>
                                @endif
                            </td>
                            <td>
                                @if(is_array($row['rightUsers']))
                                    <span class="text-gray-600">
                                        Active: {{ $row['rightUsers']['active'] ?? 0 }}, 
                                        Inactive: {{ $row['rightUsers']['inactive'] ?? 0 }}
                                    </span>
                                @else
                                    <span class="text-gray-600">{{ $row['rightUsers'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                <div class="empty-state-icon">📊</div>
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
    const table = document.getElementById('binaryTreeTable');
    const tbody = document.getElementById('tableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const levelFilter = document.getElementById('levelFilter');
    const sortableHeaders = document.querySelectorAll('.sortable');
    const visibleRowsSpan = document.getElementById('visibleRows');
    const totalRowsSpan = document.getElementById('totalRows');
    
    // Initialize variables for sorting
    let currentSortColumn = 0;
    let currentSortDirection = 'asc';
    
    // Set initial counts
    const totalCount = rows.length;
    totalRowsSpan.textContent = totalCount;
    visibleRowsSpan.textContent = totalCount;
    
    // Function to filter and display rows
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const levelValue = levelFilter.value;
    
        let visibleCount = 0;
    
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length === 0) return; // Skip empty state row
            
            const level = cells[0].textContent.trim();
            const name = cells[1].querySelector('.user-details h4')?.textContent.toLowerCase() || '';
            const referralCode = cells[1].querySelector('.user-details p')?.textContent.toLowerCase() || '';
            const statusElement = cells[4].querySelector('.status-badge');
            const status = statusElement ? statusElement.textContent.trim().toLowerCase() : '';
    
            const matchesSearch = searchTerm === '' || 
                                  name.includes(searchTerm) || 
                                  referralCode.includes(searchTerm);
    
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
    
        // Empty row logic
        const existingEmpty = tbody.querySelector('.no-results');
        if (visibleCount === 0) {
            if (!existingEmpty) {
                const emptyRow = document.createElement('tr');
                emptyRow.classList.add('no-results');
                emptyRow.innerHTML = `
                    <td colspan="9" class="empty-state">
                        <div class="empty-state-icon">🔍</div>
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
    
    // Function to sort table
    function sortTable(columnIndex, direction) {
        const rowsArray = Array.from(tbody.querySelectorAll('tr:not(.no-results)'));
        
        rowsArray.sort((a, b) => {
            const aCell = a.querySelectorAll('td')[columnIndex];
            const bCell = b.querySelectorAll('td')[columnIndex];
            
            let aValue = aCell.textContent.trim();
            let bValue = bCell.textContent.trim();
            
            // Special handling for numeric columns (Level, Left Points, Right Points)
            if (columnIndex === 0 || columnIndex === 5 || columnIndex === 6) {
                aValue = parseFloat(aValue.replace(/,/g, '')) || 0;
                bValue = parseFloat(bValue.replace(/,/g, '')) || 0;
                return direction === 'asc' ? aValue - bValue : bValue - aValue;
            }
            
            // Special handling for status column (Active comes first)
            if (columnIndex === 4) {
                if (aValue === 'Active' && bValue !== 'Active') return direction === 'asc' ? -1 : 1;
                if (aValue !== 'Active' && bValue === 'Active') return direction === 'asc' ? 1 : -1;
            }
            
            // Default string comparison
            return direction === 'asc' 
                ? aValue.localeCompare(bValue) 
                : bValue.localeCompare(aValue);
        });
        
        // Clear existing rows (except the no-results row if it exists)
        const existingEmpty = tbody.querySelector('.no-results');
        tbody.innerHTML = '';
        if (existingEmpty) tbody.appendChild(existingEmpty);
        
        // Append sorted rows
        rowsArray.forEach(row => tbody.appendChild(row));
    }
    
    // Event listeners for filtering
    searchInput.addEventListener('input', filterRows);
    statusFilter.addEventListener('change', filterRows);
    levelFilter.addEventListener('change', filterRows);
    
    // Event listeners for sorting
    sortableHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const columnIndex = parseInt(header.dataset.column);
            
            // Update sort direction
            if (currentSortColumn === columnIndex) {
                currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = columnIndex;
                currentSortDirection = 'asc';
            }
            
            // Update UI to show current sort column and direction
            sortableHeaders.forEach(h => {
                h.querySelector('.sort-icon').textContent = '↓↑';
            });
            
            const icon = header.querySelector('.sort-icon');
            icon.textContent = currentSortDirection === 'asc' ? '↑' : '↓';
            
            // Perform the sort
            sortTable(columnIndex, currentSortDirection);
        });
    });
    
    // Initialize the table
    filterRows();
});
</script>
@endsection
