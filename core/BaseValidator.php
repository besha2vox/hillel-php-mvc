<?php

namespace Core;

abstract class BaseValidator
{
    static protected array $rules = [];
    static protected array $skip = [];
    static protected array $errors = [];

    static public function validate(array $fields): bool
    {
        if (empty(static::$rules)) {
            return true;
        }

        foreach (static::$rules as $rule => $value) {
            if (in_array($rule, static::$skip)) {
                continue;
            }
            if (!empty(static::$rules[$rule]) && preg_match(static::$rules[$rule], $fields[$rule])) {
                unset(static::$errors[$rule]);
            }
        }
        return empty(static::$errors);
    }

    static public function getErrors(): array
    {
        return static::$errors;
    }

    static public function setErrors(string $error, string $message): void
    {
        static::$errors[$error] = $message;
    }
}