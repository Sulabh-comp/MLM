{{-- Hierarchy Tree Component --}}
<div class="hierarchy-tree">
    @if(isset($managers) && $managers->count() > 0)
        <div class="tree-container">
            @foreach($managers as $manager)
                @include('admin.partials.hierarchy-node', ['manager' => $manager, 'level' => 0])
            @endforeach
        </div>
    @else
        <div class="text-center text-muted py-4">
            <i class="fa-solid fa-tree fa-2x mb-3"></i>
            <p>{{ __('No hierarchy structure found') }}</p>
        </div>
    @endif
</div>

<style>
.hierarchy-tree {
    font-family: 'Courier New', monospace;
}

.hierarchy-node {
    margin: 5px 0;
    padding: 8px 12px;
    border-left: 2px solid #e3e6f0;
    transition: all 0.3s ease;
}

.hierarchy-node:hover {
    background-color: #f8f9fa;
    border-left-color: #5a9fd4;
}

.hierarchy-node.level-0 { margin-left: 0; }
.hierarchy-node.level-1 { margin-left: 25px; }
.hierarchy-node.level-2 { margin-left: 50px; }
.hierarchy-node.level-3 { margin-left: 75px; }
.hierarchy-node.level-4 { margin-left: 100px; }
.hierarchy-node.level-5 { margin-left: 125px; }

.node-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.node-icon {
    font-size: 14px;
    width: 20px;
    text-align: center;
}

.node-details {
    flex: 1;
}

.node-actions {
    display: flex;
    gap: 5px;
}

.level-badge {
    font-size: 10px;
    padding: 2px 6px;
}

.children-count {
    font-size: 11px;
    opacity: 0.7;
}
</style>
