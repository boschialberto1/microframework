<?php

namespace App\Core;

use Exception;
use Throwable;

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
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        $this->renderError($errno, $errstr, $errfile, $errline);
    }

    /**
     * @throws Exception
     */
    public function handleException(Throwable $exception): void
    {
        $this->renderError(
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
            $this->renderError(
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
    private function renderError(int $code, string $message, string $file, int $line): void
    {
        if (isCli()) {
            $this->renderCliError($code, $message, $file, $line);
        } elseif (isApi()) {
            $this->renderApiError($code, $message, $file, $line);
        } else {
            $this->renderHtmlError($code, $message, $file, $line);
        }
        exit;
    }

    /**
     * @throws Exception
     */
    private function renderHtmlError(int $code, string $message, string $file, int $line): void
    {
        http_response_code(500);
        $template500 = new View('Errors');
        echo $template500->render('500', [
            'code' => $code,
            'message' => $message,
            'file' => $file,
            'line' => $line,
        ]);
    }

    private function renderApiError(int $code, string $message, string $file, int $line): void
    {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => [
                'code' => $code,
                'message' => $message,
                'file' => $file,
                'line' => $line,
            ]
        ], JSON_PRETTY_PRINT);
    }

    private function renderCliError(int $code, string $message, string $file, int $line): void
    {
        echo "Error:\n";
        echo "\tCode: $code\n";
        echo "\tMessage: $message\n";
        echo "\tFile: $file\n";
        echo "\tLine: $line\n";
    }
}