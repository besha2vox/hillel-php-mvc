<?php


use App\Controllers\AuthController;
use App\Controllers\FoldersController;
use Core\Router;

Router::post('api/auth/register')
    ->setController(AuthController::class)
    ->setAction('register');
Router::post('api/auth/login')
    ->setController(AuthController::class)
    ->setAction('login');


Router::get('api/folders')->setController(FoldersController::class)
    ->setAction('index');
Router::get('api/folders/my')->setController(FoldersController::class)
    ->setAction('getUserFolders');
