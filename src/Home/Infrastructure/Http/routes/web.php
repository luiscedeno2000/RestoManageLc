<?php

use Illuminate\Support\Facades\Route;
use Src\Home\Infrastructure\Http\Controllers\HomeController;

Route::get('/restomanagelc', [HomeController::class, 'index'])->name('home.index');