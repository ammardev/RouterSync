<?php

namespace Luqta\RouterSync\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Luqta\RouterSync\Http\IlluminateRequestResolver;

class GatewayController extends Controller
{
    protected $http;
    protected $resolver;

    public function __construct(Client $http, IlluminateRequestResolver $resolver)
    {
        $this->http = $http;
        $this->resolver = $resolver;
    }

    public function requestMicroservice(Request $illuminateRequest)
    {
        $this->resolver->setRequest($illuminateRequest);
        $request = $this->resolver->resolve();

        $response = $this->http->request(
            $request->method,
            config('app.url').'/'.$request->getMatchedUrl(),
            $request->toArray()
        );

        return response($response->getBody(), $response->getStatusCode());
    }
}
