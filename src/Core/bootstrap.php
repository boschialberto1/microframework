<?php

// Autoload dependencies using Composer
use App\Core\Config;
use App\Core\Router;

require_once VENDOR_PATH . 'autoload.php';

// load Helper functions by autoload all files in the Helpers directory
foreach (glob(SRC_PATH . 'Helpers/*.php') as $filename) {
    require_once $filename;
}

// Load configuration using the Config handler
$config = Config::getInstance();

// Initialize the router
$router = new Router();

// Define routes
require_once SRC_PATH . 'Route/web.php';
require_once SRC_PATH . 'Route/api.php';
require_once SRC_PATH . 'Route/cli.php';

// Handle the incoming request
if (isCli()) {
    $router->dispatch('');
} else {
    $router->dispatch($_SERVER['REQUEST_URI']);
}