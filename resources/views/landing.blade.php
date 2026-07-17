<!DOCTYPE html>
<html>
<head>
    <title>TechStore - Computers & Gadgets</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <a href="{{ route('landing') }}" class="site-logo">TechStore</a>
            <nav class="site-nav">
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

    <section class="hero">
        <h1>Computers & Gadgets, Delivered.</h1>
        <p>Browse our latest collection of laptops, peripherals, and accessories.</p>
    </section>

    <div class="page-wrapper">
        <form method="GET" action="{{ route('landing') }}" class="product-filter-bar">
            <div class="field">
                <label for="q">Search products</label>
                <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by name, brand, or description">
            </div>

            <div class="field">
                <label for="brand_id">Brand</label>
                <select id="brand_id" name="brand_id">
                    <option value="">All brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->brand_id }}" {{ (string)($filters['brand_id'] ?? '') === (string)$brand->brand_id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="stock">Stock</label>
                <select id="stock" name="stock">
                    <option value="">All stock states</option>
                    <option value="in_stock" {{ ($filters['stock'] ?? '') === 'in_stock' ? 'selected' : '' }}>In stock</option>
                    <option value="out_of_stock" {{ ($filters['stock'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
                </select>
            </div>

            <div class="field">
                <label for="sort">Sort</label>
                <select id="sort" name="sort">
                    <option value="newest" {{ ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest first</option>
                    <option value="price_low" {{ ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' }}>Price: low to high</option>
                    <option value="price_high" {{ ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' }}>Price: high to low</option>
                    <option value="name_az" {{ ($filters['sort'] ?? '') === 'name_az' ? 'selected' : '' }}>Name: A to Z</option>
                </select>
            </div>

            <div class="field">
                <label for="min_price">Min price</label>
                <input type="text" id="min_price" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="0">
            </div>

            <div class="field">
                <label for="max_price">Max price</label>
                <input type="text" id="max_price" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="9999">
            </div>

            <div class="filter-actions">
                <button type="submit">Search</button>
                <a href="{{ route('landing') }}" class="btn-outline">Reset</a>
            </div>
        </form>
    </div>

    @if (session('status'))
        <div class="page-wrapper">
            <div class="status-box">{{ session('status') }}</div>
        </div>
    @endif

    <div class="page-wrapper">
        <h2 class="section-title">Latest Products</h2>

        <div class="product-grid">
            @forelse ($products as $product)
                <div class="product-card">
                    <a href="{{ route('products.show', $product->product_id) }}" class="product-card-link" aria-label="View details for {{ $product->name }}">
                        @if (($product->quantity ?? 0) <= 0)
                            <div class="status-box product-stock-badge product-stock-badge-danger">
                                Out of stock
                            </div>
                        @endif

                        <div class="product-image-placeholder">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            @else
                                <span>No image</span>
                            @endif
                        </div>
                        <h3>{{ $product->name }}</h3>
                        <p class="product-brand">{{ $product->brand_name ?? 'Unbranded' }}</p>
                        <p class="product-price">${{ number_format($product->price, 2) }}</p>
                        <p class="product-stock">
                            {{ ($product->quantity ?? 0) > 0 ? ($product->quantity . ' left in stock') : 'Out of stock' }}
                        </p>
                        <p class="card-subtext">
                            {{ ($product->quantity ?? 0) > 0 ? 'In stock' : 'Currently unavailable' }}
                        </p>
                    </a>

                    @if (($product->quantity ?? 0) > 0)
                        {{-- Add to Cart — guests get redirected to /login automatically --}}
                        <form method="POST" action="{{ route('cart.add', $product->product_id) }}">
                            @csrf
                            <button type="submit">Add to Cart</button>
                        </form>
                    @else
                        <button type="button" disabled style="opacity: 0.6; cursor: not-allowed;">
                            Add to Cart
                        </button>
                    @endif
                </div>
            @empty
                <p class="card-subtext">No products available yet. Check back soon.</p>
            @endforelse
        </div>
    </div>
</body>
</html>