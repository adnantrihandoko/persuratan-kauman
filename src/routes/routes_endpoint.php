<?php

use App\core\modules\auth\AuthController;
use App\core\modules\user\UserController;

$router->get('/api/users',UserController::class, 'getAllUsers');
$router->post('/api/create/user',UserController::class, 'createUser');
$router->put('/api/user/{id}', UserController::class, 'updateUser');
$router->delete('/api/user/{id}', UserController::class, 'deleteUser');

$router->post('/api/auth/login', AuthController::class, 'login')->middleware();