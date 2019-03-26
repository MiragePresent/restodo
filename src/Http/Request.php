<?php

namespace App\Http;

use GuzzleHttp\Psr7\Request as PsrRequest;
/**
 * Class Request
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Request extends PsrRequest
{
    /**
     * Http method OPTIONS
     *
     * @var string
     */
    public const METHOD_OPTIONS = "OPTIONS";

    /**
     * Http method GET
     *
     * @var string
     */
    public const METHOD_GET = "GET";

    /**
     * Http method POST
     *
     * @var string
     */
    public const METHOD_POST = "POST";

    /**
     * Http method PATCH
     *
     * @var string
     */
    public const METHOD_PATCH = "PATCH";

    /**
     * Http method DELETE
     *
     * @var string
     */
    public const METHOD_DELETE = "DELETE";

    /**
     * Authentication handler
     *
     * @var Auth
     */
    public $auth;

    /**
     * Route settings
     * [method => ..., controller => ..., action => ..., attributes => ...]
     *
     * @var array
     */
    protected $routeSettings;

    /**
     * Passed request JSON
     *
     * @var array
     */
    protected $parsedJson;

    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
        parent::__construct($method, $uri, $headers, $body, $version);

        $this->auth = new Auth($this);
    }

    /**
     * Set route settings of request
     *
     * @param array $settings
     *
     * @return Request
     */
    public function setRouteSettings(array $settings): self
    {
        $this->routeSettings = $settings;

        return $this;
    }

    /**
     * Returns request's route settings
     *
     * @param string|null $name Setting name
     *
     * @return mixed
     */
    public function getRouteSettings(string $name = null)
    {
        if ($name) {
            return $this->routeSettings[$name] ?? null;
        }

        return $this->routeSettings;
    }

    /**
     * Whether is JSON request
     *
     * @return bool
     */
    public function isJson(): bool
    {
        $contentType = current($this->getHeader("Content-Type"));

        return $contentType && false !== strpos($contentType, 'json');
    }

    /**
     * Returns request input
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name)
    {
        if ($this->isJson()) {
            if (!$this->parsedJson) {
                $this->parsedJson = json_decode($this->getBody()->getContents(), true);
            }

            return $this->parsedJson[$name] ?? null;
        }

        return null;
    }
}
