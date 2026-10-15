<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $countActualOrder = Order::actualStatus()->count();

        return Inertia::render('Admin/Dashboard/Index', compact('countActualOrder'));
    }
}
