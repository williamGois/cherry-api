<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\Clock\SystemClock;
use DateTimeZone;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $tokenString = $request->bearerToken();

        if (!$tokenString) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        try {
            $config = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText(env('JWT_SECRET'))
            );

            $token = $config->parser()->parse($tokenString);

            $constraints = [
                new SignedWith($config->signer(), $config->verificationKey())            ];

            if (!$config->validator()->validate($token, ...$constraints)) {
                return response()->json(['error' => 'Token inválido'], 401);
            }

            $userId = $token->claims()->get('uuid');
            $request->attributes->add(['user_id' => $userId]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar o token', 'message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
