<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AgentController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $agents = Agent::with('user')->get();

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
            'telephone' => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string|in:Homme,Femme',
            'matricule' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_embauche' => 'required|date',
            'password' => 'required|string|min:8', // Validation du mot de passe
        ]);
        
        // Création de l'utilisateur avec rôle "agent"
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
            'password' => bcrypt($validated['password']), // Utilisation du mot de passe saisi
            'role' => 'agent',
        ]);
        
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
        // Validation de base
        $validationRules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $agent->user_id,
            'telephone' => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string|in:Homme,Femme',
            'matricule' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ];
        
        // Validation conditionnelle pour le mot de passe - requis seulement si non vide
        if ($request->filled('password')) {
            $validationRules['password'] = 'string|min:8';
        }
        
        $validated = $request->validate($validationRules);
        
        // Préparation des données utilisateur pour la mise à jour
        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
            'role' => 'agent',
        ];
        
        // Mise à jour du mot de passe uniquement s'il est fourni
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }
        
        // Mise à jour de l'utilisateur lié
        $agent->user->update($userData);
        
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