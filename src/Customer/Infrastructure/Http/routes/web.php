<?php

use Illuminate\Support\Facades\Route;
use Src\Customer\Infrastructure\Http\Controllers\CustomerController;

Route::get('customer/test', [CustomerController::class, 'test'])->name('customer.test');
Route::resource('customer', CustomerController::class);