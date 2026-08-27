<?php

use App\Http\Controllers\Api\V1\AbsenceController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BulletinController;
use App\Http\Controllers\Api\V1\ClasseController;
use App\Http\Controllers\Api\V1\CoursController;
use App\Http\Controllers\Api\V1\EleveController;
use App\Http\Controllers\Api\V1\FraisController;
use App\Http\Controllers\Api\V1\GatewayController;
use App\Http\Controllers\Api\V1\InstitutionController;
use App\Http\Controllers\Api\V1\MoisController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\OptionController;
use App\Http\Controllers\Api\V1\PaiementFraisController;
use App\Http\Controllers\Api\V1\ReinscriptionController;
use App\Http\Controllers\Api\V1\ParametreController;
use App\Http\Controllers\Api\V1\PeriodeController;
use App\Http\Controllers\Api\V1\ProfesseurController;
use App\Http\Controllers\Api\V1\SessionScolaireController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'institution'])->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Passerelle RPC : toutes les opérations via un point d'entrée unique.
        Route::post('/gateway', GatewayController::class)->name('gateway');

        // Groupe (admin uniquement)
        Route::apiResource('institutions', InstitutionController::class);

        // Comptes utilisateurs (admins, scoping par école)
        Route::apiResource('utilisateurs', UserController::class)
            ->parameters(['utilisateurs' => 'user']);

        // Référentiels
        Route::apiResource('options', OptionController::class)->except(['destroy']);
        Route::apiResource('sessions-scolaires', SessionScolaireController::class)->only(['index', 'store']);
        Route::get('/sessions-scolaires/active', [SessionScolaireController::class, 'active']);
        Route::get('/mois', [MoisController::class, 'index']);

        // Scolarité — paramètres explicites : le singulariseur de Laravel 13
        // déforme certains pluriels français (eleves→elefe, frais→frai...).
        Route::apiResource('eleves', EleveController::class)->parameters(['eleves' => 'eleve']);
        Route::post('/eleves/reinscrire-en-masse', [ReinscriptionController::class, 'enMasse']);
        Route::post('/eleves/{eleve}/reinscrire', [ReinscriptionController::class, 'single']);
        Route::apiResource('classes', ClasseController::class)->parameters(['classes' => 'classe']);
        Route::apiResource('professeurs', ProfesseurController::class);
        Route::apiResource('cours', CoursController::class)->parameters(['cours' => 'cours']);
        Route::apiResource('absences', AbsenceController::class);

        // Évaluations
        Route::apiResource('periodes', PeriodeController::class)->parameters(['periodes' => 'periode']);
        Route::apiResource('notes', NoteController::class)->parameters(['notes' => 'note']);
        Route::get('/bulletins', [BulletinController::class, 'show']);

        // Finances
        Route::apiResource('frais', FraisController::class)->parameters(['frais' => 'frais']);
        Route::apiResource('paiements', PaiementFraisController::class)
            ->parameters(['paiements' => 'paiement'])
            ->only(['index', 'store', 'show', 'destroy']);

        // Paramètres de l'application (scopés par école)
        Route::get('/parametres', [ParametreController::class, 'index']);
        Route::put('/parametres', [ParametreController::class, 'update']);
    });
});
