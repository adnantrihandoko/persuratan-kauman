<?php
require_once '../vendor/autoload.php';
use App\core\router\Router;

$router = new Router();

require_once "../src/routes/routes_endpoint.php";

$router->run();