<!DOCTYPE html>
<html>
<head>
    <title>Choose Login - TechStore</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-wrapper auth-shell-glass">
        <div class="glass-orb glass-orb-one"></div>
        <div class="glass-orb glass-orb-two"></div>

        <div class="auth-split">
            <section class="auth-hero-panel">
                <div>
                    <p class="auth-kicker">TechStore access</p>
                    <h1>Login with a clean glass view.</h1>
                    <p>Choose your role once, then continue to the right login screen without hunting for separate URLs.</p>
                </div>

                <div class="auth-hero-points">
                    <div class="auth-hero-point">
                        <div>
                            <strong>Customer access</strong>
                            <span>Browse products, track orders, and leave reviews.</span>
                        </div>
                    </div>
                    <div class="auth-hero-point">
                        <div>
                            <strong>Admin access</strong>
                            <span>Manage products, orders, reviews, and inventory.</span>
                        </div>
                    </div>
                </div>
            </section>

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
                    </form>

                    <div class="login-choice-links">
                        <a href="{{ route('user.login') }}">Customer login</a>
                        <a href="{{ route('admin.login') }}">Admin login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>