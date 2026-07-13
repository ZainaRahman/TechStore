<!DOCTYPE html>
<html>
<head>
    <title>Payments - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>All Payments</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn-outline">← Dashboard</a>
        </div>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>#{{ $payment->payment_id }}</td>
                        <td><a href="{{ route('admin.orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></td>
                        <td>{{ $payment->full_name }}</td>
                        <td>{{ ucfirst(str_replace('_',' ', $payment->method)) }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span class="status-badge status-{{ $payment->status === 'completed' ? 'delivered' : 'pending' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->paid_at ?? '—' }}</td>
                        <td>
                            @if ($payment->status !== 'completed')
                                <form method="POST" action="{{ route('admin.payments.paid', $payment->payment_id) }}">
                                    @csrf
                                    <button type="submit" class="link-action">Mark Paid</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>