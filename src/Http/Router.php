<?php

namespace App\Http;

use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TaskController;

/**
 * Class Router
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Router
{
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
        [
            "path" => "/singup",
            "method" => Request::METHOD_POST,
            "controller" => AuthController::class,
            "action" => "register",
        ], [
            "path" => "/singin",
            "method" => Request::METHOD_POST,
            "controller" => AuthController::class,
            "action" => "login",
        ], [
            "path" => "/tasks",
            "method" => Request::METHOD_POST,
            "controller" => TaskController::class,
            "action" => "create",
        ],[
            "path" => "/tasks",
            "method" => Request::METHOD_GET,
            "controller" => TaskController::class,
            "action" => "index",
        ], [
            "path" => "/tasks/{id}/done",
            "method" => Request::METHOD_PATCH,
            "controller" => TaskController::class,
            "action" => "done",
        ],[
            "path" => "/tasks/{id}",
            "method" => Request::METHOD_DELETE,
            "controller" => TaskController::class,
            "action" => "delete",
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

        return $controller->{$action}(...array_values($attributes));
    }


    /**
     * Controller that handles request
     *
     * @return Controller
     */
    private function getController(): Controller
    {
        $className = $this->request->getRouteSettings('controller');

        return new $className($this->request);
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

        foreach ($this->routes as $route) {
            if ($route["method"] !== $this->request->getMethod()) {
                continue;
            }

            $attributes = [];

            $routeItems = explode("/", $route["path"]);

            if (count($requestItems) !== count($routeItems)) {
                continue;
            }

            for ($i = 0; $i < count($requestItems); $i++) {
                if (preg_match("/^{(?<attr>[a-zA-Z_]+)}$/", $routeItems[$i], $matches)) {
                    $attributes[$matches["attr"]] = $requestItems[$i];
                } elseif ($requestItems[$i] !== $routeItems[$i]) {
                    continue 2;
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
