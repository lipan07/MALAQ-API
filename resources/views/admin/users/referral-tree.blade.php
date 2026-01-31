@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="bi bi-diagram-3"></i> Referral Tree
            </h5>
            <small class="text-muted">User: <strong>{{ $rootUser->name }}</strong> ({{ $rootUser->email }})</small>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="mb-1">Total Referrals</h6>
                            <h3 id="totalReferrals">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6 class="mb-1">Level 1</h6>
                            <h3 id="level1Count">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6 class="mb-1">Level 2</h6>
                            <h3 id="level2Count">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6 class="mb-1">Level 3+</h6>
                            <h3 id="level3PlusCount">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tree-container" style="overflow-x: auto; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div id="treeVisualization"></div>
        </div>
    </div>
</div>

<style>
    .tree-node {
        display: inline-block;
        margin: 10px;
        vertical-align: top;
    }

    .node-card {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 12px;
        padding: 15px;
        min-width: 200px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s;
        position: relative;
    }

    .node-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .node-card.level-0 {
        border-color: #0d6efd;
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
    }

    .node-card.level-1 {
        border-color: #198754;
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        color: white;
    }

    .node-card.level-2 {
        border-color: #0dcaf0;
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
        color: white;
    }

    .node-card.level-3,
    .node-card.level-4,
    .node-card.level-5 {
        border-color: #ffc107;
        background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
        color: #000;
    }

    .node-header {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .node-level {
        font-size: 11px;
        opacity: 0.8;
        background: rgba(255,255,255,0.2);
        padding: 2px 8px;
        border-radius: 12px;
    }

    .node-body {
        font-size: 13px;
        line-height: 1.6;
    }

    .node-email {
        opacity: 0.9;
        font-size: 12px;
        margin-top: 4px;
        word-break: break-word;
    }

    .node-date {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 6px;
    }

    .node-badge {
        display: inline-block;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        margin-top: 4px;
        background: rgba(255,255,255,0.2);
    }

    .children-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 20px;
        position: relative;
    }

    .children-container::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        width: 2px;
        height: 20px;
        background: #dee2e6;
        transform: translateX(-50%);
    }

    .tree-branch {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        position: relative;
    }

    .tree-branch::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        width: 2px;
        height: 20px;
        background: #dee2e6;
        transform: translateX(-50%);
    }

    .tree-node-wrapper {
        text-align: center;
        position: relative;
    }

    .tree-node-wrapper::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        width: 2px;
        height: 20px;
        background: #dee2e6;
        transform: translateX(-50%);
    }

    .tree-node-wrapper:not(:first-child)::after {
        content: '';
        position: absolute;
        top: -20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #dee2e6;
    }

    .tree-node-wrapper:first-child::after {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        right: 0;
        height: 2px;
        background: #dee2e6;
    }

    .tree-node-wrapper:last-child::after {
        content: '';
        position: absolute;
        top: -20px;
        left: 0;
        right: 50%;
        height: 2px;
        background: #dee2e6;
    }

    .tree-node-wrapper:only-child::after {
        display: none;
    }

    .empty-tree {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-tree i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>

<script>
    const treeData = @json($tree);
    
    function countNodes(node) {
        if (!node) return 0;
        let count = 1;
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => {
                count += countNodes(child);
            });
        }
        return count;
    }

    function countByLevel(node, level = 0) {
        const counts = {};
        function traverse(n, l) {
            if (!n) return;
            counts[l] = (counts[l] || 0) + 1;
            if (n.children && n.children.length > 0) {
                n.children.forEach(child => traverse(child, l + 1));
            }
        }
        traverse(node, level);
        return counts;
    }

    function renderTree(node, container) {
        if (!node) {
            container.innerHTML = '<div class="empty-tree"><i class="bi bi-inbox"></i><h5>No Referrals Yet</h5><p>This user has not invited anyone yet.</p></div>';
            return;
        }

        const nodeDiv = document.createElement('div');
        nodeDiv.className = 'tree-node-wrapper';
        
        const card = document.createElement('div');
        card.className = `node-card level-${Math.min(node.level, 5)}`;
        
        card.innerHTML = `
            <div class="node-header">
                <span>${escapeHtml(node.name)}</span>
                <span class="node-level">L${node.level}</span>
            </div>
            <div class="node-body">
                ${node.email ? `<div class="node-email">${escapeHtml(node.email)}</div>` : ''}
                ${node.phone_no ? `<div class="node-email">${escapeHtml(node.phone_no)}</div>` : ''}
                ${node.joined_via_invite ? '<span class="node-badge">Invited</span>' : ''}
                <div class="node-date">Joined: ${formatDate(node.created_at)}</div>
            </div>
        `;
        
        nodeDiv.appendChild(card);
        
        if (node.children && node.children.length > 0) {
            const childrenContainer = document.createElement('div');
            childrenContainer.className = 'children-container';
            
            node.children.forEach(child => {
                const childWrapper = document.createElement('div');
                childWrapper.className = 'tree-node';
                renderTree(child, childWrapper);
                childrenContainer.appendChild(childWrapper);
            });
            
            nodeDiv.appendChild(childrenContainer);
        }
        
        container.appendChild(nodeDiv);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('treeVisualization');
        renderTree(treeData, container);
        
        // Update statistics
        const totalReferrals = countNodes(treeData) - 1; // Exclude root
        const levelCounts = countByLevel(treeData);
        
        document.getElementById('totalReferrals').textContent = totalReferrals;
        document.getElementById('level1Count').textContent = levelCounts[1] || 0;
        document.getElementById('level2Count').textContent = levelCounts[2] || 0;
        const level3Plus = Object.keys(levelCounts)
            .filter(k => parseInt(k) >= 3)
            .reduce((sum, k) => sum + (levelCounts[k] || 0), 0);
        document.getElementById('level3PlusCount').textContent = level3Plus;
    });
</script>
@endsection
