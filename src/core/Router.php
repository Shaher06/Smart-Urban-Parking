<?php

class Router
{
    private array $routes = [];

    public function add(string $page, string $controller, string $method): void
    {
        $this->routes[$page] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(string $page): void
    {
        if (!isset($this->routes[$page])) {
            $this->render404();
            return;
        }

        $route      = $this->routes[$page];
        $ctrlName   = $route['controller'];
        $methodName = $route['method'];
        $ctrlFile   = BASE_PATH . '/controllers/' . $ctrlName . '.php';

        if (!file_exists($ctrlFile)) {
            $this->render404();
            return;
        }

        require_once $ctrlFile;

        if (!class_exists($ctrlName)) {
            $this->render404();
            return;
        }

        $controller = new $ctrlName();

        if (!method_exists($controller, $methodName)) {
            $this->render404();
            return;
        }

        $controller->{$methodName}();
    }

    private function render404(): void
    {
        http_response_code(404);
        include BASE_PATH . '/views/errors/404.php';
    }
}