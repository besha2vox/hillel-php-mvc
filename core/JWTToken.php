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
        return Token::create($userId, static::getSecret(), static::$expiration, static::$issuer);
    }

    static public function parseToken(string $token): int
    {
        $token = str_replace('Bearer ', '', $token);

        if (Token::validate($token, static::getSecret())) {
            throw new Exception("Incorrect token", Status::UNAUTHORIZED->value);
        }

        $payload = Token::getPayload($token);

        if (time() > $payload['exp']) {
            throw new Exception("Token expired", Status::UNAUTHORIZED->value);
        }

        return $payload['user_id'];
    }
}