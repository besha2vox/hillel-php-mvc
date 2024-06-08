<?php

define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/vendor/autoload.php';

use App\Enums\Http\Status;
use Core\Router;

function getStatusCode(int $code): Status {
    return Status::tryFrom($code) ?? Status::INTERNAL_SERVER_ERROR;
}

try {
    die(Router::dispatch($_SERVER['REQUEST_URI']));
} catch (Exception $err) {
    die (
    jsonResponse(getStatusCode($err->getCode()), [
        'errors' => [
            'message' => $err->getMessage()
        ]
    ])
    );
}
