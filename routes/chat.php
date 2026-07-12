<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [ChatController::class, 'dashboard'])->name('dashboard');

    Route::post('/search_user', [ChatController::class, 'searchUser'])->name('user.search');
});
