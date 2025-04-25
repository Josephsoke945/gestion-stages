<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Agent;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private function generateUniqueMatricule()
    {
        do {
            // Génère un matricule au format AG-XXXXX (où X sont des chiffres)
            $matricule = 'AG-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Agent::where('matricule', $matricule)->exists());

        return $matricule;
    }

    public function index()
    {
        // Vérifiez si l'utilisateur est admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $users = User::select('id', 'nom', 'email', 'role', 'created_at')->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'agent_roles' => [
                'DPAF' => Agent::ROLE_DPAF,
                'MS' => Agent::ROLE_MS,
                'RS' => Agent::ROLE_RS,
            ]
        ]);
    }

    public function store(Request $request)
    {
        // Vérifiez si l'utilisateur est admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string|in:admin,agent,stagiaire',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ]);

        // Si le rôle est "agent", créez une entrée de base dans la table `agents`
        if ($validated['role'] === 'agent') {
            Agent::create([
                'user_id' => $user->id,
                'matricule' => $this->generateUniqueMatricule(),
                'fonction' => 'À définir',
                'role_agent' => 'DPAF', // Valeur par défaut, à modifier dans le formulaire des agents
                'date_embauche' => now(),
            ]);
        } 
        // Si le rôle est "stagiaire", créez une entrée dans la table `stagiaires`
        elseif ($validated['role'] === 'stagiaire') {
            Stagiaire::create([
                'user_id' => $user->id,
                'niveau_etude' => 'À définir',
                'universite' => 'À définir',
                'filiere' => 'À définir',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:admin,agent,stagiaire',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Mise à jour des informations de base de l'utilisateur
        $user->update([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
        ]);

        // Si le nouveau rôle est "agent" et l'utilisateur n'était pas un agent avant
        if ($validated['role'] === 'agent' && !$user->agent) {
            Agent::create([
                'user_id' => $user->id,
                'matricule' => $this->generateUniqueMatricule(),
                'fonction' => 'À définir',
                'role_agent' => 'DPAF',
                'date_embauche' => now(),
            ]);
        }
        // Si le nouveau rôle est "stagiaire" et l'utilisateur n'était pas un stagiaire avant
        elseif ($validated['role'] === 'stagiaire' && !$user->stagiaire) {
            Stagiaire::create([
                'user_id' => $user->id,
                'niveau_etude' => 'À définir',
                'universite' => 'À définir',
                'filiere' => 'À définir',
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        // Vérifier si l'utilisateur n'est pas l'utilisateur connecté
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}