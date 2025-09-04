<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
//use Src\Customer\Infrastructure\Http\Controllers\CustomerController;

// Rutas con prefijo v1
Route::prefix('v1')->group(function () {
    require_once __DIR__ . '/../src/Home/Infrastructure/Http/routes/web.php';
    require_once __DIR__ . '/../src/Customer/Infrastructure/Http/routes/web.php';
});

// Rutas autenticadas con prefijo v1
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('v1')->group(function () {
    // Aquí van las rutas que requieren autenticación
});







// only 

