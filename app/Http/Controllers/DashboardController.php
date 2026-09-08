<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the main GarmentERP ERP Dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    }
}
