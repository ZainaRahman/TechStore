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
                <h3>Total Orders</h3>
                <p class="stat-number">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Pending Orders</h3>
                <p class="stat-number">{{ $stats['pending_orders'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Pending Reviews</h3>
                <p class="stat-number">{{ $stats['pending_reviews'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Low Stock Items</h3>
                <p class="stat-number">{{ $stats['low_stock'] }}</p>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('admin.products.index') }}">Products</a>
            <a href="{{ route('admin.brands.index') }}">Brands</a>
            <a href="{{ route('admin.orders.index') }}">Orders</a>
            <a href="{{ route('admin.payments.index') }}">Payments</a>
            <a href="{{ route('admin.reviews.index') }}">Reviews</a>
            <a href="{{ route('admin.customers.index') }}">Customers</a>
        </nav>

        @if (!empty($topProducts))
            <h2 class="section-title">Top Selling Products</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProducts as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->total_sold }}</td>
                            <td>${{ number_format($p->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if (!empty($recentOrders))
            <h2 class="section-title" style="margin-top:2rem;">Recent Orders</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->order_id }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td>{{ $order->order_date }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td><span class="status-badge status-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</span></td>
                            <td><a href="{{ route('admin.orders.show', $order->order_id) }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>