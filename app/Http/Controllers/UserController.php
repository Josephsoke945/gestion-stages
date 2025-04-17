<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Agent;
use App\Models\Stagiaire;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'nom', 'email', 'role', 'created_at')->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ]);

        // Si le rôle est "agent", créez une entrée dans la table `agents`
        if ($validated['role'] === 'agent') {
            Agent::create([
                'user_id' => $user->id,
                'matricule' => 'Générer un matricule ici', // Ajoutez un matricule si nécessaire
                'fonction' => 'Définir une fonction ici', // Ajoutez une fonction si nécessaire
            ]);
        } 
        // Si le rôle est "stagiaire", créez une entrée dans la table `stagiaires`
        elseif ($validated['role'] === 'stagiaire') {
            Stagiaire::create([
                'user_id' => $user->id,
                'niveau_etude' => 'Définir un niveau d\'étude ici', // Ajoutez un niveau d'étude si nécessaire
                'universite' => 'Définir une université ici', // Ajoutez une université si nécessaire
                'filiere' => 'Définir une filière ici', // Ajoutez une filière si nécessaire
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        // Vérifier si l'utilisateur n'est pas l'utilisateur connecté
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}