<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\User;
use App\Models\DemandeStage;
use App\Models\Stagiaire;
use App\Models\MembreGroupe;
use App\Mail\DemandeConfirmationMarkdown;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DemandeController extends Controller
{
    /**
     * Affiche le formulaire de demande de stage
     */
    public function create()
    {
        $structures = Structure::all();
        $users = User::where('id', '!=', Auth::id())->get();
        
        return Inertia::render('Stagiaire/Dashboard', [
            'structures' => $structures,
            'users' => $users
        ]);
    }
    
    /**
     * Enregistre une nouvelle demande de stage
     */
    public function store(Request $request)
    {
        // Validation de la requête
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'universite' => 'required|string|max:255',
            'filiere' => 'required|string|max:255',
            'niveau_etude' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'structure_id' => 'required|exists:structures,id',
            'type' => 'required|in:Académique,Professionnelle',
            'nature' => 'required|in:Individuel,Groupe',
            'lettre_cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'membres' => 'nullable|array',
            'membres.*' => 'exists:users,id',
        ]);
        
        // Début de la transaction pour assurer l'intégrité des données
        DB::beginTransaction();
        
        try {
            // Mettre à jour les informations de l'utilisateur
            $user = User::findOrFail(Auth::id());
            $user->update([
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
            ]);
            
            // Créer ou mettre à jour les informations du stagiaire
            $stagiaire = Stagiaire::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'universite' => $validated['universite'],
                    'filiere' => $validated['filiere'],
                    'niveau_etude' => $validated['niveau_etude'],
                ]
            );
            
            // Upload du fichier s'il existe
            $path = null;
            if ($request->hasFile('lettre_cv_path') && $request->file('lettre_cv_path')->isValid()) {
                $path = $request->file('lettre_cv_path')->store('documents', 'public');
            }
            
            // Générer un code de suivi unique
            $codeSuivi = strtoupper(Str::random(8));
            Log::info('Code de suivi généré : ' . $codeSuivi);
            
            // Créer la demande de stage
            $demande = DemandeStage::create([
                'stagiaire_id' => $stagiaire->id_stagiaire,
                'structure_id' => $validated['structure_id'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'type' => $validated['type'],
                'nature' => $validated['nature'],
                'lettre_cv_path' => $path,
                'code_suivi' => $codeSuivi,
                'statut' => 'En attente',
                'date_soumission' => now(),
            ]);
            
            // Si demande en groupe, associer les membres
            if ($validated['nature'] === 'Groupe' && !empty($validated['membres'])) {
                foreach ($validated['membres'] as $membreId) {
                    MembreGroupe::create([
                        'demande_stage_id' => $demande->id,
                        'user_id' => $membreId,
                    ]);
                }
            }
            
            // Charger la relation structure pour l'email
            $demande->load('structure');
            
            // Envoyer l'email de confirmation en arrière-plan (via la file d'attente)
            try {
                // Envoyer l'email directement au demandeur principal
                Mail::to($user->email)
                    ->send(new DemandeConfirmationMarkdown($demande, $user));
                
                Log::info('Email de confirmation envoyé à ' . $user->email);
                
                // Envoyer également aux membres du groupe si demande en groupe
                if ($validated['nature'] === 'Groupe' && !empty($validated['membres'])) {
                    foreach ($validated['membres'] as $membreId) {
                        $membre = User::find($membreId);
                        if ($membre && $membre->id !== $user->id && $membre->email) {
                            Mail::to($membre->email)
                                ->send(new DemandeConfirmationMarkdown($demande, $membre));
                            
                            Log::info('Email de confirmation envoyé au membre du groupe: ' . $membre->email);
                        }
                    }
                }
                
            } catch (\Exception $emailException) {
                Log::error('Erreur lors de l\'envoi de l\'email: ' . $emailException->getMessage());
                // On continue le processus même si l'email échoue
            }
            
            // Tout s'est bien passé, on valide la transaction
            DB::commit();
            
            // Vérification avant de retourner
            Log::info('Demande créée avec succès, ID: ' . $demande->id . ', Code de suivi: ' . $codeSuivi);
            
            // Stocker le code de suivi de différentes manières pour s'assurer qu'il est disponible dans la vue
            session()->flash('code_suivi', $codeSuivi);
            
            // Redirection avec données explicites, inclure l'ID de la demande pour l'API d'emails
            return redirect()->back()->with([
                'success' => true,
                'message' => 'Demande soumise avec succès',
                'code_suivi' => $codeSuivi,
                'demande_id' => $demande->id
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, on annule toutes les opérations
            DB::rollBack();
            
            return redirect()->back()->withErrors([
                'message' => 'Une erreur est survenue lors de la soumission: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Affiche les détails d'une demande
     */
    public function show($id)
    {
        $demande = DemandeStage::with(['stagiaire.user', 'structure', 'membres.user'])
            ->findOrFail($id);
            
        // Vérifier que l'utilisateur est autorisé à voir cette demande
        if ($demande->stagiaire->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette demande.');
        }
        
        return Inertia::render('Stagiaire/ShowDemande', [
            'demande' => $demande
        ]);
    }
    
    /**
     * Recherche une demande par code de suivi
     */
    public function findByCode(Request $request)
    {
        $request->validate([
            'code_suivi' => 'required|string'
        ]);
        
        $demande = DemandeStage::where('code_suivi', $request->code_suivi)
            ->with(['stagiaire.user', 'structure'])
            ->first();
            
        if (!$demande) {
            return redirect()->route('mes.demandes')->withErrors([
                'code_suivi' => 'Aucune demande trouvée avec ce code de suivi.'
            ]);
        }
        
        // Pas besoin de vérifier les permissions car le code de suivi est public
        // Cela permet à n'importe qui avec le code de suivre l'état de la demande
        
        return Inertia::render('Stagiaire/ShowDemande', [
            'demande' => $demande
        ]);
    }

    /**
     * Affiche la liste des demandes de l'utilisateur connecté
     */
    public function index()
    {
        // Récupérer le stagiaire connecté
        $stagiaire = Stagiaire::where('user_id', Auth::id())->first();
        
        // Initialiser un tableau vide pour les demandes
        $demandes = collect([]);
        
        // Si le stagiaire existe, récupérer ses demandes
        if ($stagiaire) {
            $demandes = DemandeStage::where('stagiaire_id', $stagiaire->id_stagiaire)
                ->with('structure')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Retourner la vue avec les demandes (vides ou non)
        return Inertia::render('Stagiaire/MesDemandes', [
            'demandes' => $demandes
        ]);
    }

    /**
     * Supprime/annule une demande de stage
     */
    public function destroy($id)
    {
        // Récupérer la demande avec la structure et le stagiaire
        $demande = DemandeStage::with(['structure', 'stagiaire.user'])->findOrFail($id);
        
        // Vérifier que l'utilisateur est autorisé à supprimer cette demande
        $stagiaire = Stagiaire::where('user_id', Auth::id())->first();
        
        if (!$stagiaire || $demande->stagiaire_id !== $stagiaire->id_stagiaire) {
            return redirect()->back()->with([
                'toast' => [
                    'type' => 'error',
                    'message' => 'Vous n\'êtes pas autorisé à annuler cette demande.'
                ]
            ]);
        }
        
        // Vérifier que la demande est encore en attente
        if ($demande->statut !== 'En attente') {
            return redirect()->back()->with([
                'toast' => [
                    'type' => 'error',
                    'message' => 'Vous ne pouvez annuler que les demandes en attente.'
                ]
            ]);
        }
        
        // Stocker le code de suivi et l'utilisateur pour l'email avant suppression
        $codeSuivi = $demande->code_suivi;
        $user = $demande->stagiaire->user;
        
        // Envoyer l'email de confirmation d'annulation
        try {
            Mail::to($user->email)->send(new \App\Mail\DemandeAnnulationMail($demande, $user));
            
            // Si c'est une demande de groupe, envoyer aussi aux membres
            if ($demande->nature === 'Groupe') {
                $membres = \App\Models\MembreGroupe::where('demande_stage_id', $demande->id)->get();
                
                foreach ($membres as $membre) {
                    $membreUser = User::find($membre->user_id);
                    if ($membreUser && $membreUser->id !== $user->id) {
                        Mail::to($membreUser->email)->send(new \App\Mail\DemandeAnnulationMail($demande, $membreUser));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email d\'annulation: ' . $e->getMessage());
            // On continue le processus même si l'email échoue
        }
        
        // Supprimer la demande
        $demande->delete();
        
        return redirect()->route('mes.demandes')->with([
            'toast' => [
                'type' => 'success',
                'message' => 'Votre demande a été annulée avec succès.'
            ]
        ]);
    }
}