<?php

namespace App\Support;

/**
 * Registre de la passerelle RPC : associe chaque « action » à sa méthode HTTP
 * et à son chemin réel sous /api/v1. Les segments {param} sont remplacés
 * depuis les paramètres de la requête passerelle ; le reliquat part en query
 * string (GET) ou en corps JSON (POST/PUT).
 */
class RpcGateway
{
    /** @var array<string, array{0:string, 1:string}> */
    public const ACTIONS = [
        // Authentification
        'auth.me' => ['GET', '/auth/me'],

        // Groupe & écoles
        'institutions.index' => ['GET', '/institutions'],
        'institutions.store' => ['POST', '/institutions'],
        'institutions.show' => ['GET', '/institutions/{institution}'],
        'institutions.update' => ['PUT', '/institutions/{institution}'],
        'institutions.destroy' => ['DELETE', '/institutions/{institution}'],

        // Utilisateurs & rôles
        'utilisateurs.index' => ['GET', '/utilisateurs'],
        'utilisateurs.store' => ['POST', '/utilisateurs'],
        'utilisateurs.show' => ['GET', '/utilisateurs/{user}'],
        'utilisateurs.update' => ['PUT', '/utilisateurs/{user}'],
        'utilisateurs.destroy' => ['DELETE', '/utilisateurs/{user}'],

        // Référentiels
        'options.index' => ['GET', '/options'],
        'options.store' => ['POST', '/options'],
        'options.show' => ['GET', '/options/{option}'],
        'options.update' => ['PUT', '/options/{option}'],
        'sessions.index' => ['GET', '/sessions-scolaires'],
        'sessions.active' => ['GET', '/sessions-scolaires/active'],
        'mois.index' => ['GET', '/mois'],

        // Scolarité
        'eleves.index' => ['GET', '/eleves'],
        'eleves.store' => ['POST', '/eleves'],
        'eleves.show' => ['GET', '/eleves/{eleve}'],
        'eleves.update' => ['PUT', '/eleves/{eleve}'],
        'eleves.destroy' => ['DELETE', '/eleves/{eleve}'],
        'classes.index' => ['GET', '/classes'],
        'classes.store' => ['POST', '/classes'],
        'classes.show' => ['GET', '/classes/{classe}'],
        'classes.update' => ['PUT', '/classes/{classe}'],
        'classes.destroy' => ['DELETE', '/classes/{classe}'],
        'professeurs.index' => ['GET', '/professeurs'],
        'professeurs.store' => ['POST', '/professeurs'],
        'professeurs.show' => ['GET', '/professeurs/{professeur}'],
        'professeurs.update' => ['PUT', '/professeurs/{professeur}'],
        'professeurs.destroy' => ['DELETE', '/professeurs/{professeur}'],
        'cours.index' => ['GET', '/cours'],
        'cours.store' => ['POST', '/cours'],
        'cours.show' => ['GET', '/cours/{cours}'],
        'cours.update' => ['PUT', '/cours/{cours}'],
        'cours.destroy' => ['DELETE', '/cours/{cours}'],
        'absences.index' => ['GET', '/absences'],
        'absences.store' => ['POST', '/absences'],
        'absences.show' => ['GET', '/absences/{absence}'],
        'absences.update' => ['PUT', '/absences/{absence}'],
        'absences.destroy' => ['DELETE', '/absences/{absence}'],

        // Évaluations
        'periodes.index' => ['GET', '/periodes'],
        'periodes.store' => ['POST', '/periodes'],
        'periodes.show' => ['GET', '/periodes/{periode}'],
        'periodes.update' => ['PUT', '/periodes/{periode}'],
        'periodes.destroy' => ['DELETE', '/periodes/{periode}'],
        'notes.index' => ['GET', '/notes'],
        'notes.store' => ['POST', '/notes'],
        'notes.show' => ['GET', '/notes/{note}'],
        'notes.update' => ['PUT', '/notes/{note}'],
        'notes.destroy' => ['DELETE', '/notes/{note}'],
        'bulletins.show' => ['GET', '/bulletins'],

        // Finances
        'frais.index' => ['GET', '/frais'],
        'frais.store' => ['POST', '/frais'],
        'frais.show' => ['GET', '/frais/{frais}'],
        'frais.update' => ['PUT', '/frais/{frais}'],
        'frais.destroy' => ['DELETE', '/frais/{frais}'],
        'paiements.index' => ['GET', '/paiements'],
        'paiements.store' => ['POST', '/paiements'],
        'paiements.show' => ['GET', '/paiements/{paiement}'],
        'paiements.destroy' => ['DELETE', '/paiements/{paiement}'],
    ];
}
