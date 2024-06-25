<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\Note;
use App\Validators\Notes\CreateNoteValidator;
use App\Validators\Notes\UpdateNoteValidator;
use Core\BaseApiController;
use splitbrain\phpcli\Exception;

class NotesController extends BaseApiController
{
    public function index(): array
    {
        $notes = Note::select()->where('user_id', value: authId())
            ->orderBy([
                'pinned' => 'DESC',
                'completed' => 'ASC',
                'updated_at' => 'DESC'
            ])->get();

        return $this->response(Status::OK, $notes);
    }

    public function getById(int $id): array
    {
        $note = Note::find($id);

        if (!$note) {
            throw new Exception("Note not found", Status::NOT_FOUND->value);
        }

        if ($note->user_id !== authId()) {
            throw new Exception("FORBIDDEN", Status::FORBIDDEN->value);
        }

        return $this->response(Status::OK, $note->toArray());
    }

    public function create(): array
    {
        $fields = requestBody();
        $userId = authId();

        if (CreateNoteValidator::validate($fields)) {
            $folder = Note::create([...$fields, 'user_id' => $userId]);
            return $this->response(Status::CREATED, $folder->toArray());
        }

        return $this->response(Status::BAD_REQUEST, $fields, CreateNoteValidator::getErrors());
    }

    public function update(int $id): array
    {
        $fields = requestBody();
        if (UpdateNoteValidator::validate([...$fields, 'id' => $id]) && $this->model) {
            $fields = [...$fields, 'updated_at' => date('Y-m-d H:i:s')];
            $folder = $this->model->update($fields);

            return $this->response(Status::OK, $folder->toArray());
        }

        return $this->response(Status::OK, $fields,  UpdateNoteValidator::getErrors());
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