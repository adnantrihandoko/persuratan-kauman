<?php
namespace App\core\router;
use Exception;
class RouterHelper
{

    private $dependencies;

    function __construct($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    function panggilController($controllerClass, $callback)
    {
        if (isset($this->dependencies[$controllerClass])) {

            $controllerInstance = $this->dependencies[$controllerClass]($this->dependencies);

            if (method_exists($controllerInstance, $callback)) {
                return $controllerInstance->$callback();
            }
            throw new Exception("Method {$callback} tidak ada di {$controllerClass}");
        }
        throw new Exception("Dependencies: " . $controllerClass);
    }

    public function handle($routes)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach ($routes as $route) {
            // Mengganti parameter dalam URI dengan regex
            $routeUri = preg_replace('/\{[a-zA-Z_]+\}/', '([a-zA-Z0-9_]+)', $route['uri']);
            $routeUriRegex = "#^{$routeUri}$#";

            if ($route['method'] === $method && preg_match($routeUriRegex, $uri, $matches)) {
                array_shift($matches); // Menghapus elemen pertama yang merupakan string penuh

                $controller = $route['controllerClass'];
                $callback = $route['callback'];

                if (class_exists($controller) && method_exists($controller, $callback)) {
                    $controllerInstance = $this->dependencies[$controller]($this->dependencies);
                    return $controllerInstance->$callback(...$matches); // Passing matches as arguments
                }
                throw new Exception("Method {$callback} tidak ada di {$controller}");
            }
        }

        // Jika tidak ada rute yang cocok
        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
    }

}