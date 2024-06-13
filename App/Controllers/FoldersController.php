<?php

namespace App\Controllers;

use App\Enums\DB\SQL;
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
}