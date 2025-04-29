<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StagiaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $stagiaires = Stagiaire::with(['user', 'structure'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            Log::info('Liste des stagiaires récupérée avec succès', [
                'count' => $stagiaires->count(),
                'page' => $stagiaires->currentPage()
            ]);

            return Inertia::render('Agent/Stagiaires/Index', [
                'stagiaires' => $stagiaires
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des stagiaires', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la récupération des stagiaires.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $structures = Structure::where('active', true)->get();
            
            Log::info('Formulaire de création de stagiaire chargé', [
                'structures_count' => $structures->count()
            ]);

            return Inertia::render('Agent/Stagiaires/Create', [
                'structures' => $structures
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire de création', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors du chargement du formulaire.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'user.name' => 'required|string|max:255',
                'user.email' => 'required|email|unique:users,email',
                'structure_id' => 'required|exists:structures,id',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after:date_debut',
                'theme' => 'required|string|max:255',
                'niveau_etude' => 'required|string|max:255',
                'etablissement' => 'required|string|max:255',
                'cv_path' => 'required|file|mimes:pdf|max:2048',
                'lettre_motivation_path' => 'required|file|mimes:pdf|max:2048',
                'convention_stage_path' => 'required|file|mimes:pdf|max:2048',
            ]);

            // Store files
            $cvPath = $request->file('cv_path')->store('stagiaires/cv', 'public');
            $lettrePath = $request->file('lettre_motivation_path')->store('stagiaires/lettres', 'public');
            $conventionPath = $request->file('convention_stage_path')->store('stagiaires/conventions', 'public');

            // Create user
            $user = User::create([
                'name' => $validated['user']['name'],
                'email' => $validated['user']['email'],
                'password' => Hash::make(Str::random(12)),
                'role' => 'stagiaire'
            ]);

            // Create stagiaire
            $stagiaire = Stagiaire::create([
                'user_id' => $user->id,
                'structure_id' => $validated['structure_id'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'theme' => $validated['theme'],
                'niveau_etude' => $validated['niveau_etude'],
                'etablissement' => $validated['etablissement'],
                'cv_path' => $cvPath,
                'lettre_motivation_path' => $lettrePath,
                'convention_stage_path' => $conventionPath,
                'status' => 'en_attente'
            ]);

            DB::commit();

            Log::info('Nouveau stagiaire créé avec succès', [
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'user_id' => $user->id
            ]);

            return redirect()->route('agent.stagiaires.index')
                ->with('success', 'Stagiaire créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded files if they exist
            if (isset($cvPath)) Storage::disk('public')->delete($cvPath);
            if (isset($lettrePath)) Storage::disk('public')->delete($lettrePath);
            if (isset($conventionPath)) Storage::disk('public')->delete($conventionPath);

            Log::error('Erreur lors de la création du stagiaire', [
                'error' => $e->getMessage(),
                'data' => $request->except(['cv_path', 'lettre_motivation_path', 'convention_stage_path'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du stagiaire.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Stagiaire $stagiaire)
    {
        try {
            $stagiaire->load(['user', 'structure']);
            
            Log::info('Affichage des détails du stagiaire', [
                'stagiaire_id' => $stagiaire->id_stagiaire
            ]);

            return Inertia::render('Stagiaire/ShowDemande', [
                'demande' => [
                    'code_suivi' => str_pad($stagiaire->id_stagiaire, 8, '0', STR_PAD_LEFT),
                    'statut' => ucfirst($stagiaire->status),
                    'structure' => $stagiaire->structure,
                    'type' => $stagiaire->niveau_etude,
                    'nature' => 'Individuel',
                    'date_debut' => $stagiaire->date_debut,
                    'date_fin' => $stagiaire->date_fin,
                    'date_soumission' => $stagiaire->created_at,
                    'stagiaire' => [
                        'user' => $stagiaire->user,
                        'universite' => $stagiaire->etablissement,
                        'filiere' => $stagiaire->theme,
                        'niveau_etude' => $stagiaire->niveau_etude
                    ],
                    'lettre_cv_path' => $stagiaire->cv_path
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du stagiaire', [
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'affichage du stagiaire.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stagiaire $stagiaire)
    {
        try {
            $stagiaire->load(['user', 'structure']);
            $structures = Structure::where('active', true)->get();
            
            Log::info('Formulaire d\'édition du stagiaire chargé', [
                'stagiaire_id' => $stagiaire->id_stagiaire
            ]);

            return Inertia::render('Agent/Stagiaires/Edit', [
                'stagiaire' => $stagiaire,
                'structures' => $structures
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire d\'édition', [
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors du chargement du formulaire d\'édition.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stagiaire $stagiaire)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'user.name' => 'required|string|max:255',
                'user.email' => 'required|email|unique:users,email,' . $stagiaire->user_id,
                'structure_id' => 'required|exists:structures,id',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after:date_debut',
                'theme' => 'required|string|max:255',
                'niveau_etude' => 'required|string|max:255',
                'etablissement' => 'required|string|max:255',
                'cv_path' => 'nullable|file|mimes:pdf|max:2048',
                'lettre_motivation_path' => 'nullable|file|mimes:pdf|max:2048',
                'convention_stage_path' => 'nullable|file|mimes:pdf|max:2048',
                'status' => 'required|in:en_attente,accepte,refuse'
            ]);

            $updatedFiles = [];

            // Update files if provided
            if ($request->hasFile('cv_path')) {
                Storage::disk('public')->delete($stagiaire->cv_path);
                $updatedFiles['cv_path'] = $request->file('cv_path')->store('stagiaires/cv', 'public');
            }

            if ($request->hasFile('lettre_motivation_path')) {
                Storage::disk('public')->delete($stagiaire->lettre_motivation_path);
                $updatedFiles['lettre_motivation_path'] = $request->file('lettre_motivation_path')
                    ->store('stagiaires/lettres', 'public');
            }

            if ($request->hasFile('convention_stage_path')) {
                Storage::disk('public')->delete($stagiaire->convention_stage_path);
                $updatedFiles['convention_stage_path'] = $request->file('convention_stage_path')
                    ->store('stagiaires/conventions', 'public');
            }

            // Update user
            $stagiaire->user->update([
                'name' => $validated['user']['name'],
                'email' => $validated['user']['email']
            ]);

            // Update stagiaire
            $stagiaire->update(array_merge([
                'structure_id' => $validated['structure_id'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'theme' => $validated['theme'],
                'niveau_etude' => $validated['niveau_etude'],
                'etablissement' => $validated['etablissement'],
                'status' => $validated['status']
            ], $updatedFiles));

            DB::commit();

            Log::info('Stagiaire mis à jour avec succès', [
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'updated_files' => array_keys($updatedFiles)
            ]);

            return redirect()->route('agent.stagiaires.index')
                ->with('success', 'Stagiaire mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete any newly uploaded files
            foreach ($updatedFiles ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Erreur lors de la mise à jour du stagiaire', [
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'error' => $e->getMessage(),
                'data' => $request->except(['cv_path', 'lettre_motivation_path', 'convention_stage_path'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du stagiaire.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stagiaire $stagiaire)
    {
        try {
            DB::beginTransaction();

            // Delete associated files
            Storage::disk('public')->delete([
                $stagiaire->cv_path,
                $stagiaire->lettre_motivation_path,
                $stagiaire->convention_stage_path
            ]);

            // Delete the user (this will cascade delete the stagiaire due to foreign key constraint)
            $stagiaire->user->delete();

            DB::commit();

            Log::info('Stagiaire supprimé avec succès', [
                'stagiaire_id' => $stagiaire->id_stagiaire
            ]);

            return redirect()->route('agent.stagiaires.index')
                ->with('success', 'Stagiaire supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la suppression du stagiaire', [
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du stagiaire.');
        }
    }
} 