<?php
namespace App\Core;

class Router
{
    private $routes = [];
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function add($method, $path, $handler)
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function get($path, $handler) { $this->add('GET', $path, $handler); }
    public function post($path, $handler) { $this->add('POST', $path, $handler); }
    public function put($path, $handler) { $this->add('PUT', $path, $handler); }
    public function delete($path, $handler) { $this->add('DELETE', $path, $handler); }

    private function match($routePath, $uri, &$params)
    {
        $params = [];
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '([^/]+)', $routePath);
        $pattern = "#^$pattern$#";

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', $routePath, $paramNames);
            foreach ($paramNames[1] as $i => $name) {
                $params[$name] = $matches[$i];
            }
            return true;
        }
        return false;
    }

    public function dispatch($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            $params = [];
            if ($this->match($route['path'], $uri, $params)) {
                return $this->invoke($route['handler'], $params);
            }
        }
        http_response_code(404);
        echo "Página no encontrada";
    }

    private function invoke($handler, $params)
    {
        if (is_callable($handler)) return call_user_func_array($handler, $params);

        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            if (!class_exists($controller)) throw new \Exception("Controlador no encontrado: $controller");
            $c = new $controller($this->db);
            return call_user_func_array([$c, $method], $params);
        }
        throw new \Exception("Handler inválido");
    }
}
