<?php

namespace App\Http;

use GuzzleHttp\Psr7\Response as PsrResponse;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class Response
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Response
{
    /**
     * PSR-7 response
     *
     * @var PsrResponse
     */
    private $psrResponse;

    public function __construct()
    {
        $this->psrResponse = new PsrResponse(200, []);
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
        $this->psrResponse = $this->psrResponse->withStatus($code);

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
        foreach ($headers as $header => $value) {
            $this->psrResponse = $this->psrResponse->withAddedHeader($header, $value);
        }

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
        $this->psrResponse = $this->psrResponse->withBody(stream_for($body));

        return $this;
    }

    /**
     * Sends response
     */
    public function send(): void
    {
        $http_line = sprintf('HTTP/%s %s %s',
            $this->psrResponse->getProtocolVersion(),
            $this->psrResponse->getStatusCode(),
            $this->psrResponse->getReasonPhrase()
        );

        header($http_line, true, $this->psrResponse->getStatusCode());

        foreach ($this->psrResponse->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        $stream = $this->psrResponse->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }
    }
}
