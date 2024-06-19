<?php

namespace App\Models;

use Core\Model;

class Note extends Model
{
    static protected ?string $table = 'notes';

    public int $user_id;
    public int $folder_id;
    public string $title, $content;
    public ?bool $pinned, $completed;
    public ?string $created_at, $updated_at;
}