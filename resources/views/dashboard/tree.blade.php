@extends('layout.app')

@section('title', 'Binary Tree Structure')

@push('styles')
<style>
    /* Modern Page Layout */
    .tree-page-wrapper {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    .tree-page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .tree-page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .tree-page-header p {
        font-size: 1rem;
        opacity: 0.95;
        margin: 0;
    }

    .tree-container-wrapper {
        background: #ffffff;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .tree-container {
        width: 100%;
        height: 75vh;
        min-height: 600px;
        background: linear-gradient(135deg, #fafbfc 0%, #ffffff 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        overflow: auto;
        position: relative;
        box-shadow: inset 0 2px 12px rgba(0, 0, 0, 0.04);
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
        stroke-width: 2.5px;
        stroke: #cbd5e1;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        rx: 12;
        ry: 12;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
    }

    .node-rect.active {
        fill: url(#activeGradient);
        stroke: #16a34a;
        stroke-width: 3px;
        filter: drop-shadow(0 6px 16px rgba(34, 197, 94, 0.4));
    }

    .node-rect.active:hover {
        filter: drop-shadow(0 8px 20px rgba(34, 197, 94, 0.5));
        transform: scale(1.05);
    }

    .node-rect.inactive {
        fill: url(#inactiveGradient);
        stroke: #dc2626;
        stroke-width: 3px;
        filter: drop-shadow(0 6px 16px rgba(239, 68, 68, 0.4));
    }

    .node-rect.inactive:hover {
        filter: drop-shadow(0 8px 20px rgba(239, 68, 68, 0.5));
        transform: scale(1.05);
    }

    .node-rect.vacant {
        fill: url(#vacantGradient);
        stroke: #2563eb;
        stroke-width: 2.5px;
        stroke-dasharray: 6,4;
        filter: drop-shadow(0 6px 16px rgba(59, 130, 246, 0.4));
        cursor: pointer;
        animation: pulseVacant 2s ease-in-out infinite;
    }

    .node-rect.vacant:hover {
        fill: url(#vacantHoverGradient);
        stroke: #1d4ed8;
        stroke-width: 3px;
        filter: drop-shadow(0 8px 24px rgba(59, 130, 246, 0.6));
        transform: scale(1.08);
        animation: none;
    }

    @keyframes pulseVacant {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
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
        stroke: #cbd5e1;
        stroke-width: 2.5px;
        transition: all 0.3s ease;
        opacity: 0.7;
    }

    .link:hover {
        stroke: #94a3b8;
        stroke-width: 3.5px;
        opacity: 1;
    }

    .vacant-link {
        font-size: 9px;
        fill: #fff;
        text-decoration: underline;
        cursor: pointer;
    }

    .controls {
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        backdrop-filter: blur(10px);
    }

    .controls .btn {
        padding: 0.75rem 1.5rem;
        margin: 0;
        border: none;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: inline-block;
        text-decoration: none;
        line-height: 1.5;
        position: relative;
        overflow: hidden;
    }

    .controls .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .controls .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .controls .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .controls .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    .controls .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    }

    .controls .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(107, 114, 128, 0.4);
    }

    .controls .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .controls .btn-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
    }

    .controls .btn-info {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .controls .btn-info:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }

    .controls .btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .controls .btn:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
    }

    .zoom-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    #zoom-level {
        font-weight: 700;
        color: #667eea;
        font-size: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .legend-item:hover {
        background: rgba(255, 255, 255, 1);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .legend-color {
        width: 24px;
        height: 24px;
        border-radius: 8px;
        border: 2px solid;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    .legend-item:hover .legend-color {
        transform: scale(1.1);
    }

    .legend-color.active {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border-color: #16a34a;
    }

    .legend-color.inactive {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-color: #dc2626;
    }

    .legend-color.vacant {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-color: #2563eb;
        border-style: dashed;
    }

    .legend-text {
        font-size: 14px;
        font-weight: 500;
        color: #475569;
    }

    .legend-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 12px;
        border-left: 4px solid #f59e0b;
        margin-left: auto;
    }

    .legend-info-text {
        font-size: 13px;
        color: #92400e;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="tree-page-wrapper">
    <!-- Modern Header -->
    <div class="tree-page-header">
        <h1>🌳 Binary Tree Network</h1>
        <p>Visualize and manage your referral network structure</p>
    </div>

    <!-- Modern Controls Card -->
    <div class="controls">
        <div class="flex flex-wrap items-center gap-3">
            <button id="zoom-in" class="btn btn-primary">Zoom In</button>
            <button id="zoom-out" class="btn btn-secondary">Zoom Out</button>
            <button id="reset-zoom" class="btn btn-warning">Reset</button>
            <button id="center-tree" class="btn btn-info">Center</button>
            <div class="zoom-indicator ml-auto">
                <span style="font-size: 14px; font-weight: 500; color: #64748b;">Zoom:</span>
                <span id="zoom-level">100%</span>
            </div>
        </div>
    </div>

    <!-- Tree Container - Modern Design -->
    <div class="tree-container-wrapper">
        <div class="tree-container" id="tree-container"></div>
    </div>

    <!-- Modern Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color active"></div>
            <span class="legend-text">Active Member</span>
        </div>
        <div class="legend-item">
            <div class="legend-color inactive"></div>
            <span class="legend-text">Inactive Member</span>
        </div>
        <div class="legend-item">
            <div class="legend-color vacant"></div>
            <span class="legend-text">Vacant Position</span>
        </div>
        <div class="legend-info">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span class="legend-info-text">Click vacant to register | Right-click node to add user</span>
        </div>
    </div>
</div>

<!-- Modern Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 z-50 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden" style="animation: fadeIn 0.3s ease;">
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl transform transition-all" style="animation: slideUp 0.3s ease;">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Add New User</h2>
                <p class="text-sm text-gray-500 mt-1">Under <span id="parent-name-modal" class="font-semibold text-gray-700"></span></p>
            </div>
            <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form action="{{ route('register') }}" method="GET" id="add-user-form">
            <input type="hidden" name="place_under" id="modal-place-under">
            <input type="hidden" name="side" id="modal-position">
            <input type="hidden" name="referred_by" value="{{ Auth::user()->referral_code }}">
            
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Parent User</label>
                <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                    <p class="text-base font-semibold text-gray-800" id="parent-info-display"></p>
                    <p class="text-sm text-gray-600 mt-1" id="parent-code-display"></p>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                    <p class="text-base font-bold text-purple-700" id="position-display"></p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg transition-all transform hover:scale-105 hover:shadow-xl">
                    Continue to Registration
                </button>
                <button type="button" onclick="closeAddUserModal()" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { 
            opacity: 0;
            transform: translateY(20px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
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
    
    // Ensure we're displaying the root user's tree, not a child's tree
    // The treeData should always be the logged-in user's tree
    const loggedInUserId = {{ Auth::id() ?? 'null' }};
    if (loggedInUserId && treeData.id !== loggedInUserId) {
        console.warn('Warning: Tree data ID does not match logged-in user ID. Tree data ID:', treeData.id, 'Logged-in user ID:', loggedInUserId);
        // This shouldn't happen, but if it does, we'll still display the tree
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

    // Add gradient definitions for modern node styling
    const defs = svg.append('defs');
    
    // Active node gradient
    const activeGradient = defs.append('linearGradient')
        .attr('id', 'activeGradient')
        .attr('x1', '0%').attr('y1', '0%')
        .attr('x2', '0%').attr('y2', '100%');
    activeGradient.append('stop').attr('offset', '0%').attr('stop-color', '#22c55e');
    activeGradient.append('stop').attr('offset', '100%').attr('stop-color', '#16a34a');
    
    // Inactive node gradient
    const inactiveGradient = defs.append('linearGradient')
        .attr('id', 'inactiveGradient')
        .attr('x1', '0%').attr('y1', '0%')
        .attr('x2', '0%').attr('y2', '100%');
    inactiveGradient.append('stop').attr('offset', '0%').attr('stop-color', '#ef4444');
    inactiveGradient.append('stop').attr('offset', '100%').attr('stop-color', '#dc2626');
    
    // Vacant node gradient
    const vacantGradient = defs.append('linearGradient')
        .attr('id', 'vacantGradient')
        .attr('x1', '0%').attr('y1', '0%')
        .attr('x2', '0%').attr('y2', '100%');
    vacantGradient.append('stop').attr('offset', '0%').attr('stop-color', '#3b82f6');
    vacantGradient.append('stop').attr('offset', '100%').attr('stop-color', '#2563eb');
    
    // Vacant hover gradient
    const vacantHoverGradient = defs.append('linearGradient')
        .attr('id', 'vacantHoverGradient')
        .attr('x1', '0%').attr('y1', '0%')
        .attr('x2', '0%').attr('y2', '100%');
    vacantHoverGradient.append('stop').attr('offset', '0%').attr('stop-color', '#2563eb');
    vacantHoverGradient.append('stop').attr('offset', '100%').attr('stop-color', '#1d4ed8');

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

    // Debug: Log tree data to console
    console.log('Tree Data from Server:', treeData);
    console.log('Root User ID:', treeData?.id);
    console.log('Root User Name:', treeData?.name);
    console.log('Root User Children:', treeData?.children);

    // Build tree structure
    let rootData;
    try {
        // Ensure we're starting from the root node, not a child
        if (!treeData || !treeData.id) {
            throw new Error('No tree data available');
        }
        
        rootData = transformToD3Hierarchy(treeData);
        if (!rootData) {
            throw new Error('Failed to transform tree data');
        }
        
        // Debug: Log transformed root data
        console.log('Transformed Root Data:', rootData);
        console.log('Root Data ID:', rootData.id);
        console.log('Root Data Name:', rootData.name);
    } catch (error) {
        console.error('Error transforming tree data:', error);
        document.getElementById('tree-container').innerHTML = '<div class="text-center p-8 text-gray-500">Error loading tree data. Please refresh the page.</div>';
        return;
    }

    const root = d3.hierarchy(rootData);
    
    // Debug: Verify root hierarchy
    console.log('D3 Root Hierarchy:', root);
    console.log('Root Data Name:', root.data.name);
    console.log('Root Data ID:', root.data.id);

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

    // Add rectangles with modern gradients
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
            if (d.data.is_vacant) return 'url(#vacantGradient)';
            return d.data.is_active ? 'url(#activeGradient)' : 'url(#inactiveGradient)';
        })
        .style('stroke', d => {
            if (d.data.is_vacant) return '#2563eb';
            return d.data.is_active ? '#16a34a' : '#dc2626';
        })
        .style('stroke-width', d => d.data.is_vacant ? '2.5px' : '3px')
        .style('stroke-dasharray', d => d.data.is_vacant ? '6,4' : 'none')
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
