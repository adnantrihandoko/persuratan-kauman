<?php

namespace App\core\router;


class Router
{
    public array $routes = [];
    private RouterHelper $helper;

    private function tambahRoute($method, $uri, $controller, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'controllerClass' => $controller,
            'callback' => $callback,
        ];
    }

    public function run()
    {
        require_once '../src/core/di/dependencyinjection.php';
        $this->helper = new RouterHelper($dependenciesGlobal);
        $this->helper->handle($this->routes);
    }

    public function middleware(){
    }

    public function get($uri, $controllerClass, $callback, $middleware = null)
    {
        $this->tambahRoute('GET', $uri, $controllerClass, $callback);
    }

    public function post($uri, $controllerClass, $callback)
    {
        $this->tambahRoute('POST', $uri, $controllerClass, $callback);
        return $this;
    }

    public function put($uri, $controllerClass, $callback){
        $this->tambahRoute('PUT', $uri, $controllerClass, $callback);
        
    }

    public function delete($uri, $controllerClass, $callback){
        $this->tambahRoute('DELETE', $uri, $controllerClass, $callback);
    }
}