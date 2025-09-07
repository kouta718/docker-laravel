<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::resource('post', PostController::class);
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

// 認証済みユーザーのみアクセス可能
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 認証通知ページ
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 認証リンクからの処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 再送信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '確認メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/dashboard', [PostController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/post/{post}/lock', [PostController::class, 'lock'])->middleware(['auth'])->name('post.lock');

require __DIR__.'/auth.php';
