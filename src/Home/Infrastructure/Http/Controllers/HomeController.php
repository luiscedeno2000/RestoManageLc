<?php

namespace Src\Home\Infrastructure\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
class HomeController
{
    /**
     * View of home
     */
    public function index()
    {
        $pswd = '1234567890';
        $pswdHash = Hash::make($pswd);
        return Inertia::render('Index');
    }
}