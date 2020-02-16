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
        if (isset($illuminateRoute[1]['original_uri'])) {
            $this->request->original_url = $illuminateRoute[1]['original_uri'];
            $this->request->setMatchedUrl($illuminateRoute[2]);
        }
        $this->request->method = $this->illuminateRequest->method();
    }

    protected function resolveHeaders()
    {
        foreach ($this->illuminateRequest->header() as $key => $value) {
            if ($key == 'content-type') {
                continue;
            }
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
        $multipart = $this->encodeArrayToMultipart($inputs);
        $multipart = array_merge($multipart, $this->encodeArrayToMultipart($files));
        $this->request->setBody(['multipart' => $multipart]);
    }

    public function encodeArrayToMultipart($arr, $parent = null)
    {
        $multipart = [];
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $multipart = array_merge($multipart, $this->encodeArrayToMultipart($value, $this->formatMultipartParent($parent, $key)));
            } else {
                $multipart[] = [
                    'name' => $this->formatMultipartParent($parent, $key),
                    'contents' => $value,
                ];
            }
        }
        return $multipart;
    }

    public function formatMultipartParent($parent, $item)
    {
        if ($parent) {
            return $parent . '[' . $item . ']';
        } else {
            return $item;
        }
    }

    protected function resolveFormParams($inputs)
    {
        $this->request->setBody(['form_params' => $inputs]);
    }
}
