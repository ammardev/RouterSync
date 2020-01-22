<?php

namespace Luqta\RouterSync\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    protected $http;

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    public function requestMicroservice(Request $request)
    {
        $action = $request->route()->getAction();
        $files = $request->allFiles();
        $multipart = [];
        foreach ($files as $key => $file) {
            $multipart[] = [
                'name' => $key,
                'contents' => fopen($file->getRealPath(), 'r'),
                'fileName' => $file->getClientOriginalName(),
            ];
        }
        $response = $this->http->request($request->method(), $action['original_uri'], [
            'body' => $request->getContent(),
            'multipart' => $multipart,

        ]);

        return response($response->getBody(), $response->getStatusCode());
    }
}
