<?php

namespace App\Core;

use Exception;
use JetBrains\PhpStorm\NoReturn;

class ErrorHandler
{
    public function __construct()
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     * @throws Exception
     */
    #[NoReturn]
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        $this->renderErrorPage($errno, $errstr, $errfile, $errline);
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function handleException(\Throwable $exception): void
    {
        $this->renderErrorPage(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }

    /**
     * @throws Exception
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null) {
            $this->renderErrorPage(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    private function renderErrorPage(int $code, string $message, string $file, int $line): void
    {
        http_response_code(500);
        $template500 = new View('Errors');
        echo $template500->render('500', [
            'code' => $code,
            'message' => $message,
            'file' => $file,
            'line' => $line,
        ]);
        exit;
    }
}