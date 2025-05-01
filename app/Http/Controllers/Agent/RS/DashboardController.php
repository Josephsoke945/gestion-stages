<?php

namespace App\Http\Controllers\Agent\RS;

use App\Http\Controllers\Controller;
use App\Models\DemandeStage;
use App\Models\Structure;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->agent || $user->agent->role_agent !== 'RS') {
            abort(403, 'Accès réservé aux Responsables de Structure.');
        }
    }

    public function index()
    {
        $user = Auth::user();
        $agent = $user->agent;
        $stats = [
            'demandesEnAttente' => 0,
            'demandesTraitees' => 0,
            'demandesAcceptees' => 0,
            'demandesRejetees' => 0,
        ];

        try {
            // Récupérer la structure dont l'agent est responsable
            $structure = Structure::where('responsable_id', $agent->id)->first();

            if ($structure) {
                // Statistiques des demandes pour cette structure
                $stats['demandesEnAttente'] = DemandeStage::where('structure_id', $structure->id)
                    ->where('statut', 'En attente')
                    ->count();
                    
                $stats['demandesAcceptees'] = DemandeStage::where('structure_id', $structure->id)
                    ->where('statut', 'Approuvée')
                    ->count();
                    
                $stats['demandesRejetees'] = DemandeStage::where('structure_id', $structure->id)
                    ->where('statut', 'Refusée')
                    ->count();
                    
                $stats['demandesTraitees'] = $stats['demandesAcceptees'] + $stats['demandesRejetees'];

                // Récupérer les 5 dernières demandes
                $dernieresDemandes = DemandeStage::with(['stagiaire.user'])
                    ->where('structure_id', $structure->id)
                    ->latest()
                    ->take(5)
                    ->get();

                return Inertia::render('Agent/RS/Dashboard', [
                    'stats' => $stats,
                    'structure' => $structure,
                    'dernieresDemandes' => $dernieresDemandes,
                ]);
            }

            // Si l'agent n'est pas responsable d'une structure
            Log::warning('Agent RS sans structure assignée', [
                'agent_id' => $agent->id,
                'user_id' => $user->id
            ]);

            return Inertia::render('Agent/RS/Dashboard', [
                'stats' => $stats,
                'structure' => null,
                'dernieresDemandes' => [],
                'error' => 'Vous n\'êtes actuellement responsable d\'aucune structure.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du dashboard RS', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id
            ]);

            return Inertia::render('Agent/RS/Dashboard', [
                'stats' => $stats,
                'structure' => null,
                'dernieresDemandes' => [],
                'error' => 'Une erreur est survenue lors du chargement des données.'
            ]);
        }
    }
} 