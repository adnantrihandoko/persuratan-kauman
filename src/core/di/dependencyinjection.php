<?php

use App\core\modules\auth\AuthController;
use App\core\modules\auth\AuthDataAccess;
use App\core\modules\auth\AuthRepository;
use App\core\modules\auth\AuthUseCase;
use App\core\modules\user\UserController;
use App\core\modules\user\UserDataAccess;
use App\core\modules\user\UserRepoImpl;
use App\core\modules\user\UserUseCase;
use App\core\utils\JWTService;
use App\core\utils\Middleware;

function buildDependencies()
{
    $instances = []; // Array untuk menyimpan instance

    return [
        'PDO' => function () use (&$instances) {
            if (!isset($instances['PDO'])) {
                $dbFile = '../src/core/database/database.sqlite';
                try {
                    $instances['PDO'] = new PDO('sqlite:' . $dbFile);
                    $instances['PDO']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return $instances['PDO'];
                } catch (PDOException $e) {
                    throw new Exception('Connection failed: ' . $e->getMessage());
                }
            }
            return $instances['PDO'];
        },

        'App\core\utils\Middleware' => function ($dependencies) use ($instances){
            if(!isset($instances['App\core\utils\Middleware'])){
                $instances['App\core\utils\Middleware'] = new Middleware($dependencies['App\core\utils\JWTService'](), $dependencies['App\core\modules\auth\AuthUseCase']($dependencies));
                return $instances['App\core\utils\Middleware'];
            }
            return $instances['App\core\utils\Middleware'];
        },


        'App\core\modules\user\UserRepository' => function ($dependencies) use (&$instances) {
            if (!isset($instances['UserRepository'])) {
                $instances['UserRepository'] = new UserRepoImpl(new UserDataAccess($dependencies['PDO']()));
            }
            return $instances['UserRepository'];
        },
        'App\core\modules\user\UserUseCase' => function ($dependencies) use (&$instances) {
            if (!isset($instances['UserUseCase'])) {
                $instances['UserUseCase'] = new UserUseCase($dependencies['App\core\modules\user\UserRepository']($dependencies));
            }
            return $instances['UserUseCase'];
        },
        'App\core\modules\user\UserController' => function ($dependencies) use (&$instances) {
            if (!isset($instances['UserController'])) {
                $instances['UserController'] = new UserController($dependencies['App\core\modules\user\UserUseCase']($dependencies), $dependencies['App\core\utils\Middleware']($dependencies));
            }
            return $instances['UserController'];
        },


        'App\core\modules\auth\AuthRepository' => function ($dependencies) use (&$instances) {
            if (!isset($instances['AuthRepository'])) {
                $instances['AuthRepository'] = new AuthRepository(new AuthDataAccess($dependencies['PDO']()));
            }
            return $instances['AuthRepository'];
        },
        'App\core\utils\JWTService' => function () use (&$instances) {
            if (!isset($instances['JWTService'])) {
                $instances['JWTService'] = new JWTService();
            }
            return $instances['JWTService'];
        },
        'App\core\modules\auth\AuthUseCase' => function ($dependencies) use (&$instances) {
            if (!isset($instances['AuthUseCase'])) {
                $instances['AuthUseCase'] = new AuthUseCase(
                    $dependencies['App\core\modules\auth\AuthRepository']($dependencies),
                    $dependencies['App\core\utils\JWTService'](),
                    $dependencies['App\core\modules\user\UserUseCase']($dependencies)
                );
            }
            return $instances['AuthUseCase'];
        },
        'App\core\modules\auth\AuthController' => function ($dependencies) use (&$instances) {
            if (!isset($instances['AuthController'])) {
                $instances['AuthController'] = new AuthController($dependencies['App\core\modules\auth\AuthUseCase']($dependencies));
            }
            return $instances['AuthController'];
        },
    ];
}

$dependenciesGlobal = buildDependencies();
