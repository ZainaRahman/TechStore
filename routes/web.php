<?php
use App\Http\Controllers\Auth\AuthController;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Placeholder — real dashboard view comes with later features
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');