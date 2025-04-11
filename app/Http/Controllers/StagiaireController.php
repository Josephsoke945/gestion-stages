<?php
namespace App\Http\Controllers;

use App\Models\Structure;
use Inertia\Inertia;

class StagiaireController
{
    public function dashboard()
    {
        $structures = Structure::select('id', 'libelle')->get(); // Assurez-vous que 'nom' est inclus
        return Inertia::render('Dashboard/Stagiaire', [
            'structures' => $structures,
        ]);
    }
}