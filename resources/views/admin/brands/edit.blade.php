<!DOCTYPE html>
<html>
<head>
    <title>Edit Brand - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Edit Brand</h1>

            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.brands.update', $brand->brand_id) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $brand->name) }}">
                </div>

                <div class="field">
                    <label>Country</label>
                    <input type="text" name="country" value="{{ old('country', $brand->country) }}">
                </div>

                <div class="field">
                    <label>Logo URL</label>
                    <input type="text" name="logo_url" value="{{ old('logo_url', $brand->logo_url) }}">
                </div>

                <div class="field">
                    <label>Description</label>
                    <input type="text" name="description" value="{{ old('description', $brand->description) }}">
                </div>

                <button type="submit">Update Brand</button>
            </form>

            <a href="{{ route('admin.brands.index') }}" class="helper-link-back">← Back to Brands</a>
        </div>
    </div>
</body>
</html>