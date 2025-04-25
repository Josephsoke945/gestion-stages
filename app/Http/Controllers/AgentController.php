<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'roles' => [
                'DPAF' => Agent::ROLE_DPAF,
                'MS' => Agent::ROLE_MS,
                'RS' => Agent::ROLE_RS,
            ]
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'telephone' => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|in:Homme,Femme',
            'matricule' => 'required|string|unique:agents',
            'fonction' => 'required|string',
            'role_agent' => 'required|string|in:DPAF,MS,RS',
            'date_embauche' => 'nullable|date',
        ], [
            'telephone.regex' => 'Le numéro de téléphone n\'est pas dans un format valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
        ]);

        // Créer l'utilisateur d'abord
        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
            'role' => 'agent',
        ]);

        // Créer l'agent associé
        $agent = Agent::create([
            'user_id' => $user->id,
            'matricule' => $validated['matricule'],
            'fonction' => $validated['fonction'],
            'role_agent' => $validated['role_agent'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        return redirect()->back()->with('success', 'Agent créé avec succès.');
    }
    
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $agent->user_id,
            'telephone' => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|in:Homme,Femme',
            'matricule' => 'required|string|unique:agents,matricule,' . $agent->id,
            'fonction' => 'required|string',
            'role_agent' => 'required|string|in:DPAF,MS,RS',
            'date_embauche' => 'nullable|date',
            'password' => 'nullable|string|min:8',
        ], [
            'telephone.regex' => 'Le numéro de téléphone n\'est pas dans un format valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
        ]);

        // Mettre à jour l'utilisateur
        $userData = [
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
        ];

        // Mettre à jour le mot de passe seulement s'il est fourni
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $agent->user->update($userData);

        // Mettre à jour l'agent
        $agent->update([
            'matricule' => $validated['matricule'],
            'fonction' => $validated['fonction'],
            'role_agent' => $validated['role_agent'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        return redirect()->back()->with('success', 'Agent mis à jour avec succès.');
    }
    
    public function destroy(Agent $agent)
    {
        // Supprimer l'utilisateur associé (cela supprimera aussi l'agent grâce à la relation onDelete('cascade'))
        $agent->user->delete();
        
        return redirect()->back()->with('success', 'Agent supprimé avec succès.');
    }
}