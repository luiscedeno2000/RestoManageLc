<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    require_once __DIR__.'/../src/Home/Infrastructure/Http/routes/web.php';
    require_once __DIR__.'/../src/Admin/Infrastructure/Http/routes/web.php';
    require_once __DIR__.'/../src/Roles/Infrastructure/Http/routes/web.php';
    require_once __DIR__.'/../src/Users/Infrastructure/Http/routes/web.php';
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

use Src\Home\Infrastructure\Http\Controllers\HomeController;

Route::get('/restomanagelc', [HomeController::class, 'index'])->name('home.index');