<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginChoice()
    {
        return view('auth.login-choice');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function showUserLogin()
    {
        return view('auth.login');
    }

    public function chooseLogin(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        return $validated['role'] === 'admin'
            ? redirect()->route('admin.login')
            : redirect()->route('user.login');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $hashedPassword = Hash::make($validated['password']);

        try {
            DB::insert(
                'INSERT INTO "USERS" ("FULL_NAME", "EMAIL", "PHONE", "PASSWORD_HASH", "ADDRESS", "CITY")
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    $validated['full_name'],
                    $validated['email'],
                    $validated['phone'] ?? null,
                    $hashedPassword,
                    $validated['address'] ?? null,
                    $validated['city'] ?? null,
                ]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors([
                'email' => 'Registration failed. That email may already be in use.',
            ])->withInput();
        }

        $row = DB::selectOne('SELECT * FROM "USERS" WHERE "EMAIL" = ?', [$validated['email']]);
        Auth::login($this->hydrateUser($row));

        return redirect()->route('landing')->with('status', 'Registration successful.');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $row = DB::selectOne('SELECT * FROM "USERS" WHERE "EMAIL" = ?', [$credentials['email']]);

        if (!$row || !Hash::check($credentials['password'], $row->password_hash)) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        Auth::login($this->hydrateUser($row));
        $request->session()->regenerate();

        return redirect()->intended(route('landing'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    private function hydrateUser($row)
    {
        $user = new User();
        $user->setRawAttributes([
            'user_id'       => $row->user_id,
            'full_name'     => $row->full_name,
            'email'         => $row->email,
            'phone'         => $row->phone,
            'password_hash' => $row->password_hash,
            'address'       => $row->address,
            'city'          => $row->city,
        ], true);

        return $user;
    }
}