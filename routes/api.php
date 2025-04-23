<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\TestMailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes pour l'envoi d'emails
// Désactivation temporaire de l'authentification pour faciliter les tests
// Route::middleware('auth:sanctum')->group(function () {
    // Envoi d'email de confirmation pour une demande de stage
    Route::post('/emails/demande-confirmation', [EmailController::class, 'sendDemandeConfirmation']);
    Route::get('/emails/check-config', [EmailController::class, 'checkEmailConfig']);
// });

// Route pour le test d'envoi d'email
Route::get('/test-email', function() {
    $demande = \App\Models\DemandeStage::first();
    $user = \App\Models\User::first();
    
    if (!$demande || !$user) {
        return response()->json([
            'success' => false,
            'message' => 'Impossible de trouver une demande ou un utilisateur pour le test'
        ], 404);
    }
    
    try {
        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\DemandeConfirmationMail($demande, $user));
        
        return response()->json([
            'success' => true,
            'message' => 'Email de test envoyé à ' . $user->email
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
        ], 500);
    }
});

// Route pour diagnostiquer les problèmes de file d'attente
Route::get('/diagnose-queue', function() {
    // Vérifier la configuration de base
    $queueConnection = config('queue.default');
    $queueDriver = config("queue.connections.{$queueConnection}.driver");
    
    // Vérifier si le dossier de stockage est accessible en écriture (pour le driver 'file')
    $storageWritable = is_writable(storage_path());
    
    return response()->json([
        'success' => true,
        'queue_diagnostics' => [
            'default_connection' => $queueConnection,
            'driver' => $queueDriver,
            'storage_writable' => $storageWritable,
            'mail_config' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from' => config('mail.from.address'),
                'timeout' => config('mail.mailers.smtp.timeout') ?? '30 (default)',
            ],
            'php_config' => [
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
            ],
        ]
    ]);
}); 