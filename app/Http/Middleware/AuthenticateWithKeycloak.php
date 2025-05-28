<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthenticateWithKeycloak
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Apenas requisições JSON são permitidas'], 406);
        }

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não encontrado'], 401);
        }

        try {
            // Busca as chaves JWKS do Keycloak e cacheia por 1 hora
            $jwks = Cache::remember('keycloak_jwks', 3600, function () {
                $jwksUrl = env('KEYCLOAK_URL') . '/realms/' . env('KEYCLOAK_REALM') . '/protocol/openid-connect/certs';

                $json = file_get_contents($jwksUrl, false, stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,         
                        'verify_peer_name' => false,
                    ],
                ]));

                if (!$json) {
                    throw new Exception('Não foi possível obter as chaves públicas do Keycloak');
                }

                return $json;
            });

            $keys = json_decode($jwks, true);
            $publicKeys = JWK::parseKeySet($keys);

            $decoded = JWT::decode($token, $publicKeys);

            // Valida issuer do token
            $expectedIssuer = env('KEYCLOAK_URL') . '/realms/' . env('KEYCLOAK_REALM');
            if ($decoded->iss !== $expectedIssuer) {
                return response()->json(['error' => 'Issuer inválido'], 401);
            }

            // Valida audience opcionalmente
            if (isset($decoded->aud) && env('KEYCLOAK_CLIENT_ID')) {
                if (is_array($decoded->aud)) {
                    if (!in_array(env('KEYCLOAK_CLIENT_ID'), $decoded->aud)) {
                        return response()->json(['error' => 'Audience inválido'], 401);
                    }
                } else {
                    if ($decoded->aud !== env('KEYCLOAK_CLIENT_ID')) {
                        return response()->json(['error' => 'Audience inválido'], 401);
                    }
                }
            }

            // Seta dados do usuário na request para uso posterior
            $request->attributes->set('user', [
                'sub' => $decoded->sub ?? null,
                'name' => $decoded->name ?? null,
                'email' => $decoded->email ?? null,
                'preferred_username' => $decoded->preferred_username ?? null,
            ]);

            return $next($request);

        } catch (Exception $e) {
            Log::error('Erro na autenticação com Keycloak', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Token inválido ou expirado',
                'details' => $e->getMessage(),
            ], 401);
        }
    }
}
