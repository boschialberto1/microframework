<?php

namespace App\Core;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];

    public function setStatusCode(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    public function addHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function sendHeaders(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }

    public function html(string $content): void
    {
        $this->addHeader('Content-Type', 'text/html');
        $this->sendHeaders();
        echo $content;
    }

    public function json(array $data): void
    {
        $this->addHeader('Content-Type', 'application/json');
        $this->sendHeaders();
        echo json_encode($data);
    }

    public function text(string $content): void
    {
        $this->addHeader('Content-Type', 'text/plain');
        $this->sendHeaders();
        echo $content;
    }
}