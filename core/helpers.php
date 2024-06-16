<?php

use App\Enums\Http\Status;
use Core\DB;
use Core\JWTToken;

function db()
{
    return DB::connect();
}

function jsonResponse(Status $status, $data = []): string
{
    header_remove();
    http_response_code($status->value);
    header("Content-Type: application/json");
    header("Status: $status->value");
    return json_encode([
        ...$status->description(),
        ...$data
    ]);
}

function requestBody(): array
{
    $data = [];

    $requestBody = file_get_contents('php://input');

    if(!empty($requestBody)) {
        $data = json_decode($requestBody, true);
    }

    return $data;
}

function getToken(): string
{
    $headers = getallheaders();
    if (!array_key_exists('Authorization', $headers)) {
        throw new Exception("Unauthorized", Status::UNAUTHORIZED->value);
    }

    return JWTToken::parseToken($headers["Authorization"]);
}
