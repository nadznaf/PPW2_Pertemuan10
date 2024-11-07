<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GalleryController;

Route::view('/', 'welcome')->name('welcome');
Route::view('restricted', 'restricted')->middleware('checkage');
Route::view('admin', 'admin')->middleware('admin');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginRegisterController::class, 'login'])->name('login');
    Route::get('register', [LoginRegisterController::class, 'register'])->name('register');
    Route::post('store', [LoginRegisterController::class, 'store'])->name('store');
    Route::post('authenticate', [LoginRegisterController::class, 'authenticate'])->name('authenticate');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [LoginRegisterController::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [LoginRegisterController::class, 'logout'])->name('logout');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{id}', [UserController::class, 'update'])->name('update');
        Route::delete('{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});

Route::resource('gallery', GalleryController::class)->except(['show']);
