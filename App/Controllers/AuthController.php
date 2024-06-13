<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use Core\Controller;

class AuthController extends Controller
{
    public function register(): array
    {
        return $this->response(Status::OK, ["response" => "something is working"]);
    }
}
