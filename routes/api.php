<?php


use App\Controllers\AuthController;
use App\Controllers\FoldersController;
use Core\Router;

Router::post('api/auth/register')
    ->setController(AuthController::class)
    ->setAction('register');
Router::post('api/auth')
    ->setController(AuthController::class)
    ->setAction('auth');


Router::get('api/folders')->setController(FoldersController::class)
    ->setAction('index');
