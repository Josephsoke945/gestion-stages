<?php

// Affiche un titre pour la série de tests
echo "\n--- Tests des relations Eloquent ---\n\n";

// Test Stagiaire -> User
echo "-- Test Stagiaire -> User --\n";
$stagiaire = App\Models\Stagiaire::find(1);
if ($stagiaire) {
    if ($stagiaire->user) {
        echo "Stagiaire trouvé (ID: {$stagiaire->id}) lié à l'utilisateur : {$stagiaire->user->nom} (ID: {$stagiaire->user->id})\n";
    } else {
        echo "Stagiaire trouvé (ID: {$stagiaire->id}) n'est pas lié à un utilisateur.\n";
    }
} else {
    echo "Stagiaire avec ID 1 non trouvé.\n";
}

// Test Stagiaire -> Demandes de stages
echo "\n-- Test Stagiaire -> Demandes de stages --\n";
if ($stagiaire) {
    $demandesStages = $stagiaire->demandeStages;
    if ($demandesStages) {
        $nombreDemandes = $demandesStages->count();
        echo "Stagiaire trouvé (ID: {$stagiaire->id}) a {$nombreDemandes} demande(s) de stage associée(s).\n";
        foreach ($demandesStages as $demandeStage) {
            echo "  - Demande de stage ID: {$demandeStage->id}\n";
        }
    } else {
        echo "Stagiaire trouvé (ID: {$stagiaire->id}) n'a aucune demande de stage associée.\n";
    }
} else {
    echo "Stagiaire avec ID 1 non trouvé.\n";
}

// Test DemandeStage -> Stagiaire
echo "\n-- Test DemandeStage -> Stagiaire --\n";
$demandeStage = App\Models\DemandeStage::find(1);
if ($demandeStage) {
    if ($demandeStage->stagiaire) {
        echo "Demande de stage trouvée (ID: {$demandeStage->id}) liée au stagiaire (ID: {$demandeStage->stagiaire->id})\n";
    } else {
        echo "Demande de stage trouvée (ID: {$demandeStage->id}) n'est pas liée à un stagiaire.\n";
    }
} else {
    echo "Demande de stage avec ID 1 non trouvée.\n";
}

// Test DemandeStage -> Structure
echo "\n-- Test DemandeStage -> Structure --\n";
if ($demandeStage) {
    if ($demandeStage->structure) {
        echo "Demande de stage trouvée (ID: {$demandeStage->id}) liée à la structure : {$demandeStage->structure->nom} (ID: {$demandeStage->structure->id})\n";
    } else {
        echo "Demande de stage trouvée (ID: {$demandeStage->id}) n'est pas liée à une structure.\n";
    }
} else {
    echo "Demande de stage avec ID 1 non trouvée.\n";
}

// Test Structure -> Responsable (User)
echo "\n-- Test Structure -> Responsable (User) --\n";
$structure = App\Models\Structure::find(1);
if ($structure) {
    if ($structure->responsable) {
        echo "Structure trouvée (ID: {$structure->id}) a pour responsable l'utilisateur : {$structure->responsable->nom} (ID: {$structure->responsable->id})\n";
    } else {
        echo "Structure trouvée (ID: {$structure->id}), mais aucun responsable n'est assigné.\n";
    }
} else {
    echo "Structure avec ID 1 non trouvée.\n";
}

// Test User -> Stagiaire
echo "\n-- Test User -> Stagiaire --\n";
$user = App\Models\User::find(1);
if ($user) {
    if ($user->stagiaire) {
        echo "Utilisateur (ID: {$user->id}) lié au stagiaire (ID: {$user->stagiaire->id})\n";
    } else {
        echo "Utilisateur (ID: {$user->id}) n'est lié à aucun stagiaire.\n";
    }
} else {
    echo "Utilisateur avec ID 1 non trouvé.\n";
  }

