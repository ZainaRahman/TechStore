<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card">
            <h1>Admin Login</h1>
            <p class="card-subtext">Restricted access — TechStore management</p>

            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password">
                </div>

                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>