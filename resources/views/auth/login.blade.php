<!DOCTYPE html>
<html>
<head>
    <title>Login - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-wrapper auth-shell-glass">
        <div class="glass-orb glass-orb-one"></div>
        <div class="glass-orb glass-orb-two"></div>

        <div class="card auth-card glass-card">
            <h1>Welcome back</h1>
            <p class="card-subtext">Login to your TechStore account</p>

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

    <form method="POST" action="{{ route('user.login.submit') }}">
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
    <div class="back"><a href="{{ route('landing') }}">Back to home</a></div>
</body>
</html>