<?php

namespace App\Controllers;



use App\Enums\Http\Status;
use Core\Controller;

class SomeController extends Controller
{
        public function smth(): array
        {
            return $this->response(Status::OK, ['result' => 'something is working']);
        }
}