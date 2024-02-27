<?php
namespace App\Helpers;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class JwtGenerator
{
    public static function generateToken($userId)
    {
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(env('JWT_SECRET')));

        $now   = new \DateTimeImmutable();
        $token = $config->builder()
                        ->issuedAt($now)
                        ->withClaim('uuid', $userId) // Identificador do usuário
                        ->getToken($config->signer(), $config->signingKey());

        return $token->toString();
    }
}
