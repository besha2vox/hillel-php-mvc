<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\Folder;
use App\Validators\FolderValidator;
use Core\BaseApiController;
use splitbrain\phpcli\Exception;

class FoldersController extends BaseApiController
{
    public function index(): array
    {
        $folders = Folder::all();
        return $this->response(Status::OK, $folders);
    }

    public function getUserFolders(): array
    {
        $userId = authId();

        $folders = Folder::select()->where('user_id', value: $userId)->get();
        return $this->response(Status::OK, $folders);
    }

    public function getById(int $id): array
    {
        $folder = Folder::find($id);

        if (!$folder) {
            throw new Exception("Folder not found", Status::NOT_FOUND->value);
        }

        return $this->response(Status::OK, $folder->toArray());
    }

    public function create(): array
    {
        $fields = requestBody();
        $userId = authId();

        if (FolderValidator::validate($fields)) {
            $folder = Folder::create([...$fields, 'user_id' => $userId]);
            return $this->response(Status::CREATED, $folder->toArray());
        }

        return $this->response(Status::BAD_REQUEST, $fields, FolderValidator::getErrors());
    }

    public function update(int $id): array
    {
        $fields = requestBody();
        if (FolderValidator::validate($fields) && $this->model) {
            $fields = [...$fields, 'updated_at' => date('Y-m-d H:i:s')];
            $folder = $this->model->update($fields);

            return $this->response(Status::OK, $folder->toArray());
        }

        return $this->response(Status::OK, errors: FolderValidator::getErrors());
    }

    public function delete(int $id): array
    {
        $result = Folder::delete($id);

        if (!$result) {
            return $this->response(Status::UNPROCESSABLE_ENTITY, errors: ['message' => "Something went wrong"]);
        }

        return $this->response(Status::OK, [$this->model->toArray()]);
    }

    protected function getModelClass(): string
    {
        return Folder::class;
    }
}