<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="card confirmation-card">
            <h1>✓ Order Placed Successfully</h1>
            <p class="card-subtext">Order #{{ $order->order_id }} — {{ $order->order_date }}</p>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-row summary-total">
                <span>Total</span>
                <span>${{ number_format($order->total_amount, 2) }}</span>
            </div>

            <p class="card-subtext" style="margin-top:1rem;">
                Shipping to: {{ $order->shipping_address }}
            </p>
            <p class="card-subtext">
                Status: <strong>{{ ucfirst($order->status) }}</strong>
            </p>

            <div style="margin-top:1.5rem; display:flex; gap:1rem;">
                <a href="{{ route('orders.index') }}" class="btn-outline">My Orders</a>
                <a href="{{ route('landing') }}" class="btn-primary-link">Continue Shopping</a>
            </div>
        </div>
    </div>
</body>
</html>