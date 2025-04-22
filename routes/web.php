<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\Api\EmailController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'stagiaire') {
        $structures = \App\Models\Structure::select('id', 'libelle')->get();
        
        // Récupérer tous les utilisateurs ayant le rôle stagiaire sauf l'utilisateur actuel
        $users = \App\Models\User::where('role', 'stagiaire')
                                ->where('id', '!=', $user->id)
                                ->select('id', 'nom', 'prenom', 'email', 'telephone')
                                ->get();

        return Inertia::render('Dashboard/Stagiaire', [
            'structures' => $structures,
            'users' => $users,
        ]);
    }

    // Tableau de bord par défaut pour les agents
    return app(DashboardController::class)->index();
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Routes de profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour les demandes de stage
    Route::post('/demande-stages', [DemandeController::class, 'store'])->name('demande_stages.store');
    Route::get('/mes-demandes', [DemandeController::class, 'index'])->name('mes.demandes');
    Route::get('/mes-demandes/{id}', [DemandeController::class, 'show'])->name('mes.demandes.show');
    Route::delete('/mes-demandes/{id}', [DemandeController::class, 'destroy'])->name('mes.demandes.annuler');
    
    // Routes pour les emails
    Route::post('/api/emails/demande-confirmation', [EmailController::class, 'sendDemandeConfirmation']);
    Route::get('/api/emails/check-config', [EmailController::class, 'checkEmailConfig']);
    
    // Routes pour les stagiaires
    Route::resource('stagiaires', StagiaireController::class);
    
    // Routes pour les structures
    Route::get('/structures', [StructureController::class, 'index'])->name('structures.index');
    Route::post('/structures', [StructureController::class, 'store'])->name('structures.store');
    Route::put('/structures/{structure}', [StructureController::class, 'update'])->name('structures.update');
    Route::delete('/structures/{structure}', [StructureController::class, 'destroy'])->name('structures.destroy');
    
    // Routes pour les agents
    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::put('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
    
    // Routes pour les utilisateurs
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
