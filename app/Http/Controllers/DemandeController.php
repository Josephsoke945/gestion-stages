<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemandeController extends Controller
{
    public function store(Request $request)
    {
        // Logique pour enregistrer la demande
        return back()->with('success', 'Votre demande a été soumise avec succès.');
    }
}

