<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class RapportController extends Controller
{
    /**
     * Télécharger le rapport mensuel des activités.
     */
    public function telecharger(Request $request)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Récupérer les activités du mois courant
        $nbStagiaires = DB::table('stagiaires')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        $nbStructures = DB::table('structures')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        $nbDemandes = DB::table('demande_stages')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $content = "Rapport mensuel des activités\n";
        $content .= "Période : " . $startOfMonth->format('Y-m-d') . " au " . $endOfMonth->format('Y-m-d') . "\n";
        $content .= "Généré le : " . $now->format('Y-m-d H:i') . "\n\n";

        if ($nbStagiaires == 0 && $nbStructures == 0 && $nbDemandes == 0) {
            $content .= "Aucune activité enregistrée pour ce mois.";
        } else {
            $content .= "- Stagiaires inscrits : $nbStagiaires\n";
            $content .= "- Structures ajoutées : $nbStructures\n";
            $content .= "- Demandes de stage déposées : $nbDemandes\n";
        }

        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="rapport-mensuel-"' . $now->format('Y-m') . '.txt'
        ]);
    }
} 