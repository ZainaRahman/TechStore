<!DOCTYPE html>
<html>
<head>
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>Manage Products</h1>
            <a href="{{ route('admin.products.create') }}" class="btn-primary-link">+ New Product</a>
        </div>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand_name ?? '—' }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->quantity ?? 0 }}</td>
                        <td class="table-actions">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product->product_id) }}" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('admin.dashboard') }}" class="helper-link-back">← Back to Dashboard</a>
    </div>
</body>
</html>