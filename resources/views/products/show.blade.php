<!DOCTYPE html>
<html>
<head>
    <title>{{ $product->name }} - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <a href="{{ route('landing') }}" class="site-logo">TechStore</a>
            <nav class="site-nav">
                <a href="{{ route('landing') }}" class="btn-outline">Back to Shop</a>
                @auth
                    <a href="{{ route('cart.index') }}" class="btn-outline">Cart</a>
                    <a href="{{ route('orders.index') }}" class="btn-outline">My Orders</a>
                    <span class="welcome-text">Hi, {{ auth()->user()->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary-link">Sign Up</a>
                @endauth
            </nav>
        </div>
    </header>

    <div class="page-wrapper product-detail-layout">
        <div class="product-detail-image">
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <span>No image available</span>
            @endif
        </div>

        <div class="product-detail-content">
            @if (($product->quantity ?? 0) <= 0)
                <div class="status-box product-stock-badge product-stock-badge-danger">Out of stock</div>
            @endif

            <h1>{{ $product->name }}</h1>
            <p class="product-brand">{{ $product->brand_name ?? 'Unbranded' }}</p>
            <p class="product-price">${{ number_format($product->price, 2) }}</p>
            <p class="product-stock">
                {{ ($product->quantity ?? 0) > 0 ? ($product->quantity . ' left in stock') : 'Currently unavailable' }}
            </p>

            @if ($product->description)
                <div class="product-description">
                    <h2>Description</h2>
                    <p>{{ $product->description }}</p>
                </div>
            @endif

            @if (($product->quantity ?? 0) > 0)
                <form method="POST" action="{{ route('cart.add', $product->product_id) }}" class="product-detail-actions">
                    @csrf
                    <button type="submit">Add to Cart</button>
                </form>
            @else
                <button type="button" disabled style="opacity: 0.6; cursor: not-allowed;">
                    Add to Cart
                </button>
            @endif
        </div>
    </div>
</body>
</html>