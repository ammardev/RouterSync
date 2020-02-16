<?php

namespace Luqta\RouterSync\Http;

use Illuminate\Support\Arr;

class Request
{
    public $original_url;
    protected $matched_url;
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

    public function setMatchedUrl($parameters)
    {
        $this->matched_url = $this->replaceRouteParameters($this->original_url, $parameters);
    }

    protected function replaceRouteParameters($route, &$parameters = [])
    {
        return preg_replace_callback('/\{(.*?)(:.*?)?(\{[0-9,]+\})?\}/', function ($m) use (&$parameters) {
            return isset($parameters[$m[1]]) ? Arr::pull($parameters, $m[1]) : $m[0];
        }, $route);
    }
}
