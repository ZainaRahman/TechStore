<!DOCTYPE html>
<html>
<head>
    <title>Reviews - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page-wrapper">
        <div class="dashboard-header">
            <h1>Review Moderation</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn-outline">← Dashboard</a>
        </div>

        @if (session('status'))
            <div class="status-box">{{ session('status') }}</div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reviews as $review)
                    <tr>
                        <td>{{ $review->product_name }}</td>
                        <td>{{ $review->full_name }}</td>
                        <td>{{ $review->rating }} / 5</td>
                        <td>{{ $review->comment ?? '—' }}</td>
                        <td>
                            @if ($review->is_approved)
                                <span class="status-badge status-delivered">Approved</span>
                            @else
                                <span class="status-badge status-pending">Pending</span>
                            @endif
                        </td>
                        <td class="table-actions">
                            @if (!$review->is_approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review->review_id) }}">
                                    @csrf
                                    <button type="submit" class="link-action">Approve</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.reviews.reject', $review->review_id) }}"
                                  onsubmit="return confirm('Delete this review?');">
                                @csrf
                                <button type="submit" class="link-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>