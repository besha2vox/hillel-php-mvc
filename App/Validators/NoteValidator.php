<?php

namespace App\Validators;


use App\Models\Note;
use Core\BaseValidator;

class NoteValidator extends BaseValidator
{
    static protected array $rules = [
        'title' => '/^[a-zA-Z0-9\s]{1,20}$/',
        'content' => '/^[a-zA-Z0-9\s]{20,300}$/',
    ];

    static protected array $errors = [
        'title' => 'The string must be 1-20 characters long and contain only letters and numbers',
        'content' => 'The string must be 20-300 characters long and contain only letters and numbers',
    ];

    static protected array $skip = ['user_id', 'folder_id'];

    static public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            !static::isExist($fields['title'])
        ];

        return !in_array(false, $result);
    }

    static protected function isExist(string $title): bool
    {
        $isExists = Note::select()
            ->where('user_id', value: authId())
            ->and('title', value: $title)
            ->exists();

        if($isExists){
            static::setErrors('title', "The note with name '{$title}' already exists");
        }

        return $isExists;
    }
}