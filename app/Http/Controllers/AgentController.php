<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgentController extends Controller
{
    public function index()
    {
        // Récupère tous les agents avec les informations complètes de l'utilisateur lié
        $agents = Agent::whereHas('user', function($query) {
            $query->where('role', 'Agent');
        })->with('user')->get();
        return Inertia::render('Agents/Index', [
            'agents' => $agents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'telephone' => 'required|string|max:20',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string|in:Homme,Femme',
            'matricule' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            
        ]);

        // Création de l'utilisateur avec rôle "agent"
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
            'password' => bcrypt('password'), // Utilisez une méthode adaptée pour générer le mot de passe (ou définissez-le via un autre formulaire d'inscription)
            'role' => 'agent',
        ]); // Si vous utilisez une bibliothèque de rôles comme Spatie

        // Création de l'agent lié au user
        Agent::create([
            'user_id' => $user->id,
            'matricule' => $validated['matricule'],
            'fonction' => $validated['fonction'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        return redirect()->route('agents.index')->with('success', 'Agent ajouté avec succès.');
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $agent->user_id,
            'telephone' => 'required|string|max:20',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string|in:Homme,Femme',
            'matricule' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            
        ]);

        // Mise à jour de l'utilisateur lié
        $agent->user->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
            'role' => 'agent', // <-- ici on force le rôle "agent"
        ]);
        

        // Mise à jour de l'agent
        $agent->update([
            'matricule' => $validated['matricule'],
            'fonction' => $validated['fonction'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        return redirect()->route('agents.index')->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();
        return redirect()->route('agents.index')->with('success', 'Agent supprimé avec succès.');
    }
}
