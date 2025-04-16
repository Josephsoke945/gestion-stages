<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StagiaireController extends Controller
{
    public function index()
    {
        // Modification de la requête pour filtrer par rôle
        $stagiaires = Stagiaire::whereHas('user', function($query) {
            $query->where('role', 'stagiaire');
        })->with('user')->get();
    
        return Inertia::render('Stagiaires/Index', [
            'stagiaires' => $stagiaires,
        ]);
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string|in:Homme,Femme',
            'niveau_etude' => 'required|string|in:Licence 1,Licence 2,Licence 3,Master 1,Master 2,Autre',
            'universite' => 'required|string|max:255',
            'filiere' => 'required|string|max:255',
        ]);

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
            'password' => bcrypt('password'),
            'role' => 'stagiaire',
        ]);

        Stagiaire::create([
            'user_id' => $user->id,
            'niveau_etude' => $validated['niveau_etude'],
            'universite' => $validated['universite'],
            'filiere' => $validated['filiere'],
        ]);

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire ajouté avec succès.');
    }

    public function update(Request $request, Stagiaire $stagiaire)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $stagiaire->user_id,
            'telephone' => 'required|string|max:20',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string|in:Homme,Femme',
            'niveau_etude' => 'required|string|in:Licence 1,Licence 2,Licence 3,Master 1,Master 2,Autre',
            'universite' => 'required|string|max:255',
            'filiere' => 'required|string|max:255',
        ]);

        $stagiaire->user->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'sexe' => $validated['sexe'],
        ]);

        $stagiaire->update([
            'niveau_etude' => $validated['niveau_etude'],
            'universite' => $validated['universite'],
            'filiere' => $validated['filiere'],
        ]);

        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire mis à jour avec succès.');
    }

    public function destroy(Stagiaire $stagiaire)
    {
        $stagiaire->delete();
        return redirect()->route('stagiaires.index')->with('success', 'Stagiaire supprimé avec succès.');
    }
}