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
.dashboard-container {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    margin-bottom: 40px;
}

.card-group {
    flex: 1 1 300px; /* grow, shrink, base width */
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgb(0 0 0 / 0.1);
    padding: 20px;
    min-width: 280px;
}

.card-group h3 {
    font-size: 18px;
    font-weight: 700;
    color: #222;
    margin-bottom: 18px;
    border-left: 5px solid #007BFF;
    padding-left: 10px;
}

.dashboard-grid {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.card-box {
    display: flex;
    align-items: center;
    background: #f9faff;
    border-radius: 8px;
    padding: 15px 20px;
    box-shadow: 0 3px 6px rgb(0 0 0 / 0.05);
    transition: box-shadow 0.3s ease;
    cursor: default;
}

.card-box:hover {
    box-shadow: 0 8px 24px rgb(0 0 0 / 0.15);
}

.card-icon {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 18px;
}

.card-icon svg {
    width: 24px;
    height: 24px;
}

.card-details {
    display: flex;
    flex-direction: column;
}

.card-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.card-count {
    font-size: 28px;
    font-weight: 700;
    color: #007BFF;
    line-height: 1;
}
.section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    padding: 25px 30px;
    margin-top: 40px;
    max-width: 100%;
}

.section h2 {
    font-size: 22px;
    font-weight: 700;
    color: #222;
    margin-bottom: 20px;
    border-left: 6px solid #007BFF;
    padding-left: 12px;
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
    border-bottom: 1px solid #eee;
    font-size: 15px;
    color: #444;
    transition: background-color 0.25s ease;
}

.section .trend-list li:last-child {
    border-bottom: none;
}

.section .trend-list li:hover {
    background-color: #f5faff;
    cursor: default;
}

.section .trend-list li span {
    font-weight: 600;
}

.section .trend-list li strong {
    color: #007BFF;
    font-weight: 700;
}

</style>

<div class="dashboard-container">

  {{-- Overview Group --}}
  <div class="card-group">
      <h3>📊 Overview</h3>
      <div class="dashboard-grid">
          {{-- Total Agencies --}}
          <div class="card-box">
              <div class="card-icon" style="background: #e3f2fd;">
                  <svg fill="#2196f3" viewBox="0 0 24 24"><path d="M3 6l9 6 9-6-9-6-9 6zm0 6v6l9 6 9-6v-6l-9 6-9-6z"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Agencies</div>
                  <div class="card-count">{{ $stats['total_agencies'] }}</div>
              </div>
          </div>

          {{-- Total Customers --}}
          <div class="card-box">
              <div class="card-icon" style="background: #e8f5e9;">
                  <svg fill="#43a047" viewBox="0 0 24 24"><path d="M12 12c2.7 0 8 1.3 8 4v4H4v-4c0-2.7 5.3-4 8-4zm0-2a4 4 0 100-8 4 4 0 000 8z"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Customers</div>
                  <div class="card-count">{{ $stats['total_customers'] }}</div>
              </div>
          </div>

          {{-- Total Family Members --}}
          <div class="card-box">
              <div class="card-icon" style="background: #f3e5f5;">
                  <svg fill="#8e24aa" viewBox="0 0 24 24"><path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3a3 3 0 100 6zm-8 0a3 3 0 100-6 3 3 0 000 6zm0 2c-2.33 0-7 1.17-7 3.5V20h14v-3.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 2.06 1.97 3.45V20h6v-3.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Family Members</div>
                  <div class="card-count">{{ $stats['total_family_members'] }}</div>
              </div>
          </div>

          {{-- Total Employees --}}
          <div class="card-box">
              <div class="card-icon" style="background: #fff3e0;">
                  <svg fill="#fb8c00" viewBox="0 0 24 24"><path d="M12 12c2.7 0 8 1.3 8 4v4H4v-4c0-2.7 5.3-4 8-4zm0-2a4 4 0 100-8 4 4 0 000 8z"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Total Employees</div>
                  <div class="card-count">{{ $stats['total_employees'] }}</div>
              </div>
          </div>
      </div>
  </div>

  {{-- Agency Stats Group --}}
  <div class="card-group">
      <h3>🏢 Agency Stats</h3>
      <div class="dashboard-grid">
          <div class="card-box">
              <div class="card-icon" style="background: #e1f5fe;">
                  <svg fill="#0288d1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Active Agencies</div>
                  <div class="card-count">{{ $stats['active_counts']['agencies'] }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon" style="background: #e0f2f1;">
                  <svg fill="#00796b" viewBox="0 0 24 24"><path d="M12 7v5l4 2"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Agencies Today</div>
                  <div class="card-count">{{ $stats['agency_creation']->today }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon" style="background: #f3e5f5;">
                  <svg fill="#6a1b9a" viewBox="0 0 24 24"><path d="M12 7v5l4 2"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Agencies This Month</div>
                  <div class="card-count">{{ $stats['agency_creation']->this_month }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon" style="background: #ffebee;">
                  <svg fill="#c62828" viewBox="0 0 24 24"><path d="M12 7v5l4 2"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Agencies This Year</div>
                  <div class="card-count">{{ $stats['agency_creation']->this_year }}</div>
              </div>
          </div>
      </div>
  </div>

  {{-- Customer Stats Group --}}
  <div class="card-group">
      <h3>👥 Customer Stats</h3>
      <div class="dashboard-grid">
          <div class="card-box">
              <div class="card-icon" style="background: #f1f8e9;">
                  <svg fill="#558b2f" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Active Customers</div>
                  <div class="card-count">{{ $stats['active_counts']['customers'] }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon" style="background: #ede7f6;">
                  <svg fill="#5e35b1" viewBox="0 0 24 24"><path d="M12 7v5l4 2"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Customers Today</div>
                  <div class="card-count">{{ $stats['customer_creation']->today }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon" style="background: #e1f5fe;">
                  <svg fill="#039be5" viewBox="0 0 24 24"><path d="M12 7v5l4 2"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Customers This Month</div>
                  <div class="card-count">{{ $stats['customer_creation']->this_month }}</div>
              </div>
          </div>

          <div class="card-box">
              <div class="card-icon" style="background: #fff8e1;">
                  <svg fill="#fbc02d" viewBox="0 0 24 24"><path d="M12 7v5l4 2"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Customers This Year</div>
                  <div class="card-count">{{ $stats['customer_creation']->this_year }}</div>
              </div>
          </div>
      </div>
  </div>

  {{-- Employee Stats Group --}}
  <div class="card-group">
      <h3>🧑‍💼 Employee Stats</h3>
      <div class="dashboard-grid">
          <div class="card-box">
              <div class="card-icon" style="background: #fbe9e7;">
                  <svg fill="#d84315" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
              </div>
              <div class="card-details">
                  <div class="card-title">Active Employees</div>
                  <div class="card-count">{{ $stats['active_counts']['employees'] }}</div>
              </div>
          </div>
      </div>
  </div>

</div>

{{-- Trend Lists (below groups) --}}

<div class="section">
    <h2>🏆 Top 5 Agencies by Customer Count</h2>
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
    <h2>💼 Top 5 Employees by Agency Count</h2>
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
    <h2>📈 Employee Creation Trend (Last 7 Days)</h2>
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
    <h2>📊 Agency Creation Trend (Last 7 Days)</h2>
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
    });
</script>
@endsection
