<?php

use App\Models\Stagiaire;
use App\Models\DemandeStage;
use App\Models\Structure;
use App\Models\NatureDemande;
use App\Models\User;
use App\Models\ThemeStage;
use App\Models\Stage;
use App\Models\Universite;
use App\Models\Agent;
use App\Models\AffectationMaitreStage;
use App\Models\AffectationResponsableStructure;
use App\Models\Notification;
use App\Models\DemandeAttestation;

echo "--- Tests des relations Eloquent ---\n\n";

// Test de la relation Stagiaire -> User
echo "-- Test Stagiaire -> User --\n";
$stagiaireUser = Stagiaire::first();
if ($stagiaireUser && $stagiaireUser->user) {
    echo "Stagiaire trouvé (ID: " . $stagiaireUser->id . ") lié à l'utilisateur : " . $stagiaireUser->user->nom . " " . $stagiaireUser->user->prenom . "\n";
} else {
    echo "Aucun stagiaire trouvé ou aucun utilisateur lié.\n";
}
echo "\n";

// Test de la relation Stagiaire -> Demandes de stages
echo "-- Test Stagiaire -> Demandes de stages --\n";
$stagiaireDemandes = Stagiaire::first();
if ($stagiaireDemandes && $stagiaireDemandes->demandesStages->isNotEmpty()) {
    echo "Stagiaire trouvé (ID: " . $stagiaireDemandes->id . ") a " . $stagiaireDemandes->demandesStages->count() . " demande(s) de stage associée(s).\n";
} else {
    echo "Aucun stagiaire trouvé ou aucune demande de stage associée.\n";
}
echo "\n";

// Test de la relation DemandeStage -> Stagiaire
echo "-- Test DemandeStage -> Stagiaire --\n";
$demandeStagiaire = DemandeStage::first();
if ($demandeStagiaire && $demandeStagiaire->stagiaire) {
    echo "Demande de stage trouvée (ID: " . $demandeStagiaire->id . ") liée au stagiaire (ID: " . $demandeStagiaire->stagiaire->id . ").\n";
} else {
    echo "Aucune demande de stage trouvée ou aucun stagiaire lié.\n";
}
echo "\n";

// Test de la relation DemandeStage -> Structure
echo "-- Test DemandeStage -> Structure --\n";
$demandeStructure = DemandeStage::first();
if ($demandeStructure && $demandeStructure->structure) {
    echo "Demande de stage trouvée (ID: " . $demandeStructure->id . ") liée à la structure : " . $demandeStructure->structure->nom . " (ID: " . $demandeStructure->structure->id . ").\n";
} else {
    echo "Aucune demande de stage trouvée ou aucune structure liée.\n";
}
echo "\n";

// Test de la relation DemandeStage -> NatureDemande
echo "-- Test DemandeStage -> NatureDemande --\n";
$demandeNature = DemandeStage::first();
if ($demandeNature && $demandeNature->natureDemande) {
    echo "Demande de stage trouvée (ID: " . $demandeNature->id . ") de nature : " . $demandeNature->natureDemande->libelle . " (ID: " . $demandeNature->natureDemande->id . ").\n";
} else {
    echo "Aucune demande de stage trouvée ou aucune nature de demande liée.\n";
}
echo "\n";

// Test de la relation Structure -> Responsable (User)
echo "-- Test Structure -> Responsable (User) --\n";
$structureResponsable = Structure::first();
if ($structureResponsable && $structureResponsable->responsable) {
    echo "Structure trouvée (ID: " . $structureResponsable->id . ") a pour responsable l'utilisateur : " . $structureResponsable->responsable->nom . " " . $structureResponsable->responsable->prenom . " (ID: " . $structureResponsable->responsable->id . ").\n";
} else {
    echo "Aucune structure trouvée ou aucun responsable lié.\n";
}
echo "\n";

// Test de la relation User -> Stagiaire (One-to-One inverse)
echo "-- Test User -> Stagiaire --\n";
$userStagiaire = User::where('role', 'stagiaire')->first();
if ($userStagiaire && $userStagiaire->stagiaire) {
    echo "Utilisateur (ID: " . $userStagiaire->id . ") lié au stagiaire (ID: " . $userStagiaire->stagiaire->id . ").\n";
} else {
    echo "Aucun utilisateur stagiaire trouvé ou aucun stagiaire lié.\n";
}
echo "\n";

