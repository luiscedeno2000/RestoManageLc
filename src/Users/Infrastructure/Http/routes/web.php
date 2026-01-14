<?php

use Illuminate\Support\Facades\Route;
use Src\Users\Infrastructure\Http\Controllers\UsersController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('users', UsersController::class);
});