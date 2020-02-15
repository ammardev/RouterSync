<?php

namespace Luqta\RouterSync\Http;

use Illuminate\Http\Request as IlluminateRequest;

class IlluminateRequestResolver
{
    private $illuminateRequest;
    private $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    public function setRequest(IlluminateRequest $illuminateRequest)
    {
        $this->illuminateRequest = $illuminateRequest;
    }

    public function resolve(): Request
    {
        $this->resolveHeaders();
        $this->resolveBody();
        return $this->request;
    }

    protected function resolveHeaders()
    {
        foreach ($this->illuminateRequest->header() as $key => $value) {
            $this->request->addHeader($key, $value[0]);
        }
        $this->request->addHeader('Accept', 'application/json');
    }

    protected function resolveBody()
    {
        $inputs = $this->illuminateRequest->input();
        $files = $this->illuminateRequest->allFiles();
        if (count($files) === 0) {
            $this->resolveFormParams($inputs);
        } else {
            $this->resolveMultipart($inputs, $files);
        }
    }

    protected function resolveMultipart($inputs, $files)
    {
        $body = [];
        foreach ($inputs as $input) {
        }
    }

    protected function resolveFormParams($inputs)
    {
        $this->request->setBody(['form_params' => $inputs ]);
    }
}
