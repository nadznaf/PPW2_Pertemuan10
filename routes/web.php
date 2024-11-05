<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\UserController;



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('restricted', function () {
    return "Anda berusia lebih dari 18 tahun!";
})->middleware('checkage');

Route::get('admin', function () {
    return "Anda adalah Admin";
})->middleware('admin');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginRegisterController::class, 'login'])->name('login');;
    Route::get('/register', [LoginRegisterController::class, 'register'])->name('register');
    Route::post('/store', [LoginRegisterController::class, 'store'])->name('store');
    Route::post('/authenticate', [LoginRegisterController::class, 'authenticate'])->name('authenticate');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [LoginRegisterController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
    // Route::get('/user-list', [UserController::class, 'user-list'])->name('user-list');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

