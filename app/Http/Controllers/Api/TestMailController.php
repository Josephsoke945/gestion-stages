<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DemandeConfirmationMarkdown;
use App\Models\DemandeStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    /**
     * Endpoint de test pour envoyer un email
     */
    public function testMail(Request $request)
    {
        try {
            // Récupérer un utilisateur et une demande pour le test
            // Remplacer par des valeurs réelles si nécessaire
            $demande = DemandeStage::first();
            
            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune demande trouvée dans la base de données.'
                ], 404);
            }
            
            // Charger la relation structure pour l'email
            $demande->load('structure');
            
            // Si l'adresse email est fournie, l'utiliser, sinon utiliser l'adresse de l'utilisateur
            $email = $request->input('email', null);
            $user = User::find($demande->stagiaire->user_id);
            
            if ($email) {
                $user = new User([
                    'nom' => 'Test',
                    'prenom' => 'Utilisateur',
                    'email' => $email
                ]);
            }
            
            // Envoyer l'email de test
            Mail::to($user->email)->send(new DemandeConfirmationMarkdown($demande, $user));
            
            // Journaliser l'envoi
            Log::info('Email de test envoyé à ' . $user->email);
            
            return response()->json([
                'success' => true,
                'message' => 'L\'email de test a été envoyé à ' . $user->email,
                'demande' => $demande->code_suivi
            ]);
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'envoi de l\'email de test: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
} 