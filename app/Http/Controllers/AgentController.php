<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use App\Models\Structure;
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
                'MS'   => Agent::ROLE_MS,
                'RS'   => Agent::ROLE_RS,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nom'               => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8',
            'telephone'         => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe'              => 'required|in:Homme,Femme',
            'matricule'         => 'required|string|unique:agents',
            'fonction'          => 'required|string',
            'role_agent'        => 'required|string|in:DPAF,MS,RS',
            'date_embauche'     => 'nullable|date',
        ];

        // Ajouter validation spécifique si le rôle est RS
        if ($request->role_agent === 'RS') {
            $rules['structure_selectionnee_id'] = 'required|exists:structures,id|unique:structures,responsable_id';
        }

        $validated = $request->validate($rules, [
            'structure_selectionnee_id.required' => 'Une structure doit être sélectionnée pour un responsable.',
            'structure_selectionnee_id.unique'   => 'Cette structure a déjà un responsable.',
        ]);

        $user = User::create([
            'nom'               => $validated['nom'],
            'prenom'            => $validated['prenom'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'telephone'         => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe'              => $validated['sexe'],
            'role'              => 'agent',
        ]);

        $agent = Agent::create([
            'user_id'       => $user->id,
            'matricule'     => $validated['matricule'],
            'fonction'      => $validated['fonction'],
            'role_agent'    => $validated['role_agent'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        // Mise à jour structure uniquement si RS
        if ($validated['role_agent'] === 'RS' && isset($validated['structure_selectionnee_id'])) {
            Structure::where('id', $validated['structure_selectionnee_id'])
                ->update(['responsable_id' => $agent->id]);
        }

        return redirect()->back()->with('success', 'Agent créé avec succès.');
    }

    public function update(Request $request, Agent $agent)
    {
        $rules = [
            'nom'               => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $agent->user_id,
            'telephone'         => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe'              => 'required|in:Homme,Femme',
            'matricule'         => 'required|string|unique:agents,matricule,' . $agent->id,
            'fonction'          => 'required|string',
            'role_agent'        => 'required|string|in:DPAF,MS,RS',
            'date_embauche'     => 'nullable|date',
            'password'          => 'nullable|string|min:8',
        ];

        if ($request->role_agent === 'RS') {
            $rules['structure_selectionnee_id'] = 'required|exists:structures,id|unique:structures,responsable_id,' . $agent->id;
        }

        $validated = $request->validate($rules, [
            'structure_selectionnee_id.required' => 'Une structure doit être sélectionnée pour un responsable.',
            'structure_selectionnee_id.unique'   => 'Cette structure a déjà un responsable.',
        ]);

        $userData = [
            'nom'               => $validated['nom'],
            'prenom'            => $validated['prenom'],
            'email'             => $validated['email'],
            'telephone'         => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe'              => $validated['sexe'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $agent->user->update($userData);

        $ancienRole = $agent->role_agent;

        $agent->update([
            'matricule'     => $validated['matricule'],
            'fonction'      => $validated['fonction'],
            'role_agent'    => $validated['role_agent'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        // Cas 1 : le nouvel agent est RS
        if ($validated['role_agent'] === 'RS' && isset($validated['structure_selectionnee_id'])) {
            Structure::where('id', $validated['structure_selectionnee_id'])
                ->update(['responsable_id' => $agent->id]);
        }

        // Cas 2 : il n'est plus RS, on le supprime de sa structure
        if ($ancienRole === 'RS' && $validated['role_agent'] !== 'RS') {
            Structure::where('responsable_id', $agent->id)->update(['responsable_id' => null]);
        }

        return redirect()->back()->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy(Agent $agent)
    {
        // Supprimer aussi son lien s’il est RS
        Structure::where('responsable_id', $agent->id)->update(['responsable_id' => null]);
        $agent->user->delete();

        return redirect()->back()->with('success', 'Agent supprimé avec succès.');
    }
}
