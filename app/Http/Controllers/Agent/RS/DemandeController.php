<?php

namespace App\Http\Controllers\Agent\RS;

use App\Http\Controllers\Controller;
use App\Models\DemandeStage;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class DemandeController extends Controller
{
    public function __construct(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->agent || $user->agent->role_agent !== 'RS') {
            abort(403, 'Accès réservé aux Responsables de Structure.');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $agent = $user->agent;
        
        try {
            $structure = Structure::where('responsable_id', $agent->id)->first();
            
            if (!$structure) {
                return redirect()->back()->with('error', 'Vous n\'êtes responsable d\'aucune structure.');
            }

            // Construction de la requête de base
            $query = DemandeStage::with(['stagiaire.user'])
                ->where('structure_id', $structure->id);

            // Filtre par statut
            if ($request->filled('status')) {
                $query->where('statut', $request->status);
            }

            // Filtre par recherche
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('stagiaire.user', function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            // Filtre par période
            if ($request->filled('period')) {
                $now = now();
                switch ($request->period) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [
                            $now->startOfWeek()->toDateTimeString(),
                            $now->endOfWeek()->toDateTimeString()
                        ]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', $now->month)
                              ->whereYear('created_at', $now->year);
                        break;
                    case 'year':
                        $query->whereYear('created_at', $now->year);
                        break;
                }
            }

            // Récupération des demandes avec pagination
            $demandes = $query->latest()->paginate(10)->withQueryString();

            return Inertia::render('Agent/RS/Demandes/Index', [
                'demandes' => $demandes,
                'structure' => $structure,
                'filters' => $request->only(['status', 'search', 'period', 'page'])
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des demandes RS', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors du chargement des demandes.');
        }
    }

    public function show(DemandeStage $demande)
    {
        $user = Auth::user();
        $agent = $user->agent;

        try {
            $structure = Structure::where('responsable_id', $agent->id)->first();
            
            if (!$structure || $demande->structure_id !== $structure->id) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès à cette demande.');
            }

            $demande->load(['stagiaire.user', 'structure']);

            return Inertia::render('Agent/RS/Demandes/Show', [
                'demande' => $demande
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de la demande RS', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id,
                'demande_id' => $demande->id
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors du chargement de la demande.');
        }
    }

    public function approve(DemandeStage $demande)
    {
        $user = Auth::user();
        $agent = $user->agent;

        try {
            $structure = Structure::where('responsable_id', $agent->id)->first();
            
            if (!$structure || $demande->structure_id !== $structure->id) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès à cette demande.');
            }

            $demande->update([
                'statut' => 'Approuvée',
                'date_traitement' => now(),
                'traite_par' => $agent->id
            ]);

            return redirect()->back()->with('success', 'La demande a été approuvée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'approbation de la demande RS', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id,
                'demande_id' => $demande->id
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'approbation de la demande.');
        }
    }

    public function reject(Request $request, DemandeStage $demande)
    {
        $user = Auth::user();
        $agent = $user->agent;

        try {
            $structure = Structure::where('responsable_id', $agent->id)->first();
            
            if (!$structure || $demande->structure_id !== $structure->id) {
                return redirect()->back()->with('error', 'Vous n\'avez pas accès à cette demande.');
            }

            $validated = $request->validate([
                'motif_rejet' => 'required|string|max:500'
            ]);

            $demande->update([
                'statut' => 'Refusée',
                'date_traitement' => now(),
                'traite_par' => $agent->id,
                'motif_rejet' => $validated['motif_rejet']
            ]);

            return redirect()->back()->with('success', 'La demande a été rejetée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet de la demande RS', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id,
                'demande_id' => $demande->id
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors du rejet de la demande.');
        }
    }
} 