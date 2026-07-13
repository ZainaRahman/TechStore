<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>All Orders</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn-outline">← Dashboard</a>
        </div>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->order_id }}</td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->order_date }}</td>
                        <td>{{ $order->item_count }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td><a href="{{ route('admin.orders.show', $order->order_id) }}">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>