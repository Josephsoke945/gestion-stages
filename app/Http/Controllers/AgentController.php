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
        $agents = Agent::with('user:id,nom')->get(); // Charge les agents avec leurs utilisateurs
        $users = User::select('id', 'nom')->get(); // Récupère les utilisateurs disponibles

        return Inertia::render('Agents/Index', [
            'agents' => $agents,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'matricule' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);

        Agent::create($validated);

        return redirect()->route('agents.index')->with('success', 'Agent ajouté avec succès.');
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'matricule' => 'required|string|max:255',
            'fonction' => 'required|string|max:255',
            'date_embauche' => 'required|date',
        ]);

        $agent->update($validated);

        return redirect()->route('agents.index')->with('success', 'Agent mis à jour avec succès.');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();

        return redirect()->route('agents.index')->with('success', 'Agent supprimé avec succès.');
    }
}