// Test de la relation NatureDemande -> DemandeStages (HasMany)
echo "-- Test NatureDemande -> DemandeStages --\n";
$natureDemandeStages = NatureDemande::first();
if ($natureDemandeStages && $natureDemandeStages->demandeStages->isNotEmpty()) {
    echo "Nature de demande (ID: " . $natureDemandeStages->id . ") a " . $natureDemandeStages->demandeStages->count() . " demande(s) de stage associée(s).\n";
} else {
    echo "Aucune nature de demande trouvée ou aucune demande de stage associée.\n";
}
echo "\n";

// Test de la relation ThemeStage -> Stages (HasMany)
echo "-- Test ThemeStage -> Stages --\n";
$themeStageStages = ThemeStage::first();
if ($themeStageStages && $themeStageStages->stages->isNotEmpty()) {
    echo "Thème de stage (ID: " . $themeStageStages->id . ") a " . $themeStageStages->stages->count() . " stage(s) associé(s).\n";
} else {
    echo "Aucun thème de stage trouvé ou aucun stage associé.\n";
}
echo "\n";

// Test de la relation Stage -> DemandeStage (BelongsTo)
echo "-- Test Stage -> DemandeStage --\n";
$stageDemande = Stage::first();
if ($stageDemande && $stageDemande->demandeStage) {
    echo "Stage trouvé (ID: " . $stageDemande->id . ") lié à la demande de stage (ID: " . $stageDemande->demandeStage->id . ").\n";
} else {
    echo "Aucun stage trouvé ou aucune demande de stage liée.\n";
}
echo "\n";

// Test de la relation Stage -> Structure (BelongsTo)
echo "-- Test Stage -> Structure --\n";
$stageStructure = Stage::first();
if ($stageStructure && $stageStructure->structure) {
    echo "Stage trouvé (ID: " . $stageStructure->id . ") lié à la structure (ID: " . $stageStructure->structure->id . ").\n";
} else {
    echo "Aucun stage trouvé ou aucune structure liée.\n";
}
echo "\n";

// Test de la relation Stage -> ThemeStage (BelongsTo)
echo "-- Test Stage -> ThemeStage --\n";
$stageTheme = Stage::first();
if ($stageTheme && $stageTheme->themeStage) {
    echo "Stage trouvé (ID: " . $stageTheme->id . ") lié au thème de stage (ID: " . $stageTheme->themeStage->id . ").\n";
} else {
    echo "Aucun stage trouvé ou aucun thème de stage lié.\n";
}
echo "\n";

echo "\n--- Tests des relations avec Universite ---\n\n";

// Test de la relation Universite -> Stagiaires (HasMany)
echo "-- Test Universite -> Stagiaires --\n";
$universiteA = Universite::find(1);
if ($universiteA && $universiteA->stagiaires->isNotEmpty()) {
    echo "Université A (ID: " . $universiteA->id . ") a " . $universiteA->stagiaires->count() . " stagiaire(s) associé(s).\n";
} else {
    echo "Université A (ID: 1) n'a aucun stagiaire associé.\n";
}
echo "\n";

// Test de la relation Stagiaire -> Universite (BelongsTo)
echo "-- Test Stagiaire -> Universite --\n";
$stagiaireUni = Stagiaire::where('universite_id', 1)->first();
if ($stagiaireUni && $stagiaireUni->universite) {
    echo "Stagiaire (ID: " . $stagiaireUni->id . ") est lié à l'université : " . $stagiaireUni->universite->nom_universite . " (ID: " . $stagiaireUni->universite->id . ").\n";
} else {
    echo "Aucun stagiaire trouvé lié à l'Université A.\n";
}
echo "\n";

// Test de la relation Universite -> Responsable (Agent)
echo "-- Test Universite -> Responsable (Agent) --\n";
$universiteAResponsable = Universite::find(1);
if ($universiteAResponsable && $universiteAResponsable->responsable) {
    echo "Université A (ID: " . $universiteAResponsable->id . ") a pour responsable l'agent (ID: " . $universiteAResponsable->responsable->id . ").\n";
} else {
    echo "Université A (ID: 1) n'a aucun responsable (agent) associé.\n";
}
echo "\n";

