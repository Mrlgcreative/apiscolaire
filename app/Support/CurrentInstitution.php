<?php

namespace App\Support;

/**
 * Contexte de l'institution résolue pour la requête courante.
 * Singleton muté par le middleware ResolveInstitution.
 */
final class CurrentInstitution
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $code = null,
    ) {}

    public function isSet(): bool
    {
        return $this->id !== null;
    }

    public function with(?string $id, ?string $code): self
    {
        return new self($id, $code);
    }
}
