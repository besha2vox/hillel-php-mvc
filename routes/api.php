<?php


use Core\Router;

Router::post('api/auth/register')
    ->setController(\App\Controllers\AuthController::class)
    ->setAction('register');
Router::post('api/auth')
    ->setController(\App\Controllers\AuthController::class)
    ->setAction('auth');
Router::get('api/smth')->setController(\App\Controllers\SomeController::class)
    ->setAction('smth');