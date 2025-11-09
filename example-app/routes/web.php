<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwoFactorController;

Route::resource('post', PostController::class)->middleware(['auth', 'verified', 'two_factor']);
// Route::get('/post', [PostController::class, 'index'])->name('post.index');
// Route::get('/post/create', [PostController::class, 'create']);
// Route::post('/post', [PostController::class, 'store'])->name('post.store');
// Route::get('/post/show/{post}', [PostController::class, 'show'])->name('post.show');
// Route::get('/post/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
// Route::patch('/post/{post}', [PostController::class, 'update'])->name('post.update');
// Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');

Route::get('/', function () {
    return view('welcome');
});

// メール認証関連のルートは auth.php で定義

Route::get('/dashboard', [PostController::class, 'index'])->middleware(['auth', 'verified', 'two_factor'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/verify-pin', [TwoFactorController::class, 'show'])->name('verify.pin');
    Route::post('/verify-pin', [TwoFactorController::class, 'verify'])->name('verify.pin.store');
    Route::post('/verify-pin/regenerate', [TwoFactorController::class, 'regenerate'])->name('verify.pin.regenerate');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::post('/post/{post}/lock', [PostController::class, 'lock'])->middleware(['auth', 'verified', 'two_factor'])->name('post.lock');

require __DIR__.'/auth.php';
