@extends('layout.app')

@section('title', 'Binary Tree Structure')

@push('styles')
<style>
    .tree-container-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .tree-container {
        width: 100%;
        height: 80vh;
        min-height: 600px;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        overflow: auto;
        position: relative;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .tree-container::-webkit-scrollbar {
        width: 12px;
        height: 12px;
    }

    .tree-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }

    .tree-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 6px;
    }

    .tree-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .node {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .node:hover {
        opacity: 0.9;
        transform: scale(1.08);
    }

    .node:hover .node-rect.active {
        filter: drop-shadow(0 6px 12px rgba(34, 197, 94, 0.5));
    }

    .node:hover .node-rect.vacant {
        filter: drop-shadow(0 6px 12px rgba(59, 130, 246, 0.5));
    }

    .node-rect {
        fill: #fff;
        stroke-width: 2px;
        stroke: #cbd5e1;
        transition: all 0.3s ease;
        rx: 8;
        ry: 8;
    }

    .node-rect.active {
        fill: #22c55e;
        stroke: #16a34a;
        filter: drop-shadow(0 4px 6px rgba(34, 197, 94, 0.3));
    }

    .node-rect.inactive {
        fill: #ef4444;
        stroke: #dc2626;
    }

    .node-rect.vacant {
        fill: #3b82f6;
        stroke: #2563eb;
        stroke-dasharray: 5,5;
        filter: drop-shadow(0 4px 6px rgba(59, 130, 246, 0.3));
        cursor: pointer;
    }

    .node-rect.vacant:hover {
        fill: #2563eb;
        stroke: #1d4ed8;
        transform: scale(1.05);
    }

    .node-text {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12px;
        fill: #ffffff;
        text-anchor: middle;
        pointer-events: none;
        font-weight: 600;
    }

    .node-text.name {
        font-size: 13px;
        font-weight: 700;
        fill: #ffffff;
    }

    .node-text.code {
        font-size: 10px;
        fill: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }

    .node-text.vacant-text {
        fill: #ffffff;
        font-weight: 700;
    }

    .node-text.register-link {
        fill: #ffffff;
        font-size: 9px;
        text-decoration: underline;
        cursor: pointer;
    }

    .link {
        fill: none;
        stroke: #94a3b8;
        stroke-width: 2px;
        transition: stroke 0.3s ease;
    }

    .link:hover {
        stroke: #64748b;
        stroke-width: 3px;
    }

    .vacant-link {
        font-size: 9px;
        fill: #fff;
        text-decoration: underline;
        cursor: pointer;
    }

    .controls {
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .btn {
        padding: 8px 16px;
        margin: 0 5px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: white;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .legend {
        display: flex;
        gap: 20px;
        margin-top: 15px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid;
    }

    .legend-color.active {
        background: #22c55e;
        border-color: #16a34a;
    }

    .legend-color.inactive {
        background: #ef4444;
        border-color: #dc2626;
    }

    .legend-color.vacant {
        background: #3b82f6;
        border-color: #2563eb;
        border-style: dashed;
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:px-8">
    <!-- Controls -->
    <div class="controls">
        <div class="flex flex-wrap items-center gap-3">
            <button id="zoom-in" class="btn">🔍 Zoom In</button>
            <button id="zoom-out" class="btn">🔍 Zoom Out</button>
            <button id="reset-zoom" class="btn">🔄 Reset</button>
            <button id="center-tree" class="btn">🎯 Center</button>
            <span class="text-sm text-gray-600">Zoom: <span id="zoom-level">100%</span></span>
        </div>
    </div>

    <!-- Tree Container - Enhanced Styling -->
    <div class="tree-container-wrapper">
        <div class="tree-container" id="tree-container"></div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color active"></div>
            <span class="text-sm">Active Member</span>
        </div>
        <div class="legend-item">
            <div class="legend-color inactive"></div>
            <span class="text-sm">Inactive Member</span>
        </div>
        <div class="legend-item">
            <div class="legend-color vacant"></div>
            <span class="text-sm">Vacant Position</span>
        </div>
        <div class="legend-item ml-4 pl-4 border-l border-gray-300">
            <span class="text-xs text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Click vacant to register | Right-click node to add user
            </span>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Add User Under <span id="parent-name-modal"></span></h2>
            <button onclick="closeAddUserModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('register') }}" method="GET" id="add-user-form">
            <input type="hidden" name="place_under" id="modal-place-under">
            <input type="hidden" name="side" id="modal-position">
            <input type="hidden" name="referred_by" value="{{ Auth::user()->referral_code }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Parent User</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm font-semibold text-gray-800" id="parent-info-display"></p>
                    <p class="text-xs text-gray-500 mt-1" id="parent-code-display"></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm font-semibold text-gray-800" id="position-display"></p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-user-plus mr-2"></i> Continue to Registration
                </button>
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Tree Data -->
<script>
    const treeData = @json($treeData ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
</script>
<script src="https://d3js.org/d3.v7.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const containerEl = document.getElementById('tree-container');
    
    if (!treeData || !treeData.id) {
        containerEl.innerHTML = '<div class="text-center p-8 text-gray-500">No tree data available. Please check if you have any referrals.</div>';
        return;
    }

    // Configuration
    const config = {
        nodeWidth: 140,
        nodeHeight: 60,
        nodeSpacing: { x: 180, y: 100 },
        minZoom: 0.3,
        maxZoom: 3,
        zoomStep: 0.2
    };

    // Setup SVG
    const container = d3.select('#tree-container');
    const width = container.node().offsetWidth || 1200;
    const height = container.node().offsetHeight || 800;

    const svg = d3.select('#tree-container')
        .append('svg')
        .attr('width', width)
        .attr('height', height);

    const g = svg.append('g');

    // Zoom behavior
    const zoom = d3.zoom()
        .scaleExtent([config.minZoom, config.maxZoom])
        .on('zoom', (event) => {
            g.attr('transform', event.transform);
            updateZoomLevel(event.transform.k);
        });

    svg.call(zoom);

    // Transform tree data to D3 hierarchy format
    function transformToD3Hierarchy(node, parent = null, position = null) {
        if (!node) return null;

        const d3Node = {
            id: node.id || node.user_id || Math.random(),
            name: node.name || 'Unknown',
            referral_code: node.referral_code || '',
            status: node.status || (node.is_active ? 'active' : 'inactive'),
            is_active: node.is_active !== undefined ? node.is_active : (node.status === 'active'),
            parent: parent,
            children: [],
            position: position || node.position || null,
            parent_referral_code: parent ? (parent.referral_code || '') : '',
            is_vacant: false
        };

        // Process children - filter out null values
        const children = Array.isArray(node.children) ? node.children.filter(c => c !== null && c !== undefined) : [];
        
        // Controller returns children as array: [leftChild, rightChild]
        // First element (index 0) is left, second (index 1) is right
        const leftChild = children[0] || null;
        const rightChild = children[1] || null;

        // Add left child or vacant
        if (leftChild && leftChild.id) {
            const left = transformToD3Hierarchy(leftChild, d3Node, 'left');
            if (left) d3Node.children.push(left);
        } else {
            d3Node.children.push({
                id: `vacant-left-${d3Node.id}`,
                name: 'Vacant',
                referral_code: '',
                status: 'vacant',
                is_active: false,
                position: 'left',
                parent: d3Node,
                parent_name: d3Node.name || 'Unknown',
                parent_referral_code: d3Node.referral_code || '',
                children: [],
                is_vacant: true
            });
        }

        // Add right child or vacant
        if (rightChild && rightChild.id) {
            const right = transformToD3Hierarchy(rightChild, d3Node, 'right');
            if (right) d3Node.children.push(right);
        } else {
            d3Node.children.push({
                id: `vacant-right-${d3Node.id}`,
                name: 'Vacant',
                referral_code: '',
                status: 'vacant',
                is_active: false,
                position: 'right',
                parent: d3Node,
                parent_name: d3Node.name || 'Unknown',
                parent_referral_code: d3Node.referral_code || '',
                children: [],
                is_vacant: true
            });
        }

        return d3Node;
    }

    // Build tree structure
    let rootData;
    try {
        rootData = transformToD3Hierarchy(treeData);
        if (!rootData) {
            throw new Error('No tree data available');
        }
    } catch (error) {
        console.error('Error transforming tree data:', error);
        document.getElementById('tree-container').innerHTML = '<div class="text-center p-8 text-gray-500">Error loading tree data. Please refresh the page.</div>';
        return;
    }

    const root = d3.hierarchy(rootData);

    // Create tree layout - HORIZONTAL (left to right)
    // Swap x and y: x becomes horizontal distance, y becomes vertical distance
    const treeLayout = d3.tree()
        .nodeSize([config.nodeSpacing.y, config.nodeSpacing.x])
        .separation((a, b) => {
            return a.parent === b.parent ? 1 : 1.2;
        });

    treeLayout(root);

    // Draw links - HORIZONTAL (left to right)
    // In horizontal layout: x is horizontal position, y is vertical position
    const links = g.selectAll('.link')
        .data(root.links())
        .enter()
        .append('path')
        .attr('class', 'link')
        .attr('d', d3.linkHorizontal()
            .x(d => d.y)  // y becomes horizontal
            .y(d => d.x)  // x becomes vertical
        );

    // Draw nodes - HORIZONTAL (y is horizontal position, x is vertical position)
    const nodes = g.selectAll('.node')
        .data(root.descendants())
        .enter()
        .append('g')
        .attr('class', 'node')
        .attr('transform', d => `translate(${d.y},${d.x})`);

    // Add rectangles instead of circles
    nodes.append('rect')
        .attr('class', d => {
            if (d.data.is_vacant) return 'node-rect vacant';
            return d.data.is_active ? 'node-rect active' : 'node-rect inactive';
        })
        .attr('width', config.nodeWidth)
        .attr('height', config.nodeHeight)
        .attr('x', -config.nodeWidth / 2)
        .attr('y', -config.nodeHeight / 2)
        .style('fill', d => {
            if (d.data.is_vacant) return '#3b82f6';
            return d.data.is_active ? '#22c55e' : '#ef4444';
        })
        .style('stroke', d => {
            if (d.data.is_vacant) return '#2563eb';
            return d.data.is_active ? '#16a34a' : '#dc2626';
        })
        .style('stroke-width', d => d.data.is_vacant ? '2px' : '3px')
        .style('stroke-dasharray', d => d.data.is_vacant ? '5,5' : 'none')
        .attr('title', d => {
            if (d.data.is_vacant) return 'Click to register a new user here';
            return `Right-click or Ctrl+Click to add user under ${d.data.name}`;
        })
        .on('click', function(event, d) {
            if (d.data.is_vacant) {
                openAddUserModal(d.data);
            } else {
                // Show context menu or allow adding user under this node
                if (event.ctrlKey || event.metaKey) {
                    // Ctrl/Cmd + Click to add user under this node
                    openAddUserUnderNode(d);
                } else {
                    showNodeInfo(d.data);
                }
            }
        })
        .on('contextmenu', function(event, d) {
            event.preventDefault();
            if (!d.data.is_vacant) {
                openAddUserUnderNode(d);
            }
        });

    // Add name text inside rectangle
    nodes.append('text')
        .attr('class', 'node-text name')
        .attr('dy', -12)
        .attr('dx', 0)
        .style('fill', '#ffffff')
        .style('font-size', '13px')
        .style('font-weight', '700')
        .style('text-anchor', 'middle')
        .text(d => {
            if (d.data.is_vacant) return 'Vacant';
            const name = d.data.name || 'Unknown';
            return name.length > 16 ? name.substring(0, 16) + '...' : name;
        });

    // Add referral code text inside rectangle
    nodes.append('text')
        .attr('class', d => d.data.is_vacant ? 'node-text vacant-text' : 'node-text code')
        .attr('dy', 5)
        .attr('dx', 0)
        .style('fill', '#ffffff')
        .style('font-size', '11px')
        .style('font-weight', '500')
        .style('text-anchor', 'middle')
        .text(d => {
            if (d.data.is_vacant) return d.data.position.toUpperCase();
            return d.data.referral_code || '';
        });

    // Add register link for vacant nodes
    nodes.filter(d => d.data.is_vacant)
        .append('text')
        .attr('class', 'node-text register-link')
        .attr('dy', 20)
        .attr('dx', 0)
        .style('fill', '#ffffff')
        .style('font-size', '10px')
        .style('text-decoration', 'underline')
        .style('text-anchor', 'middle')
        .style('cursor', 'pointer')
        .text('Register')
        .on('click', function(event, d) {
            event.stopPropagation();
            openAddUserModal(d.data);
        });

    // Center the tree - HORIZONTAL
    function centerTree() {
        const bounds = g.node().getBBox();
        const fullWidth = width;
        const fullHeight = height;
        const widthScale = (fullWidth - 100) / bounds.width;
        const heightScale = (fullHeight - 100) / bounds.height;
        const scale = Math.min(widthScale, heightScale, 1);
        
        const translate = [
            fullWidth / 2 - scale * (bounds.x + bounds.width / 2),
            fullHeight / 2 - scale * (bounds.y + bounds.height / 2)
        ];

        svg.transition()
            .duration(750)
            .call(zoom.transform, d3.zoomIdentity.translate(translate[0], translate[1]).scale(scale));
    }

    // Zoom functions
    function zoomIn() {
        svg.transition()
            .duration(300)
            .call(zoom.scaleBy, 1 + config.zoomStep);
    }

    function zoomOut() {
        svg.transition()
            .duration(300)
            .call(zoom.scaleBy, 1 - config.zoomStep);
    }

    function resetZoom() {
        svg.transition()
            .duration(750)
            .call(zoom.transform, d3.zoomIdentity);
        centerTree();
    }

    function updateZoomLevel(scale) {
        document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
    }

    // Event listeners
    document.getElementById('zoom-in').addEventListener('click', zoomIn);
    document.getElementById('zoom-out').addEventListener('click', zoomOut);
    document.getElementById('reset-zoom').addEventListener('click', resetZoom);
    document.getElementById('center-tree').addEventListener('click', centerTree);

    // Show node info
    function showNodeInfo(data) {
        const info = `
Name: ${data.name}
Referral Code: ${data.referral_code || 'N/A'}
Status: ${data.status || 'unknown'}
        `.trim();
        alert(info);
    }

    // Add User Modal Functions
    function openAddUserModal(data) {
        const modal = document.getElementById('add-user-modal');
        const parentName = data.parent_name || data.parent?.name || 'Selected User';
        const parentCode = data.parent_referral_code || '';
        const position = data.position || '';

        document.getElementById('parent-name-modal').textContent = parentName;
        document.getElementById('parent-info-display').textContent = `Name: ${parentName}`;
        document.getElementById('parent-code-display').textContent = `Referral Code: ${parentCode}`;
        document.getElementById('position-display').textContent = position.toUpperCase();
        document.getElementById('modal-place-under').value = parentCode;
        document.getElementById('modal-position').value = position;

        modal.classList.remove('hidden');
    }

    function openAddUserUnderNode(d3Node) {
        const modal = document.getElementById('add-user-modal');
        const data = d3Node.data;
        const parentName = data.name || 'Selected User';
        const parentCode = data.referral_code || '';
        
        // Check which position is available by looking at children
        // In D3 hierarchy, d3Node.children is an array of D3 nodes
        const children = d3Node.children || [];
        const leftChild = children.find(c => c && c.data && c.data.position === 'left');
        const rightChild = children.find(c => c && c.data && c.data.position === 'right');
        
        const hasLeft = leftChild && !leftChild.data.is_vacant;
        const hasRight = rightChild && !rightChild.data.is_vacant;
        
        let position = 'left';
        if (hasLeft && !hasRight) {
            position = 'right';
        } else if (!hasLeft) {
            position = 'left';
        } else if (hasLeft && hasRight) {
            // Both filled, default to left but user can change in registration form
            position = 'left';
        }

        document.getElementById('parent-name-modal').textContent = parentName;
        document.getElementById('parent-info-display').textContent = `Name: ${parentName}`;
        document.getElementById('parent-code-display').textContent = `Referral Code: ${parentCode}`;
        document.getElementById('position-display').textContent = position.toUpperCase() + (hasLeft && hasRight ? ' (Auto-selected)' : '');
        document.getElementById('modal-place-under').value = parentCode;
        document.getElementById('modal-position').value = position;

        modal.classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('add-user-modal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('add-user-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddUserModal();
        }
    });

    // Initial centering
    setTimeout(centerTree, 100);

    // Handle window resize
    window.addEventListener('resize', function() {
        const newWidth = container.node().offsetWidth;
        const newHeight = container.node().offsetHeight;
        svg.attr('width', newWidth).attr('height', newHeight);
        centerTree();
    });
});
</script>
@endpush
