<?php


namespace Claassenmarius\PhpSkynet\Tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public Response $psr7Response;

    public \Claassenmarius\PhpSkynet\Response $response;

    protected function setUp(): void
    {
        $this->psr7Response = new Response(
            200,
            ['Content-Type' => 'application/json; charset=utf-8'],
            "{\"Key\":\"Value\"}"
        );

        $this->response = new \Claassenmarius\PhpSkynet\Response($this->psr7Response);
    }

    /** @test */
    public function it_returns_a_psr_7_response_type()
    {
        $psr7Response = $this->response->toPsrResponse();

        $this->assertInstanceOf(Response::class, $psr7Response);
    }

    /** @test */
    public function it_returns_the_body_of_a_psr_7_response()
    {
        $this->assertEquals("{\"Key\":\"Value\"}", $this->response->body());
    }

    /** @test */
    public function it_returns_the_body_as_an_array_of_a_psr_7_response()
    {
        $this->assertEquals(["Key" => "Value"], $this->response->json());
    }

    /** @test */
    public function it_returns_the_body_as_an_object_of_a_psr_7_response()
    {
        $this->assertInstanceOf(\stdClass::class, $this->response->object());
    }

    /** @test */
    public function it_returns_the_header_of_a_psr_7_response()
    {
        $this->assertEquals('application/json; charset=utf-8', $this->response->header('Content-Type'));
    }

    /** @test */
    public function it_returns_all_the_headers_of_a_psr_7_response()
    {
        foreach ($this->response->headers() as $headerValue) {
            $this->assertEquals('application/json; charset=utf-8', implode(', ', $headerValue));
        }
    }

    /** @test */
    public function it_returns_a_200_success_http_code()
    {
        $this->assertEquals(true, $this->response->successful());
    }

    /** @test */
    public function it_returns_a_client_error_http_code()
    {
        $newPsr7Response = $this->psr7Response->withStatus(400);
        $newResponse = new \Claassenmarius\PhpSkynet\Response($newPsr7Response);
        $this->assertEquals(true, $newResponse->clientError());
    }

    /** @test */
    public function it_returns_a_server_error_http_code()
    {
        $newPsr7Response = $this->psr7Response->withStatus(500);
        $newResponse = new \Claassenmarius\PhpSkynet\Response($newPsr7Response);
        $this->assertEquals(true, $newResponse->serverError());
    }

    /** @test */
    public function it_returns_a_failed_error_http_code()
    {
        $newPsr7Response = $this->psr7Response->withStatus(401);
        $newResponse = new \Claassenmarius\PhpSkynet\Response($newPsr7Response);
        $this->assertEquals(true, $newResponse->failed());
    }
}
