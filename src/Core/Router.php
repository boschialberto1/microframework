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

    /**
     * Dispatches an HTTP request.
     *
     * This method is responsible for handling HTTP requests. It checks the request method and URI,
     * matches them against the registered routes, and calls the corresponding handler if a match is found.
     * If no match is found, it sends a 404 response.
     *
     * @param string $uri The request URI.
     * @return void
     */
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

    /**
     * Dispatches a CLI command.
     *
     * This method is responsible for handling CLI commands. It checks the global `$argv` array
     * for the command, determines the appropriate controller and method, and then calls the
     * corresponding method with any additional arguments.
     *
     * @return void
     */
    protected function dispatchCli(): void
    {
        global $argv;
        $command = $argv[1] ?? '';

        // Split the command into controller and method, defaulting to 'index' if method is not provided
        list($controller, $method) = array_pad(explode(':', $command), 2, 'index');
        $controllerClass = "App\\Controller\\$controller";

        // Check if the controller class and method exist, then call the method with additional arguments
        if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
            $controllerInstance = new $controllerClass();
            call_user_func_array([$controllerInstance, $method], array_slice($argv, 2));
        } // If the command is registered in cliCommands, call the corresponding handler
        elseif (isset($this->cliCommands[$command])) {
            call_user_func($this->cliCommands[$command], array_slice($argv, 2));
        } // If the command is not found, print an error message
        else {
            echo "Command not found: $command\n";
        }
    }
}