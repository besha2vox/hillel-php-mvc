<?php

namespace App\Validators\Notes;


use App\Models\Folder;
use App\Models\Note;
use Core\BaseValidator;

class BaseNoteValidator extends BaseValidator
{
    static protected array $rules = [
        'title' => '/^[a-zA-Z0-9\s]{1,20}$/',
    ];

    static protected array $errors = [
        'title' => 'The [title] must be 1-20 characters long and contain only letters and numbers',
    ];
    protected static array $skip = ['user_id', 'content', 'pinned', 'completed', 'created_at', 'updated_at'];

    static protected function isBoolean(array $fields, string $key): bool
    {
        if (empty($fields[$key])) {
            return true;
        }

        $result = is_bool($fields[$key]) || $fields[$key] === 1;
        if (!$result) {
            static::setErrors($key, "[$key] should be boolean");
        }

        return $result;
    }

    static protected function isFolderExist(int $folderId): bool
    {
        $folder = Folder::find($folderId);

        return $folder && $folder->user_id === authId();
    }

    static protected function checkOnDuplicate(string $title, int $folder_id): bool
    {
        $isExists = Note::select()
            ->where('user_id', value: authId())
            ->and('folder_id', value: $folder_id)
            ->and('title', value: $title)
            ->exists();

        if ($isExists) {
            static::setErrors('title', "The note with name '{$title}' already exists");
        }

        return $isExists;
    }
}