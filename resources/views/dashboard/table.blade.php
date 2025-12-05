@extends('layout.app')

@section('title', 'Binary Tree View')

@section('content')

<!-- Page Header -->
<header class="bg-white shadow p-4 mb-6">
    <h1 class="text-xl font-bold text-cyan-500">Binary Tree View</h1>
</header>

<!-- Overview Section -->
<section class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Your downline structure and points overview.</h2>
        <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-4 py-2 rounded-lg shadow-sm">
             &larr; Back
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4 shadow-sm" role="alert">
            <p class="font-bold">Success</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
</section>

<!-- Filter Controls -->
<section class="mb-6 bg-white rounded-xl shadow-md">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Filters</h3>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Search --}}
            <div>
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchInput" placeholder="Search by name, referral code..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            
            {{-- Status Filter --}}
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            {{-- Level Filter --}}
            <div>
                <label for="levelFilter" class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <select id="levelFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">All Levels</option>
                    @foreach(range(1, 12) as $level)
                        <option value="{{ $level }}">Level {{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Binary Tree Table -->
<section class="mb-6 bg-white rounded-xl shadow-md">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Downline Structure</h3>
        <div id="tableStatus" class="text-sm text-gray-500">
            Showing <span id="visibleRows">0</span> of <span id="totalRows">0</span> entries
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="binaryTreeTable">
            <thead class="bg-gray-100">
                <tr class="border-b">
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="0">
                        Level <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="1">
                        User <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="2">
                        Referred By <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="3">
                        Parent <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="4">
                        Status <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-center text-gray-600 font-medium sortable" data-column="5">
                        Left Points <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-center text-gray-600 font-medium sortable" data-column="6">
                        Right Points <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="7">
                        Left Users <span class="sort-icon">↓↑</span>
                    </th>
                    <th class="py-3 px-4 text-left text-gray-600 font-medium sortable" data-column="8">
                        Right Users <span class="sort-icon">↓↑</span>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse ($tableData as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-gray-800 font-medium">
                            {{ $row['level'] }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <img class="h-8 w-8 rounded-full object-cover shadow-sm" 
                                     src="{{ asset('/public/storage/'.$row['image']) }}" 
                                     alt="{{ $row['name'] }}"
                                     onerror="this.onerror=null;this.src='https://placehold.co/32x32/f87171/ffffff?text={{ substr($row['name'], 0, 1) }}';">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $row['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['referral_code'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-gray-500">
                            {{ $row['referred_by'] ?? '—' }}
                        </td>
                        <td class="py-3 px-4 text-gray-500">
                            {{ $row['parent'] ?? '—' }}
                        </td>
                        <td class="py-3 px-4">
                            @if(strtolower($row['status']) == 'active')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">{{ ucfirst($row['status']) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-medium text-indigo-500">
                            {{ $row['leftPoints'] }}
                        </td>
                        <td class="py-3 px-4 text-center font-medium text-purple-500">
                            {{ $row['rightPoints'] }}
                        </td>
                        <td class="py-3 px-4 text-gray-500">
                            {{ is_array($row['leftUsers']) ? implode(', ', $row['leftUsers']) : $row['leftUsers'] }}
                        </td>
                        <td class="py-3 px-4 text-gray-500">
                            {{ is_array($row['rightUsers']) ? implode(', ', $row['rightUsers']) : $row['rightUsers'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-500">
                            <i class="fas fa-database text-4xl mb-2 text-gray-400"></i>
                            <h3 class="text-lg font-medium">No Data Available</h3>
                            <p>There is no binary tree data to display at the moment.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

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
    totalRowsSpan.textContent = rows.length;
    visibleRowsSpan.textContent = rows.length;
    
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
            const name = cells[1].querySelector('div > div:first-child').textContent.toLowerCase();
            const referralCode = cells[1].querySelector('div > div:last-child').textContent.toLowerCase();
            const status = cells[4].querySelector('span').textContent.trim().toLowerCase();
    
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
                    <td colspan="9" class="py-12 text-center text-gray-500">
                        <i class="fas fa-database text-4xl mb-2 text-gray-400"></i>
                        <h3 class="text-lg font-medium">No Matching Data Found</h3>
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
                aValue = parseFloat(aValue) || 0;
                bValue = parseFloat(bValue) || 0;
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

<style>
.sortable {
    cursor: pointer;
    position: relative;
}

.sortable:hover {
    background-color: #f3f4f6;
}

.sort-icon {
    margin-left: 4px;
    font-size: 0.75rem;
    opacity: 0.7;
}
</style>
@endsection
