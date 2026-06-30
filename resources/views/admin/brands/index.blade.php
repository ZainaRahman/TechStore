<!DOCTYPE html>
<html>
<head>
    <title>Manage Brands - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>Manage Brands</h1>
            <a href="{{ route('admin.brands.create') }}" class="btn-primary-link">+ New Brand</a>
        </div>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                    <tr>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->country ?? '—' }}</td>
                        <td class="table-actions">
                            <a href="{{ route('admin.brands.edit', $brand->brand_id) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand->brand_id) }}" onsubmit="return confirm('Delete this brand?');">
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