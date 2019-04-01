<?php

namespace App\Http;

/**
 * Class Response
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Response
{

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $body;


    public function __construct()
    {
        $this->statusCode = 200;
        $this->headers = [];
    }

    /**
     * Change response status code
     *
     * @param int $code
     *
     * @return Response
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * Add headers to response
     *
     * @param array $headers
     *
     * @return Response
     */
    public function addHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Set response data
     *
     * @param string $body
     *
     * @return Response
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Returns response header
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Returns response headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Sends response
     */
    public function send(): void
    {
        $http_line = sprintf('HTTP/1.1 %s', $this->getStatusCode());

        header($http_line, true, $this->getStatusCode());

        foreach ($this->getHeaders() as $name => $value) {
            header("$name: $value", false);
        }

        echo $this->body;
        exit;
    }
}
