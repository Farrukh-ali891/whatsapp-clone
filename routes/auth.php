<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'registerView'])->name('register');
    Route::post('register', [AuthController::class, 'registerUser']);

    Route::get('login', [AuthController::class, 'loginView'])->name('login');
    Route::post('login', [AuthController::class, 'loginUser']);

    Route::get('forgot-password', [AuthController::class, 'forgetPasswordView'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'forgetPasswordUser'])->name('password.email');

    Route::get('reset-password/{token}', [AuthController::class, 'resetPasswordView'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetUserPassword'])->name('password.store');

    Route::get('/otp-verification', [AuthController::class, 'otpVerifyView'])->name('otp.verify');
    Route::post('/otp-verification', [AuthController::class, 'otpVerifyUser']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('confirm-password', [AuthController::class, 'confirmPasswordView'])->name('password.confirm');
    Route::post('confirm-password', [AuthController::class, 'confirmPasswordUser']);

    Route::put('password', [AuthController::class, 'updatePasswordUser'])->name('password.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // CHAT CONTROLLER ROUTES
});
