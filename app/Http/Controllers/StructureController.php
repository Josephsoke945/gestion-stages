<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StructureController extends Controller
{
    /**
     * Affiche la liste des structures.
     */
    public function index()
    {
        $structures = Structure::with('responsable')->get();

        return Inertia::render('Admin/Structures/Index', [
            'structures' => $structures,
        ]);
    }

    /**
     * Affiche le formulaire de création d'une structure.
     */
    public function create()
    {
        return Inertia::render('Admin/Structures/Create');
    }

    /**
     * Enregistre une nouvelle structure.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sigle' => 'required|string|max:255|unique:structures',
            'libelle' => 'required|string|max:255|unique:structures',
            'description' => 'nullable|string',
        ]);

        Structure::create($validated);

        return redirect()->route('admin.structures.index')->with('success', 'Structure créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition pour une structure.
     */
    public function edit(Structure $structure)
    {
        return Inertia::render('Admin/Structures/Edit', [
            'structure' => $structure,
        ]);
    }

    /**
     * Met à jour une structure existante.
     */
    public function update(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'sigle' => 'required|string|max:255|unique:structures,sigle,' . $structure->id,
            'libelle' => 'required|string|max:255|unique:structures,libelle,' . $structure->id,
            'description' => 'nullable|string',
        ]);

        $structure->update($validated);

        return redirect()->route('admin.structures.index')->with('success', 'Structure mise à jour avec succès.');
    }

    /**
     * Supprime une structure.
     */
    public function destroy(Structure $structure)
    {
        $structure->delete();

        return redirect()->route('admin.structures.index')->with('success', 'Structure supprimée avec succès.');
    }

    /**
     * Retourne les structures sans responsable (utilisé pour sélection dynamique).
     */
    public function available()
    {
        $structures = Structure::whereNull('responsable_id')->get(['id', 'libelle']);

        return response()->json($structures);
    }
}
