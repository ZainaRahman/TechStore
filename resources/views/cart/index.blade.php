<!DOCTYPE html>
<html>
<head>
    <title>Your Cart - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <h1>Your Cart</h1>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        @if (empty($cartItems))
            <p class="card-subtext">Your cart is empty. <a href="{{ route('landing') }}">Browse products</a></p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->brand_name }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $item->cart_id) }}" class="quantity-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->cart_quantity }}" min="1" max="99">
                                    <button type="submit" class="link-action">Update</button>
                                </form>
                            </td>
                            <td>${{ number_format($item->price * $item->cart_quantity, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.remove', $item->cart_id) }}" onsubmit="return confirm('Remove this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cart-total">
                <span>Total: ${{ number_format($total, 2) }}</span>
                <a href="{{ route('checkout') }}" class="btn-primary-link">Proceed to Checkout</a>
            </div>
        @endif

        <a href="{{ route('landing') }}" class="helper-link-back">← Continue Shopping</a>
    </div>
</body>
</html>