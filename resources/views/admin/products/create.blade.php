<!DOCTYPE html>
<html>
<head>
    <title>New Product - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Add New Product</h1>

            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.products.store') }}">
                @csrf

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name') }}">
                </div>

                <div class="field">
                    <label>Description</label>
                    <input type="text" name="description" value="{{ old('description') }}">
                </div>

                <div class="field">
                    <label>Price</label>
                    <input type="text" name="price" value="{{ old('price') }}">
                </div>

                <div class="field">
                    <label>Brand</label>
                    <select name="brand_id">
                        <option value="">Select brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->brand_id }}" {{ old('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Image URL</label>
                    <input type="text" name="image_url" value="{{ old('image_url') }}">
                </div>

                <div class="field">
                    <label>Stock Quantity</label>
                    <input type="text" name="quantity" value="{{ old('quantity') }}">
                </div>

                <div class="field">
                    <label>Reorder Level</label>
                    <input type="text" name="reorder_level" value="{{ old('reorder_level', 5) }}">
                </div>

                <button type="submit">Create Product</button>
            </form>

            <a href="{{ route('admin.products.index') }}" class="helper-link-back">← Back to Products</a>
        </div>
    </div>
</body>
</html>