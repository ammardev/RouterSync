<?php

namespace Luqta\RouterSync\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GatewayController extends Controller
{
    protected $http;

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    protected function replaceRouteParameters($route, &$parameters = [])
    {
        return preg_replace_callback('/\{(.*?)(:.*?)?(\{[0-9,]+\})?\}/', function ($m) use (&$parameters) {
            return isset($parameters[$m[1]]) ? Arr::pull($parameters, $m[1]) : $m[0];
        }, $route);
    }

    public function requestMicroservice(Request $request) 
    {
        $original_uri = $request->route()[1]['original_uri'];
        $matchedUrl = $this->replaceRouteParameters($original_uri, $request->route()[2]);

        $headers = [
                'Accept' => 'application/json',
        ];
        if($request->header('Authorization')) {
                $headers['Authorization'] = $request->header('Authorization');
        }

        $files = $request->allFiles();
        $multipart = [];
        foreach ($files as $key => $file) {
            $multipart[] = [
                'name' => $key,
                'contents' => fopen($file->getRealPath(), 'r'),
                'fileName' => $file->getClientOriginalName(),
            ];
        }
        $response = $this->http->request($request->method(), config('app.url') . '/' . $matchedUrl, [
            'http_errors' => false,
            'form_params' => $request->all(),
            'headers' => $headers
        ]);

        return response($response->getBody(), $response->getStatusCode());
    }
    
}
