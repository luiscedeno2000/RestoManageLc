<?php

namespace Src\Home\Infrastructure\Http\Controllers;

use Inertia\Inertia;

class HomeController
{
    /**
     * View of home
     */
    public function index()
    {
        return Inertia::render('Home/Index');
    }
}