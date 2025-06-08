<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;

//Padod arī items
Route::get('/', [ItemController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::get('/chat/{user}', [MessageController::class, 'show'])->name('chat.show');
    Route::get('/messages/{user}', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
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

Route::get('/profile', [UserController::class, 'myProfile'])->name('profile');
Route::get('/profile/{user}', [UserController::class, 'showProfile'])->name('user.profile');

Route::patch('/items/{item}/mark-sold', [ItemController::class, 'markAsSold'])->name('items.markSold');


Route::get('/chat-contacts', [MessageController::class, 'contacts'])->name('chat.contacts');
