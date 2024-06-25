<?php

namespace App\Validators\Notes;

class CreateNoteValidator extends BaseNoteValidator
{

    static public function validate(array $fields = []): bool
    {
        $result = [
            parent::validate($fields),
            static::isFolderExist($fields['folder_id']),
            !static::checkOnDuplicate($fields['title'], $fields['folder_id']),
            static::isBoolean($fields, 'pinned'),
            static::isBoolean($fields, 'completed')
        ];

        return !in_array(false, $result);
    }
}