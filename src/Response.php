<?php


namespace Claassenmarius\PhpSkynet;

use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * The underlying PSR response
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * The decode JSON response
     *
     * @var array
     */
    protected $decoded;

    /**
     * Create a new response instance
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get the underlying PSR response for the response.
     *
     * @return ResponseInterface
     */
    public function toPsrResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get the body of the response
     *
     * @return string
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * Get the JSON decoded body of the response as an array or scalar value.
     *
     * @return array | string | int
     */
    public function json(): mixed
    {
        if (! $this->decoded) {
            $this->decoded = json_decode($this->body(), true);
        }

        return $this->decoded;
    }

    /**
     * Get the JSON decoded body of the response as an object.
     *
     * @return object
     */
    public function object(): object
    {
        return json_decode($this->body(), false);
    }

    /**
     * Get a header from the response.
     *
     * @param string $header
     * @return string
     */
    public function header(string $header): string
    {
        return $this->response->getHeaderLine($header);
    }

    /**
     * Get the headers from the response.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Get the status code of the response.
     *
     * @return int
     */
    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Determine if the request was successful
     *
     * @return bool
     */
    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     *
     * @return bool
     */
    public function ok(): bool
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     *
     * @return bool
     */
    public function failed(): bool
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     *
     * @return bool
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     *
     * @return bool
     */
    public function serverError(): bool
    {
        return $this->status() >= 500;
    }
}
