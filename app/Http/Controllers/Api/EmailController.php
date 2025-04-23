<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DemandeConfirmationMarkdown;
use App\Models\DemandeStage;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    /**
     * Send a confirmation email for a demande.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendDemandeConfirmation(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'demande_id' => 'required|exists:demande_stages,id',
            'email' => 'required|email',
            'send_to_members' => 'boolean',
        ]);

        try {
            // Get the demande
            $demande = DemandeStage::findOrFail($validated['demande_id']);
            
            // Load structure relation for the email
            $demande->load('structure');
            
            // Get the user from email
            $user = User::where('email', $validated['email'])->firstOrFail();
            
            // Send email to the main recipient
            Mail::to($validated['email'])->send(new DemandeConfirmationMarkdown($demande, $user));
            
            // If send_to_members is true, send emails to group members
            if ($request->input('send_to_members', false)) {
                $stagiaire = Stagiaire::where('user_id', $demande->stagiaire->user_id)->first();
                
                if ($stagiaire && $stagiaire->group_members) {
                    $members = json_decode($stagiaire->group_members, true);
                    
                    foreach ($members as $member) {
                        if (isset($member['email']) && filter_var($member['email'], FILTER_VALIDATE_EMAIL)) {
                            // Don't send again to the main recipient
                            if ($member['email'] !== $validated['email']) {
                                // Trouver l'utilisateur correspondant à l'email du membre
                                $memberUser = User::where('email', $member['email'])->first();
                                
                                // Si l'utilisateur n'existe pas, utiliser une instance temporaire
                                if (!$memberUser) {
                                    $memberUser = new User();
                                    $memberUser->nom = $member['nom'] ?? 'Utilisateur';
                                    $memberUser->prenom = $member['prenom'] ?? '';
                                    $memberUser->email = $member['email'];
                                }
                                
                                Mail::to($member['email'])->send(new DemandeConfirmationMarkdown($demande, $memberUser));
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Email de confirmation envoyé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
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
