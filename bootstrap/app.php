<?php

use App\Http\Middleware\ResolveInstitution;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);

        $middleware->alias([
            'institution' => ResolveInstitution::class,
        ]);

        // Résoudre l'institution AVANT la liaison des modèles de route,
        // sinon les global scopes tenant ne s'appliquent pas au binding.
        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: ResolveInstitution::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }
        });

        // Ressource inexistante OU hors du périmètre de l'école courante.
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Ressource introuvable ou hors de votre école.',
                ], 404);
            }
        });

        // 404 propres : route inconnue sous /api ou mauvaise méthode.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Route introuvable. Vérifiez l\'URL et la méthode HTTP.',
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Méthode HTTP non autorisée pour cette route.',
                ], 405);
            }
        });
    })->create();
