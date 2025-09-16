<?php
namespace App\Framework;

class Response
{
    public function __construct(
        private string $content,
        private int $status = 200,
        private array $headers = []
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
