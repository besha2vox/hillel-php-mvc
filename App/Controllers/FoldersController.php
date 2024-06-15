<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\Folder;
use Core\BaseApiController;
use Core\JWTToken;
use ReallySimpleJWT\Token;
use splitbrain\phpcli\Exception;

class FoldersController extends BaseApiController
{
    public function index()
    {
        $folders = Folder::all();
        return $this->response(Status::OK, $folders);
    }

    public function getUserFolders()
    {
        $headers = getallheaders();
        if (!array_key_exists('Authorization', $headers)) {
            throw new Exception("Unauthorized", Status::UNAUTHORIZED->value);
        }

        $token = $headers["Authorization"];

        $userId = JWTToken::parseToken($token);

        $folders = Folder::select()->where('user_id', value: $userId)->get();
        return $this->response(Status::OK, $folders);
    }
}