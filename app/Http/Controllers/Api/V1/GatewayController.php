<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\RpcGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

/**
 * Passerelle RPC : POST /api/v1/gateway
 *
 * Corps attendu :
 * {
 *   "action": "eleves.show",
 *   "params": { "eleve": 1, "section": "primaire" }
 * }
 *
 * La requête est re-dispatchée en interne vers la route réelle : auth,
 * validation, ressources et isolation par école s'appliquent à l'identique.
 */
class GatewayController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'regex:/^[a-z]+\.[a-z]+$/'],
            'params' => ['nullable', 'array'],
        ]);

        $action = $validated['action'];
        /** @var array{0:string, 1:string}|null $route */
        $route = RpcGateway::ACTIONS[$action] ?? null;

        if ($route === null) {
            return response()->json([
                'message' => "Action inconnue : {$action}.",
                'actions_disponibles' => array_keys(RpcGateway::ACTIONS),
            ], 404);
        }

        [$method, $pattern] = $route;
        $input = $validated['params'] ?? [];

        // Segments de chemin {param} consommés depuis les paramètres.
        $path = preg_replace_callback('/\{(\w+)\}/', function (array $m) use (&$input) {
            return rawurlencode((string) Arr::pull($input, $m[1]));
        }, $pattern);

        $hasBody = in_array($method, ['POST', 'PUT', 'PATCH'], true);
        $server = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];
        foreach (['authorization' => 'HTTP_AUTHORIZATION', 'x-institution' => 'HTTP_X_INSTITUTION'] as $from => $to) {
            if (($value = $request->headers->get($from)) !== null) {
                $server[$to] = $value;
            }
        }

        $sub = Request::create(
            '/api/v1'.$path,
            $method,
            [],
            [],
            [],
            $server,
            $hasBody ? json_encode($input ?: []) : null,
        );

        if (! $hasBody) {
            foreach ($input as $key => $value) {
                $sub->query->set($key, $value);
            }
        }

        $response = app()->handle($sub);
        $status = $response->getStatusCode();

        $body = json_decode($response->getContent(), true);

        return new JsonResponse(
            $body,
            $status,
            ['X-Gateway-Action' => $action],
            JSON_UNESCAPED_UNICODE,
        );
    }
}
