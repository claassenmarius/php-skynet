<?php


namespace Claassenmarius\PhpSkynet;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;


class Response
{
    /*
     * The underlying PSR response
     */
    protected ResponseInterface $response;

    /**
     * The decode JSON response
     *
     * @var array
     */
    protected $decoded;

    /*
     * Create a new response instance
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get the underlying PSR response for the response.
     */
    public function toPsrResponse(): ResponseInterface
    {
        return $this->response;
    }

    /*
     * Get the body of the response
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /*
     * Get the JSON decoded body of the response as an array or scalar value.
     */
    public function json(): mixed
    {
        if(! $this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        return $this->decoded;
    }

    /*
     * Get the JSON decoded body of the response as an object.
     */
    public function object(): object
    {
        return json_decode($this->body(), false);
    }

    /*
     * Get a header from the response.
     */
    public function header(string $header): string
    {
        return $this->response->getHeaderLine($header);
    }

    /*
     * Get the headers from the response.
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /*
     * Get the status code of the response.
     */
    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    /*
     * Determine if the request was successful
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /*
     * Determine if the response code was "OK".
     */
    public function ok()
    {
        return $this->status() === 200;
    }

    /*
     * Determine if the response indicates a client or server error occurred.
     */
    public function failed(): bool
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     */
    public function serverError(): bool
    {
        return $this->status() >= 500;
    }

    

}