<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="welcome-text">Logged in as {{ auth('admin')->user()->username }}</p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="logout-form">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Customers</h3>
                <p class="stat-number">{{ $stats['total_customers'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Products</h3>
                <p class="stat-number">{{ $stats['total_products'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Orders</h3>
                <p class="stat-number">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Pending Reviews</h3>
                <p class="stat-number">{{ $stats['pending_reviews'] }}</p>
            </div>
        </div>

        <nav class="admin-nav">
        <a href="{{ route('admin.products.index') }}">Manage Products</a>
        <a href="{{ route('admin.brands.index') }}">Manage Brands</a>
        <a href="#">Manage Orders</a>
        <a href="#">Manage Reviews</a>
        <a href="#">Manage Customers</a>
    </nav>
    </div>
</body>
</html>