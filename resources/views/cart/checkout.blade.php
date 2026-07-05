<!DOCTYPE html>
<html>
<head>
    <title>Checkout - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <h1>Checkout</h1>

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
            <div class="card">
                <h2>Order Summary</h2>
                @foreach ($cartItems as $item)
                    <div class="summary-row">
                        <span>{{ $item->name }} × {{ $item->cart_quantity }}</span>
                        <span>${{ number_format($item->price * $item->cart_quantity, 2) }}</span>
                    </div>
                @endforeach
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="card">
                <h2>Shipping Details</h2>
                <form method="POST" action="{{ route('orders.place') }}">
                    @csrf
                    <div class="field">
                        <label>Shipping Address</label>
                        <input type="text" name="shipping_address"
                               value="{{ old('shipping_address', auth()->user()->address) }}">
                    </div>
                    <button type="submit">Place Order</button>
                </form>
            </div>
        </div>

        <a href="{{ route('cart.index') }}" class="helper-link-back">← Back to Cart</a>
    </div>
</body>
</html>