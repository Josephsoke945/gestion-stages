<?php
namespace App\Http\Controllers;

use App\Models\Structure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
class StructureController extends Controller
{
    public function index()
    {
        $structures = Structure::with('responsable:id,nom')->get(); // Assurez-vous que la colonne s'appelle 'name' ou remplacez-la par la bonne colonne
        $users = User::select('id', 'nom')->get(); // Récupère les utilisateurs avec leurs noms

        return Inertia::render('Structures/Index', [
            'structures' => $structures,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sigle' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            
        ]);

        Structure::create($validated);

        return redirect()->route('structures.index')->with('success', 'Structure ajoutée avec succès.');
    }

    public function update(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'sigle' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'responsable_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $structure->update($validated);

        return redirect()->route('structures.index')->with('success', 'Structure mise à jour avec succès.');
    }

    public function destroy(Structure $structure)
    {
        $structure->delete();

        return redirect()->route('structures.index')->with('success', 'Structure supprimée avec succès.');
    }
}
