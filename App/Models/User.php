<?php

namespace App\Models;

use Core\Model;
use DateTime;

class User extends Model
{
    static protected ?string $table = 'users';
    public string $email;
    public string $password;
    public ?string $token = null;
    public ?string $token_expired_at = null;
    public ?string $created_at;
}