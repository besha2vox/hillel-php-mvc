<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\Folder;
use Core\BaseApiController;

class FoldersController extends BaseApiController
{
    public function index()
    {
        $folders = Folder::all();
        return $this->response(Status::OK, $folders);
    }

    public function getUserFolders()
    {
        $userId = getToken();

        $folders = Folder::select()->where('user_id', value: $userId)->get();
        return $this->response(Status::OK, $folders);
    }
}