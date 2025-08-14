{{-- Hierarchy Node Component --}}
<div class="hierarchy-node level-{{ $level }}">
    <div class="node-info">
        <div class="node-icon">
            @if($manager->children && $manager->children->count() > 0)
                <i class="fa-solid fa-users text-primary" title="{{ __('Has Subordinates') }}"></i>
            @else
                <i class="fa-solid fa-user text-secondary" title="{{ __('Individual Manager') }}"></i>
            @endif
        </div>
        
        <div class="node-details">
            <div class="d-flex align-items-center">
                <strong>{{ $manager->name }}</strong>
                
                @if($manager->level_name)
                    <span class="badge bg-primary level-badge ms-2">{{ $manager->level_name }}</span>
                @endif
                
                @if($manager->children && $manager->children->count() > 0)
                    <span class="children-count ms-2">({{ $manager->children->count() }} {{ __('direct') }})</span>
                @endif
            </div>
            
            <div class="text-muted small">
                @if($manager->email)
                    <i class="fa-solid fa-envelope me-1"></i>{{ $manager->email }}
                @endif
                @if($manager->phone)
                    <i class="fa-solid fa-phone ms-3 me-1"></i>{{ $manager->phone }}
                @endif
            </div>
        </div>
        
        <div class="node-actions">
            <a href="{{ route('admin.managers.show', $manager) }}" class="btn btn-outline-info btn-sm" title="{{ __('View') }}">
                <i class="fa-solid fa-eye"></i>
            </a>
            <a href="{{ route('admin.managers.edit', $manager) }}" class="btn btn-outline-primary btn-sm" title="{{ __('Edit') }}">
                <i class="fa-solid fa-edit"></i>
            </a>
        </div>
    </div>
</div>

{{-- Recursively render children --}}
@if($manager->children && $manager->children->count() > 0 && $level < 5)
    @foreach($manager->children as $child)
        @include('admin.partials.hierarchy-node', ['manager' => $child, 'level' => $level + 1])
    @endforeach
@endif
