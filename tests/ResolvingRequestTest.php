<?php

namespace Luqta\RouterSync\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Luqta\RouterSync\Http\IlluminateRequestResolver;

class ResolvingRequestTest extends TestCase
{

    private $resolver;

    protected function setUp(): void 
    {
        $this->resolver = new IlluminateRequestResolver();
        parent::setUp();
    }

    public function test_accept_header_value() {
        $request = $this->createAndResolveRequest();
        $this->assertTrue(
            $request->getHeader('accept') === 'application/json',
            'Accept header value is incorrect'
        );
    }

    public function test_custom_header_resolving() {
        $request = $this->createAndResolveRequest('GET', '/', [] ,[
            'X-Test' => 'success'
        ]);
        $this->assertTrue(
            $request->getHeader('X-Test') === 'success',
            'The header in the original request is not exist in the resolved one.'
        );
    }

    public function test_resolving_input_params() {
        $request = $this->createAndResolveRequest('POST', '/', [
            'lang' => 'php'
        ]);
        $this->assertTrue($request->getBody()['form_params']['lang'] === 'php');
    }

    public function test_resolving_multipart_body() {
        $file = UploadedFile::fake()->createWithContent('file.txt', 'routersync');
        $request = $this->createAndResolveRequest('POST', '/', [
            'lang' => 'php'
        ],
        [],
        [
            'doc' => $file
        ]);
        $this->assertTrue($request->getBody()['multipart'][0]['name'] === 'lang');
        $this->assertTrue($request->getBody()['multipart'][0]['contents'] === 'php');
        $this->assertTrue($request->getBody()['multipart'][1]['name'] === 'doc');
        $this->assertTrue($request->getBody()['multipart'][1]['contents'] === $file);
    }

    private function createAndResolveRequest(
        $method = 'GET',
        $path = '/',
        $input_params = [],
        $headers = [],
        $files = []
    ) 
    {
        $illuminateRequest= Request::create($method, $path, $input_params, [], $files);
        foreach($headers as $key => $value) {
            $illuminateRequest->headers->set($key, $value);
        }
        $this->resolver->setRequest($illuminateRequest);
        return $this->resolver->resolve();
    }
}
