<!DOCTYPE html>
<html>
<head>
    <title>Customers - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>All Customers</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn-outline">← Dashboard</a>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                        <td>{{ $customer->city ?? '—' }}</td>
                        <td>{{ $customer->total_orders }}</td>
                        <td>${{ number_format($customer->total_spent, 2) }}</td>
                        <td><a href="{{ route('admin.customers.show', $customer->user_id) }}">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>