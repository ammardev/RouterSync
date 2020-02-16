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
        $this->resolveAttributes();
        $this->resolveHeaders();
        $this->resolveBody();
        return $this->request;
    }

    protected function resolveAttributes()
    {
        $illuminateRoute = $this->illuminateRequest->route();
        if(isset($illuminateRoute[1]['original_uri'])) {
            $this->request->original_url = $illuminateRoute[1]['original_uri'];
            $this->request->setMatchedUrl($illuminateRoute[2]);
        }
        $this->request->method = $this->illuminateRequest->method();
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
        $multipart = [];
        foreach ($inputs as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value
            ];
        }

        foreach($files as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value
            ];
        }

        $this->request->setBody(['multipart' => $multipart]);
    }

    protected function resolveFormParams($inputs)
    {
        $this->request->setBody(['form_params' => $inputs ]);
    }
}
