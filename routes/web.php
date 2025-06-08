<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

//Padod arī items
Route::get('/', [ItemController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');

});
Route::resource('item', ItemController::class);
// Show login form
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
// Handle login submit
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Show register form
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');

// Handle register submit
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/profile/{user}', [UserController::class, 'showProfile'])->name('profile.show');

Route::patch('/items/{item}/mark-sold', [ItemController::class, 'markAsSold'])->name('items.markSold');


