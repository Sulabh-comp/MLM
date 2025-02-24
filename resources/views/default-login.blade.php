<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to DeepSeek</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9fafb;
      color: #111827;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
      text-align: center;
    }

    .header {
      margin-bottom: 3rem;
    }

    .header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      color: #1e40af;
      margin-bottom: 1rem;
    }

    .header p {
      font-size: 1.125rem;
      color: #4b5563;
      max-width: 600px;
      margin: 0 auto;
    }

    .cards {
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      width: 280px;
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1e40af;
      margin-bottom: 1rem;
    }

    .card p {
      font-size: 1rem;
      color: #6b7280;
      margin-bottom: 1.5rem;
    }

    .card button {
      background-color: #1e40af;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .card button:hover {
      background-color: #1e3a8a;
    }

    .footer {
      margin-top: 4rem;
      font-size: 0.875rem;
      color: #6b7280;
    }

    @media (max-width: 768px) {
      .cards {
        flex-direction: column;
        align-items: center;
      }

      .card {
        width: 100%;
        max-width: 400px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header Section -->
    <div class="header">
      <h1>Welcome to DeepSeek</h1>
      <p>
        DeepSeek is a leading AI-powered platform that helps businesses and individuals achieve their goals faster and smarter. Explore our services and join our growing community.
      </p>
    </div>

    <!-- Cards Section -->
    <div class="cards">
      <!-- User Card -->
      <div class="card">
        <h2>Admin</h2>
        <p>Access Overall System and manage all the agencies, employees and their customers.</p>
        <button onclick="window.location.href='{{route('admin.login}}'">Login as admin</button>
      </div>

      <!-- Agency Card -->
      <div class="card">
        <h2>Agency</h2>
        <p>Manage your agency account, view/manage your customer details.</p>
        <button onclick="window.location.href='{{route('agency.login'}}'">Login as Agency</button>
      </div>

      <!-- Employee Card -->
      <div class="card">
        <h2>Employee</h2>
        <p>Access your employee portal, view tasks, and manage associated Agencies.</p>
        <button onclick="window.location.href='{{route('employee.login')}}'">Login as Employee</button>
      </div>
    </div>

    <!-- Footer Section -->
    <div class="footer">
      <p>&copy; {{ date('Y') }} {{ env('SITE_NAME') }}. All rights reserved.</p>
    </div>
  </div>
</body>
</html>