<?php

namespace App\Http;

use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;

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
     * Http request
     *
     * @var Request
     */
    protected $request;

    /**
     * App routes
     *
     * @var array
     */
    private $routes = [
        "/singin" => [
            "method" => self::METHOD_GET,
            "controller" => AuthController::class,
            "action" => "login",
        ],
        "/singup" => [
            "method" => self::METHOD_POST,
            "uses" => "AuthController@register",
        ],
        "/singout" => [
            "method" => self::METHOD_GET,
            "uses" => "AuthController@logout",
        ],
        "/users/{user_id}/tasks/{task_id}" => [
            "method" => self::METHOD_GET,
            "controller" => "",
        ],
    ];

    /**
     * Current route
     *
     * @var array
     */
    protected $current;

    /**
     * Router constructor.
     *
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function __construct()
    {
        $this->request = $this->createRequest();
        $this->request->setRouteSettings($this->detectRoute());
    }

    /**
     * Process request
     *
     * @return Response
     */
    public function handle(): Response
    {
        $controller = $this->getController();
        $attributes = $this->request->getRouteSettings('attributes');
        $action = $this->request->getRouteSettings('action');

        return $controller->{$action}(...$attributes);
    }


    /**
     * Controller that handles request
     *
     * @return Controller
     */
    public function getController(): Controller
    {
        $className = $this->request->getRouteSettings('controller');
        /** @var Controller $controller */
        $controller = new $className();
        $controller->setRequest($this->request);

        return $controller;
    }

    /**
     * Creates PSR request
     *
     * @return Request
     * @throws BadRequestException
     */
    private function createRequest(): Request
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $method = 'PUT';
            } else {
                throw new BadRequestException("Unexpected Header");
            }
        }

        $headers = getallheaders();

        return new Request(
            $method,
            $_SERVER['REQUEST_URI'],
            $headers,
            file_get_contents('php://input')
        );
    }

    /**
     * Detects route settings
     *
     * @return array Array with settings
     * @throws NotFoundException
     */
    protected function detectRoute(): array
    {
        $detected = null;
        $requestItems = explode("/", $this->request->getUri()->getPath());

        foreach ($this->routes as $routeUri => $route) {
            $attributes = [];

            $routeItems = explode("/", $routeUri);

            if (count($requestItems) !== count($routeItems)) {
                continue;
            }

            for ($i = 0; $i < count($requestItems); $i++) {
                if (preg_match("/^{(?<attr>[a-zA-Z_]+)}$/", $routeItems[$i], $matches)) {
                    $attributes[$matches["attr"]] = $requestItems[$i];
                } elseif ($requestItems[$i] !== $routeItems[$i]) {
                    continue;
                }
            }

            $route['attributes'] = $attributes;
            $detected = $route;
            break;
        }

        if (!$detected) {
            throw new NotFoundException("API not found");
        }

        return $detected;
    }
}
