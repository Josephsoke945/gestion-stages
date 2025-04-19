<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\Stagiaire;
use App\Models\Structure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $stats = [
            'utilisateurs' => User::count(),
            'structures' => Structure::count(),
            'agents' => Agent::count(),
            'stagiaires' => Stagiaire::count()
        ];
        
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentStructures = Structure::orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentStructures'));
    }

    // Gestion des utilisateurs
    public function utilisateurs()
    {
        $utilisateurs = User::with(['agent', 'stagiaire'])->paginate(10);
        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

    public function creerUtilisateur()
    {
        $structures = Structure::all();
        return view('admin.utilisateurs.creer', compact('structures'));
    }

    public function enregistrerUtilisateur(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'role' => 'required|in:admin,agent,stagiaire',
            'photo_profil' => 'nullable|image|max:1024', // 1MB max
        ]);

        // Gestion de l'upload de photo
        $photoPath = null;
        if ($request->hasFile('photo_profil')) {
            $photoPath = $request->file('photo_profil')->store('profile_photos', 'public');
        }

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telephone' => $validated['telephone'],
            'adresse' => $validated['adresse'],
            'role' => $validated['role'],
            'photo_profil' => $photoPath,
        ]);

        // Si c'est un agent, créer un enregistrement Agent
        if ($validated['role'] === 'agent') {
            $agentValidated = $request->validate([
                'structure_id' => 'required|exists:structures,id',
                'fonction' => 'required|string|max:255',
                'description_poste' => 'nullable|string',
            ]);

            Agent::create([
                'user_id' => $user->id,
                'structure_id' => $agentValidated['structure_id'],
                'fonction' => $agentValidated['fonction'],
                'description_poste' => $agentValidated['description_poste'] ?? null,
            ]);
        }

        // Si c'est un stagiaire, créer un enregistrement Stagiaire
        if ($validated['role'] === 'stagiaire') {
            $stagiaireValidated = $request->validate([
                'structure_universite_id' => 'nullable|exists:structures,id',
                'niveau_etude' => 'nullable|string|max:255',
                'diplome' => 'nullable|string|max:255',
                'competences' => 'nullable|string',
            ]);

            Stagiaire::create([
                'user_id' => $user->id,
                'structure_universite_id' => $stagiaireValidated['structure_universite_id'] ?? null,
                'niveau_etude' => $stagiaireValidated['niveau_etude'] ?? null,
                'diplome' => $stagiaireValidated['diplome'] ?? null,
                'competences' => $stagiaireValidated['competences'] ?? null,
            ]);
        }

        return redirect()->route('admin.utilisateurs')->with('success', 'Utilisateur créé avec succès');
    }

    public function editerUtilisateur($id)
    {
        $utilisateur = User::with(['agent', 'stagiaire'])->findOrFail($id);
        $structures = Structure::all();
        return view('admin.utilisateurs.editer', compact('utilisateur', 'structures'));
    }

    public function mettreAJourUtilisateur(Request $request, $id)
    {
        $utilisateur = User::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
            'role' => 'required|in:admin,agent,stagiaire',
            'photo_profil' => 'nullable|image|max:1024', // 1MB max
        ]);

        // Gestion de l'upload de photo
        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne photo si elle existe
            if ($utilisateur->photo_profil) {
                Storage::disk('public')->delete($utilisateur->photo_profil);
            }
            $validated['photo_profil'] = $request->file('photo_profil')->store('profile_photos', 'public');
        }

        // Mise à jour des informations utilisateur
        $utilisateur->nom = $validated['nom'];
        $utilisateur->prenom = $validated['prenom'];
        $utilisateur->email = $validated['email'];
        if (!empty($validated['password'])) {
            $utilisateur->password = Hash::make($validated['password']);
        }
        $utilisateur->telephone = $validated['telephone'];
        $utilisateur->adresse = $validated['adresse'];
        $utilisateur->role = $validated['role'];
        if (isset($validated['photo_profil'])) {
            $utilisateur->photo_profil = $validated['photo_profil'];
        }
        $utilisateur->save();

        // Mise à jour ou création de l'agent
        if ($validated['role'] === 'agent') {
            $agentValidated = $request->validate([
                'structure_id' => 'required|exists:structures,id',
                'fonction' => 'required|string|max:255',
                'description_poste' => 'nullable|string',
            ]);

            Agent::updateOrCreate(
                ['user_id' => $utilisateur->id],
                [
                    'structure_id' => $agentValidated['structure_id'],
                    'fonction' => $agentValidated['fonction'],
                    'description_poste' => $agentValidated['description_poste'] ?? null,
                ]
            );
        }

        // Mise à jour ou création du stagiaire
        if ($validated['role'] === 'stagiaire') {
            $stagiaireValidated = $request->validate([
                'structure_universite_id' => 'nullable|exists:structures,id',
                'niveau_etude' => 'nullable|string|max:255',
                'diplome' => 'nullable|string|max:255',
                'competences' => 'nullable|string',
            ]);

            Stagiaire::updateOrCreate(
                ['user_id' => $utilisateur->id],
                [
                    'structure_universite_id' => $stagiaireValidated['structure_universite_id'] ?? null,
                    'niveau_etude' => $stagiaireValidated['niveau_etude'] ?? null,
                    'diplome' => $stagiaireValidated['diplome'] ?? null,
                    'competences' => $stagiaireValidated['competences'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.utilisateurs')->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function supprimerUtilisateur($id)
    {
        $utilisateur = User::findOrFail($id);
        
        // Supprimer la photo de profil si elle existe
        if ($utilisateur->photo_profil) {
            Storage::disk('public')->delete($utilisateur->photo_profil);
        }
        
        // Supprimer l'utilisateur
        $utilisateur->delete();
        
        return redirect()->route('admin.utilisateurs')->with('success', 'Utilisateur supprimé avec succès');
    }

    // Gestion des structures
    public function structures()
    {
        $structures = Structure::with('structureParent')->paginate(10);
        return view('admin.structures.index', compact('structures'));
    }

    public function creerStructure()
    {
        $structures = Structure::all();
        return view('admin.structures.creer', compact('structures'));
    }

    public function enregistrerStructure(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'type' => 'required|in:ministere,universite,autre',
            'description' => 'nullable|string',
            'est_dpaf' => 'boolean',
            'structure_parent_id' => 'nullable|exists:structures,id',
        ]);

        Structure::create($validated);

        return redirect()->route('admin.structures')->with('success', 'Structure créée avec succès');
    }

    public function editerStructure($id)
    {
        $structure = Structure::findOrFail($id);
        $structures = Structure::where('id', '!=', $id)->get();
        return view('admin.structures.editer', compact('structure', 'structures'));
    }

    public function mettreAJourStructure(Request $request, $id)
    {
        $structure = Structure::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'type' => 'required|in:ministere,universite,autre',
            'description' => 'nullable|string',
            'est_dpaf' => 'boolean',
            'structure_parent_id' => 'nullable|exists:structures,id',
        ]);

        $structure->update($validated);

        return redirect()->route('admin.structures')->with('success', 'Structure mise à jour avec succès');
    }

    public function supprimerStructure($id)
    {
        $structure = Structure::findOrFail($id);
        
        // Vérifier si la structure a des agents ou des sous-structures
        if ($structure->agents()->count() > 0 || $structure->sousServices()->count() > 0) {
            return redirect()->route('admin.structures')->with('error', 'Impossible de supprimer cette structure car elle a des agents ou des sous-structures associés.');
        }
        
        $structure->delete();
        
        return redirect()->route('admin.structures')->with('success', 'Structure supprimée avec succès');
    }
}