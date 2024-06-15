<?php

namespace App\Models;

use Core\Model;

class Folder extends Model
{
    static protected ?string $table = 'folders';
    public int $user_id;
    public string $title;
    public ?string $created_at, $updated_at;
}
