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
            $rules['structure_id'] = [
                'required',
                'exists:structures,id',
                function ($attribute, $value, $fail) {
                    $structure = \App\Models\Structure::find($value);
                    if ($structure && $structure->responsable_id !== null) {
                        $fail('Cette structure a déjà un responsable.');
                    }
                }
            ];
        }

        $validated = $request->validate($rules);

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
        if ($validated['role_agent'] === 'RS' && isset($validated['structure_id'])) {
            Structure::where('id', $validated['structure_id'])
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

        // Validation spécifique pour le rôle RS
        if ($request->role_agent === 'RS') {
            $rules['structure_id'] = [
                'required',
                'exists:structures,id',
                function ($attribute, $value, $fail) use ($agent) {
                    $structure = \App\Models\Structure::find($value);
                    if ($structure && $structure->responsable_id !== null && $structure->responsable_id !== $agent->id) {
                        $fail('Cette structure a déjà un responsable.');
                    }
                }
            ];
        }

        $validated = $request->validate($rules);

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
        $ancienneStructure = Structure::where('responsable_id', $agent->id)->first();

        $agent->update([
            'matricule'     => $validated['matricule'],
            'fonction'      => $validated['fonction'],
            'role_agent'    => $validated['role_agent'],
            'date_embauche' => $validated['date_embauche'],
        ]);

        // Gestion de la structure
        if ($validated['role_agent'] === 'RS') {
            // Si l'agent était déjà responsable d'une autre structure, on retire son rôle
            if ($ancienneStructure && $ancienneStructure->id != $validated['structure_id']) {
                $ancienneStructure->update(['responsable_id' => null]);
            }
            
            // Mise à jour de la nouvelle structure
            Structure::where('id', $validated['structure_id'])
                ->update(['responsable_id' => $agent->id]);
        } else if ($ancienRole === 'RS') {
            // Si l'agent n'est plus RS, on retire son rôle de responsable
            Structure::where('responsable_id', $agent->id)
                ->update(['responsable_id' => null]);
        }

        return redirect()->back()->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy(Agent $agent)
    {
        // Supprimer le lien avec la structure s'il est RS
        Structure::where('responsable_id', $agent->id)->update(['responsable_id' => null]);
        
        // Supprimer l'utilisateur (ce qui supprimera aussi l'agent grâce à la cascade)
        $agent->user->delete();

        return redirect()->back()->with('success', 'Agent supprimé avec succès.');
    }
}