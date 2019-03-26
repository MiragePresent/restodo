<?php

namespace App;

use App\Exception\HttpExceptionInterface;
use App\Http\Response;
use App\Http\Router;

/**
 * Class Application
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Application
{
    /**
     * Application router
     *
     * @var Router
     */
    private $router;

    /**
     * Application constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Process request
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $response = $this->router->handle();
        } catch (HttpExceptionInterface $e) {
            // create 404 response
            $response = new Response();
            $response->setStatusCode($e->getCode());
        }

        $response->send();
    }
}
