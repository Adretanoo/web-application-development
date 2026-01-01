<?php

use Slim\App;
use Acer\Pr8\controllers\BlogController;
use Acer\Pr8\controllers\AuthController;
use Acer\Pr8\controllers\CommentController;
use Acer\Pr8\controllers\TagController;

/** @var App $app */

$app->get('/', [BlogController::class, 'index']);

$app->get('/posts/create', [BlogController::class, 'create']);
$app->post('/posts', [BlogController::class, 'store']);
$app->get('/posts/{id}', [BlogController::class, 'show']);
$app->get('/posts/{id}/edit', [BlogController::class, 'edit']);
$app->put('/posts/{id}', [BlogController::class, 'update']);
$app->delete('/posts/{id}', [BlogController::class, 'destroy']);

$app->get('/login', [AuthController::class, 'showLoginForm']);
$app->post('/login', [AuthController::class, 'login']);
$app->get('/register', [AuthController::class, 'showRegistrationForm']);
$app->post('/register', [AuthController::class, 'register']);
$app->get('/logout', [AuthController::class, 'logout']);

$app->post('/comments', [CommentController::class, 'create']);
$app->put('/comments/{id}', [CommentController::class, 'update']);
$app->delete('/comments/{id}', [CommentController::class, 'delete']);

$app->get('/tags', [TagController::class, 'index']);
$app->post('/tags', [TagController::class, 'store']);
$app->delete('/tags/{id}', [TagController::class, 'destroy']);
