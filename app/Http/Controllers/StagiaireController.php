<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
    
    // Les méthodes store(), update() et destroy() ont été supprimées
    // car l'administrateur ne peut plus modifier les stagiaires
}