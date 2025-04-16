<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StagiaireController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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
    $user = auth()->user();

    if ($user->role === 'stagiaire') {
        $structures = \App\Models\Structure::select('id', 'libelle')->get();

        return Inertia::render('Dashboard/Stagiaire', [
            'structures' => $structures,
        ]);
    }

    // Tableau de bord par défaut pour les agents
    return app(DashboardController::class)->index();
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/structures', [StructureController::class, 'index'])->name('structures.index');
Route::post('/structures', [StructureController::class, 'store'])->name('structures.store');
Route::put('/structures/{structure}', [StructureController::class, 'update'])->name('structures.update');
Route::delete('/structures/{structure}', [StructureController::class, 'destroy'])->name('structures.destroy');

Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
Route::put('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');

//Route::post('/demande', [DemandeController::class, 'store'])->name('demande.store');
Route::post('/demande-stages', [DemandeController::class, 'store'])->name('demande_stages.store');
Route::get('/mes-demandes', [DemandeController::class, 'index'])->name('mes.demandes');
Route::delete('/mes-demandes/{id}', [DemandeController::class, 'destroy'])->name('mes.demandes.annuler');

Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index'); // Afficher la liste des utilisateurs
    Route::post('/users', [UserController::class, 'store'])->name('users.store'); // Ajouter un utilisateur
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); // Modifier un utilisateur
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy'); // Supprimer un utilisateur
});

Route::resource('stagiaires', StagiaireController::class);
Route::get('/stagiaires/{stagiaire}/demandes', [StagiaireController::class, 'demandes'])->name('stagiaires.demandes');
Route::get('/stagiaires/{stagiaire}/demandes/{demande}', [StagiaireController::class, 'showDemande'])->name('stagiaires.demandes.show');
Route::get('/stagiaires/{stagiaire}/demandes/{demande}/edit', [StagiaireController::class, 'editDemande'])->name('stagiaires.demandes.edit');
Route::put('/stagiaires/{stagiaire}/demandes/{demande}', [StagiaireController::class, 'updateDemande'])->name('stagiaires.demandes.update');
Route::get('/stagiaires/{stagiaire}/demandes/{demande}/delete', [StagiaireController::class, 'deleteDemande'])->name('stagiaires.demandes.delete');
Route::get('/stagiaires/{stagiaire}/demandes/{demande}/show', [StagiaireController::class, 'showDemande'])->name('stagiaires.demandes.show');

require __DIR__.'/auth.php';
