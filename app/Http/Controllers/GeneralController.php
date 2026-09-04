<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class GeneralController extends Controller
{
    public function index()
    {
        return Inertia::render('Home');
    }

    public function menu()
    {
        return Inertia::render('Menu/Index');
    }

    public function profile()
    {
        return Inertia::render('Profile/Index');
    }
}
