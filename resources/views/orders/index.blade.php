<!DOCTYPE html>
<html>
<head>
    <title>My Orders - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <a href="{{ route('landing') }}" class="site-logo">TechStore</a>
            <nav class="site-nav">
                <a href="{{ route('landing') }}" class="btn-outline">Shop</a>
                <a href="{{ route('cart.index') }}" class="btn-outline">Cart</a>
                <a href="{{ route('dashboard') }}" class="btn-outline">Dashboard</a>
                <span class="welcome-text">Hi, {{ auth()->user()->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="page-wrapper">

        {{-- ── Orders Summary Table ── --}}
        <div class="dashboard-header">
            <div>
                <h1>My Orders</h1>
                <p class="welcome-text">Track your purchases and review delivered items below.</p>
            </div>
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

        @if (empty($orders))
            <p class="card-subtext">You haven't placed any orders yet.</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>#{{ $order->order_id }}</td>
                            <td>{{ $order->order_date }}</td>
                            <td>{{ $order->item_count }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.confirmation', $order->order_id) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ── Review Section (delivered orders only) ── --}}
        <div class="dashboard-section" style="margin-top: 2.5rem;">
            <h2 class="section-title">Review Delivered Orders</h2>

            @if (empty($reviewOrders))
                <p class="card-subtext">
                    Once an order is marked delivered, you can review its products here.
                </p>
            @else
                <div class="review-order-list">
                    @foreach ($reviewOrders as $reviewOrder)
                        <div class="review-order-card">
                            <div class="dashboard-header review-order-header">
                                <div>
                                    <h3>Order #{{ $reviewOrder->order_id }}</h3>
                                    <p class="welcome-text">Delivered on {{ $reviewOrder->order_date }}</p>
                                </div>
                                <span class="status-badge status-delivered">Delivered</span>
                            </div>

                            <div class="review-item-list">
                                @foreach ($reviewOrder->items as $item)
                                    <div class="review-item">
                                        <div class="review-item-preview">
                                            @if ($item->image_url)
                                                <img src="{{ $item->image_url }}"
                                                     alt="{{ $item->product_name }}">
                                            @else
                                                <span>No image</span>
                                            @endif
                                        </div>

                                        <div class="review-item-content">
                                            <div class="review-item-topline">
                                                <div>
                                                    <h4>{{ $item->product_name }}</h4>
                                                    <p class="product-stock">
                                                        {{ $item->stock_quantity }} left in stock
                                                    </p>
                                                </div>
                                                @if ($item->review_id)
                                                    <span class="status-badge status-confirmed">Reviewed</span>
                                                @else
                                                    <span class="status-badge status-pending">Pending review</span>
                                                @endif
                                            </div>

                                            <p class="card-subtext">
                                                Ordered quantity: {{ $item->order_quantity }}
                                            </p>

                                            @if ($item->review_id)
                                                {{-- Already reviewed — show their existing review --}}
                                                <div class="review-existing">
                                                    <strong>Your rating:</strong>
                                                    {{ $item->review_rating }} / 5
                                                    @if ($item->review_comment)
                                                        <p>{{ $item->review_comment }}</p>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Not yet reviewed — show the form --}}
                                                <form method="POST"
                                                      action="{{ route('reviews.store', $item->product_id) }}"
                                                      class="review-form">
                                                    @csrf

                                                    <div class="field">
                                                        <label>Rating</label>
                                                        <select name="rating" required>
                                                            <option value="">Select rating</option>
                                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                                <option value="{{ $rating }}">
                                                                    {{ $rating }} / 5
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div class="field">
                                                        <label>Comment</label>
                                                        <textarea name="comment"
                                                                  rows="3"
                                                                  placeholder="Share your experience with this product"
                                                                  maxlength="1000"></textarea>
                                                    </div>

                                                    <button type="submit">Submit Review</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <a href="{{ route('landing') }}" class="helper-link-back">← Back to Shop</a>
    </div>
</body>
</html>