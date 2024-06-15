<?php

namespace App\Validators\Auth;

use App\Models\User;
use Core\BaseValidator;

abstract class BaseAuthValidator extends BaseValidator
{
    static  protected array $rules = [
        'email' => '/^\\S+@\\S+\\.\\S+$/',
        'password' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
    ];

    static public function checkEmailOnExists(string $email, bool $eqError = true, $message = 'Email already exists'): bool
    {
        $isExists = (bool) User::findBy('email', $email);

        if ($isExists === $eqError) {
            static::setErrors('email', $message);
        }

        return $isExists;
    }
}