// Test de la relation Agent -> Universite (BelongsTo - inverse)
echo "-- Test Agent -> Universite --\n";
$agentResponsableUni = Agent::where('universite_responsable_id', 1)->first();
if ($agentResponsableUni && $agentResponsableUni->universiteResponsable) {
    echo "Agent (ID: " . $agentResponsableUni->id . ") est responsable de l'université : " . $agentResponsableUni->universiteResponsable->nom_universite . " (ID: " . $agentResponsableUni->universiteResponsable->id . ").\n";
} else {
    echo "Aucun agent trouvé responsable de l'Université A.\n";
}
echo "\n";

echo "\n--- Tests des relations avec AffectationMaitreStage ---\n\n";

// Test de la relation AffectationMaitreStage -> Stage (BelongsTo)
echo "-- Test AffectationMaitreStage -> Stage --\n";
$affectationStage = AffectationMaitreStage::first();
if ($affectationStage && $affectationStage->stage) {
    echo "Affectation (ID: " . $affectationStage->id . ") liée au stage (ID: " . $affectationStage->stage->id . ").\n";
} else {
    echo "Aucune affectation trouvée ou aucun stage lié.\n";
}
echo "\n";

// Test de la relation AffectationMaitreStage -> MaitreStage (User) (BelongsTo)
echo "-- Test AffectationMaitreStage -> MaitreStage (User) --\n";
$affectationMaitre = AffectationMaitreStage::first();
if ($affectationMaitre && $affectationMaitre->maitreStage) {
    echo "Affectation (ID: " . $affectationMaitre->id . ") liée au maître de stage (ID: " . $affectationMaitre->maitreStage->id . ") : " . $affectationMaitre->maitreStage->nom . " " . $affectationMaitre->maitreStage->prenom . "\n";
} else {
    echo "Aucune affectation trouvée ou aucun maître de stage lié.\n";
}
echo "\n";

echo "\n--- Tests des relations avec AffectationResponsableStructure ---\n\n";

// Test de la relation AffectationResponsableStructure -> Structure (BelongsTo)
echo "-- Test AffectationResponsableStructure -> Structure --\n";
$affectationStructure = AffectationResponsableStructure::first();
if ($affectationStructure && $affectationStructure->structure) {
    echo "Affectation responsable structure (ID: " . $affectationStructure->id . ") liée à la structure (ID: " . $affectationStructure->structure->id . ") : " . $affectationStructure->structure->nom . "\n";
} else {
    echo "Aucune affectation responsable structure trouvée ou aucune structure liée.\n";
}
echo "\n";

// Test de la relation AffectationResponsableStructure -> Responsable (User) (BelongsTo)
echo "-- Test AffectationResponsableStructure -> Responsable (User) --\n";
$affectationResponsable = AffectationResponsableStructure::first();
if ($affectationResponsable && $affectationResponsable->responsable) {
    echo "Affectation responsable structure (ID: " . $affectationResponsable->id . ") liée au responsable (ID: " . $affectationResponsable->responsable->id . ") : " . $affectationResponsable->responsable->nom . " " . $affectationResponsable->responsable->prenom . "\n";
} else {
    echo "Aucune affectation responsable structure trouvée ou aucun responsable lié.\n";
}
echo "\n";

echo "\n--- Tests des relations restantes ---\n\n";

// Test de la relation Notification -> User (BelongsTo)
echo "-- Test Notification -> User --\n";
$notification = Notification::first();
if ($notification && $notification->user) {
    echo "Notification (ID: " . $notification->id . ") appartient à l'utilisateur (ID: " . $notification->user->id . ") : " . $notification->user->nom . " " . $notification->user->prenom . "\n";
} else {
    echo "Aucune notification trouvée ou aucun utilisateur lié.\n";
}
echo "\n";

// Test de la relation DemandeAttestation -> Stagiaire (BelongsTo)
echo "-- Test DemandeAttestation -> Stagiaire --\n";
$demandeAttestation = DemandeAttestation::first();
if ($demandeAttestation && $demandeAttestation->stagiaire) {
    echo "Demande d'attestation (ID: " . $demandeAttestation->id . ") appartient au stagiaire (ID: " . $demandeAttestation->stagiaire->id . ") : " . $demandeAttestation->stagiaire->nom . " " . $demandeAttestation->stagiaire->prenom . "\n";
} else {
    echo "Aucune demande d'attestation trouvée ou aucun stagiaire lié.\n";
}
echo "\n";

echo "--- Fin des tests ---\n";