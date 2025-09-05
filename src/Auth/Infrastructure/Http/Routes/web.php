<?php
use Illuminate\Support\Facades\Route;
use Src\Auth\Infrastructure\Http\Controllers\ForgotPasswordController;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Home/Index'))->name('dashboard');
});