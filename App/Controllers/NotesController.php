<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\Note;
use App\Validators\NoteValidator;
use Core\BaseApiController;
use splitbrain\phpcli\Exception;

class NotesController extends BaseApiController
{
    public function index(): array
    {
        $folders = Note::all();
        return $this->response(Status::OK, $folders);
    }

    public function getUserNotes(): array
    {
        $userId = authId();

        $folders = Note::select()->where('user_id', value: $userId)->get();
        return $this->response(Status::OK, $folders);
    }

    public function getById(int $id): array
    {
        $folder = Note::find($id);

        if (!$folder) {
            throw new Exception("Note not found", Status::NOT_FOUND->value);
        }

        return $this->response(Status::OK, $folder->toArray());
    }

    public function create(): array
    {
        $fields = requestBody();
        $userId = authId();

        if (NoteValidator::validate($fields)) {
            $folder = Note::create([...$fields, 'user_id' => $userId]);
            return $this->response(Status::CREATED, $folder->toArray());
        }

        return $this->response(Status::BAD_REQUEST, $fields, NoteValidator::getErrors());
    }

    public function update(int $id): array
    {
        $fields = requestBody();
        if (NoteValidator::validate($fields) && $this->model) {
            $fields = [...$fields, 'updated_at' => date('Y-m-d H:i:s')];
            $folder = $this->model->update($fields);

            return $this->response(Status::OK, $folder->toArray());
        }

        return $this->response(Status::OK, errors: NoteValidator::getErrors());
    }

    public function delete(int $id): array
    {
        $result = Note::delete($id);

        if (!$result) {
            return $this->response(Status::UNPROCESSABLE_ENTITY, errors: ['message' => "Something went wrong"]);
        }

        return $this->response(Status::OK, [$this->model->toArray()]);
    }

    protected function getModelClass(): string
    {
        return Note::class;
    }
}