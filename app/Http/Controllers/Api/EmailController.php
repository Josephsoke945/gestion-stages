<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DemandeConfirmationMail;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    /**
     * Envoie un email de confirmation pour une demande de stage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendDemandeConfirmation(Request $request)
    {
        $request->validate([
            'demande_id' => 'required|exists:demandes,id',
            'email' => 'required|email',
            'include_members' => 'boolean'
        ]);

        try {
            // Récupérer la demande
            $demande = Demande::findOrFail($request->demande_id);
            $user = User::where('email', $request->email)->first();

            // Générer un code de suivi s'il n'existe pas déjà
            if (!$demande->code_suivi) {
                $demande->code_suivi = strtoupper(Str::random(8));
                $demande->save();
            }

            // Envoyer l'email au demandeur principal
            Mail::to($request->email)
                ->send(new DemandeConfirmationMail(
                    $demande,
                    $user,
                    $demande->code_suivi,
                    route('stagiaire.mes-demandes')
                ));

            // Envoyer aux membres du groupe si demandé
            if ($request->include_members && !empty($demande->membres_groupe)) {
                $emails = collect(json_decode($demande->membres_groupe))
                    ->pluck('email')
                    ->filter(function($email) use ($request) {
                        return $email !== $request->email; // Exclure l'email principal qui a déjà reçu
                    })
                    ->toArray();

                if (count($emails) > 0) {
                    Mail::to($emails)
                        ->send(new DemandeConfirmationMail(
                            $demande,
                            null,
                            $demande->code_suivi,
                            route('stagiaire.mes-demandes')
                        ));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Email de confirmation envoyé avec succès',
                'code_suivi' => $demande->code_suivi
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email de confirmation',
                'error' => $e->getMessage()
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
