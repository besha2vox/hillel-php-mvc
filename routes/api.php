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
Router::get('api/folders/{id:\d+}')->setController(FoldersController::class)
    ->setAction('getById');
Router::post('api/folders/add')->setController(FoldersController::class)
    ->setAction('create');
Router::put('api/folders/{id:\d+}/update')->setController(FoldersController::class)
    ->setAction('update');
Router::delete('api/folders/{id:\d+}/delete')->setController(FoldersController::class)
    ->setAction('delete');