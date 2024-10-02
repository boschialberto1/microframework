<?php

namespace App\Core;

use Exception;

class View
{
    protected string $templateDir;

    public function __construct(string $templateDir)
    {
        $this->templateDir = rtrim($templateDir, '/') . '/';
    }

    /**
     * @throws Exception
     */
    public function render(string $template, array $data = []): string
    {
        $templateFile = VIEWS_PATH . $this->templateDir . $template . '.php';
        if (!file_exists($templateFile)) {
            throw new Exception("Template file not found: $templateFile");
        }

        // Extract data to variables
        extract($data);

        // Start output buffering
        ob_start();
        include $templateFile;
        // Get the contents of the buffer and end buffering
        return ob_get_clean();
    }
}