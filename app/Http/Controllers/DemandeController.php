<?php
namespace App\Http\Controllers;

use App\Models\DemandeStage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemandeSoumise;

class DemandeController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'stagiaire_id' => 'required|exists:users,id',
            'structure_id' => 'required|exists:structures,id',
            'nature' => 'required|in:Individuel,Groupe',
            'lettre_cv_path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Vérification du fichier lettre et CV
        if ($request->hasFile('lettre_cv_path')) {
            $validated['lettre_cv_path'] = $request->file('lettre_cv_path')->store('lettres_cv', 'public');
        }

        // Générer un code de suivi unique
        $validated['code_suivi'] = strtoupper(Str::random(10));

        // Enregistrer la demande dans la base de données
        $demande = DemandeStage::create($validated);

        // Envoi de l'email avec le code de suivi
        Mail::to($request->user()->email)->send(new DemandeSoumise($demande));

        // Retourner la réponse avec succès et code de suivi
        return response()->json([
            'message' => 'Votre demande a été soumise avec succès.',
            'code_suivi' => $demande->code_suivi,
        ]);
    }

    public function index()
    {
        $stagiaireId = auth()->id(); // ID du stagiaire connecté

        // Récupérer les demandes du stagiaire connecté
        $demandes = DemandeStage::where('stagiaire_id', $stagiaireId)
            ->with('structure:id,libelle') // Inclure les informations sur la structure
            ->get();

        // Retourner une réponse Inertia
        return \Inertia\Inertia::render('Demandes/Index', [
            'demandes' => $demandes,
        ]);
    }

    public function destroy($id)
    {
        $demande = DemandeStage::where('id', $id)->where('stagiaire_id', auth()->id())->firstOrFail();

        // Supprimer uniquement les demandes en attente
        if ($demande->statut === null || $demande->statut === 'En attente') {
            $demande->delete();
            return back()->with('success', 'Votre demande a été annulée avec succès.');
        }

        return back()->with('error', 'Seules les demandes en attente peuvent être annulées.');
    }
}
