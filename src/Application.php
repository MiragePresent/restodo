<?php

namespace App;

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
        echo "Application result";
    }
}
