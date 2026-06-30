<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Edit Product</h1>

            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.products.update', $product->product_id) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}">
                </div>

                <div class="field">
                    <label>Description</label>
                    <input type="text" name="description" value="{{ old('description', $product->description) }}">
                </div>

                <div class="field">
                    <label>Price</label>
                    <input type="text" name="price" value="{{ old('price', $product->price) }}">
                </div>

                <div class="field">
                    <label>Brand</label>
                    <select name="brand_id">
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->brand_id }}" {{ $product->brand_id == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Image URL</label>
                    <input type="text" name="image_url" value="{{ old('image_url', $product->image_url) }}">
                </div>

                <div class="field">
                    <label>Stock Quantity</label>
                    <input type="text" name="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}">
                </div>

                <div class="field">
                    <label>Reorder Level</label>
                    <input type="text" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level ?? 5) }}">
                </div>

                <button type="submit">Update Product</button>
            </form>

            <a href="{{ route('admin.products.index') }}" class="helper-link-back">← Back to Products</a>
        </div>
    </div>
</body>
</html>