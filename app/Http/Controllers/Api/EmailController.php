<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DemandeConfirmationMarkdown;
use App\Models\DemandeStage;
use App\Models\MembreGroupe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Envoie un email de confirmation de demande de stage
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendDemandeConfirmation(Request $request)
    {
        $request->validate([
            'demande_id' => 'required|exists:demande_stages,id',
        ]);

        try {
            // Récupérer la demande avec ses relations
            $demande = DemandeStage::with(['stagiaire.user', 'structure', 'membres.user'])
                ->findOrFail($request->demande_id);
            
            // Récupérer l'utilisateur principal (stagiaire qui a fait la demande)
            $user = $demande->stagiaire->user;
            
            // Envoyer l'email au stagiaire principal
            Mail::to($user->email)
                ->send(new DemandeConfirmationMarkdown($demande, $user));
            
            Log::info("Email de confirmation envoyé à {$user->email} pour la demande #{$demande->code_suivi}");
            
            // Si la demande est de type Groupe, envoyer aussi aux autres membres
            if ($demande->nature === 'Groupe' && $demande->membres->count() > 0) {
                foreach ($demande->membres as $membre) {
                    // S'assurer que l'utilisateur existe
                    if ($membre->user) {
                        Mail::to($membre->user->email)
                            ->send(new DemandeConfirmationMarkdown($demande, $membre->user));
                        
                        Log::info("Email de confirmation envoyé au membre {$membre->user->email} pour la demande #{$demande->code_suivi}");
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Emails de confirmation envoyés avec succès'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des emails de confirmation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des emails de confirmation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
