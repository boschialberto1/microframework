<?php

use App\Core\Router;

$router = new Router();

// Define your CLI commands here
$router->cli('TestController:banana', function ($args) {
    $controller = new \App\Controller\TestController();
    $controller->index(...$args);
});