<?php

namespace App\Http;

/**
 * Class Router
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Router
{
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
     * App routes
     *
     * @var array
     */
    private $routes = [
        "singin" => [
            "method" => self::METHOD_GET,
            "uses" => "AuthController@login",
        ],
        "singup" => [
            "method" => self::METHOD_POST,
            "uses" => "AuthController@register",
        ],
        "singout" => [
            "method" => self::METHOD_GET,
            "uses" => "AuthController@logout",
        ],
    ];

    /**
     * Checks whether route is valid
     *
     * @param string $method
     * @param string $uri
     *
     * @return bool
     */
    public function isValid(string $method, string $uri): bool {
        return isset($this->routes[$uri]) && $this->routes[$uri]["method"] === strtoupper($method);
    }
}
