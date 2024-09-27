<?php

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $cliCommands = [];
    protected string $groupPrefix = '';

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->groupPrefix . $path,
            'handler' => $handler,
        ];
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousGroupPrefix = $this->groupPrefix;
        $this->groupPrefix = $previousGroupPrefix . $prefix;
        $callback($this);
        $this->groupPrefix = $previousGroupPrefix;
    }

    public function restful(string $resource, string $controller): void
    {
        $this->get("/$resource", [$controller, 'index']);
        $this->get("/$resource/{id}", [$controller, 'show']);
        $this->post("/$resource", [$controller, 'store']);
        $this->put("/$resource/{id}", [$controller, 'update']);
        $this->delete("/$resource/{id}", [$controller, 'destroy']);
    }

    public function cli(string $command, callable $handler): void
    {
        $this->cliCommands[$command] = $handler;
    }

    public function dispatch(string $uri): void
    {
        if (php_sapi_name() === 'cli') {
            $this->dispatchCli();
        } else {
            $this->dispatchHttp($uri);
        }
    }

    protected function dispatchHttp(string $uri): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $uri) {
                call_user_func($route['handler']);
                return;
            }
        }

        // If no route matches, send a 404 response
        http_response_code(404);
        echo '404 Not Found';
    }

    protected function dispatchCli(): void
    {
        global $argv;
        $command = $argv[1] ?? '';

        if (isset($this->cliCommands[$command])) {
            call_user_func($this->cliCommands[$command], array_slice($argv, 2));
        } else {
            echo "Command not found: $command\n";
        }
    }
}