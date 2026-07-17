<!DOCTYPE html>
<html>
<head>
    <title>Register - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-wrapper auth-shell-glass">
        <div class="glass-orb glass-orb-one"></div>
        <div class="glass-orb glass-orb-two"></div>

        <div class="card auth-card glass-card">
            <h1>Create your account</h1>
            <p class="card-subtext">Join TechStore in a few seconds</p>

    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label>Full Name</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}">
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}">
        </div>

        <div class="field">
            <label>Address</label>
            <input type="text" name="address" value="{{ old('address') }}">
        </div>

        <div class="field">
            <label>City</label>
            <input type="text" name="city" value="{{ old('city') }}">
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password">
        </div>

        <div class="field">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation">
        </div>

        <button type="submit">Register</button>
    </form>

    <p class="helper-link">Already have an account? <a href="{{ route('login') }}">Login</a></p>
</body>
</html>