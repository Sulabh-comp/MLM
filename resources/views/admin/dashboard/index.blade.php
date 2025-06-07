@extends('layouts.admin.master')

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

.dashboard-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.card-group {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-group:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card-group h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.card-box {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    border-left: 4px solid var(--primary);
    transition: all 0.3s ease;
    cursor: default;
}

.card-box:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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

.section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    padding: 25px;
    margin-top: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.section:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.section h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
}

.section .trend-list {
    list-style: none;
    padding-left: 0;
    margin: 0;
}

.section .trend-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f1f1;
    font-size: 15px;
    color: #495057;
    transition: all 0.25s ease;
}

.section .trend-list li:last-child {
    border-bottom: none;
}

.section .trend-list li:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.section .trend-list li span {
    font-weight: 500;
    flex-grow: 1;
}

.section .trend-list li strong {
    color: var(--primary);
    font-weight: 600;
    margin-left: 15px;
}

.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-primary {
    background-color: var(--primary-light);
    color: var(--primary);
}

@media (max-width: 768px) {
    .dashboard-container {
        grid-template-columns: 1fr;
    }
    
    .card-group {
        min-width: auto;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-group, .section {
    animation: fadeIn 0.5s ease forwards;
}

.card-group:nth-child(2) { animation-delay: 0.1s; }
.card-group:nth-child(3) { animation-delay: 0.2s; }
.card-group:nth-child(4) { animation-delay: 0.3s; }
.section:nth-child(1) { animation-delay: 0.4s; }
.section:nth-child(2) { animation-delay: 0.5s; }
.section:nth-child(3) { animation-delay: 0.6s; }
.section:nth-child(4) { animation-delay: 0.7s; }
</style>

<div class="dashboard-container">

  {{-- Overview Group --}}
  <div class="card-group">
      <h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"></path><rect x="5" y="9" width="4" height="10"></rect><rect x="11" y="5" width="4" height="14"></rect><rect x="17" y="11" width="4" height="8"></rect></svg> Overview</h3>
      <div class="dashboard-grid">
          {{-- Total Agencies --}}
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Agencies</div>
                  <div class="card-count">{{ $stats['total_agencies'] }}</div>
              </div>
              <div class="card-growth">+{{ $stats['agency_creation']->this_month }} this month</div>
          </div>

          {{-- Total Customers --}}
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Customers</div>
                  <div class="card-count">{{ $stats['total_customers'] }}</div>
              </div>
              <div class="card-growth">+{{ $stats['customer_creation']->this_month }} this month</div>
          </div>

          {{-- Total Family Members --}}
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M20 8v6"></path><path d="M23 11h-6"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Family Members</div>
                  <div class="card-count">{{ $stats['total_family_members'] }}</div>
              </div>
          </div>

          {{-- Total Employees --}}
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M17 11l2 2 4-4"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Employees</div>
                  <div class="card-count">{{ $stats['total_employees'] }}</div>
              </div>
              <div class="card-growth">+{{ $stats['employee_creation_trend']->sum('count') }} this week</div>
          </div>
      </div>
  </div>

  {{-- Agency Stats Group --}}
  <div class="card-group">
      <h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> Agency Stats</h3>
      <div class="dashboard-grid">
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Active Agencies</div>
                  <div class="card-count">{{ $stats['active_counts']['agencies'] }}</div>
              </div>
              <div class="badge badge-primary">{{ $stats['total_agencies']?round(($stats['active_counts']['agencies'] / $stats['total_agencies']) * 100, 1): 'NA' }}% active</div>
          </div>

          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Today</div>
                  <div class="card-count">{{ $stats['agency_creation']->today }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">This Month</div>
                  <div class="card-count">{{ $stats['agency_creation']->this_month }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">This Year</div>
                  <div class="card-count">{{ $stats['agency_creation']->this_year }}</div>
              </div>
          </div>
      </div>
  </div>

  {{-- Customer Stats Group --}}
  <div class="card-group">
      <h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> Customer Stats</h3>
      <div class="dashboard-grid">
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Active Customers</div>
                  <div class="card-count">{{ $stats['active_counts']['customers'] }}</div>
              </div>
              <div class="badge badge-primary">{{ $stats['total_customers']?round(($stats['active_counts']['customers'] / $stats['total_customers']) * 100, 1): 'NA' }}% active</div>
          </div>

          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Today</div>
                  <div class="card-count">{{ $stats['customer_creation']->today }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">This Month</div>
                  <div class="card-count">{{ $stats['customer_creation']->this_month }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">This Year</div>
                  <div class="card-count">{{ $stats['customer_creation']->this_year }}</div>
              </div>
          </div>
      </div>
  </div>

  {{-- Employee Stats Group --}}
  <div class="card-group">
      <h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M17 11l2 2 4-4"></path></svg> Employee Stats</h3>
      <div class="dashboard-grid">
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Active Employees</div>
                  <div class="card-count">{{ $stats['active_counts']['employees'] }}</div>
              </div>
              <div class="badge badge-primary">{{ $stats['total_employees']?round(($stats['active_counts']['employees'] / $stats['total_employees']) * 100, 1): 'NA' }}% active</div>
          </div>
          
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">This Week</div>
                  <div class="card-count">{{ $stats['employee_creation_trend']->sum('count') }}</div>
              </div>
          </div>
          
          <div class="card-box">
              <div class="card-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Avg per Agency</div>
                  <div class="card-count">{{ round($stats['total_employees'] / max(1, $stats['total_agencies']), 1) }}</div>
              </div>
          </div>
      </div>
  </div>
</div>

{{-- Trend Lists (below groups) --}}

<div class="section">
    <h2><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 21V7l-8-5-8 5v14l8-2.5L22 21z"></path></svg> Top 5 Agencies by Customer Count</h2>
    <ul class="trend-list">
        @foreach ($stats['top_agencies'] as $agency)
            <li>
                <span>{{ $agency->name }}</span>
                <strong>{{ $agency->customers_count }} customers</strong>
            </li>
        @endforeach
    </ul>
</div>

<div class="section">
    <h2><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><path d="M17 11l2 2 4-4"></path></svg> Top 5 Employees by Agency Count</h2>
    <ul class="trend-list">
        @foreach ($stats['top_employees'] as $employee)
            <li>
                <span>{{ $employee->name }}</span>
                <strong>{{ $employee->agencies_count }} agencies</strong>
            </li>
        @endforeach
    </ul>
</div>

<div class="section">
    <h2><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg> Employee Creation Trend (Last 7 Days)</h2>
    <ul class="trend-list">
        @foreach ($stats['employee_creation_trend'] as $trend)
            <li>
                <span>{{ $trend->date }}</span>
                <strong>{{ $trend->count }} employees</strong>
            </li>
        @endforeach
    </ul>
</div>

<div class="section">
    <h2><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"></path><rect x="5" y="9" width="4" height="10"></rect><rect x="11" y="5" width="4" height="14"></rect><rect x="17" y="11" width="4" height="8"></rect></svg> Agency Creation Trend (Last 7 Days)</h2>
    <ul class="trend-list">
        @foreach ($stats['agency_creation_trend'] as $trend)
            <li>
                <span>{{ $trend->date }}</span>
                <strong>{{ $trend->count }} agencies</strong>
            </li>
        @endforeach
    </ul>
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
        
        document.querySelectorAll('.card-group, .section').forEach(el => {
            observer.observe(el);
        });
    });
</script>
@endsection