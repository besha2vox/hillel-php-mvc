<?php

namespace App\Models;

use Core\Model;

class Note extends Model
{
    static protected ?string $table = 'notes';

    public int $folder_id, $user_id;
    public ?bool $pinned, $completed;
    public ?string $title, $content, $created_at, $updated_at;
}