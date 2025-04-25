<?php

namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DemandeStage;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stagiaire = $user->stagiaire;

        // Récupérer toutes les structures actives
        $structures = Structure::where('active', true)->get();
        
        // Récupérer tous les utilisateurs pour la sélection des membres
        $users = User::where('role', 'stagiaire')
            ->where('id', '!=', $user->id)
            ->get();

        // Log pour vérifier les informations du stagiaire
        Log::info('Informations du stagiaire', [
            'user_id' => $user->id,
            'stagiaire' => $stagiaire,
            'stagiaire_id' => $stagiaire ? $stagiaire->id_stagiaire : null
        ]);

        if (!$stagiaire) {
            Log::error('Stagiaire non trouvé pour l\'utilisateur', [
                'user_id' => $user->id
            ]);
            return Inertia::render('Stagiaire/Dashboard', [
                'demandes' => [],
                'stats' => [
                    'total_demandes' => 0,
                    'en_attente' => 0,
                    'approuvees' => 0,
                    'refusees' => 0,
                ],
                'structures' => $structures,
                'users' => $users
            ]);
        }

        // Récupérer toutes les demandes pour les statistiques
        $allDemandes = DemandeStage::where('stagiaire_id', $stagiaire->id_stagiaire)->get();

        // Log pour vérifier les demandes trouvées
        Log::info('Demandes trouvées', [
            'nombre_demandes' => $allDemandes->count(),
            'demandes' => $allDemandes->toArray()
        ]);

        // Récupérer les 5 dernières demandes pour l'affichage
        $recentDemandes = DemandeStage::where('stagiaire_id', $stagiaire->id_stagiaire)
            ->with(['structure'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Stagiaire/Dashboard', [
            'demandes' => $recentDemandes,
            'stats' => [
                'total_demandes' => $allDemandes->count(),
                'en_attente' => $allDemandes->where('statut', 'En attente')->count(),
                'approuvees' => $allDemandes->where('statut', 'Approuvée')->count(),
                'refusees' => $allDemandes->where('statut', 'Refusée')->count(),
            ],
            'structures' => $structures,
            'users' => $users
        ]);
    }
} 