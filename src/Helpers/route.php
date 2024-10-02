<?php

if (!function_exists('isCli')) {
    function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }
}

if (!function_exists('isApi')) {
    function isApi(): bool
    {
        return str_starts_with($_SERVER['REQUEST_URI'], '/api/');
    }
}

if (!function_exists('isWeb')) {
    function isWeb(): bool
    {
        return !isApi() && !isCli();
    }
}

if (!function_exists('isPost')) {
    function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

if (!function_exists('isPut')) {
    function isPut(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }
}

if (!function_exists('isDelete')) {
    function isDelete(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }
}

if (!function_exists('isGet')) {
    function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}

if (!function_exists('isOptions')) {
    function isOptions(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'OPTIONS';
    }
}

if (!function_exists('isHead')) {
    function isHead(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'HEAD';
    }
}

if (!function_exists('isPatch')) {
    function isPatch(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'PATCH';
    }
}

