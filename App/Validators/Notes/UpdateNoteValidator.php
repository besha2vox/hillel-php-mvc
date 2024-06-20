<?php

namespace App\Validators\Notes;

use App\Enums\Http\Status;
use App\Models\Note;

class UpdateNoteValidator extends BaseNoteValidator
{
    static public function validate(array $fields = []): bool
    {
        $note = Note::find($fields['id']);

        if (!$note) {
            throw new Exception("Note not found", Status::NOT_FOUND->value);
        }

        if (!isset($fields['title'])) {
            static::$skip[] = 'title';
            unset(static::$errors['title']);
        }

        $result = [
            parent::validate($fields),
            static::isBoolean($fields, 'pinned'),
            static::isBoolean($fields, 'completed'),
        ];

        if (isset($fields['title'])) {
            $result[] = !static::checkOnDuplicate($fields['title'], $note->folder_id);
        }

        return !in_array(false, $result);
    }
}