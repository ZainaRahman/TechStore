<!DOCTYPE html>
<html>
<head>
    <title>Order #{{ $order->order_id }} - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>Order #{{ $order->order_id }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="btn-outline">← Back to Orders</a>
        </div>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-grid">
            {{-- Customer + order info --}}
            <div class="card">
                <h2>Order Details</h2>
                <div class="detail-row"><span>Customer:</span><span>{{ $order->full_name }}</span></div>
                <div class="detail-row"><span>Email:</span><span>{{ $order->email }}</span></div>
                <div class="detail-row"><span>Phone:</span><span>{{ $order->phone ?? '—' }}</span></div>
                <div class="detail-row"><span>Ship to:</span><span>{{ $order->shipping_address }}</span></div>
                <div class="detail-row"><span>Date:</span><span>{{ $order->order_date }}</span></div>
                <div class="detail-row"><span>Total:</span><span>${{ number_format($order->total_amount, 2) }}</span></div>
                <div class="detail-row">
                    <span>Status:</span>
                    <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>

                {{-- Status update form — calls the PL/SQL procedure --}}
                <form method="POST" action="{{ route('admin.orders.status', $order->order_id) }}" style="margin-top:1.25rem;">
                    @csrf
                    <div class="field">
                        <label>Update Status</label>
                        <select name="status">
                            @foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit">Update</button>
                </form>
            </div>

            {{-- Payment --}}
            <div class="card">
                <h2>Payment</h2>
                @if ($payment)
                    <div class="detail-row"><span>Method:</span><span>{{ ucfirst(str_replace('_',' ',$payment->method)) }}</span></div>
                    <div class="detail-row"><span>Amount:</span><span>${{ number_format($payment->amount, 2) }}</span></div>
                    <div class="detail-row">
                        <span>Status:</span>
                        <span class="status-badge status-{{ $payment->status === 'completed' ? 'delivered' : 'pending' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="detail-row"><span>Transaction ID:</span><span>{{ $payment->transaction_id ?? '—' }}</span></div>
                    <div class="detail-row"><span>Paid At:</span><span>{{ $payment->paid_at ?? 'Not yet' }}</span></div>

                    @if ($payment->status !== 'completed')
                        <form method="POST" action="{{ route('admin.payments.paid', $payment->payment_id) }}" style="margin-top:1rem;">
                            @csrf
                            <button type="submit">Mark as Paid</button>
                        </form>
                    @endif
                @else
                    <p class="card-subtext">No payment recorded yet.</p>
                    <form method="POST" action="{{ route('admin.payments.store', $order->order_id) }}" style="margin-top:1rem;">
                        @csrf
                        <div class="field">
                            <label>Payment Method</label>
                            <select name="method">
                                <option value="card">Card</option>
                                <option value="mobile_banking">Mobile Banking</option>
                                <option value="emi">EMI</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Transaction ID</label>
                            <input type="text" name="transaction_id">
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input type="text" name="amount" value="{{ $order->total_amount }}">
                        </div>
                        <button type="submit">Record Payment</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Order items --}}
        <h2 class="section-title">Items</h2>
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
    </div>
</body>
</html>