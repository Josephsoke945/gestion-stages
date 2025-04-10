<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role === 'stagiaire') {
            return inertia('Dashboard/Stagiaire'); // Vue spécifique pour les stagiaires
        }

        return inertia('Dashboard/Default'); // Vue par défaut pour les autres utilisateurs
    }
}
