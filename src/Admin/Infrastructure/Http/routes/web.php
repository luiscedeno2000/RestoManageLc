<?php

use Illuminate\Support\Facades\Route;
use Src\Admin\Infrastructure\Http\Controllers\AdminController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});