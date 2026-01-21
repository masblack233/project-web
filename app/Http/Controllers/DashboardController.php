<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = $user->products()->latest()->get();
        $services = $user->services()->latest()->get();

        return view('dashboard', compact('products', 'services'));
    }
}