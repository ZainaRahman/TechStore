<!DOCTYPE html>
<html>
<head>
    <title>Login - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>Login</h1>

    @if (session('status'))
        <div class="status-box">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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

    <p class="helper-link">No account? <a href="{{ route('register') }}">Register</a></p>
</body>
</html>