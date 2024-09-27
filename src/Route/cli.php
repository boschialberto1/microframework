<?php

use App\Core\Router;

$router = new Router();

$router->cli('make:controller', function($args) {
    $controllerName = $args[0] ?? 'DefaultController';
    echo "Creating controller: $controllerName\n";
});