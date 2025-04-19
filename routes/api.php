<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailController;

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
Route::middleware('auth:sanctum')->group(function () {
    // Envoi d'email de confirmation pour une demande de stage
    Route::post('/emails/demande-confirmation', [EmailController::class, 'sendDemandeConfirmation']);
}); 