<?php

use App\Enums\Http\Status;

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
