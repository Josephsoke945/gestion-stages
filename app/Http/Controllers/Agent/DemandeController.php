<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DemandeStage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

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
            if ($request->filled('status') && in_array($request->status, ['En attente', 'Approuvée', 'Refusée'])) {
                $query->where('statut', $request->status);
            }

            $demandes = $query->paginate(10)
                ->withQueryString();

            return Inertia::render('Agent/Demandes/Index', [
                'demandes' => $demandes,
                'filters' => $request->only(['search', 'status'])
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche des demandes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'search' => $request->search,
                'status' => $request->status
            ]);

            return Inertia::render('Agent/Demandes/Index', [
                'demandes' => [],
                'filters' => $request->only(['search', 'status']),
                'error' => 'Une erreur est survenue lors de la recherche.'
            ]);
        }
    }

    public function show(DemandeStage $demande)
    {
        $demande->load(['stagiaire.user', 'structure']);

        return Inertia::render('Agent/Demandes/Show', [
            'demande' => $demande
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
} 