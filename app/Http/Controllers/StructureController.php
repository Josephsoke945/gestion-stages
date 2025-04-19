<?php
namespace App\Http\Controllers;

use App\Models\Structure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class StructureController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }
        $structures = Structure::select('id', 'sigle', 'libelle', 'description')->get();

        return Inertia::render('Structures/Index', [
            'structures' => $structures,
        ]);
    }

    public function store(Request $request)
    {
        // Validation des données avec contrôle des doublons sur sigle et libellé
        $validated = $request->validate([
            'sigle' => 'required|string|max:255|unique:structures,sigle',
            'libelle' => 'required|string|max:255|unique:structures,libelle',
            'description' => 'nullable|string',
        ]);

        // Créer la nouvelle structure
        Structure::create($validated);

        return redirect()->route('structures.index')->with('success', 'Structure ajoutée avec succès.');
    }

    public function update(Request $request, Structure $structure)
    {
        // Validation des données avec contrôle des doublons, excepté pour la structure en cours de modification
        $validated = $request->validate([
            'sigle' => 'required|string|max:255|unique:structures,sigle,' . $structure->id,
            'libelle' => 'required|string|max:255|unique:structures,libelle,' . $structure->id,
            'description' => 'nullable|string',
        ]);

        // Mise à jour de la structure
        $structure->update($validated);

        return redirect()->route('structures.index')->with('success', 'Structure mise à jour avec succès.');
    }

    public function destroy(Structure $structure)
    {
        $structure->delete();

        return redirect()->route('structures.index')->with('success', 'Structure supprimée avec succès.');
    }
}
