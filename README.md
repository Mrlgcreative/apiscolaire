# apiscolaire

API REST de gestion scolaire multi-institutions (Laravel 13 + Sanctum).

Chaque école (institution) dispose de ses données isolées : élèves, classes, cours, professeurs, frais et paiements. Les comptes avec `institution_id = null` sont des super-admins du groupe, capables de naviguer entre les écoles via le header `X-Institution` (code ou id).

## Stack

- Laravel 13, PHP 8.5
- Sanctum (tokens personnels)
- SQLite (démo) — adaptable MySQL/PostgreSQL

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

L'API est servie sous le préfixe `/api/v1`.

## Authentification

`POST /api/v1/auth/login` avec `{ "login": "email-ou-username", "password": "…" }`
renvoie un token à passer sur chaque appel :

```
Authorization: Bearer <token>
X-Institution: CSK   # super-admin uniquement (sélection de l'école)
```

## Documentation interactive

Un explorateur d'API (catalogue complet, exemples de requêtes/réponses par code HTTP, playground) est disponible dans `../frontend`.
