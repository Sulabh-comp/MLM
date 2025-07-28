@extends('layouts.agency.master')

@section('title', 'Dashboard')

@section('content-header', __('Dashboard'))

@section('breadcrumbs')
<li class="breadcrumb-item active">
  {{ __('Dashboard') }}
</li>
@endsection

@section('content')
<style>
:root {
    --primary: #4361ee;
    --primary-light: #e6e9ff;
    --secondary: #3f37c9;
    --success: #4cc9f0;
    --danger: #f72585;
    --warning: #f8961e;
    --info: #4895ef;
    --dark: #212529;
    --light: #f8f9fa;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.dashboard-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid var(--primary);
    cursor: default;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card-content {
    display: flex;
    align-items: center;
}

.card-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    background-color: var(--primary-light);
    color: var(--primary);
}

.card-icon svg {
    width: 22px;
    height: 22px;
}

.card-details {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card-title {
    font-weight: 500;
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.card-count {
    font-size: 24px;
    font-weight: 700;
    color: var(--dark);
    line-height: 1.2;
}

.card-growth {
    font-size: 12px;
    font-weight: 600;
    margin-left: auto;
    padding: 3px 8px;
    border-radius: 20px;
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.card-growth.negative {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.dashboard-card {
    animation: fadeIn 0.5s ease forwards;
}

.dashboard-card:nth-child(1) { animation-delay: 0.1s; }
.dashboard-card:nth-child(2) { animation-delay: 0.2s; }
.dashboard-card:nth-child(3) { animation-delay: 0.3s; }
.dashboard-card:nth-child(4) { animation-delay: 0.4s; }
.dashboard-card:nth-child(5) { animation-delay: 0.5s; }
.dashboard-card:nth-child(6) { animation-delay: 0.6s; }
</style>

<div class="dashboard-grid">
    {{-- Total Counts --}}
    <div class="dashboard-card">
        <div class="card-content">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="card-details">
                <div class="card-title">Total Customers</div>
                <div class="card-count">{{ $stats['total_customers'] }}</div>
            </div>
            <div class="card-growth">+{{ $stats['customer_creation']->this_month }} this month</div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-content">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <path d="M20 8v6"></path>
                    <path d="M23 11h-6"></path>
                </svg>
            </div>
            <div class="card-details">
                <div class="card-title">Total Family Members</div>
                <div class="card-count">{{ $stats['total_family_members'] }}</div>
            </div>
        </div>
    </div>

    {{-- Active Counts --}}
    <div class="dashboard-card">
        <div class="card-content">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4l3 3"></path>
                </svg>
            </div>
            <div class="card-details">
                <div class="card-title">Active Customers</div>
                <div class="card-count">{{ $stats['active_counts']['customers'] }}</div>
            </div>
            <div class="card-growth">{{ round(($stats['active_counts']['customers'] / ($stats['total_customers']?:1)) * 100, 1) }}% active</div>
        </div>
    </div>

    {{-- Customer Creation Stats --}}
    <div class="dashboard-card">
        <div class="card-content">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="card-details">
                <div class="card-title">Today's Customers</div>
                <div class="card-count">{{ $stats['customer_creation']->today }}</div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-content">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="card-details">
                <div class="card-title">This Month</div>
                <div class="card-count">{{ $stats['customer_creation']->this_month }}</div>
            </div>
            @if($stats['customer_creation']->last_month)
                @php $growth = (($stats['customer_creation']->this_month - $stats['customer_creation']->last_month) / max(1, $stats['customer_creation']->last_month) * 100); @endphp
                <div class="card-growth {{ $growth < 0 ? 'negative' : '' }}">
                    {{ round($growth, 1) }}% from last month
                </div>
            @endif
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-content">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="card-details">
                <div class="card-title">This Year</div>
                <div class="card-count">{{ $stats['customer_creation']->this_year }}</div>
            </div>
            @if($stats['customer_creation']->last_year)
                @php $growth = (($stats['customer_creation']->this_year - $stats['customer_creation']->last_year) / max(1, $stats['customer_creation']->last_year) * 100); @endphp
                <div class="card-growth {{ $growth < 0 ? 'negative' : '' }}">
                    {{ round($growth, 1) }}% from last year
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.data-table').DataTable();
        
        // Add animation class to all cards when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.dashboard-card').forEach(el => {
            observer.observe(el);
        });
    });
</script>
@endsection