<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminUserController;

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
    Route::get('/admin/panel', [AdminUserController::class, 'panel'])->name('admin.panel');
    Route::post('/admin/users/{user}/promote', [AdminUserController::class, 'promote'])->name('admin.users.promote');
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
});

Route::resource('item', ItemController::class);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/profile', [UserController::class, 'myProfile'])->name('profile');
Route::get('/profile/{user}', [UserController::class, 'showProfile'])->name('user.profile');
Route::patch('/items/{item}/mark-sold', [ItemController::class, 'markAsSold'])->name('items.markSold');
Route::get('/chat-contacts', [MessageController::class, 'contacts'])->name('chat.contacts');
Route::get('/items/{item}/mark-sold', [ItemController::class, 'markAsSold'])->name('items.markSoldForm');
Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
