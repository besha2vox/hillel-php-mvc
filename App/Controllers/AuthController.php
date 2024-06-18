<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\User;
use App\Validators\Auth\LoginValidator;
use App\Validators\Auth\RegisterValidator;
use Core\Controller;
use Core\JWTToken;

class AuthController extends Controller
{
    public function register(): array
    {
        $fields = requestBody();
        if (RegisterValidator::validate($fields)) {
            $user = User::create([
                ...$fields,
                'password' => password_hash($fields['password'], PASSWORD_BCRYPT)
            ]);

            return $this->response(Status::CREATED, $user->toArray());
        }

        return $this->response(Status::BAD_REQUEST, $fields, RegisterValidator::getErrors());
    }

    public function login(): array
    {
        $fields = requestBody();

        if (LoginValidator::validate($fields)) {
            $user = User::findBy('email', $fields['email']);

            if (password_verify($fields['password'], $user->password)) {
                $token = JWTToken::createToken($user->id);
                return $this->response(Status::OK, compact('token'));
            }

        }

        return $this->response(Status::UNPROCESSABLE_ENTITY, errors: LoginValidator::getErrors());
    }
}
