<?php

namespace Core;

use App\Enums\Http\Status;
use ReallySimpleJWT\Token;
use splitbrain\phpcli\Exception;

class JWTToken
{
    static protected int $expiration = 86400;
    static protected string $issuer = 'localhost';

    static protected function getSecret(): string
    {
        return getenv("SECRET");
    }

    static public function createToken(int $userId): string
    {
        $expirationTime = time() + static::$expiration;
        return Token::create($userId, static::getSecret(), $expirationTime, static::$issuer);
    }

    static public function getPayload(string $token): array
    {
        static::validate($token);

        $payload = Token::getPayload($token);

        if (time() > $payload['exp']) {
            throw new Exception("Token expired", Status::UNAUTHORIZED->value);
        }

        return $payload;
    }

    static  public function validate(string $token): void
    {
        if (!Token::validate($token, static::getSecret())) {
            throw new Exception("Incorrect token", Status::UNAUTHORIZED->value);
        }
    }
}