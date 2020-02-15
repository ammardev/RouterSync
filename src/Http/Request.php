<?php

namespace Luqta\RouterSync\Http;

class Request
{
    protected $headers;
    protected $body;
    protected $files;
    protected $cookies;

    public function getHeader(string $key): string
    {
        $key = ucwords($key, '-');
        return $this->headers[$key];
    }

    public function addHeader(string $key, string $value)
    {
        $this->headers[ucwords($key, '-')] = $value;
    }

    public function setBody(array $body) {
        $this->body = $body;
    }

    public function getBody(): array {
        return $this->body;
    }
}
