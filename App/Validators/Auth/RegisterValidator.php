<?php

namespace App\Validators\Auth;

class RegisterValidator extends BaseAuthValidator
{

    static protected array $errors = [
      'email' => 'Email is incorrect',
      'password' => 'Password is incorrect',
    ];

    static public function validate(array $fields = []): bool
    {
        $result = [
          parent::validate($fields),
          !static::checkEmailOnExists($fields['email']),
        ];

        return !in_array(false, $result);
    }
}