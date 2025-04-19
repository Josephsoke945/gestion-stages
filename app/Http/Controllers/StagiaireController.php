<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class StagiaireController extends Controller
{
    public function index()
    { 
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé. Vous devez être administrateur pour accéder à cette page.');
        }
        // Récupérer tous les stagiaires avec la relation user
        // et filtrer ceux dont le rôle est "stagiaire"
        $stagiaires = Stagiaire::with('user')
            ->whereHas('user', function ($query) {
                $query->where('role', 'stagiaire');
            })
            ->get();
        
        return Inertia::render('Stagiaires/Index', [
            'stagiaires' => $stagiaires
        ]);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string',
            'niveau_etude' => 'required|string',
            'universite' => 'required|string',
            'filiere' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'date_de_naissance' => $data['date_de_naissance'],
            'sexe' => $data['sexe'],
            'password' => bcrypt($data['password']),
            'role' => 'stagiaire',
        ]);

        Stagiaire::create([
            'user_id' => $user->id,
            'niveau_etude' => $data['niveau_etude'],
            'universite' => $data['universite'],
            'filiere' => $data['filiere'],
        ]);

        return redirect()->route('stagiaires.index')->with('success', "Le stagiaire {$data['prenom']} {$data['nom']} a été ajouté avec succès !");
    }

    public function update(Request $request, Stagiaire $stagiaire)
    {
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $stagiaire->user_id,
            'telephone' => 'required|integer',
            'date_de_naissance' => 'required|date',
            'sexe' => 'required|string',
            'niveau_etude' => 'required|string',
            'universite' => 'required|string',
            'filiere' => 'required|string',
        ];

        // Ajouter la validation du mot de passe seulement s'il est fourni
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $data = $request->validate($rules);

        // Mettre à jour les données de l'utilisateur
        $userData = [
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'date_de_naissance' => $data['date_de_naissance'],
            'sexe' => $data['sexe'],
        ];

        // Ajouter le mot de passe s'il est fourni
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($data['password']);
        }

        $stagiaire->user->update($userData);

        $stagiaire->update([
            'niveau_etude' => $data['niveau_etude'],
            'universite' => $data['universite'],
            'filiere' => $data['filiere'],
        ]);

        return redirect()->route('stagiaires.index')->with('success', "Les informations de {$data['prenom']} {$data['nom']} ont été mises à jour avec succès !");
    }

    public function destroy(Stagiaire $stagiaire)
    {
        $nom = $stagiaire->user->nom;
        $prenom = $stagiaire->user->prenom;
        
        $stagiaire->user()->delete();
        $stagiaire->delete();
        
        return redirect()->route('stagiaires.index')->with('success', "Le stagiaire {$prenom} {$nom} a été supprimé avec succès !");
    }
}