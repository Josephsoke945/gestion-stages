<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DemandeStage;
use App\Models\Structure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Models\AffectationResponsableStructure;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DemandeStage::query()
                ->with(['stagiaire.user', 'structure'])
                ->latest();

            // Filtre par recherche
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    // Recherche dans la table users (stagiaire)
                    $q->whereHas('stagiaire.user', function($query) use ($search) {
                        $terms = explode(' ', $search);
                        $query->where(function($q) use ($terms) {
                            foreach($terms as $term) {
                                $q->where(function($q) use ($term) {
                                    $q->where('nom', 'like', "%{$term}%")
                                      ->orWhere('prenom', 'like', "%{$term}%")
                                      ->orWhere('email', 'like', "%{$term}%");
                                });
                            }
                        });
                    })
                    // Recherche dans la table structures
                    ->orWhereHas('structure', function($query) use ($search) {
                        $query->where('libelle', 'like', "%{$search}%");
                    });
                });
            }

            // Filtre par statut
            if ($request->filled('status') && in_array($request->status, ['En attente','En cours' ,'Approuvée', 'Refusée'])) {
                $query->where('statut', $request->status);
            }

            // Filtre par structure
            if ($request->filled('structure_id')) {
                $query->where('structure_id', $request->structure_id);
            }

            $demandes = $query->paginate(10)
                ->withQueryString();

            // Récupérer toutes les structures pour le filtre
            $structures = Structure::select('id', 'libelle', 'sigle')
                ->orderBy('libelle')
                ->get()
                ->map(function($structure) {
                    return [
                        'id' => $structure->id,
                        'libelle' => $structure->sigle ? $structure->sigle . ' - ' . $structure->libelle : $structure->libelle
                    ];
                });

            return Inertia::render('Agent/Demandes/Index', [
                'demandes' => $demandes,
                'structures' => $structures,
                'filters' => $request->only(['search', 'status', 'structure_id'])
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche des demandes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'search' => $request->search,
                'status' => $request->status,
                'structure_id' => $request->structure_id
            ]);

            return Inertia::render('Agent/Demandes/Index', [
                'demandes' => [],
                'structures' => [],
                'filters' => $request->only(['search', 'status', 'structure_id']),
                'error' => 'Une erreur est survenue lors de la recherche.'
            ]);
        }
    }

    public function show(DemandeStage $demande)
    {
        $demande->load(['stagiaire.user', 'structure']);
        $structures = Structure::select('id', 'libelle', 'sigle')
            ->orderBy('libelle')
            ->get()
            ->map(function($structure) {
                return [
                    'id' => $structure->id,
                    'libelle' => $structure->sigle ? $structure->sigle . ' - ' . $structure->libelle : $structure->libelle
                ];
            });

        return Inertia::render('Agent/Demandes/Show', [
            'demande' => $demande,
            'structures' => $structures
        ]);
    }

    public function approve(DemandeStage $demande)
    {
        try {
            if ($demande->statut !== 'En attente') {
                return back()->with('error', 'Cette demande ne peut plus être approuvée.');
            }

            $demande->update([
                'statut' => 'Approuvée',
                'date_traitement' => now()
            ]);

            // TODO: Envoyer un email de confirmation au stagiaire

            return redirect()->route('agent.demandes.show', $demande)
                ->with('success', 'La demande a été approuvée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'approbation de la demande', [
                'demande_id' => $demande->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'approbation de la demande.');
        }
    }

    public function reject(DemandeStage $demande)
    {
        try {
            if ($demande->statut !== 'En attente') {
                return back()->with('error', 'Cette demande ne peut plus être refusée.');
            }

            $demande->update([
                'statut' => 'Refusée',
                'date_traitement' => now()
            ]);

            // TODO: Envoyer un email de notification au stagiaire

            return redirect()->route('agent.demandes.show', $demande)
                ->with('success', 'La demande a été refusée.');
        } catch (\Exception $e) {
            Log::error('Erreur lors du refus de la demande', [
                'demande_id' => $demande->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors du refus de la demande.');
        }
    }

    public function affecter(Request $request, DemandeStage $demande)
    {
        $request->validate([
            'structure_id' => 'required|exists:structures,id'
        ]);

        try {
            // Récupérer la structure
            $structure = Structure::findOrFail($request->structure_id);

            // Mettre à jour le statut de la demande en "En cours"
            $demande->update([
                'statut' => 'En cours'
            ]);

            // Créer une nouvelle affectation
            AffectationResponsableStructure::create([
                'structure_id' => $request->structure_id,
                'id_demande_stages' => $demande->id,
                'date_affectation' => now(),
            ]);

            return redirect()->back()->with('success', "La demande '{$demande->code_suivi}' a été affectée à la structure '{$structure->sigle}' avec succès");
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affectation de la structure', [
                'demande_id' => $demande->id,
                'structure_id' => $request->structure_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'affectation de la structure.');
        }
    }
} 