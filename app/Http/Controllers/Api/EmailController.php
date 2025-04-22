<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DemandeConfirmationMail;
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
        $validated = $request->validate([
            'demande_id' => 'required|exists:demande_stages,id',
            'email' => 'nullable|email',
            'send_to_membres' => 'nullable|boolean',
        ]);

        try {
            // Récupérer la demande avec ses relations
            $demande = DemandeStage::with(['stagiaire.user', 'structure', 'membres.user'])
                ->findOrFail($validated['demande_id']);
            
            // Si l'email est fourni, l'utiliser, sinon utiliser l'email du stagiaire
            $email = $validated['email'] ?? $demande->stagiaire->user->email;
            
            // Récupérer l'utilisateur principal ou créer un utilisateur temporaire si email personnalisé
            $user = null;
            if (isset($validated['email']) && $validated['email'] !== $demande->stagiaire->user->email) {
                // Utilisateur temporaire (pour les tests ou pour envoyer à une adresse différente)
                $user = new User([
                    'nom' => $demande->stagiaire->user->nom,
                    'prenom' => $demande->stagiaire->user->prenom,
                    'email' => $validated['email']
                ]);
            } else {
                $user = $demande->stagiaire->user;
            }
            
            // Envoyer l'email au stagiaire principal ou à l'adresse spécifiée
            Mail::to($email)
                ->send(new DemandeConfirmationMail($demande, $user));
            
            Log::info("Email de confirmation envoyé à {$email} pour la demande #{$demande->code_suivi}");
            
            $emailsSent = 1;
            $recipients = [$email];
            
            // Si demandé et si la demande est de type Groupe, envoyer aussi aux autres membres
            if (($validated['send_to_membres'] ?? false) && $demande->nature === 'Groupe' && $demande->membres->count() > 0) {
                foreach ($demande->membres as $membre) {
                    // S'assurer que l'utilisateur existe et qu'il n'est pas déjà le destinataire principal
                    if ($membre->user && $membre->user->email !== $email) {
                        Mail::to($membre->user->email)
                            ->send(new DemandeConfirmationMail($demande, $membre->user));
                        
                        Log::info("Email de confirmation envoyé au membre {$membre->user->email} pour la demande #{$demande->code_suivi}");
                        
                        $emailsSent++;
                        $recipients[] = $membre->user->email;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Emails de confirmation envoyés avec succès',
                'emails_sent' => $emailsSent,
                'recipients' => $recipients,
                'demande' => [
                    'id' => $demande->id,
                    'code_suivi' => $demande->code_suivi,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des emails de confirmation: ' . $e->getMessage());
            
            // Log de l'erreur complète pour le débogage
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des emails de confirmation: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
    
    /**
     * Vérifie l'état de la configuration d'email
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmailConfig()
    {
        $config = [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'queue_connection' => config('queue.default'),
        ];
        
        return response()->json([
            'success' => true,
            'config' => $config
        ]);
    }
}
