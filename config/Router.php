<?php
class Router
{
    private $routes = [];
    private $notFoundCallback;

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function notFound($callback)
    {
        $this->notFoundCallback = $callback;
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Remove query string
        $uri = explode('?', $uri)[0];

        // Remove trailing slash
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Look for direct match
        if (isset($this->routes[$method][$uri])) {
            return $this->executeCallback($this->routes[$method][$uri]);
        }


        foreach ($this->routes[$method] as $route => $callback) {
            if (strpos($route, ':') === false) {
                continue;
            }

            $pattern = preg_replace('#:\w+#', '([^\/]+)', $route);
            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                return $this->executeCallback($callback, $matches);
            }
        }

        // No match , return 404
        if ($this->notFoundCallback) {
            return $this->executeCallback($this->notFoundCallback);
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function executeCallback($callback, $params = [])
    {
        if (is_array($callback)) {
            $controllerClass = $callback[0];
            $controller = new $controllerClass();
            $method = $callback[1];

            return call_user_func_array([$controller, $method], $params);
        }

        return call_user_func_array($callback, $params);
    }
}