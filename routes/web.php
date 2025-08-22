<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
//use Src\Customer\Infrastructure\Http\Controllers\CustomerController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('v2/customer')->group(function () {
    //Route::get('/test', [CustomerController::class, 'test']);
});

require_once __DIR__ . '/../src/Customer/Infrastructure/Http/routes/web.php';







// only 

