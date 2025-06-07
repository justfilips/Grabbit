<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('home');
})->name('home');


// Show login form
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
// Handle login submit
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Show register form
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');

// Handle register submit
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profile', [UserController::class, 'profile'])->name('profile')->middleware('auth');

Route::resource('item', ItemController::class);
