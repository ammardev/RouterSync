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
        $query = trim(str_replace('/&', '?', $_SERVER['QUERY_STRING']), '/');
        $query .= $this->setCustomQueryString($illuminateRequest);
        if($query[0] == '&') {
            $query[0] = '?';
        }
        $method = $request->method;

        if($method == 'PATCH') {
            $method = 'POST';
        }

        $response = $this->http->request(
            $method,
            config('app.url').'/'.$request->getMatchedUrl() . $query,
            $request->toArray()
        );

        return response($response->getBody(), $response->getStatusCode());
    }

    /**
     * A user can override this method to add custom queries
     */
    protected function setCustomQueryString(Request $request)
    {
        return '';
    }
}
