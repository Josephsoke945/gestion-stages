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

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirection du dashboard principal vers le dashboard approprié
Route::get('/dashboard', function () {
    $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->agent && $user->agent->role_agent === 'DPAF') {
            return redirect()->route('agent.dashboard');
        }

        if ($user->role === 'stagiaire') {
            return redirect()->route('stagiaire.dashboard');
    }

        // Redirection par défaut
        return redirect()->route('login');
    })->name('dashboard');

    // Routes de profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function() {
        // Dashboard admin avec statistiques
        Route::get('/dashboard', function () {
            $stats = [
                'users' => \App\Models\User::count(),
                'structures' => \App\Models\Structure::count(),
                'stagiaires' => \App\Models\Stagiaire::count(),
                'agents' => \App\Models\Agent::count(),
            ];
            
            return Inertia::render('Dashboard/Admin', [
                'stats' => $stats
            ]);
        })->name('dashboard');

        // Autres routes admin...
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/structures', [StructureController::class, 'index'])->name('structures.index');
        Route::post('/structures', [StructureController::class, 'store'])->name('structures.store');
        Route::put('/structures/{structure}', [StructureController::class, 'update'])->name('structures.update');
        Route::delete('/structures/{structure}', [StructureController::class, 'destroy'])->name('structures.destroy');

        Route::get('/stagiaires', [StagiaireController::class, 'index'])->name('stagiaires.index');
        
        // Routes pour les agents
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
        Route::put('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
        Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
    });
    
    // Routes pour les demandes de stage
    Route::post('/demande-stages', [DemandeController::class, 'store'])->name('demande_stages.store');
    Route::get('/mes-demandes', [DemandeController::class, 'index'])->name('mes.demandes');
    Route::get('/mes-demandes/{id}', [DemandeController::class, 'show'])->name('mes.demandes.show');
    Route::delete('/mes-demandes/{id}', [DemandeController::class, 'destroy'])->name('mes.demandes.annuler');
    Route::post('/demandes/recherche', [DemandeController::class, 'findByCode'])->name('demandes.search');
    Route::get('/demandes/recherche', [DemandeController::class, 'findByCode'])->name('demandes.search.get');
    Route::get('/recherche-code', function () {
        return Inertia::render('Stagiaire/RechercheCode');
    })->name('recherche.code');
    
    // Routes pour les emails
    Route::post('/api/emails/demande-confirmation', [EmailController::class, 'sendDemandeConfirmation']);
    Route::get('/api/emails/check-config', [EmailController::class, 'checkEmailConfig']);
    
    // Routes pour les stagiaires
    Route::resource('stagiaires', StagiaireController::class);
    
    // Routes pour les agents DPAF
    Route::prefix('agent')->name('agent.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/demandes', [App\Http\Controllers\Agent\DemandeController::class, 'index'])->name('demandes');
        Route::get('/demandes/{demande}', [App\Http\Controllers\Agent\DemandeController::class, 'show'])->name('demandes.show');
        Route::post('/demandes/{demande}/approve', [App\Http\Controllers\Agent\DemandeController::class, 'approve'])->name('demandes.approve');
        Route::post('/demandes/{demande}/reject', [App\Http\Controllers\Agent\DemandeController::class, 'reject'])->name('demandes.reject');
        Route::get('/structures', [App\Http\Controllers\Agent\StructureController::class, 'index'])->name('structures');
        Route::get('/structures/{structure}', [App\Http\Controllers\Agent\StructureController::class, 'show'])->name('structures.show');
        Route::get('/stagiaires', [App\Http\Controllers\Agent\StagiaireController::class, 'index'])->name('stagiaires.index');
        Route::get('/stagiaires/{stagiaire}', [App\Http\Controllers\Agent\StagiaireController::class, 'show'])->name('stagiaires.show');
    });
    
    // Routes pour les stagiaires
    Route::prefix('stagiaire')->name('stagiaire.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Stagiaire\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/demandes', [App\Http\Controllers\Stagiaire\DemandeController::class, 'index'])->name('demandes');
        Route::get('/demandes/{demande}', [App\Http\Controllers\Stagiaire\DemandeController::class, 'show'])->name('demandes.show');
    });
});

require __DIR__.'/auth.php';
