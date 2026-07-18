<!DOCTYPE html>
<html>
<head>
    <title>Choose Login - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-wrapper auth-shell-glass">
        <div class="auth-split">

            <div class="auth-card-shell">
                <div class="card auth-card glass-card">
                    <p class="auth-kicker">Choose your login</p>
                    <h1>Who are you?</h1>
                    <p class="card-subtext">Select whether you want to continue as a customer or as an admin.</p>

                    @if ($errors->any())
                        <div class="error-box">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.choose') }}" class="login-choice-form">
                        @csrf

                        <div class="field">
                            <label for="role">Login as</label>
                            <select id="role" name="role" required>
                                <option value="">Select role</option>
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <button type="submit">Continue</button>
                        <div class="back"><a href="{{ route('landing') }}">Back to home</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>