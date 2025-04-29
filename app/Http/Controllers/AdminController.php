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

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord d'administration
     */
    public function dashboard()
    {
        // Vérifier si l'utilisateur est un administrateur
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé. Vous devez être administrateur.');
        }

        try {
            // Count statistics for admin dashboard with error handling
            $stats = [
                'users' => User::count(),
                'structures' => Structure::count(),
                'stagiaires' => Stagiaire::count(),
                'agents' => Agent::count(),
            ];
            
            return Inertia::render('Dashboard/Admin', [
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            // Capture l'erreur et fourni un fallback pour les statistiques
            Log::error("Erreur dans le dashboard admin: " . $e->getMessage());
            return Inertia::render('Dashboard/Admin', [
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