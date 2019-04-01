<?php

namespace App\Http;

/**
 * Class Request
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Request
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
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $content;

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
        $parsed = parse_url($uri);

        $this->method = $method;
        $this->uri = $parsed["path"] ?? "";

        foreach ($headers as $header => $value) {
            $this->headers[strtolower($header)] = $value;
        }

        $this->content = $body;

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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function hasHeader(string $name): bool
    {
        $name = strtolower($name);

        return isset($this->headers[$name]);
    }

    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);

        return $this->headers[$name] ?? null;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Whether is JSON request
     *
     * @return bool
     */
    public function isJson(): bool
    {
        $contentType = $this->getHeader("Content-Type");

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
                $this->parsedJson = json_decode($this->getContent(), true);
            }

            return $this->parsedJson[$name] ?? null;
        }

        return null;
    }
}
