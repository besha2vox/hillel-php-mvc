<?php

namespace App\Enums\Http;

enum Status: int
{
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case UNPROCESSABLE_ENTITY = 422;
    case INTERNAL_SERVER_ERROR = 500;

    public function description(): array
    {
        $description = match ($this->value) {
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            408 => 'Request Timeout',
            409 => 'Conflict',
            500 => 'Internal Server Error',
            422 => 'Unprocessable Entity',
            default => 'Unknown Error'
        };

        return [
            'code' => $this->value,
            'status' => "$this->value $description",
        ];
    }
}