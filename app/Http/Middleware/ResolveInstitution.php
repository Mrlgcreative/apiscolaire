<?php

namespace App\Http\Middleware;

use App\Models\Institution;
use App\Support\CurrentInstitution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveInstitution
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CurrentInstitution $current */
        $current = app(CurrentInstitution::class);
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        // Un membre d'une école est verrouillé sur son école.
        if ($user->institution_id !== null) {
            $institution = Institution::find($user->institution_id);
            $current = $current->with($institution?->id, $institution?->code);
            app()->instance(CurrentInstitution::class, $current);

            return $next($request);
        }

        // Admin du groupe : peut naviguer entre les écoles via le header.
        $header = $request->header('X-Institution');

        if (filled($header)) {
            $institution = is_numeric($header)
                ? Institution::find((int) $header)
                : Institution::where('code', $header)->first();

            abort_if($institution === null, 404, 'Institution inconnue.');

            $current = $current->with($institution->id, $institution->code);
        }

        app()->instance(CurrentInstitution::class, $current);

        return $next($request);
    }
}
