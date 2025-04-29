<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Structure;
use App\Models\Agent;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else if ($user && $user->role === 'stagiaire') {
            return inertia('Dashboard/Stagiaire');
        }

        return inertia('Dashboard/Default');
    }
    
    public function adminDashboard()
    {
        try {
            // Count statistics for admin dashboard with error handling
            $stats = [
                'users' => User::count(),
                'structures' => Structure::count(),
                'stagiaires' => User::where('role', 'stagiaire')->count(),
                'agents' => User::where('role', 'agent')->count(),
            ];

            return inertia('Dashboard/Admin', [
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            // Capture l'erreur et fourni un fallback pour les statistiques
            Log::error("Erreur dans le dashboard admin: " . $e->getMessage());
            return inertia('Dashboard/Admin', [
                'stats' => [
                    'users' => 0,
                    'structures' => 0,
                    'stagiaires' => 0,
                    'agents' => 0
                ],
                'error' => "Une erreur s'est produite lors du chargement des statistiques."
            ]);
        }
    }
}
